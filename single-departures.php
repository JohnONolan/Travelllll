<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
global $post;
require_once('departures/departure-display.php');
// set page params
$page_tweets = 10;
$custom_meta = get_post_custom( $post->ID );
$cache_tweets = unserialize($custom_meta['departure_tweets_cache_data'][0]);
$taxonomies = get_blogtrip_taxonomies( $post->ID, array(), 'name' );
$taxonomy_objects = get_blogtrip_taxonomies( $post->ID, array());
$status = implode(', ', $taxonomies['status']);
$status_slug = array_keys($taxonomies['status']);
$status_slug = $status_slug[0];

$hash = get_post_meta($post->ID, 'departure_blogtrip_hashtag', true);
$dest = get_post_meta($post->ID, 'departure_destination', true);							
$dep = get_post_meta($post->ID, 'departure_date_start', true);
$rtn = get_post_meta($post->ID, 'departure_date_end', true);

foreach ($taxonomies['crew'] as $slug => $crew_name) {
	$crew .= (!empty($crew)) ? ', ' : '';
	$crew .= '<a href="'. get_term_link($slug, 'crew') . '">' . $crew_name . '</a>';
}
foreach ($taxonomies['organizer'] as $slug => $organizer_name) {
	$organizer .= (!empty($organizer)) ? ', ' : '';
	$organizer .= '<a href="'. get_term_link($slug, 'organizer') . '">' . $organizer_name . '</a>';
}

$people = array();
foreach ($taxonomy_objects['crew'] as $slug => $crew_term) {
	$people[] = get_avatar($crew_term->description, '102', '', $crew_term->name);
}

$images = get_children( 'post_parent=' . $post->ID . '&post_type=attachment&post_mime_type=image' );
$taxes = get_blogtrip_taxonomies(array('post' => $post->ID));


$term_id = get_term_by('slug', $custom_meta['departure_blogtrip_hashtag'][0], 'post_tag');

$blog_object_ids2 = get_posts_by_tag($custom_meta['departure_blogtrip_hashtag'][0], 'DESC', 4);

// setup photo style - style from *.pds
$image_sizes = array(
	'large' => array('width' => 396, 'height' => 252),
	'medium' => array('width' => 244, 'height' => 252),
	'thumb' => array('width' => 153, 'height' => 153)
);

$photo_styles = array(
	array('class' =>'prime', 'width' => $image_sizes['large']['width'], 'height' => $image_sizes['large']['height'], 'type' => 'large'),
	array('class' =>'sub-prime', 'width' => $image_sizes['medium']['width'], 'height' => $image_sizes['medium']['height'], 'type' => 'medium'),
	array('class' =>'sub', 'width' => $image_sizes['thumb']['width'], 'height' => $image_sizes['thumb']['height'], 'type' => 'thumb'),
	array('class' =>'sub push-2', 'width' => $image_sizes['thumb']['width'], 'height' => $image_sizes['thumb']['height'], 'type' => 'thumb'),
	array('class' =>'sub push-4', 'width' => $image_sizes['thumb']['width'], 'height' => $image_sizes['thumb']['height'], 'type' => 'thumb'),
	array('class' =>'sub push-6', 'width' => $image_sizes['thumb']['width'], 'height' => $image_sizes['thumb']['height'], 'type' => 'thumb')
);


// setup photo style - 12 thumbs
$photo_styles = array(
//	array('class' =>'prime', 'width' => $image_sizes['large']['width'], 'height' => $image_sizes['large']['height'], 'type' => 'large'),
//	array('class' =>'sub-prime', 'width' => $image_sizes['medium']['width'], 'height' => $image_sizes['medium']['height'], 'type' => 'medium'),
	array('class' =>'sub', 'width' => $image_sizes['thumb']['width'], 'height' => $image_sizes['thumb']['height'], 'type' => 'thumb'),
	array('class' =>'sub push-2', 'width' => $image_sizes['thumb']['width'], 'height' => $image_sizes['thumb']['height'], 'type' => 'thumb'),
	array('class' =>'sub push-4', 'width' => $image_sizes['thumb']['width'], 'height' => $image_sizes['thumb']['height'], 'type' => 'thumb'),
	array('class' =>'sub push-6', 'width' => $image_sizes['thumb']['width'], 'height' => $image_sizes['thumb']['height'], 'type' => 'thumb'),

	array('class' =>'sub', 'width' => $image_sizes['thumb']['width'], 'height' => $image_sizes['thumb']['height'], 'type' => 'thumb'),
	array('class' =>'sub push-2', 'width' => $image_sizes['thumb']['width'], 'height' => $image_sizes['thumb']['height'], 'type' => 'thumb'),
	array('class' =>'sub push-4', 'width' => $image_sizes['thumb']['width'], 'height' => $image_sizes['thumb']['height'], 'type' => 'thumb'),
	array('class' =>'sub push-6', 'width' => $image_sizes['thumb']['width'], 'height' => $image_sizes['thumb']['height'], 'type' => 'thumb'),

	array('class' =>'sub', 'width' => $image_sizes['thumb']['width'], 'height' => $image_sizes['thumb']['height'], 'type' => 'thumb'),
	array('class' =>'sub push-2', 'width' => $image_sizes['thumb']['width'], 'height' => $image_sizes['thumb']['height'], 'type' => 'thumb'),
	array('class' =>'sub push-4', 'width' => $image_sizes['thumb']['width'], 'height' => $image_sizes['thumb']['height'], 'type' => 'thumb'),
	array('class' =>'sub push-6', 'width' => $image_sizes['thumb']['width'], 'height' => $image_sizes['thumb']['height'], 'type' => 'thumb')
);


