<?php

function filter_where($where = '', $query) {
	if ( isset($query->yarpp_cache_type) )
		return $where;
	$where .= " AND post_date > '" . date('Y-m-d', strtotime('-3 days')) . "'";
	return $where;
}
add_filter('posts_where', 'filter_where', 10, 2);

$args = array( 
    'posts_per_page' => 4, 
    'orderby' => 'comment_count', 
    'order' => 'DESC',
    'post__not_in' => get_option( 'sticky_posts' )
);
$popular_posts = new WP_Query( $args ); 

if( $popular_posts->have_posts() ) : ?>

<section id="trending">

    <h2 class="bar"><span>Trending Right Now</span></h2>
    
	<?php $count = 0; ?>
	<?php while( $popular_posts->have_posts() ) : $popular_posts->the_post(); $count++; ?>
    
    <?php if ( $count == 1 && !is_single() ) { ?>
    <?php $author = get_the_author_meta('ID'); ?>
	<article id="post-<?php the_ID(); ?>" class="number<?php echo $count; ?>">
		<figure><a href="<?php the_permalink(); ?>" rel="nofollow"><?php the_post_thumbnail( 'trending' ); ?></a></figure>
		<div class="sharewrap">
			<?php get_template_part( 'library/templates/content', 'cats' ); ?>
			<section class="sharebuttons">
				<section class="twittershare">
                	<a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php the_permalink(); ?>" data-text="<?php the_title(); ?>" data-count="vertical" data-via="Travelllll">Tweet</a>
                </section>
                <section class="facebookshare">
                	<fb:like href="<?php the_permalink(); ?>" layout="box_count"></fb:like>
                </section>
            </section>
        </div>
        <div class="contentwrap">
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
			<section class="entry-summary">
				<?php t5_excerpt( '48' ); ?>
			</section>
			<section class="entry-meta">
				<time class="published" datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></time>
				by <span class="vcard"><a class="author fn" href="<?php echo get_author_posts_url( $author ); ?>" title="See <?php the_author_meta('user_firstname'); ?>'s Profile"><?php the_author_meta('user_firstname'); ?> <?php the_author_meta('user_lastname'); ?></a></span>
				<a class="count" href="<?php comments_link(); ?>" rel="nofollow"><?php comments_number('0', '1', '%'); ?> <span class="screen-reader-text">Comments</span></a>
			</section>
		</div>
	</article>
	
	<?php if( !is_single() ) { ?><div class="rightcol"><?php } ?>
	<?php } else { ?>
		<article id="post-<?php the_ID(); ?>" class="number<?php echo $count; ?>">
			<a href="<?php the_permalink(); ?>" rel="nofollow"><?php the_post_thumbnail( 'thumb' ); ?></a>
			<h2 class="entry-title">
				<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
				<a class="count" href="<?php comments_link(); ?>" rel="nofollow"><?php comments_number('0', '1', '%'); ?> <span class="screen-reader-text">Comments</span></a>
			</h2>
		</article>
	<?php } ?>
	
    <?php endwhile; ?>
    <div class="clearfix"></div>
    
    <?php if( !is_single() ) : ?>
        <section class="related">
        <?php
        	wp_reset_query();
        	$args = array( 
		        'posts_per_page' => 1, 
		        'orderby' => 'comment_count', 
		        'order' => 'DESC',
		        'ignore_sticky_posts' => 1
		    );
		    $popular_posts = new WP_Query( $args ); 
		    remove_filter( 'posts_where', 'filter_where' );
		    if( $popular_posts->have_posts() ) : while( $popular_posts->have_posts() ) : $popular_posts->the_post(); ?>
		    <div class="clearfix"></div>
		    <?php related_posts( array( 'template_file'=>'yarpp-template-t5-simple.php' ), true, get_the_ID() ); ?>
			<?php endwhile; endif; ?>
		</section>
    </div>
    <div class="clearfix"></div>
    <?php endif; ?>
    
</section>

<?php 
endif; 
remove_filter( 'posts_where', 'filter_where' );
wp_reset_query();
?>