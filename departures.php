<?php

require_once('departures/departure-display.php');
global $post, $wp_query;

// add js scripts
wp_enqueue_script('jquery');
wp_enqueue_script('departures_bigtarget', get_template_directory_uri() . '/departures/js/jquery.bigtarget.1.0.1.js');
?>

<?php get_template_part( 'header' ); ?>

<section id="content" class="column-12">
	<div id="hfeed">
		<div class="departures multi">
		
			<header>
				<?php
				$count_post = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'departures'");
				if (0 < $count_post) { $count_post = number_format($count_post); }
				?>
				<div class="info-text">Tracking <span class="number"><?php echo wp_count_terms('crew'); ?></span> bloggers on <span class="number"><?php echo $count_post; ?></span> trips<br />in <span class="number"><?php echo wp_count_terms('country'); ?></span> countries across the world</div>
				<h1><a href="<?php bloginfo( 'url' ); ?>/departures/">Departures</a> <div class="btn-wrap"><a href="<?php bloginfo( 'url' ); ?>/new-trip/" class="btn">Tell us about a trip</a></div></h1>
				<div class="th-like">
					<div class="blog-id">Trip ID</div>
					<div class="destination">Destination</div>
					<div class="date depart">Departure Date</div>
					<div class="date return">Return Date</div>
					<div class="status">Status</div>
				</div>
			</header>

			<?php get_template_part( 'loop', 'departures' ); ?>
						
		</div>
	</div>
</section>

<script type="text/javascript">
		jQuery(document).ready(function () {
			jQuery("#main").addClass("dep");
			jQuery("#breadcrumbs").css("display", "none");
			jQuery("div.dep-item > div.row > div.blog-id > a").bigTarget({
				hoverClass: '', // CSS class applied to the click zone onHover
				clickZone : 'div.dep-item' // jQuery parent selector
			});
		})

</script>

<?php 
get_template_part( 'footer' ); ?>