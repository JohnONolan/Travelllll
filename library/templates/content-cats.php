<section class="content-cats">
	<ul>
		<?php if(has_term('interviews', 'content_type')) { ?>
		<li class="interview"><a href="<?php bloginfo('url'); ?>/interviews/" rel="nofollow">Interview</a></li>	
		<?php } elseif(has_term('news', 'content_type')) { ?>
		<li class="news"><a href="<?php bloginfo('url'); ?>/news/" rel="nofollow">News</a></li>
		<?php } elseif(has_term('opinion', 'content_type')) { ?>
		<li class="opinion"><a href="<?php bloginfo('url'); ?>/opinion/" rel="nofollow">Opinion</a></li>
		<?php } elseif(has_term('photos', 'content_type')) { ?>
		<li class="photo"><a href="<?php bloginfo('url'); ?>/photos/" rel="nofollow">Photo</a></li>
		<?php } elseif(has_term('podcast', 'content_type')) { ?>
		<li class="podcast"><a href="<?php bloginfo('url'); ?>/podcast/" rel="nofollow">Podcast</a></li>
		<?php } elseif(has_term('videos', 'content_type')) { ?>
		<li class="video"><a href="<?php bloginfo('url'); ?>/videos/" rel="nofollow">Video</a></li>
		<?php } elseif(has_term('reviews', 'content_type')) { ?>
		<li class="review"><a href="<?php bloginfo('url'); ?>/reviews/" rel="nofollow">Review</a></li>
		<?php } elseif(has_term('features', 'content_type')) { ?>
		<li class="feature"><a href="<?php bloginfo('url'); ?>/features/" rel="nofollow">Feature</a></li>
		<?php } elseif(has_term('alerts', 'content_type')) { ?>
		<li class="alert"><a href="<?php bloginfo('url'); ?>/alerts/" rel="nofollow">Alert</a></li>
		<?php } elseif(has_term('links', 'content_type')) { ?>
		<li class="link"><a href="<?php bloginfo('url'); ?>/links/" rel="nofollow">Link</a></li>
		<?php } elseif(has_term('reports', 'content_type')) { ?>
		<li class="report"><a href="<?php bloginfo('url'); ?>/reports/" rel="nofollow">Report</a></li>
		<?php } elseif(has_term('live', 'content_type')) { ?>
		<li class="live"><a href="<?php bloginfo('url'); ?>/live/" rel="nofollow"><span>Live</span></a></li>
		<?php } elseif(has_term('discussions', 'content_type')) { ?>
		<li class="discussion"><a href="<?php bloginfo('url'); ?>/discussions/" rel="nofollow">Discussion</a></li>
		<?php } elseif(has_term('guides', 'content_type')) { ?>
		<li class="guide"><a href="<?php bloginfo('url'); ?>/guides/" rel="nofollow">Guide</a></li>
		<?php } elseif(has_term('how-to', 'content_type')) { ?>
		<li class="howto"><a href="<?php bloginfo('url'); ?>/how-to/" rel="nofollow">How To</a></li>
		<?php } elseif(has_term('essays', 'content_type')) { ?>
		<li class="essay"><a href="<?php bloginfo('url'); ?>/essays/" rel="nofollow">Essay</a></li>
		<?php } ?>
		
		<?php if(in_category( 'business' )) { ?>
		<li class="tag"><a href="<?php bloginfo('url'); ?>/business/" rel="nofollow">Business</a></li>
		<?php } ?>
		<?php if(in_category( 'travel' )) { ?>
		<li class="tag"><a href="<?php bloginfo('url'); ?>/travel/" rel="nofollow">Travel</a></li>
		<?php } ?>
		<?php if(in_category( 'tech' )) { ?>
		<li class="tag"><a href="<?php bloginfo('url'); ?>/tech/" rel="nofollow">Tech</a></li>
		<?php } ?>
		<?php if(in_category( 'tourism' )) { ?>
		<li class="tag"><a href="<?php bloginfo('url'); ?>/tourism/" rel="nofollow">Tourism</a></li>
		<?php } ?>
		<?php if(in_category( 'events' )) { ?>
		<li class="tag"><a href="<?php bloginfo('url'); ?>/events/" rel="nofollow">Event</a></li>
		<?php } ?>
	</ul>
	
	<?php if ( is_single() ) : ?>
	<div class="sharewrap">
		<section id="sharebar" class="sharebuttons">
			<section class="twittershare">
	        	<a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php the_permalink(); ?>" data-text="<?php the_title(); ?>" data-count="vertical" data-via="Travelllll">Tweet</a>
	        </section>
	        <section class="facebookshare">
	        	<fb:like href="<?php the_permalink(); ?>" layout="box_count"></fb:like>
	        </section>
	        <section class="googleshare">
	        	<g:plusone size="tall"></g:plusone>
	        </section>
	        <section class="stumbleshare">
	        	<div id="stumble"></div>
	        </section>
		</section>
	</div>
	<?php endif; ?>
	
</section>