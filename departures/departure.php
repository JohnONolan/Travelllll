<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once ( get_template_directory() . '/departures/departure-cache.php' );
// create custom type
// create fields
// create taxonomies: statuses, crew members, organizer
add_action('init', 'create_tax_terms' );
add_action('init', 'departure_register');
add_action('departure_load_new_tweets_hook', 'departure_load_new_tweets');
//$timestamp_schedule = wp_next_scheduled('departure_load_new_tweets_hook');
//wp_unschedule_event($timestamp_schedule, 'departure_load_new_tweets_hook');
if (!wp_next_scheduled('departure_load_new_tweets_hook')) {
	//wp_schedule_single_event(time(), 'departure_load_new_tweets_hook');
	wp_schedule_event(time(), 'hourly', 'departure_load_new_tweets_hook', array());
}


/*
 * Creates taxonomies
 */
function create_tax_terms() {

	static $taxonomies = array(
		'status' => array(
			'Active' => 'active',
			'Cancelled' => 'cancelled'
		)
	);
	
	$args = array(
		'hierarchical' => true, 
		'label' => 'Trip Status',
		'singular_label' => 'status', 
		'rewrite' => array( 'slug' => 'departures/status', 'with_front' => false )
	);
	register_taxonomy( 'status', array( 'departures' ), $args );
	
	$args = array( 
		'hierarchical' => false, 
		'label' => 'Crew', 
		'singular_label' => 'crew',
		'rewrite' => array( 'slug' => 'departures/crew', 'with_front' => false )
	);
	register_taxonomy( 'crew', array( 'departures' ), $args );
	
	$args = array( 
		'hierarchical' => false, 
		'label' => 'Organizers', 
		'singular_label' => 'organizer', 
		'rewrite' => array( 'slug' => 'departures/organizer', 'with_front' => false ) 
	);
	register_taxonomy( 'organizer', array( 'departures' ), $args );
	
	$args = array( 
		'hierarchical' => false, 
		'label' => 'Countries', 
		'singular_label' => 'country', 
		'rewrite' => array( 'slug' => 'departures/country', 'with_front' => false )
	);
	register_taxonomy( 'country', array( 'departures' ), $args );

	foreach ($taxonomies as $taxonomy => $values) {
		$terms = get_terms($taxonomy, array("hide_empty" => false));
		$terms_names = array();
		foreach ($terms as $term) {
			$terms_names[$term->name] = $term;
		}
		foreach ($values as $tax_term => $tax_slug) {
			if (!isset($terms_names[$tax_term])) {
				wp_insert_term($tax_term, $taxonomy, array('slug' => $tax_slug));
			}
		}
	}
}


/*
 * departure custom post type register
 */
