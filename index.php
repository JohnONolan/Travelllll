<?php get_template_part( 'header' ); ?>

	<section id="content" class="column-8">
		<h1 class="homeh1">The Home of Travel News, Technology, Media and Travel Tips</h1>
		<?php if ( !is_paged() ) { ?>
			<?php // get_template_part( 'library/templates/trending' ); ?>
			<h2 class="bar"><span>Latest Posts</span></h2>
		<?php } ?>
		<?php get_template_part( 'loop', 'index' ); ?>
	</section><!-- #content -->
	
	<?php get_template_part( 'sidebar' ); ?>

<?php get_template_part( 'footer' ); ?>