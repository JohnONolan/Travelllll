<?php 
// Get the path to the root.
$full_path = __FILE__;

$path_bits = explode( 'wp-content', $full_path );

$url = $path_bits[0];

// Require WordPress bootstrap.
require_once( $url . '/wp-load.php' );

$woo_theme_css = get_template_directory_uri() . '/style.css';
$woo_shortcode_css = get_template_directory_uri() . '/library/functions/css/shortcodes.css';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js" ></script>
<link rel="stylesheet" href="<?php echo $woo_theme_css; ?>" media="all" />
<link rel="stylesheet" href="<?php echo $woo_shortcode_css; ?>" media="all" />
<style>
	.post  { margin: -5px 0 0 0; }
	.shortcode-typography { display: block; margin-top: 20px; }	
</style>
<?php
	$font = trim( strip_tags( $_REQUEST['font'] ) );
	
	// Build array of usabel typefaces.
	$fonts_whitelist = array( 
						'Arial, Helvetica, sans-serif', 
						'Verdana, Geneva, sans-serif', 
						'|Trebuchet MS|, Tahoma, sans-serif', 
						'Georgia, |Times New Roman|, serif', 
						'Tahoma, Geneva, Verdana, sans-serif', 
						'Palatino, |Palatino Linotype|, serif', 
						'|Helvetica Neue|, Helvetica, sans-serif', 
						'Calibri, Candara, Segoe, Optima, sans-serif', 
						'|Myriad Pro|, Myriad, sans-serif', 
						'|Lucida Grande|, |Lucida Sans Unicode|, |Lucida Sans|, sans-serif', 
						'|Arial Black|, sans-serif', 
						'|Gill Sans|, |Gill Sans MT|, Calibri, sans-serif', 
						'Geneva, Tahoma, Verdana, sans-serif', 
						'Impact, Charcoal, sans-serif'
						);
	
	$fonts_whitelist = array(); // Temporarily remove the default fonts.
						
	if ( ! in_array( $font, $fonts_whitelist ) ) {
	
		woo_shortcode_typography_loadgooglefonts( $font );
		
	} // End IF Statement
?>
</head>
<body>

<?php

$shortcode = isset($_REQUEST['shortcode']) ? $_REQUEST['shortcode'] : '';

// WordPress automatically adds slashes to quotes
// http://stackoverflow.com/questions/3812128/although-magic-quotes-are-turned-off-still-escaped-strings
$shortcode = stripslashes($shortcode);

echo do_shortcode($shortcode);

?>
<?php do_action( 'woo_shortcode_generator_preview_footer' ); ?>
<script type="text/javascript">

    jQuery( '#woo-preview h3:first', window.parent.document).removeClass( 'woo-loading' );

</script>
<script type="text/javascript" src="<?php echo get_template_directory_uri() . '/library/functions/js/shortcodes.js'; ?>"></script>
</body>
</html>
