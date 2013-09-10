<?php

require_once('departures/departure-display.php');

$bar = 1;

if ( have_posts() ) : while( have_posts() ) : the_post();
	
$hash = get_post_meta($post->ID, 'departure_blogtrip_hashtag', true);
$dest = get_post_meta($post->ID, 'departure_destination', true);							
$dep = get_post_meta($post->ID, 'departure_date_start', true);
$rtn = get_post_meta($post->ID, 'departure_date_end', true);

// get data for display
$taxonomies = get_blogtrip_taxonomies( get_the_ID(), array(), 'name' );
$crew = $organizer = '';
foreach ($taxonomies['crew'] as $slug => $crew_name) {
	$crew .= (!empty($crew)) ? ', ' : '';
	$crew .= '<a href="'. get_term_link($slug, 'crew') . '">' . $crew_name . '</a>';
}
foreach ($taxonomies['organizer'] as $slug => $organizer_name) {
	$organizer .= (!empty($organizer)) ? ', ' : '';
	$organizer .= '<a href="'. get_term_link($slug, 'organizer') . '">' . $organizer_name . '</a>';
}
	
?>
			<?php 
			if ( $dep < current_time( 'timestamp' ) && $rtn < current_time( 'timestamp' ) && isset($bar) ) : unset($bar);?>
			<div class="sep-item">
				Past Trips
			</div>
			<?php endif ?>
			
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
			
						
<?php endwhile; ?>
<?php 
wp_reset_postdata();
wp_pagenavi(array('before' => '<div class="sep-item final">', 'after' => '</div>'));
endif;
?>