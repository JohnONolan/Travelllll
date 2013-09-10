<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/** WordPress Administration File API */
require_once(ABSPATH . 'wp-admin/includes/file.php');

/** WordPress Image Administration API */
require_once(ABSPATH . 'wp-admin/includes/image.php');

/** WordPress Media Administration API */
require_once(ABSPATH . 'wp-admin/includes/media.php');

// CRON START
/*
 * Loads new tweets for active departures with hashcode and store it as is to departure_tweets_cache_data
 */
function departure_load_new_tweets() {
	global $post;
	static $field_hashtag = 'departure_blogtrip_hashtag';
	static $field_tweets_data = 'departure_tweets_cache_data';
	static $field_tweet_since_id = '_departure_tweet_since_id';
	static $field_date_start = 'departure_date_start';
	static $field_date_end = 'departure_date_end';
	static $field_image_cache = '_departure_tweet_image_cache';
	static $media_sizes = array('large', 'medium', 'small', 'thumb');

	add_filter('posts_join', 'departure_load_tweets_join' );
	add_filter('posts_where', 'departure_load_tweets_where' );
	add_filter('post_limits', 'departure_load_tweets_limit' );
	$args = array('post_type' => 'departure',
		'post_status' => 'publish');
	query_posts($args);
	remove_filter('posts_join', 'departure_load_tweets_join' );
	remove_filter('posts_where', 'departure_load_tweets_where' );
	remove_filter('post_limits', 'departure_load_tweets_limit' );
	if (have_posts()) {
		while (have_posts()) {
			the_post();
			// get hashtag, since, last load time
			update_postmeta_cache( get_the_ID() );
			$custom_meta = get_post_custom();
			$hash_tag = $custom_meta[$field_hashtag][0];
			// get tweets
			$tweets = unserialize($custom_meta[$field_tweets_data][0]);
			if (!is_array($tweets) || !$tweets) {
				$tweets = array();
			}

			$tweet_images = unserialize($custom_meta[$field_image_cache][0]);
			if (!is_array($tweet_images) || !$tweet_images) {
				$tweet_images = array();
			}

			$since_id = $custom_meta[$field_tweet_since_id][0];
			$date_start = $custom_meta[$field_date_start][0];
			$date_end = $custom_meta[$field_date_end][0];
			// store tweets
			$new_tweets = get_new_tweets($hash_tag, $since_id, $date_start, $date_end, $tweets);
			
			// get and attach photos
			// use media_sideload_image();
			foreach ($new_tweets as $twit) {
				if (isset($tweets[$twit->id_str])) {
					continue;
				}
				if (!isset($twit->entities->media)) {
					continue;
				}
				foreach ($twit->entities->media as $media) {
					if ('photo' == $media->type) {
						foreach ($media_sizes as $size) {
							if (isset($media->sizes->$size) && !isset($tweet_images[$media->media_url])) {
								// $html = "<img src='$src' alt='$alt' />";
								$html_str = media_sideload_image($media->media_url . ':' . $size, get_the_ID(), urldecode($twit->text));
								if (is_string($html_str)) {
									// attachment processed success
									$src = substr($html_str,
											strpos($html_str, 'src=\'') + 5,
											strpos($html_str, '\'', strpos($html_str, 'src=\'') + 5) - strpos($html_str, 'src=\'') - 5
											);
									// get attachments id
									$id = get_attachment_id_from_guid ($src);
									// add to post id custom fields
									add_post_meta($id, '_twits_id_str', $twit->id_str);
									add_post_meta($id, '_twits_url_str', sprintf('http://twitter.com/%s/status/%s', $twit->from_user, $twit->id_str));
									$tweet_images[ $media->media_url ] = array('_twits_id_str' => $twit->id_str, 'attachment_id' => $id);
									break;
								} else {
									// attachment failed - delete them
									wp_delete_attachment($html_str);
								}								
							}
						}
                    }
                }
			}
			ksort($new_tweets);
			$new_tweets = array_reverse($new_tweets);
			// add to custom tweet field twits and images
			$tweets = array_merge($new_tweets, $tweets);
			update_post_meta(get_the_ID(), $field_tweets_data, $tweets);
			update_post_meta(get_the_ID(), $field_image_cache, $tweet_images);
			// set new last time
			$since_id = max(array_keys($tweets));
			update_post_meta(get_the_ID(), $field_tweet_since_id, $since_id);
		}
	}
	
}

