<?php 

//TRENDING POST
if ( !is_paged() ) {
	function filter_where($where = '') {
		$where .= " AND post_date > '" . date('Y-m-d', strtotime('-30 days')) . "'";
		return $where;
	}
	add_filter('posts_where', 'filter_where');
	
	global $query_string;
	query_posts( $query_string . '&posts_per_page=1&orderby=comment_count&order=DESC&ignore_sticky_posts=1' );
	if ( have_posts() ) : while( have_posts() ) : the_post();?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'grid-trending column-12' ); ?>>
				
				<?php //VIDEO POST EMBED
				if(has_term('videos', 'content_type')) { ?>
							
				<figure class="thumbnail">
					<a href="<?php the_permalink(); ?>" rel="nofollow"><?php the_post_thumbnail( 'loop' ); ?></a>
				</figure>
				
				<figure class="video column-8">
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
				
				<?php } else { // ANY OTHER POST THUMBNAIL ?>
							
				<figure class="column-8">
					<a href="<?php the_permalink(); ?>" rel="nofollow"><?php the_post_thumbnail( 'type-photo' ); ?></a>
					<?php t5_post_thumbnail_caption(); ?>
				</figure>
				
				<?php } //END CONTENT-TYPE FILTER ?>
				
				<div class="column-4 last">
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
					<section class="entry-meta">
						<?php $author = get_the_author_meta('ID'); ?>
						<span class="text">
							<time class="updated" datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></time>
							by <span class="vcard"><a class="author" rel="profile" href="<?php echo get_author_posts_url( $author ); ?>" title="See <?php the_author_meta('user_firstname'); ?>'s Profile"><?php the_author_meta('user_firstname'); ?> <?php the_author_meta('user_lastname'); ?></a></span>
							<a class="authorrss" href="<?php echo get_author_posts_url( $author ); ?>feed/" rel="nofollow" title="Subscribe to <?php the_author_meta('user_firstname'); ?>'s posts by RSS"><span class="screen-reader-text">RSS</a>
							<?php edit_post_link( 'Edit', '| ', '', '' ); ?>
						</span>
						<a class="count" href="<?php comments_link(); ?>" rel="nofollow"><?php comments_number('0', '1', '%'); ?> <span class="screen-reader-text">Comments</span></a>
					</section>
					<section class="entry-summary">
						<?php if(has_term('videos', 'content_type')) { t5_excerpt('35'); } else { t5_excerpt('80'); } ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link"><span class="page-link-meta">' . __( 'Pages:' ) . '</span>', 'after' => '</div>', 'next_or_number' => 'number' ) ); ?>
					</section>
					<footer class="entry-utility">
						<ul>
							<li><a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php the_permalink(); ?>" data-text="<?php the_title(); ?>" data-via="Travelllll">Tweet this post</a></li>
							<li><fb:like href="<?php the_permalink(); ?>" layout="button_count"></fb:like></li>
						</ul>
					</footer>
				</div>
			</article>
	<?php 
	endwhile; endif;
	remove_filter('posts_where', 'filter_where');
}

//GRID
wp_reset_query();
global $query_string;
query_posts( $query_string . '&posts_per_page=15&ignore_sticky_posts=1' );

if ( have_posts() ) : ?>
			    
		<div id="hfeed" class="grid">
			<?php $i = 0; while ( have_posts() ) : the_post(); $i++; ?>
			<?php $count = 'number' . $i; ?>
			
			<article id="post-<?php the_ID(); ?>" <?php post_class( $count ); ?>>
				<div class="<?php if(has_term('videos', 'content_type')) { echo 'video'; } else { echo 'image'; } ?>">
					<a class="link" href="<?php the_permalink(); ?>" rel="nofollow"><?php the_post_thumbnail( 'grid', array('title' => "") ); ?></a>
					<section class="entry-meta">
						<time class="updated" datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php the_time('F j, Y'); ?></time>
						<a class="count" href="<?php comments_link(); ?>" rel="nofollow"><?php comments_number('0', '1', '%'); ?> <span class="screen-reader-text">Comments</span></a>
						<div class="icon"></div>
					</section>
				</div>
				<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
			</article><!-- #post-<?php the_ID(); ?> -->
			

				
			<?php endwhile; ?>
		</div><!-- #hfeed -->
		
		<?php wp_pagenavi(); ?>

<?php else : ?>

		<h1>No posts found.</h1>
		
<?php endif; ?>