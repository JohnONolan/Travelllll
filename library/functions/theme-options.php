<?php

if (!function_exists( 'woo_options')) {
function woo_options(){
	
// THEME VARIABLES
$themename = "Travelllll";
$themeslug = "travelllll";
					                

// Woo Metabox Options
// Start name with underscore to hide custom key from the user
$woo_metaboxes = array();

global $post;


if ( ( get_post_type() == 'post') || ( !get_post_type() ) ) {

	$woo_metaboxes[] = array (	"name" => "general_heading",
								"label" => "General Options",
								"type" => "info",
								"desc" => "" );
	
	$woo_metaboxes[] = array (	"name" => "source_name",
								"label" => "Source Name",
								"type" => "text",
								"desc" => "Enter the name of the source of this story. Keep this as short as possible." );
	
	$woo_metaboxes[] = array (	"name" => "source_url",
								"label" => "Source URL",
								"type" => "text",
								"desc" => "Enter the URL of the source of this story." );
	
	$woo_metaboxes[] = array (	"name" => "video_heading",
								"label" => "Video Options",
								"type" => "info",
								"desc" => "" );
	
	$woo_metaboxes[] = array (  "name"  => "embed",
					            "std"  => "",
					            "label" => "Video Embed",
					            "type" => "textarea",
					            "desc" => "Enter the video embed code for your video (YouTube, Vimeo or similar). Embed size attributes don't matter - any size will work." );
	
	$woo_metaboxes[] = array (	"name" => "featured_heading",
								"label" => "Featured Options",
								"type" => "info",
								"desc" => "" );
					            
	$woo_metaboxes[] = array (	"name" => "image",
								"label" => "Slider Image",
								"type" => "upload",
								"desc" => "Upload an image or enter an URL. This is ONLY for manually promoted stories on the homepage. 978x180" );
								
	$woo_metaboxes[] = array (	"name" => "sponsored_heading",
								"label" => "Sponsored Options",
								"type" => "info",
								"desc" => "" );
	
	$woo_metaboxes[] = array (	"name" => "sponsored_image",
								"label" => "Sponsored Image",
								"type" => "upload",
								"desc" => "Upload an image of the sponsor logo. The image will be resized to fit into a 220x180 box." );
								
	$woo_metaboxes[] = array (  "name"  => "sponsored_text",
					            "std"  => "",
					            "label" => "Sponsored Caption",
					            "type" => "textarea",
					            "desc" => "Enter the agreed caption for this sponsor. No more than 2 links please." );
					            
	$woo_metaboxes[] = array (	"name" => "sponsored_background",
								"label" => "Sponsored Background",
								"type" => "upload",
								"desc" => "If applicable, upload a featured background image for this post. Must be at least 1280px wide." );
	
	$woo_metaboxes[] = array (	"name" => "sponsored_background_padding",
								"label" => "Sponsored BG Padding",
								"type" => "text",
								"desc" => "Optionally, enter a custom value for vertical padding space in px. Eg: 350. Default is 250px." );
	
	$woo_metaboxes[] = array (	"name" => "sponsored_background_color",
								"label" => "Sponsored BG Color",
								"type" => "text",
								"desc" => "Optionally, enter a custom hex value for background colour. Eg: 000000. Default is white." );

					            
} // End post


// Add extra metaboxes through function
if ( function_exists( "woo_metaboxes_add") )
	$woo_metaboxes = woo_metaboxes_add($woo_metaboxes);
    
if ( get_option( 'woo_custom_template' ) != $woo_metaboxes) update_option( 'woo_custom_template', $woo_metaboxes );      

} // END woo_options()
} // END function_exists()

// Add options to admin_head
add_action( 'admin_head','woo_options' );  

//Global options setup
add_action( 'init','woo_global_options' );
function woo_global_options(){
	// Populate WooThemes option in array for use in theme
	global $woo_options;
	$woo_options = get_option( 'woo_options' );
}

?>