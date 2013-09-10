<?php

function another_filter($where = '') {
	$where .= " AND post_date > '" . date('Y-m-d', strtotime('-30 days')) . "'";
	return $where;
}
add_filter('posts_where', 'another_filter');

$args = array( 
    'posts_per_page' => 6, 
    'orderby' => 'comment_count', 
    'order' => 'DESC',
    'ignore_sticky_posts' => 1
);
$popular_posts = new WP_Query( $args ); 
if( $popular_posts->have_posts() ) : ?>
<div class="clearfix"></div>
<section class="popularposts">
    <h2 class="bar"><span>Popular This Month</span></h2>
    <?php $count = 0; ?>
    <?php while( $popular_posts->have_posts() ) : $popular_posts->the_post(); $count++; ?>
	<article id="post-<?php the_ID(); ?>" class="number<?php echo $count; ?>">
		<a href="<?php the_permalink(); ?>" rel="nofollow"><?php the_post_thumbnail( 'thumb' ); ?></a>
		<h3 class="entry-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			<a class="count" href="<?php comments_link(); ?>" rel="nofollow"><?php comments_number('0', '1', '%'); ?> <span class="screen-reader-text">Comments</span></a>
		</h3>
	</article>
    <?php endwhile; ?>
    <div class="clearfix"></div>
</section>
<?php 
endif; 
remove_filter( 'posts_where', 'another_filter' );
?>