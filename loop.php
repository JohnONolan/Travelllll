<?php global $query_string; query_posts( $query_string . '&ignore_sticky_posts=1' ); ?>
<?php if ( have_posts() ) : $number = 0; ?>
			    
			<div id="hfeed">
				<?php while ( have_posts() ) : the_post(); $number++; ?>
					<?php $thecount = 'number' . $number; ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( $thecount ); ?>>
					
						<header>
							<?php get_template_part( 'library/templates/content', 'cats' ); ?>
														
							<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

							<section class="entry-meta">
								<?php $author = get_the_author_meta('ID'); ?>
								<span class="text">
									<time class="updated" datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></time>
									by <span class="vcard"><a class="author fn" href="<?php echo get_author_posts_url( $author ); ?>" title="See <?php the_author_meta('user_firstname'); ?>'s Profile"><?php the_author_meta('user_firstname'); ?> <?php the_author_meta('user_lastname'); ?></a></span> 
									<a class="authorrss" href="<?php echo get_author_posts_url( $author ); ?>feed/" rel="nofollow" title="Subscribe to <?php the_author_meta('user_firstname'); ?>'s posts via RSS"><span class="screen-reader-text">RSS</span></a>
									<?php edit_post_link( 'Edit', '| ', '', '' ); ?>
								</span>
								<a class="count" href="<?php comments_link(); ?>" rel="nofollow"><?php comments_number('0', '1', '%'); ?> <span class="screen-reader-text">Comments</span></a>
							</section>
						</header>
						
						<?php // PHOTO POST 
						if(has_term('photos', 'content_type')) { ?>
						
						<figure>
							<a href="<?php the_permalink(); ?>" rel="nofollow"><?php the_post_thumbnail( 'type-photo' ); ?></a>
							<?php t5_post_thumbnail_caption(); ?>
						</figure>
						
						<?php // VIDEO POST
						// Set an image-based thumbnail and a video embed. Image thumbnail is shown for small-screen
						// devices, video embed is shown for large-screen devices. 
						} elseif(has_term('videos', 'content_type')) { ?>
						
						<figure class="thumbnail">
							<a href="<?php the_permalink(); ?>" rel="nofollow"><?php the_post_thumbnail( 'loop' ); ?></a>
						</figure>
						
						<figure class="video">
							<?php 
							$has_embed = false;
							$custom_meta = get_post_custom( $post->ID );
							if ( isset( $custom_meta['embed'] ) && $custom_meta['embed'][0] != '' ) {
								$has_embed = true;
							}
							if ( $has_embed == true ) {
		        				echo woo_embed( 'key=embed&width=642&class=video-embed' );
		        			} ?>
						</figure>
						
						<?php 
						} else { // NORMAL POST ?>
						
						<figure>
							<a href="<?php the_permalink(); ?>" rel="nofollow"><?php the_post_thumbnail( 'loop' ); ?></a>
						</figure>
						<section class="entry-summary">
							<?php the_excerpt(); ?>
							<?php wp_link_pages( array( 'before' => '<div class="page-link"><span class="page-link-meta">' . __( 'Pages:' ) . '</span>', 'after' => '</div>', 'next_or_number' => 'number' ) ); ?>
						</section>
						
						<?php 
						} //END CONTENT-TYPE FILTERS ?>

						<footer class="entry-utility">
						
							<section class="meta">
								<?php get_template_part( 'library/templates/source' ); ?>
							</section>
							
							<ul class="sharelinks">
								<li><a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php the_permalink(); ?>" data-text="<?php the_title(); ?>" data-via="Travelllll">Tweet this post</a></li>
								<li><fb:like href="<?php the_permalink(); ?>" layout="button_count"></fb:like></li>
							</ul>
							
						</footer>
					</article><!-- #post-<?php the_ID(); ?> -->
					<?php comments_template( '', true ); ?>
					
					<?php if ( is_home() && !is_paged() && $number == 3 ) {
						get_template_part( 'library/templates/popular', 'index' );
					} ?>
					
				<?php endwhile; ?>
			</div><!-- #hfeed -->

			<?php wp_pagenavi(); ?>

<?php else : ?>
	<h1>No posts found :(</h1>
<?php endif; ?>