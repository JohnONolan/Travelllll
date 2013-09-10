<?php
/*
Template Name: T5 Survey
*/
?>
<?php get_template_part( 'header' ); ?>
	
	<section id="content" class="column-12">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<section class="entry-content">
				<h1>Thanks for your input!</h1>
				<p>The more responses we get, the more accurate and complete the results will be when we share them with you. Please help us to get the full picture by sharing this survey with your friends and colleagues!</p>
				<section class="twittershare">
					<a href="http://twitter.com/share" class="twitter-share-button" data-text="I took the @Travelllll 2011 Industry Survey, and you should too!" data-url="http://travelllll.com/2011/11/16/the-2011-survey/" data-count="horizontal">Tweet</a>
				</section>
				<section class="facebookshare">
					<fb:like href="http://travelllll.com/2011/11/16/the-2011-survey/" layout="button_count"></fb:like>
				</section>
				<section class="stumbleshare">
					<div id="stumble3"></div>
				</section>
				<section class="googleshare">
					<g:plusone size="medium" href="http://travelllll.com/2011/11/16/the-2011-survey/"></g:plusone>
				</section>
			</section>
		</article>
		<?php endwhile; endif; ?>
	</section><!-- #content -->
	<script src="http://www.stumbleupon.com/hostedbadge.php?s=1&r=http%3A%2F%2Ftravelllll.com%2F2011%2F11%2F16%2Fthe-2011-survey%2F&a=1&d=stumble3"></script>
<?php get_template_part( 'footer' ); ?>