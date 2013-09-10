<?php /*
Travelllll Related Posts
Author: John O'Nolan
*/
?>
<?php if ($related_query->have_posts()): $i = 0; ?>
<section class="relatedposts">
<h3 class="bar"><span>You Might Also Like</span></h3>
	<?php while ($related_query->have_posts()) : $related_query->the_post(); $i++ ?>
		<article id="post-<?php the_ID(); ?>" class="number<?php echo $i; ?>">
			<a href="<?php the_permalink(); ?>?utm_source=travelllll&utm_medium=widget&utm_campaign=related" rel="nofollow"><?php the_post_thumbnail( 'thumb' ); ?></a>
			<h3 class="entry-title">
				<a href="<?php the_permalink(); ?>?utm_source=travelllll&utm_medium=widget&utm_campaign=related"><?php the_title(); ?></a>
				<a class="count" href="<?php comments_link(); ?>?utm_source=travelllll&utm_medium=widget&utm_campaign=related" rel="nofollow"><?php comments_number('0', '1', '%'); ?> <span class="screen-reader-text">Comments</span></a>
			</h3>
		</article>
	<?php endwhile; ?>
</section>
<?php endif; ?>