function departure_register() {
	$labels = array(
		'name' => _x('Departures', 'post type general name'),
		'singular_name' => _x('Trip', 'post type singular name'),
		'add_new' => _x('Add New', 'trip'),
		'add_new_item' => __('Add New Trip'),
		'edit_item' => __('Edit Trip'),
		'new_item' => __('New Trip'),
		'view_item' => __('View Trip'),
		'search_items' => __('Search Departures'),
		'not_found' => __('Nothing found'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => ''
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => get_stylesheet_directory_uri() . '/departures/images/dep-menu-icon.png',
		'rewrite' => true,
		'capability_type' => 'page',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title', 'editor'),
        'show_in_nav_menus' => true,
        'show_in_menu' => true,
        'show_in_admin_bar' => true,
		'exclude_from_search' => false,
		'has_archive' => true
	);

	register_post_type("departures", $args);

}

add_action("admin_init", "departure_admin_init");

/*
 * added block to admin departure edit page
 */
function departure_admin_init() {
    // add limits only for dep page
	add_meta_box("departure_general-meta", "Trip Information", "departure_general", "departures", "normal", "low");
/*	if (!wp_next_scheduled('departure_load_new_tweets_hook')) {
		wp_schedule_event(time(), 'hourly', 'departure_load_new_tweets_hook');
	}
 */
}

/*
 * theme block for custom fields
 */
function departure_general() {
	global $post;

	if (is_object($post) && 'departures' == $post->post_type) {
		$custom = get_post_custom($post->ID);
	
		$blogtrip_id = (isset($custom["departure_blogtrip_hashtag"][0])) ? $custom["departure_blogtrip_hashtag"][0] : '';
		$destination = (isset($custom["departure_destination"][0])) ? $custom["departure_destination"][0] : '';
		$date_start = (isset($custom["departure_date_start"][0])) ? $custom["departure_date_start"][0] : -1;
		$date_end = (isset($custom["departure_date_end"][0])) ? $custom["departure_date_end"][0] : -1;
		$impression = (isset($custom["departure_impression"][0])) ? $custom["departure_impression"][0] : '';
		$people_reach = (isset($custom["departure_people_reach"][0])) ? $custom["departure_people_reach"][0] : '';
		$tweet = (isset($custom["departure_tweets_count"][0])) ? $custom["departure_tweets_count"][0] : '';

		echo theme_form_input_text('BlogTripID HashTag', 'departure_blogtrip_hashtag', $blogtrip_id);
		echo theme_form_input_text('Destination', 'departure_destination', $destination);
		echo departure_time_form_element($date_start, 'Departure date', 'dd_y', 'dd_m', 'dd_d');
		echo departure_time_form_element($date_end, 'Return date', 'rd_y', 'rd_m', 'rd_d');
		echo theme_form_input_text('Impressions', 'departure_impression', $impression);
		echo theme_form_input_text('People reach', 'departure_people_reach', $people_reach);
		echo theme_form_input_text('Tweets', 'departure_tweets_count', $tweet);
	}
}

/*
 * theme and format time fields
 */
function departure_time_form_element($time = -1, $label = 'Date', $id_year = 'departure_idy', $id_month = 'departure_idm', $id_day = 'departure_idd', $tab_index = 0) {
	global $wp_locale;

	$time = ($time < 0) ? current_time('timestamp') : $time;
	$jj = gmdate('d', $time);
	$mm = gmdate('m', $time);
	$aa = gmdate('Y', $time);

	$tab_index = ( (int) $tab_index > 0 ) ? ' tabindex="' . $tab_index . '"' : '';

	$month = "<select id=\"{$id_month}\" name=\"{$id_month}\"{$tab_index}>\n";
	for ($i = 1; $i < 13; $i++) {
		$monthnum = zeroise($i, 2);
		$month .= "\t\t\t" . '<option value="' . $monthnum . '"';
		$month .= ( $i == $mm ) ? ' selected="selected"' : '';
		$month .= '>' . $monthnum . '-' . $wp_locale->get_month_abbrev($wp_locale->get_month($i)) . "</option>\n";
	}
	$month .= '</select>';

	$day = '<input type="text" id="' . $id_day . '" name="' . $id_day . '" value="' . $jj . '" size="2" maxlength="2"' . $tab_index . ' autocomplete="off" />';
	$year = '<input type="text" id="' . $id_year . '" name="' . $id_year . '" value="' . $aa . '" size="4" maxlength="4"' . $tab_index . ' autocomplete="off" />';

	$output = '<div class="timestamp-wrap"><label>' . $label . ':</label>&nbsp;';
	/* translators: 1: month input, 2: day input, 3: year input, 4: hour input, 5: minute input */
	$output .= sprintf(__('%1$s%2$s, %3$s'), $month, $day, $year) . '</div>';

	return $output;
}

/*
 * theme input form field
 */
function theme_form_input_text($label, $id, $value) {
	return sprintf("<br/><label>%s:</label>
  <input name=\"%s\" value=\"%s\" />", $label, $id, $value);
}

/*
 * theme textarea form field
 */
function theme_form_textarea($label, $id, $value, $cols = 50, $rows = 5) {

	return ($cols > 0 && $rows > 0) ? sprintf("<p><label>%s:</label><br/>
  <textarea cols=\"%d\" rows=\"%d\" name=\"%s\" />%s</textarea></p>", $label, $cols, $rows, $id, $value) : sprintf("<p><label>%s:</label><br/>
  <textarea name=\"%s\" />%s</textarea></p>", $label, $id, $value);
}

add_action('save_post', 'save_departure_details');

/*
 * save custom fields
 */
function save_departure_details() {
	static $external_data = false;
	global $post;

	if (empty($external_data)) {
		$external_data = array(
			'departure_blogtrip_hashtag' => 'departure_blogtrip_hashtag',
			'departure_destination' => 'departure_destination',
			'departure_impression' => 'departure_impression',
			'departure_date_start' => 'departure_date_start',
			'departure_date_end' => 'departure_date_end',
			'departure_people_reach' => 'departure_people_reach',
			'departure_tweets_count' => 'departure_tweets_count',
		);
	}
	$external_data_hidden = array('departure_destination' => array('_g_lat' => '1', '_g_long' => '2'),
		'departure_blogtrip_hashtag' => array('departure_tweets_cache_data' => '', '_departure_tweet_since_id' => 0)
		);
	$hidden_fields = array('_departure_tweet_image_cache' => '');

	if (is_object($post) && 'departures' == $post->post_type) { //
		global $wp_taxonomies;
		register_taxonomy_for_object_type( "content_type", "departures");
		$terms = get_terms("content_type", array("hide_empty" => false, "slug" => "departures"));
		wp_set_post_terms( $post->ID, $terms[0]->term_id, 'content_type', false);

		if (isset($_POST['dd_y']) && isset($_POST['dd_m']) && isset($_POST['dd_d'])) {
			$_POST['departure_date_start'] = get_timestamp($_POST['dd_y'], $_POST['dd_m'], $_POST['dd_d']);
			unset($_POST['dd_y'], $_POST['dd_m'], $_POST['dd_d']);
		}
		if (isset($_POST['rd_y']) && isset($_POST['rd_m']) && isset($_POST['rd_d'])) {
			$_POST['departure_date_end'] = get_timestamp($_POST['rd_y'], $_POST['rd_m'], $_POST['rd_d']);
			unset($_POST['rd_y'], $_POST['rd_m'], $_POST['rd_d']);
		}
		foreach ($external_data as $metadata => $postkey) {
			$metadata_post = get_post_meta($post->ID, $metadata);
			if (isset($_POST[$postkey])) {
				if (empty($metadata_post)) { 
					// create
					add_post_meta($post->ID, $metadata, $_POST[$postkey]);
					if (isset($external_data_hidden[$postkey])) {
						if ('departure_destination' == $postkey) {
							// get geocodes: lat and long
							list($external_data_hidden[$postkey]['_g_lat'], $external_data_hidden[$postkey]['_g_long']) = departure_gmap_geocode($_POST[$postkey]);
						}
						// update hidden data
						foreach ($external_data_hidden[$postkey] as $hiddenkey => $hiddenvalue) {
							$metadata_post = get_post_meta($post->ID, $hiddenkey);
							if (empty($metadata_post)) {
								// create
								add_post_meta($post->ID, $hiddenkey, $hiddenvalue);
							} else {
								// unset
								update_post_meta($post->ID, $hiddenkey, $hiddenvalue);
							}
						}
					}
				} else if ($metadata_post != $_POST[$postkey]) {
					// update
					update_post_meta($post->ID, $metadata, $_POST[$postkey]);
					if (isset($external_data_hidden[$postkey])) {
						if ('departure_destination' == $postkey) {
							// get geocodes: lat and long
							list($external_data_hidden[$postkey]['_g_lat'], $external_data_hidden[$postkey]['_g_long']) = departure_gmap_geocode($_POST[$postkey]);
						}
						// update hidden data
						foreach ($external_data_hidden[$postkey] as $hiddenkey => $hiddenvalue) {
							$metadata_post = get_post_meta($post->ID, $hiddenkey);
							if (empty($metadata_post)) {
								// create
								add_post_meta($post->ID, $hiddenkey, $hiddenvalue);
							} else {
								// unset
								update_post_meta($post->ID, $hiddenkey, $hiddenvalue);
							}
						}
					}
				}
			}
		}
		// add hiddens values
		foreach ($hidden_fields as $hiddens => $default) {
			$metadata_hiddens = get_post_meta($post->ID, $hiddens);
			if (empty($metadata_hiddens) && $metadata_hiddens != $default) {
				add_post_meta($post->ID, $hiddens, $default);
			}
		}
	}
}


/*
 * Converts input date to timestamp
 */
function get_timestamp($year = 0, $month = 0, $day = 0) {
	return mktime(0, 0, 0, (int) $month, (int) $day, (int) $year);
}


/*
 * Updates long and lat with destination updating
 */
function departure_gmap_geocode($addr) {
	static $default_false_lat = '';
	static $default_false_long = '';
	$api_url = "http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($addr) . "&sensor=false";
	$request = wp_remote_get($api_url);
	$json = json_decode(wp_remote_retrieve_body($request));
	
	if (empty($json)) {
		return array($default_false_lat, $default_false_long);
	}

	$lat = (isset($json->results[0]) && $json->results[0]->geometry->location->lat) ? $json->results[0]->geometry->location->lat : $default_false_lat;

	$long = (isset($json->results[0]) && $json->results[0]->geometry->location->lng) ? $json->results[0]->geometry->location->lng : $default_false_long;

	return array($lat, $long);
	
}


// Filter WordPress Query for Departures Page Archives
//--------------------------------------------------------------------------------
function t5_departures_query( $query ) {
	
	if ( !$query->is_main_query() )
		return;
	if ( !is_post_type_archive( 'departures' ) )
		return;
	$query->set( 'posts_per_page', '8' );
	$query->set( 'meta_key', 'departure_date_start' );
	$query->set( 'orderby', 'meta_value_num' );
	$query->set( 'order', 'DESC' );
	
}
add_action( 'pre_get_posts', 't5_departures_query' );
