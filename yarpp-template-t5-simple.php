<?php /*
Simple T5 Related Posts Template
Author: John O'Nolan
*/
?><h3 class="bar"><span>You Might Also Like</span></h3>
<?php if ($related_query->have_posts()):?>
<ol>
	<?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
	<li><a href="<?php the_permalink() ?>?utm_source=travelllll&utm_medium=widget&utm_campaign=related" rel="bookmark"><?php the_title(); ?></a></li>
	<?php endwhile; ?>
</ol>
<?php else: ?>
<p>No related posts.</p>
<?php endif; ?>