/*
 * define attachment post id by url
 */
function get_attachment_id_from_guid ($image_src) {
	global $wpdb;
	$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
	$id = $wpdb->get_var($query);
	return $id;
}

/*
 * added additional conditions for current departure
 */
function departure_load_tweets_join($join) {
	global $wpdb;
	static $field_date_start = 'departure_date_start';
	static $field_date_end = 'departure_date_end';
	$join .= ' JOIN ' . $wpdb->postmeta . ' AS psd ON (psd.post_id=' . $wpdb->posts . '.ID AND psd.meta_key="' .  $field_date_start . '")';
	$join .= ' JOIN ' . $wpdb->postmeta . ' AS ped ON (ped.post_id=' . $wpdb->posts . '.ID AND ped.meta_key="' .  $field_date_end . '")';
	return $join;
}

/*
 * added additional conditions for current departure
 */
function departure_load_tweets_where($where) {
	if (empty($where)) {
		$where = '1=1';
	}
	$where .= ' AND psd.meta_value <= UNIX_TIMESTAMP() AND ped.meta_value >= UNIX_TIMESTAMP()';
	return $where;
}

/*
 * remove limits for departure searching
 */
function departure_load_tweets_limit($limit) {
	return '';
}

/*
 * gets and formats a new twits
 */
function get_new_tweets($tweet_tag, $since_id = 0, $since_date = '', $until_date = '', $tweets_data = array(), $tweet_users = array()) {
    $new_max_id = $since_id;
    // get tweet search api
    list($max_id, $twits) = get_last_tweets($since_id, $tweet_tag, $since_date, $until_date, $tweet_user);
    $new_max_id = max($new_max_id, $max_id);
    if (empty($twits)) {
        $twits = array();
    }
    $new_twits = array();
    // format data
    // handle tweet: format them, check and bind photos
    foreach ($twits as $twit) {
		if (!isset($tweets_data[$twit->id_str])) {
			$new_twits[$twit->id_str] = $twit;
        }
    }
    return $new_twits;
}

/*
 * Selects twits using Twitter Search API
 */
function get_last_tweets($since_id, $hash_tag, $since_date = '', $until_date = '', $user = '') {
    //static $api_url = 'http://search.twitter.com/search.json?callback=?';
    static $api_url = 'http://search.twitter.com/search.json?';
	$hash_tag = urlencode($hash_tag);
	if (empty($since_id) && !empty($since_date)) {
		$hash_tag .= ' since:' . date('Y-m-d', $since_date);
	}

    $twitter_params = array(
        'since_id' => $since_id,
        'lang' => 'en',
        'rpp' => 100,
        'show_user' => 'true',
        'show_avatar' => 'true',
        'allow_reply' => 'true',
        'show_tweet_link' => 'true',
        'include_entities' => 'true',
        'q' => $hash_tag
    );
	if (!empty($user)) {
		$twitter_params['from'] = $user;
	}
	if (!empty($until_date)) {
		$twitter_params['until'] = date('Y-m-d', $until_date);
	}
	$twitter_params_array = array();
	foreach ($twitter_params as $twitter_param_key => $twitter_param_value) {
		$twitter_params_array[] = $twitter_param_key . '=' . urlencode($twitter_param_value);
	}
    $get_url = $api_url . implode('&', $twitter_params_array);
    $answer = array();
    $i = 0;
	$max_id = $since_id;
    while ($get_url != '') {
        $response = wp_remote_get($get_url);
        if (is_wp_error($response)) {
			break;
        }
        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $twits = json_decode($body);
        $get_url = (!empty($twits->next_page) && $get_url != $api_url . $twits->next_page) ? $api_url . $twits->next_page : '';
		$max_id = $twits->max_id_str;
		if (is_array($twits->results)) {
			$answer = array_merge($answer, $twits->results);
		}
    }
    
    return array($max_id, $answer);
 }
 // CRON END

?>