$count_photo = count($photo_styles);
// include twitter script
$tweet_params = array('hashcode' => $custom_meta['departure_blogtrip_hashtag'][0],
	'since_id' => $custom_meta['_departure_tweet_since_id'][0],
	'until' => date('Y-m-d', $custom_meta['departure_date_end'][0]),
	'since' => date('Y-m-d', $custom_meta['departure_date_start'][0]),
	'live' => (time() < $custom_meta['departure_date_end'][0]) ? true : false,
	'page_tweets' => $page_tweets,
	'photo_count' => $count_photo
);




$photo_cached = array(); $i = 0;
foreach ($images as $post_image) {
	$image_custom_fields = get_post_custom($post_image->ID);
	$large = $medium = $thumb = '';
	$image = wp_get_attachment_image_src($post_image->ID,  array( $image_sizes['large']['width'], $image_sizes['large']['height'] ), false);
	if ( $image ) {
		list($large, $width, $height) = $image;
	}
	$image = wp_get_attachment_image_src($post_image->ID,  array( $image_sizes['medium']['width'], $image_sizes['medium']['height'] ), false);
	if ( $image ) {
		list($medium, $width, $height) = $image;
	}
	$image = wp_get_attachment_image_src($post_image->ID,  array( $image_sizes['thumb']['width'], $image_sizes['thumb']['height'] ), false);
	if ( $image ) {
		list($thumb, $width, $height) = $image;
	}

	$photo_cached[] = array('photo_id' => $post_image->ID, 'large' => $large, 'medium' => $medium, 'thumb' => $thumb, 'title' => $post_image->post_title, 'href' => (!empty($image_custom_fields) && !empty($image_custom_fields['_twits_url_str'])) ? $image_custom_fields['_twits_url_str'][0] : 'javascript://');
	$i++;
	if ($i >= $count_photo) {
		break;
	}
}

// added jquery and streamtwits script
wp_enqueue_script('jquery');
wp_enqueue_script('departure_twits', get_template_directory_uri() . '/departures/js/streamtweets.js');
wp_localize_script('departure_twits', 'tweet_params', $tweet_params);
wp_localize_script('departure_twits', 'twitpics_slots', $photo_styles);
wp_localize_script('departure_twits', 'twitpics_cache', $photo_cached);

