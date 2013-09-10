	</div><!--#main.wrap-->
</div><!--#main-->

<footer id="sitefoot" role="contentinfo">
	<div class="wrap">
		<section id="aboutt5" class="column-3">
			<a id="footerlogo" href="<?php bloginfo( 'url' ); ?>" rel="nofollow"><img src="<?php bloginfo( 'template_directory' ); ?>/library/images/logo-footer.png" alt="Travelllll.com" /></a>
			<span id="copyright">All content &copy;<?php the_date( 'Y' ); ?> <strong>Travellll</strong>.com</span>
			<p>Travelllll.com is the hottest travel news site on the planet. We write about international travel news and trends in digital travel journalism.</p>
		</section>
		<nav id="contenthubs" class="column-2">
			<h5>Content Hubs</h5>
			<?php echo wp_nav_menu( array( 'theme_location' => 'footer1' ) ); ?>
		</nav>
		<nav id="channels" class="column-2">
			<h5>Channels</h5>
			<?php echo wp_nav_menu( array( 'theme_location' => 'footer2' ) ); ?>
		</nav>
		<nav id="company" class="column-2">
			<h5>Company</h5>
			<?php echo wp_nav_menu( array( 'theme_location' => 'footer3' ) ); ?>
		</nav>
		<section id="connect" class="column-3 last">
			<h5>Connect</h5>
			<ul class="leftcol">
				<li><a id="fttwitter" href="http://twitter.com/Travelllll" rel="nofollow"><span>Twitter</span></a></li>
				<li><a id="ftfacebook" href="http://facebook.com/Travelllll" rel="nofollow"><span>Facebook</span></a></li>
				<li><a id="ftgplus" href="http://plus.google.com/b/106314355435460595106/" rel="nofollow"><span>Google+</span></a></li>
			</ul>
			<ul class="rightcol">
				<li><a id="ftlinkedin" href="http://www.linkedin.com/company/travelllll-com" rel="nofollow"><span>LinkedIn</span></a></li>
				<li><a id="ftrss" href="http://feeds.feedburner.com/Travelllll" rel="nofollow"><span>RSS</span></a></li>
			</ul>
			<div class="clearfix"></div>
			<div class="bottom">
				<?php 
					$numposts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post'");
					if (0 < $numposts) $numposts = number_format($numposts);
					$numcomms = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1'");
					if (0 < $numcomms) $numcomms = number_format($numcomms);
					$numcomms = $numcomms;
					
				?>
				<span id="sitestats">Proudly sharing <strong><?php echo $numposts; ?></strong> posts <span class="basker">&</span> <strong><?php echo $numcomms; ?></strong> comments with <strong>14,000+</strong> subscribers.</span>
				<nav id="top-link"><a href="#top" id="link-to-top">Back to Top <span class="up-arr">↑</span></a></nav>
			</div>
		</section>
	</div><!--#colophon-->
</footer>

<?php if (is_singular()) : // Load sharing buttons ?>
<script type="text/javascript">
  var sharetopoffset = jQuery("#sharebar").offset().top - parseFloat(jQuery("#sharebar").css("margin-top").replace(/auto/,0));
  
   jQuery(window).scroll(function(event) {
       var y = jQuery(this).scrollTop();
       if(y >= sharetopoffset) {
           jQuery("#sharebar").addClass("fixed");
       } else {
           jQuery("#sharebar").removeClass("fixed");
       }
   });
	jQuery("div.post-revisions h4").append(" (<span>Show</span>)");
	jQuery('div.post-revisions h4 span').click(function() {
		jQuery("div.post-revisions ul, div.post-revisions p").toggleClass("show");
	});
</script>
<script src="http://www.stumbleupon.com/hostedbadge.php?s=5&a=1&d=stumble"></script>
<script src="http://www.stumbleupon.com/hostedbadge.php?s=1&a=1&d=stumble2"></script>
<?php endif; ?>

<script type="text/javascript">
	jQuery(document).ready(function() {
		//back to top
		/Mobile/.test(navigator.userAgent) && !location.hash && setTimeout(function () {
		    if (!pageYOffset) window.scrollTo(0, 1);
		}, 1000);
		
		jQuery('#link-to-top').click(function(){
			jQuery('html, body').animate({scrollTop:0}, 'slow');
			return false;
		});
		//responsive video embeds
		jQuery(".content-videos .video").fitVids();
		//google plusone
		var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.src = 'https://apis.google.com/js/plusone.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	})();
</script>
<script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>
<?php wp_footer(); ?>
</body>
</html>