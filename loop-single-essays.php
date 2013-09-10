<?php if ( have_posts() ) : ?>
			    
	<div id="hfeed">
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				
				<header>
												
					<h1 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>

					<section class="entry-meta">
						<?php $author = get_the_author_meta('ID'); ?>
						<span class="text">
							by <a class="author" rel="profile" href="<?php echo get_author_posts_url( $author ); ?>" title="See <?php the_author_meta('user_firstname'); ?>'s Profile"><?php the_author_meta('user_firstname'); ?> <?php the_author_meta('user_lastname'); ?></a> 
							<?php edit_post_link( 'Edit', '| ', '', '' ); ?>
						</span>
					</section>
										
				</header>
										
				<section class="entry-content">
				
					<?php get_template_part( 'library/templates/sponsored', 'header' ); ?>
				
					<?php the_post_thumbnail( 'essay' ); ?>
					
					<?php the_content(); ?>
					
					<?php wp_link_pages( array( 'before' => '<div class="page-link"><span class="page-link-meta">' . __( 'Pages:' ) . '</span>', 'after' => '</div>', 'next_or_number' => 'number' ) ); ?>
										
				</section>

			</article><!-- #post-<?php the_ID(); ?> -->
			
			<div class="clearfix"></div>
			<?php get_template_part( 'library/templates/sponsored', 'footer' ); ?>
			
			<div class="column-8 before-2 after-2">
			
				<?php get_template_part( 'library/templates/authorbox' ); ?>
				
				<?php comments_template( '', true ); ?>
				
			</div><!--column-8-->
								
		<?php endwhile; ?>
	</div><!-- #hfeed -->

<?php else : ?>
	<h1>No posts found.</h1>
<?php endif; ?>