// include gmap script
$gmap_params = array('lat' => $custom_meta['_g_lat'][0],
	'long' => $custom_meta['_g_long'][0],
	'title' => (!is_null($post->post_title)) ? get_the_title() : ''
);
wp_enqueue_script('departure_gmap_lib', 'http://maps.google.com/maps/api/js?sensor=false');
wp_enqueue_script('departure_gmap', get_template_directory_uri() . '/departures/js/gmap.js');
wp_localize_script('departure_gmap', 'gmap_params', $gmap_params);
?>
<?php get_template_part( 'header' ); ?>
			<script type="text/javascript">
	jQuery(document).ready(function () {
		jQuery("#main").addClass("dep");
		jQuery("#breadcrumbs").css("display", "none");
	})
				</script>
				<section id="content" class="column-12">
					<div id="hfeed">
					<div class="departures">
				<header>
					<h1><?php echo $custom_meta['departure_blogtrip_hashtag'][0] . ' - ' . ((false !== strpos($custom_meta['departure_destination'][0], ',')) ? substr($custom_meta['departure_destination'][0], 0, strpos($custom_meta['departure_destination'][0], ',')) : $custom_meta['departure_destination'][0]) . ', ' . date('Y', $custom_meta['departure_date_end'][0]);?><a href="<?php bloginfo( 'url' ); ?>/departures/" class="btn">Back to Departures</a></h1>
					<div class="th-like">
						<div class="blog-id">BlogTrip ID</div>
						<div class="destination">Destination</div>
						<div class="date">Departure Date</div>
						<div class="date">Return Date</div>
						<div class="status">Status</div>
					</div>
					<div class="dep-item">
						<div class="row">
							
							<div class="blog-id"><a href="<?php the_permalink() ?>" rel="bookmark"><div class="box"><div class="decor"><span class="ring"></span></div><?php echo $hash; ?></div></a></div>
							<div class="destination"><div class="box"><div class="decor"><span class="ring"></span></div><?php echo $dest; ?></div></div>
							<div class="date depart"><div class="box"><div class="decor"><span class="ring"></span></div><?php echo departure_date_format( $dep ); ?></div></div>
							<div class="date return"><div class="box"><div class="decor"><span class="ring"></span></div><?php echo departure_date_format( $rtn ); ?></div></div>
							
							<?php 
								
								if(has_term('cancelled', 'status')) {
									$status = 'Cancelled';
								} else {
									if ( $dep < current_time( 'timestamp' ) && $rtn < current_time( 'timestamp' ) ) {
										$status = 'Completed';
									} elseif ( current_time( 'timestamp' ) > strtotime( '-4 days', $dep ) && current_time( 'timestamp' ) < $dep ) {
										$status = 'Boarding';
									} elseif ( $dep > current_time( 'timestamp' ) && $rtn > current_time( 'timestamp' ) ) {
										$status = 'Planned';
									} elseif ( $dep <= current_time( 'timestamp' ) && $rtn >= current_time( 'timestamp' ) ) { 
										$status = 'In Progress';
									}
								}
																
							?>
							
							<div class="status <?php echo $status; ?>"><div class="box"><div class="decor"><span class="ring"></span></div><?php echo $status; ?></div></div>
							
						</div>
						<div class="adv-info">
							<div class="organizer"><b>Host:</b> <?php echo $organizer; ?></div>
							<div class="crew"><b>Crew:</b> <?php echo $crew; ?></div>
							<div class="clearfix"></div>
						</div>
						<div class="clearfix"></div>
					</div>
				</header>

				<div id="gm_canvas" class="map"></div>
				<script type="text/javascript">
					jQuery(document).ready(function () {
						iniMap("gm_canvas");
					})
				</script>
				<div class="w-area clearfix">
							<section class="column-8 alignright" id="departurecontent">
								<?php echo $post->post_content; ?><br/><br/><?php if (!empty($custom_meta['departure_impression'][0]) || !empty($custom_meta['departure_people_reach'][0]) || !empty($custom_meta['departure_tweets_count'][0])):?>
								<h2 class="bar"><span>Impact statistics</span></h2>
								<div class="statistics">
									<div class="inner"><div class="txt-holder"><span class="number"><?php echo number_format($custom_meta['departure_impression'][0], 0, '', ','); ?></span>impressions</div></div>
									<div class="inner"><div class="txt-holder"><span class="number"><td><?php echo number_format($custom_meta['departure_people_reach'][0], 0, '', ','); ?></span>people reach</div></div>
									<div class="inner"><div class="txt-holder"><span class="number"><?php echo number_format($custom_meta['departure_tweets_count'][0], 0, '', ','); ?></span>tweets</div></div>
								</div><?php endif;
								if (!empty($people)): ?>
								<h2 class="bar"><span>People on This Trip</span></h2>
								<div class="photo-container">
									<?php foreach ($people as $img): ?><div class="photo-wrap">
										<?php echo $img; ?>
									</div><?php endforeach; ?>
								</div><?php endif; if (!empty($blog_object_ids2) && count($blog_object_ids2)): ?>
								<h2 class="bar"><span>Travelllll.com Coverage</span></h2>
								<section id="trending">
									<?php $post_current = $post; $i = 0;
	foreach ($blog_object_ids2 as $post_blog_id => $related_post):
		$post = $related_post; $i++; ?><article class="<?php echo (0 == $i%2) ? "alignright" : ''; ?>" id="post-<?php the_ID(); ?>" >
										<a rel="nofollow"href="<?php the_permalink(); ?>">
											<?php the_post_thumbnail( 'thumb' ); ?>
										</a>
										<h2 class="entry-title">
											<a rel="bookmark" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
											<a rel="nofollow"href="<?php comments_link(); ?>" class="count"><?php comments_number('0', '1', '%'); ?> <span class="screen-reader-text">Comments</span></a>
										</h2>
									</article><?php endforeach;
									$post = $post_current; ?>
								<div class="clearfix"></div>
								</section><?php endif; if (!empty($images) && count($images)): ?>
								<h2 class="bar"><span>Trip Photos</span></h2>
								<div class="trip-photo-wrap">
									<?php
									$i = 0;
									foreach ($photo_cached as $photo) {
										echo '<a href="' . $photo['href'] .'" target="_blank">', wp_get_attachment_image( $photo['photo_id'], array( $photo_styles[$i]['width'], $photo_styles[$i]['height'] ), false, array('class' => $photo_styles[$i]['class']) ), '</a>';
										$i++;
										if ($i >= $count_photo) {
											break;
										}
									}
									?>
								</div><?php endif; ?>
							</section>
							<aside id="sidebar" class="column-4">
								<div class="twit-list">
									<ul id="twitspace">
										<?php $i = 0;
									if (!empty($cache_tweets) && count($cache_tweets)) {
										foreach ($cache_tweets as $id => $twit) {
											echo departure_display_tweet($twit, $custom_meta['departure_blogtrip_hashtag'][0]);
											$i++;
											if ($i > $page_tweets - 1) {
												break;
											}
										}
									}
										?>
									</ul>
								</div>
							</aside>
						</div>
					</div>
				</div>
				</section>

<?php get_template_part( 'footer' ); ?>