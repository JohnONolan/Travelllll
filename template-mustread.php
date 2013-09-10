<?php
/*
Template Name: Must Read
*/
?>
<?php get_template_part( 'header' ); ?>
	
	<section id="content" class="column-8">
		<article>
			<header>
				<h1 class="entry-title">Must Read Posts on Travelllll.com</h1>
			</header>
			<section class="entry-content">
				<p>Here are the most popular articles ever published on Travelllll.com – enjoy!</p>
				<?php $args = array( 
				    'posts_per_page' => 32, 
				    'orderby' => 'comment_count', 
				    'order' => 'DESC',
				    'ignore_sticky_posts' => 1,
				    'tag__not_in' => '449'
				);
				$popular_posts = new WP_Query( $args ); 
				if( $popular_posts->have_posts() ) : ?>
				<div class="clearfix"></div>
				<section class="popularposts">
				    <?php $count = 0; ?>
				    <?php while( $popular_posts->have_posts() ) : $popular_posts->the_post(); $count++; ?>
					<article id="post-<?php the_ID(); ?>" class="number<?php echo $count; ?>">
						<a class="popularimg" href="<?php the_permalink(); ?>" rel="nofollow"><?php the_post_thumbnail( 'thumb' ); ?></a>
						<h3 class="entry-title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							<a class="count" href="<?php comments_link(); ?>" rel="nofollow"><?php comments_number('0', '1', '%'); ?> <span class="screen-reader-text">Comments</span></a>
						</h3>
						<div class="clearfix"></div>
					</article>
				    <?php endwhile; endif;?>
				    <div class="clearfix"></div>
				</section>
				
				<?php wp_reset_postdata(); ?>
				
			</section>
		</article>
	</section><!-- #content -->
	<?php get_template_part( 'sidebar' ); ?>

<?php get_template_part( 'footer' ); ?>