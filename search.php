<?php get_template_part( 'header' ); ?>
	
	<section id="content" class="column-8">
		<?php if ( !is_paged() ) { ?>
			<h2 class="bar"><span>Search Results for "<?php the_search_query(); ?>"</span></h2>
		<?php } ?>
		<?php get_template_part( 'loop', 'search' ); ?>	
	</section><!-- #content -->
	
	<?php get_template_part( 'sidebar' ); ?>
<?php get_template_part( 'footer' ); ?>