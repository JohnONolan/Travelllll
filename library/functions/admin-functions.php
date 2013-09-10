<?php
/*-----------------------------------------------------------------------------------*/
/* woo_image - Get Image from custom field  */
/*-----------------------------------------------------------------------------------*/

/*
This function retrieves/resizes the image to be used with the post in this order:

1. Image passed through parameter 'src'
2. WP Post Thumbnail (if option activated)
3. Custom field
4. First attached image in post (if option activated)
5. First inline image in post (if option activated)

Resize options (enabled in options panel):
- vt_resize() is used to natively resize #2 and #4
- Thumb.php is used to resize #1, #3, #4 (only if vt_resize is disabled) and #5

Parameters:
        $key = Custom field key eg. "image"
        $width = Set width manually without using $type
        $height = Set height manually without using $type
        $class = CSS class to use on the img tag eg. "alignleft". Default is "thumbnail"
        $quality = Enter a quality between 80-100. Default is 90
        $id = Assign a custom ID, if alternative is required.
        $link = Echo with anchor ( 'src'), without anchor ( 'img') or original image URL ( 'url').
        $repeat = Auto Img Function. Adjust amount of images to return for the post attachments.
        $offset = Auto Img Function. Offset the $repeat with assigned amount of objects.
        $before = Auto Img Function. Add Syntax before image output.
        $after = Auto Img Function. Add Syntax after image output.
        $single = (true/false) Force thumbnail to link to the post instead of the image.
        $force = Force smaller images to not be effected with image width and height dimensions (proportions fix)
        $return = Return results instead of echoing out.
		$src = A parameter that accepts a img url for resizing. (No anchor)
		$meta = Add a custom meta text to the image and anchor of the image.
		$alignment = Crop alignment for thumb.php (l, r, t, b)
		$size = Custom pre-defined size for WP Thumbnail (string)
*/

