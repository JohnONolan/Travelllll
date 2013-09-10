<?php
/*
Template Name: Blank
*/
?>
<?php get_template_part( 'header' ); ?>
	
	<section id="content" class="column-12">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<section class="entry-content">
				
				<?php the_content(); ?>
				
			</section>
		</article>
		<?php endwhile; endif; ?>
	</section><!-- #content -->
<?php get_template_part( 'footer' ); ?>