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

/*
 * Converts date for displaying
 */
function departure_date_format($time) {
	return date('d', $time) . '&nbsp;' . date('M', $time) . '&nbsp;' . date('y', $time);
}

/*
 * return formatted taxonomies terms for post
 */
function get_blogtrip_taxonomies($post = 0, $args = array(), $format = '' ) {
	if ( is_int($post) )
		$post =& get_post($post);
	elseif ( !is_object($post) )
		$post =& $GLOBALS['post'];

	$args = wp_parse_args( $args, array(
		'template' => '%s: %l.',
	) );
	extract( $args, EXTR_SKIP );

	$taxonomies = array();

	if ( !$post )
		return $taxonomies;

	foreach ( get_object_taxonomies($post) as $taxonomy ) {
		$t = (array) get_taxonomy($taxonomy);
		if ( empty($t['label']) )
			$t['label'] = $taxonomy;
		if ( empty($t['args']) )
			$t['args'] = array();
		if ( empty($t['template']) )
			$t['template'] = $template;

		$terms = get_object_term_cache($post->ID, $taxonomy);
		if ( empty($terms) ) {
			$terms = wp_get_object_terms($post->ID, $taxonomy, $t['args']);
		}

		if ('' != $format) {
			$taxonomies[$taxonomy] = array();
			foreach ($terms as $term) {
				$taxonomies[$taxonomy][$term->slug] = (isset($term->$format)) ? $term->$format :  $term->name;
			}
		} else {
			$taxonomies[$taxonomy] = $terms;
		}

	}
	return $taxonomies;
}

/*
 * Binds user and taxonomy
 */
 function get_users_from_taxonomy($user_data) {
	 $users = array();
	 foreach ($user_data as $user_search) {
		 $user = get_users(array('search' => $user_search));
		 $users[] = $user[0];
	 }
	 return $users;
 }

/*
 * get twitter user custom type value for $users
 */
 function get_user_twits($users) {
	 $twit_users = array();
	 foreach ($users as $user) {
		 $custom_data = get_user_meta($user->ID, 'twitter');
		 if (!empty($custom_data) && !empty($custom_data[0])) {
			 $custom_data = explode('/', $custom_data[0]);
			 $custom_data = array_pop($custom_data);
			 $twit_users[] = $custom_data;
		 }
	 }
	 return $twit_users;
 }

/*
 * Gets all post, except revisions, with post_tag term as $tag
 * 
 */
 function get_posts_by_tag($tag, $order = 'ASC', $limit = 0) {
	global $wpdb;
	$term_id = get_term_by('slug', $tag, 'post_tag');
	if (!empty($limit)) {
		$limit = ' LIMIT ' . intval($limit);
	}

	$q = "SELECT DISTINCT tr.object_id FROM $wpdb->term_relationships AS tr
			INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
			INNER JOIN $wpdb->posts AS p ON tr.object_id=p.ID
			WHERE tt.taxonomy IN ('post_tag') AND tt.term_id IN ($term_id->term_id) 
			AND p.post_type NOT IN ('revision') AND p.post_status='publish'
			ORDER BY tr.object_id $order $limit";
	$object_ids = $wpdb->get_col($q);
	$blog_object_ids = array();
	foreach ($object_ids as $i => $post_blog_id) {
		unset($object_ids[$i]);
		$blog_object_ids[$post_blog_id] = get_post($post_blog_id);
	}
	return $blog_object_ids;
}

/*
 * Return html for twit on single departure page
 */
function departure_display_tweet($twit, $hash_tag = '') {
	$time_format = time_difference(strtotime($twit->created_at));
	$hash_tag = '';
	$html = '<li>
				<div class="photo-wrap">
					<a href="http://twitter.com/' . $twit->from_user . '" title="' . $twit->from_user . '"><img src="' . $twit->profile_image_url . '" alt="' . $twit->from_user . '"  width="40" height="40" /></a>
				</div>' . parse_tweet($twit->text) . ' <a href="http://twitter.com/?status=@' . $twit->from_user . '%20&in_reply_to_status_id=' . $twit->id_str . '&in_reply_to=' . $twit->from_user . '" target="_blank"> </a>'
			. $hash_tag . '<span class="date"><a href="http://twitter.com/' . $twit->from_user . '/status/' . $twit->id_str . '" target="_blank">' . $time_format . '</a></span>
			</li>';
	return $html;
}

/*
 * parse tweet text
 */
function parse_tweet($tweet) {
	return trim($tweet);
}

/*
 * Twit time format
 */
function time_difference($time) {
    $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
    $lengths = array(60, 60, 24, 7, 4.35, 12, 10);
	$count_length = count($lengths);

    $now = time();
    $difference = $now - $time;

    for($j = 0; $difference >= $lengths[$j] && $j < $count_length; $j++) {
        $difference /= $lengths[$j];
    }

    $difference = round($difference);
    if ($difference != 1) {
        $periods[$j] .= "s";
    }

    return $difference . ' ' . $periods[$j] . ' ago';
}

?>
