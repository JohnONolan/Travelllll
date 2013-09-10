<?php get_template_part( 'header' ); ?>
		
		<?php //get_template_part( 'library/templates/trending' ); ?>
		
		<?php if(has_term('photos', 'content_type')) { //SINGLE PHOTO LOOP ?>
		
			<section id="content" class="column-12">
				<?php get_template_part( 'loop', 'single-photos' ); ?>
			</section><!-- #content -->
			
		<?php } elseif(has_term('videos', 'content_type')) { //SINGLE VIDEO LOOP ?>
			
			<section id="content" class="column-12">
				<?php get_template_part( 'loop', 'single-videos' ); ?>
			</section><!-- #content -->
			
		<?php } elseif(has_term('essays', 'content_type')) { //SINGLE ESSAY LOOP ?>
			
			<section id="content" class="column-12">
				<?php get_template_part( 'loop', 'single-essays' ); ?>
			</section><!-- #content -->
			
		<?php } else { //SINGLE STANDARD LOOP ?>
			
			<section id="content" class="column-8">
				<?php get_template_part( 'loop', 'single' ); ?>
			</section><!-- #content -->
			<?php get_template_part( 'sidebar' ); ?>
			
		<?php }	?>
		
<?php get_template_part( 'footer' ); ?>