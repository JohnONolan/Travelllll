<?php if ( have_posts() ) : ?>
			    
	<div id="hfeed">
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				
				<header>
					<?php get_template_part( 'library/templates/content', 'cats' ); ?>
												
					<h1 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>

					<section class="entry-meta">
						<?php $author = get_the_author_meta('ID'); ?>
						<span class="text">
							<time class="updated" datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php the_time('F j, Y'); ?></time>
							by <a class="author" rel="profile" href="<?php echo get_author_posts_url( $author ); ?>" title="See <?php the_author_meta('user_firstname'); ?>'s Profile"><?php the_author_meta('user_firstname'); ?> <?php the_author_meta('user_lastname'); ?></a> 
							<a class="authorrss" href="<?php echo get_author_posts_url( $author ); ?>feed/" rel="nofollow" title="Subscribe to <?php the_author_meta('user_firstname'); ?>'s posts by RSS"><span class="screen-reader-text">RSS</span></a>
							<?php edit_post_link( 'Edit', '| ', '', '' ); ?>
						</span>
					</section>
					
					<?php get_template_part( 'library/templates/sharebar' ); ?>
					
				</header>
										
				<section class="entry-content">
				
					<figure class="video">
						<?php 
						$has_embed = false;
						$custom_meta = get_post_custom( $post->ID );
						if ( isset( $custom_meta['embed'] ) && $custom_meta['embed'][0] != '' ) {
							$has_embed = true;
						}
						if ( $has_embed == true ) {
	        				echo woo_embed( 'key=embed&width=978&class=video-embed' );
	        			} ?>
					</figure>
					
					<?php 
					if( t5_is_old_post() ) {
						get_template_part( 'library/templates/contentad', 'box' );
					}
					
					the_content();
					?>
					
					<?php wp_link_pages( array( 'before' => '<div class="page-link"><span class="page-link-meta">' . __( 'Pages:' ) . '</span>', 'after' => '</div>', 'next_or_number' => 'number' ) ); ?>
										
				</section>
				
				<footer class="entry-utility column-12">
				
					<section class="meta">
						
						<?php get_template_part( 'library/templates/source' ); ?>
						
						<div class="fbrecommend">
							<fb:like href="<?php the_permalink(); ?>" send="true" width="785" show_faces="true" action="recommend" font=""></fb:like>
						</div>
						<div class="clearfix"></div>
						
					</section>
					
				</footer>
			</article><!-- #post-<?php the_ID(); ?> -->
			
			<div class="column-8">
			
				<?php get_template_part( 'library/templates/signupbox' ); ?>
			
				<?php get_template_part( 'library/templates/authorbox' ); ?>
				
				<?php 
				if( t5_is_old_post() ) {
					get_template_part( 'library/templates/contentad', 'links' );
				} 
				?>
											
				<?php if (function_exists( 'related_posts' )) { related_posts(); } ?>
				
				<div class="fb-recommendations-bar" data-href="<?php the_permalink(); ?>" data-trigger="50%" data-read-time="20" data-site="travelllll.com"></div>
				
				<div class="endofcontentad"></div>
				
				<?php comments_template( '', true ); ?>
				
			</div><!--column-8-->
				
			<?php get_template_part( 'sidebar' ); ?>
				
		<?php endwhile; ?>
	</div><!-- #hfeed -->

<?php else : ?>
	<h1>No posts found.</h1>
<?php endif; ?>