if ( !function_exists('woo_image') ) {
function woo_image($args) {

	/* ------------------------------------------------------------------------- */
	/* SET VARIABLES */
	/* ------------------------------------------------------------------------- */

	global $post;
	global $woo_options;

	//Defaults
	$key = 'image';
	$width = null;
	$height = null;
	$class = '';
	$quality = 90;
	$id = null;
	$link = 'src';
	$repeat = 1;
	$offset = 0;
	$before = '';
	$after = '';
	$single = false;
	$force = false;
	$return = false;
	$is_auto_image = false;
	$src = '';
	$meta = '';
	$alignment = '';
	$size = '';

	$alt = '';
	$img_link = '';

	$attachment_id = array();
	$attachment_src = array();

	if ( !is_array($args) )
		parse_str( $args, $args );

	extract($args);

    // Set post ID
    if ( empty($id) ) {
		$id = $post->ID;
    }

	$thumb_id = get_post_meta($id,'_thumbnail_id',true);

	// Set alignment
	if ( $alignment == '')
		$alignment = get_post_meta($id, '_image_alignment', true);

	// Get standard sizes
	if ( !$width && !$height ) {
		$width = '100';
		$height = '100';
	}

	/* ------------------------------------------------------------------------- */
	/* FIND IMAGE TO USE */
	/* ------------------------------------------------------------------------- */

	// When a custom image is sent through
	if ( $src != '' ) {
		$custom_field = $src;
		$link = 'img';

	// Grab the image from custom field
	} else {
    	$custom_field = get_post_meta($id, $key, true);
	}

	// Automatic Image Thumbs - get first image from post attachment
	if ( empty($custom_field) && get_option( 'woo_auto_img') == 'true' && empty($img_link) && !(is_singular() AND in_the_loop() AND $link == "src") ) {

        if( $offset >= 1 )
			$repeat = $repeat + $offset;

        $attachments = get_children( array(	'post_parent' => $id,
											'numberposts' => $repeat,
											'post_type' => 'attachment',
											'post_mime_type' => 'image',
											'order' => 'DESC',
											'orderby' => 'menu_order date')
											);

		// Search for and get the post attachment
		if ( !empty($attachments) ) {

			$counter = -1;
			$size = 'large';
			foreach ( $attachments as $att_id => $attachment ) {
				$counter++;
				if ( $counter < $offset )
					continue;

				if ( get_option( 'woo_post_image_support' ) == "true" AND get_option( 'woo_pis_resize') == "true" ) {

					// Dynamically resize the post thumbnail
					$vt_crop = get_option( 'woo_pis_hard_crop' );
					if ($vt_crop == "true") $vt_crop = true; else $vt_crop = false;
					$vt_image = vt_resize( $att_id, '', $width, $height, $vt_crop );

					// Set fields for output
					$custom_field = $vt_image['url'];
					$width = $vt_image['width'];
					$height = $vt_image['height'];

				} else {

					$src = wp_get_attachment_image_src($att_id, $size, true);
					$custom_field = $src[0];
					$attachment_id[] = $att_id;
					$src_arr[] = $custom_field;

				}
				$thumb_id = $att_id;
				$is_auto_image = true;
			}

		// Get the first img tag from content
		} else {

			$first_img = '';
			$post = get_post($id);
			ob_start();
			ob_end_clean();
			$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
			if ( !empty($matches[1][0]) ) {

				// Save Image URL
				$custom_field = $matches[1][0];

				// Search for ALT tag
				$output = preg_match_all( '/<img.+alt=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
				if ( !empty($matches[1][0]) ) {
					$alt = $matches[1][0];
				}
			}

		}

	}

	// Check if there is YouTube embed
	if ( empty($custom_field) && empty($img_link) ) {
		$embed = get_post_meta($id, "embed", true);
		if ( $embed )
	    	$custom_field = woo_get_video_image($embed);
	}

	// Return if there is no attachment or custom field set
	if ( empty($custom_field) && empty($img_link) ) {

		// Check if default placeholder image is uploaded
		$placeholder = get_option( 'framework_woo_default_image' );
		if ( $placeholder && !(is_singular() AND in_the_loop()) ) {
			$custom_field = $placeholder;

			// Resize the placeholder if
			if ( get_option( 'woo_post_image_support' ) == "true" AND get_option( 'woo_pis_resize') == "true") {

				// Dynamically resize the post thumbnail
				$vt_crop = get_option( 'woo_pis_hard_crop' );
				if ($vt_crop == "true") $vt_crop = true; else $vt_crop = false;
				$vt_image = vt_resize( '', $placeholder, $width, $height, $vt_crop );

				// Set fields for output
				$custom_field = $vt_image['url'];
				$width = $vt_image['width'];
				$height = $vt_image['height'];

			}

		} else {
	       return;
	    }

	}

	if(empty($src_arr) && empty($img_link)){ $src_arr[] = $custom_field; }

	/* ------------------------------------------------------------------------- */
	/* BEGIN OUTPUT */
	/* ------------------------------------------------------------------------- */

    $output = '';

    // Set output height and width
    $set_width = ' width="' . $width .'" ';
    $set_height = ' height="' . $height .'" ';
    if($height == null OR $height == '') $set_height = '';

	// Set standard class
	if ( $class ) $class = 'woo-image ' . $class; else $class = 'woo-image';

	// Do check to verify if images are smaller then specified.
	if($force == true){ $set_width = ''; $set_height = ''; }

	// WP Post Thumbnail
	if(!empty($img_link) ){

		if( $link == 'img' ) {  // Output the image without anchors
			$output .= $before;
			$output .= $img_link;
			$output .= $after;

		} elseif( $link == 'url' ) {  // Output the large image

			$src = wp_get_attachment_image_src($thumb_id, 'large', true);
			$custom_field = $src[0];
			$output .= $custom_field;

		} else {  // Default - output with link

			if ( ( is_single() OR is_page() ) AND $single == false ) {
				$rel = 'rel="lightbox"';
				$href = false;
			} else {
				$href = get_permalink($id);
				$rel = '';
			}

			$title = 'title="' . get_the_title($id) .'"';

			$output .= $before;
			if($href == false){
				$output .= $img_link;
			} else {
				$output .= '<a '.$title.' href="' . $href .'" '.$rel.'>' . $img_link . '</a>';
			}

			$output .= $after;
		}
	}

	// Use thumb.php to resize. Skip if image has been natively resized with vt_resize.
	elseif ( get_option( 'woo_resize') == 'true' && empty($vt_image['url']) ) {

		foreach($src_arr as $key => $custom_field){

			// Clean the image URL
			$href = $custom_field;
			$custom_field = cleanSource( $custom_field );

			// Check if WPMU and set correct path AND that image isn't external
			if ( function_exists( 'get_current_site') && strpos($custom_field,"http://") !== 0 ) {
				get_current_site();
				//global $blog_id; Breaks with WP3 MS
				if ( !$blog_id ) {
					global $current_blog;
					$blog_id = $current_blog->blog_id;
				}
				if ( isset($blog_id) && $blog_id > 0 ) {
					$imageParts = explode( 'files/', $custom_field );
					if ( isset($imageParts[1]) )
						$custom_field = '/blogs.dir/' . $blog_id . '/files/' . $imageParts[1];
				}
			}

			//Set the ID to the Attachment's ID if it is an attachment
			if($is_auto_image == true){
				$quick_id = $attachment_id[$key];
			} else {
			 	$quick_id = $id;
			}

			//Set custom meta
			if ($meta) {
				$alt = $meta;
				$title = 'title="'. $meta .'"';
			} else {
				if ($alt == '' AND get_post_meta($thumb_id, '_wp_attachment_image_alt', true) )
					$alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
				else
					$alt = get_the_title($quick_id);
				$title = 'title="'. get_the_title($quick_id) .'"';
			}

			// Set alignment parameter
			if ($alignment <> '')
				$alignment = '&amp;a='.$alignment;

			$img_link = '<img src="'. get_template_directory_uri(). '/thumb.php?src='. $custom_field .'&amp;w='. $width .'&amp;h='. $height .'&amp;zc=1&amp;q='. $quality . $alignment . '" alt="'.$alt.'" class="'. stripslashes($class) .'" '. $set_width . $set_height . ' />';

			if( $link == 'img' ) {  // Just output the image
				$output .= $before;
				$output .= $img_link;
				$output .= $after;

			} elseif( $link == 'url' ) {  // Output the image without anchors

				if($is_auto_image == true){
					$src = wp_get_attachment_image_src($thumb_id, 'large', true);
					$custom_field = $src[0];
				}
				$output .= $custom_field;

			} else {  // Default - output with link

				if ( ( is_single() OR is_page() ) AND $single == false ) {
					$rel = 'rel="lightbox"';
				} else {
					$href = get_permalink($id);
					$rel = '';
				}

				$output .= $before;
				$output .= '<a '.$title.' href="' . $href .'" '.$rel.'>' . $img_link . '</a>';
				$output .= $after;
			}
		}

	// No dynamic resizing
	} else {

		foreach($src_arr as $key => $custom_field){

			//Set the ID to the Attachment's ID if it is an attachment
			if($is_auto_image == true AND isset($attachment_id[$key])){
				$quick_id = $attachment_id[$key];
			} else {
			 	$quick_id = $id;
			}

			//Set custom meta
			if ($meta) {
				$alt = $meta;
				$title = 'title="'. $meta .'"';
			} else {
				if ($alt == '') $alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
				$title = 'title="'. get_the_title($quick_id) .'"';
			}

			$img_link =  '<img src="'. $custom_field .'" alt="'. $alt .'" '. $set_width . $set_height . ' class="'. stripslashes($class) .'" />';

			if ( $link == 'img' ) {  // Just output the image
				$output .= $before;
				$output .= $img_link;
				$output .= $after;

			} elseif( $link == 'url' ) {  // Output the URL to original image
				if ( $vt_image['url'] || $is_auto_image ) {
					$src = wp_get_attachment_image_src($thumb_id, 'full', true);
					$custom_field = $src[0];
				}
				$output .= $custom_field;

			} else {  // Default - output with link

				if ( ( is_single() OR is_page() ) AND $single == false ) {

					// Link to the large image if single post
					if ( $vt_image['url'] || $is_auto_image ) {
						$src = wp_get_attachment_image_src($thumb_id, 'full', true);
						$custom_field = $src[0];
					}

					$href = $custom_field;
					$rel = 'rel="lightbox"';
				} else {
					$href = get_permalink($id);
					$rel = '';
				}

				$output .= $before;
				$output .= '<a href="' . $href .'" '. $rel . $title .'>' . $img_link . '</a>';
				$output .= $after;
			}
		}
	}

	// Return or echo the output
	if ( $return == TRUE )
		return $output;
	else
		echo $output; // Done

}
}

/* Get thumbnail from Video Embed code */

if (!function_exists( 'woo_get_video_image')) {
	function woo_get_video_image($embed) {

		// YouTube - get the video code if this is an embed code (old embed)
		preg_match( '/youtube\.com\/v\/([\w\-]+)/', $embed, $match);

		// YouTube - if old embed returned an empty ID, try capuring the ID from the new iframe embed
		if($match[1] == '')
			preg_match( '/youtube\.com\/embed\/([\w\-]+)/', $embed, $match);

		// YouTube - if it is not an embed code, get the video code from the youtube URL
		if($match[1] == '')
			preg_match( '/v\=(.+)&/',$embed ,$match);

		// YouTube - get the corresponding thumbnail images
		if($match[1] != '')
			$video_thumb = "http://img.youtube.com/vi/".$match[1]."/0.jpg";

		// return whichever thumbnail image you would like to retrieve
		return $video_thumb;
	}
}


/*-----------------------------------------------------------------------------------*/
/* vt_resize - Resize images dynamically using wp built in functions
/*-----------------------------------------------------------------------------------*/
/*
 * Resize images dynamically using wp built in functions
 * Victor Teixeira
 *
 * php 5.2+
 *
 * Exemplo de uso:
 *
 * <?php
 * $thumb = get_post_thumbnail_id();
 * $image = vt_resize( $thumb, '', 140, 110, true );
 * ?>
 * <img src="<?php echo $image[url]; ?>" width="<?php echo $image[width]; ?>" height="<?php echo $image[height]; ?>" />
 *
 * @param int $attach_id
 * @param string $img_url
 * @param int $width
 * @param int $height
 * @param bool $crop
 * @return array
 */
if ( !function_exists( 'vt_resize') ) {
	function vt_resize( $attach_id = null, $img_url = null, $width, $height, $crop = false ) {

		// this is an attachment, so we have the ID
		if ( $attach_id ) {

			$image_src = wp_get_attachment_image_src( $attach_id, 'full' );
			$file_path = get_attached_file( $attach_id );

		// this is not an attachment, let's use the image url
		} else if ( $img_url ) {

			$file_path = parse_url( $img_url );
			$file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];

			//$file_path = ltrim( $file_path['path'], '/' );
			//$file_path = rtrim( ABSPATH, '/' ).$file_path['path'];

			$orig_size = getimagesize( $file_path );

			$image_src[0] = $img_url;
			$image_src[1] = $orig_size[0];
			$image_src[2] = $orig_size[1];
		}

		$file_info = pathinfo( $file_path );

		// check if file exists
		$base_file = $file_info['dirname'].'/'.$file_info['filename'].'.'.$file_info['extension'];
		if ( !file_exists($base_file) )
		 return;

		$extension = '.'. $file_info['extension'];

		// the image path without the extension
		$no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];

		$cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;

		// checking if the file size is larger than the target size
		// if it is smaller or the same size, stop right here and return
		if ( $image_src[1] > $width ) {

			// the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
			if ( file_exists( $cropped_img_path ) ) {

				$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );

				$vt_image = array (
					'url' => $cropped_img_url,
					'width' => $width,
					'height' => $height
				);

				return $vt_image;
			}

			// $crop = false
			if ( $crop == false ) {

				// calculate the size proportionaly
				$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
				$resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;

				// checking if the file already exists
				if ( file_exists( $resized_img_path ) ) {

					$resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

					$vt_image = array (
						'url' => $resized_img_url,
						'width' => $proportional_size[0],
						'height' => $proportional_size[1]
					);

					return $vt_image;
				}
			}

			// check if image width is smaller than set width
			$img_size = getimagesize( $file_path );
			if ( $img_size[0] <= $width ) $width = $img_size[0];

			// no cache files - let's finally resize it
			$new_img_path = image_resize( $file_path, $width, $height, $crop );
			$new_img_size = getimagesize( $new_img_path );
			$new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

			// resized output
			$vt_image = array (
				'url' => $new_img,
				'width' => $new_img_size[0],
				'height' => $new_img_size[1]
			);

			return $vt_image;
		}

		// default output - without resizing
		$vt_image = array (
			'url' => $image_src[0],
			'width' => $width,
			'height' => $height
		);

		return $vt_image;
	}
}

