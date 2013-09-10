<?php get_template_part( 'header' ); ?>

	<section id="content" class="column-8">
		<?php if( term_description() !== '' ) : ?>
			<h1 class="homeh1"><?php echo term_description(); ?></h1>
		<?php endif; ?>
		<?php get_template_part( 'loop', 'archive' ); ?>
	</section><!-- #content -->
	
	<?php get_template_part( 'sidebar' ); ?>
				
<?php get_template_part( 'footer' ); ?>