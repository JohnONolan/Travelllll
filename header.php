<!DOCTYPE HTML>
<html <?php language_attributes(); ?> xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'template_directory' ); ?>/library/fonts/typography.css" />
	
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>" charset="<?php bloginfo('charset'); ?>" />

	<title>
		<?php 
		if ( ereg( 'iPhone', $_SERVER['HTTP_USER_AGENT'] ) || ereg( 'iPod', $_SERVER['HTTP_USER_AGENT'] ) ) {
			echo 'Travelllll';
		} else {
			wp_title( '', true, 'right' ); 
		}
		?>
	</title>
	
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
	
	<link rel="shortcut icon" href="<?php bloginfo('stylesheet_directory'); ?>/library/images/favicon.ico" />
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php bloginfo('stylesheet_directory'); ?>/library/images/apple-touch-icon-114x114-precomposed.png" />
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php bloginfo('stylesheet_directory'); ?>/library/images/apple-touch-icon-72x72-precomposed.png" />
	<link rel="apple-touch-icon-precomposed" href="<?php bloginfo('stylesheet_directory'); ?>/library/images/apple-touch-icon-precomposed.png" />
	
	<?php if( is_home() ) : ?><link href="https://plus.google.com/106314355435460595106" rel="publisher" /><?php endif; ?>

	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
	<link rel="alternate" type="application/rss+xml" title="Travelllll.com" href="http://feeds.feedburner.com/Travelllll" />
	
	<!--[if IE]>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<script type="text/javascript" src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	 
	<?php
	if ( is_singular() && get_option( 'thread_comments' ) ) { 
	  wp_enqueue_script( 'comment-reply' ); 
	} 
    wp_enqueue_script( 'jquery' ); 
    wp_register_script( 'nivoslider', get_bloginfo( 'template_directory' ) .'/library/js/jquery.nivo.slider.pack.js', array('jquery') );
    wp_enqueue_script( 'nivoslider' ); 
	wp_head();
	?>
		
</head>
<body <?php body_class(); ?>>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=250874831606508";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>
<!-- BuySellAds Ad Code -->
<script type="text/javascript">
(function(){
  var bsa = document.createElement('script');
     bsa.type = 'text/javascript';
     bsa.async = true;
     bsa.src = 'http://s3.buysellads.com/ac/bsa.js';
  (document.getElementsByTagName('head')[0]||document.getElementsByTagName('body')[0]).appendChild(bsa);
})();
</script>
<!-- End BuySellAds Ad Code -->
<div class="roadblockheader"></div>
<header id="top" role="banner">
	<div id="branding" class="wrap">
		<a id="site-title" href="<?php bloginfo( 'url' ); ?>">Travelllll.com</a>
		<?php echo wp_nav_menu( array( 'container' => 'nav', 'container_class' => 'top-nav-wrap', 'menu_class' => 'menu', 'theme_location' => 'secondary', 'show_home' => true ) ); ?>
		<div id="site-search">
			<?php get_search_form(); ?>
		</div><!--#site-search-->
	</div><!--#branding-->
	
	<div id="site-navigation" role="navigation">
		<?php echo wp_nav_menu( array( 'container' => 'nav', 'container_class' => 'wrap', 'menu_class' => 'menu wrap', 'theme_location' => 'primary', 'show_home' => true ) ); ?>
	</div><!--#site-navigation-->
</header><!--header-->
<?php 
	if( is_front_page() && get_option( 'sticky_posts' ) ) {
		get_template_part( 'library/templates/slider' ); 
	}

	if( is_single() || is_page() ) { 
		get_template_part( 'library/templates/breadcrumbs' ); 
	}
?>
<div id="main" role="main">
	<div class="wrap">
		<?php if ( !is_404() ) : ?>
		<div class="headerads">
			<div class="ad1"></div>
			<div class="ad2"></div>
			<div class="clearfix"></div>
		</div>
		<?php endif; ?>