/*-----------------------------------------------------------------------------------*/
/* woo_embed - Get Video embed code from custom field */
/*-----------------------------------------------------------------------------------*/

/*
Get Video
This function gets the embed code from the custom field
Parameters:
        $key = Custom field key eg. "embed"
        $width = Set width manually without using $type
        $height = Set height manually without using $type
		$class = Custom class to apply to wrapping div
		$id = ID from post to pull custom field from
*/

if ( !function_exists('woo_embed') ) {
function woo_embed($args) {

	//Defaults
	$key = 'embed';
	$width = null;
	$height = null;
	$class = 'video';
	$id = null;

	if ( !is_array($args) )
		parse_str( $args, $args );

	extract($args);

  if(empty($id))
    {
    global $post;
    $id = $post->ID;
    }


$custom_field = get_post_meta($id, $key, true);

if ($custom_field) :

	$custom_field = html_entity_decode( $custom_field ); // Decode HTML entities.

    $org_width = $width;
    $org_height = $height;
    $calculated_height = '';

    // Get custom width and height
    $custom_width = get_post_meta($id, 'width', true);
    $custom_height = get_post_meta($id, 'height', true);

    //Dynamic Height Calculation
    if ($org_height == '' && $org_width != '') {
    	$raw_values = explode( " ", $custom_field);

    	foreach ($raw_values as $raw) {
    		$embed_params = explode( "=",$raw);
    		if ($embed_params[0] == 'width') {
   		 		$embed_width = ereg_replace( "[^0-9]", "", $embed_params[1]);
    		}
    		elseif ($embed_params[0] == 'height') {
    			$embed_height = ereg_replace( "[^0-9]", "", $embed_params[1]);
    		}
    	}

    	$float_width = floatval($embed_width);
		$float_height = floatval($embed_height);
		@$float_ratio = $float_height / $float_width;
		$calculated_height = intval($float_ratio * $width);
    }

    // Set values: width="XXX", height="XXX"
    if ( !$custom_width ) $width = 'width="'.$width.'"'; else $width = 'width="'.$custom_width.'"';
    if ( $height == '' ) { $height = 'height="'.$calculated_height.'"'; } else { if ( !$custom_height ) { $height = 'height="'.$height.'"'; } else { $height = 'height="'.$custom_height.'"'; } }
    $custom_field = stripslashes($custom_field);
    $custom_field = preg_replace( '/width="([0-9]*)"/' , $width , $custom_field );
    $custom_field = preg_replace( '/height="([0-9]*)"/' , $height , $custom_field );

    // Set values: width:XXXpx, height:XXXpx
    if ( !$custom_width ) $width = 'width:'.$org_width.'px'; else $width = 'width:'.$custom_width.'px';
    if (  $height == '' ) { $height = 'height:'.$calculated_height.'px'; } else { if ( !$custom_height ) { $height = 'height:'.$org_height.'px'; } else { $height = 'height:'.$custom_height.'px'; } }
    $custom_field = stripslashes($custom_field);
    $custom_field = preg_replace( '/width:([0-9]*)px/' , $width , $custom_field );
    $custom_field = preg_replace( '/height:([0-9]*)px/' , $height , $custom_field );

	// Suckerfish menu hack
	$custom_field = str_replace( '<embed ','<param name="wmode" value="transparent"></param><embed wmode="transparent" ',$custom_field);

	$output = '';
    $output .= '<div class="'. $class .'">' . $custom_field . '</div>';

    return do_shortcode($output);

else :

	return false;

endif;

}
}

/*-----------------------------------------------------------------------------------*/
/* Get page ID */
/*-----------------------------------------------------------------------------------*/
function get_page_id($page_slug){
	$page_id = get_page_by_path($page_slug);
    if ($page_id) {
        return $page_id->ID;
    } else {
        return null;
    }

}

/*-----------------------------------------------------------------------------------*/
/* Tidy up the image source url */
/*-----------------------------------------------------------------------------------*/
function cleanSource($src) {

	// remove slash from start of string
	if(strpos($src, "/") == 0) {
		$src = substr($src, -(strlen($src) - 1));
	}

	// Check if same domain so it doesn't strip external sites
	$host = str_replace( 'www.', '', $_SERVER['HTTP_HOST']);
	if ( !strpos($src,$host) )
		return $src;


	$regex = "/^((ht|f)tp(s|):\/\/)(www\.|)" . $host . "/i";
	$src = preg_replace ($regex, '', $src);
	$src = htmlentities ($src);

    // remove slash from start of string
    if (strpos($src, '/') === 0) {
        $src = substr ($src, -(strlen($src) - 1));
    }

	return $src;
}

?>