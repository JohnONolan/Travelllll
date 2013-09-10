<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Custom fields for WP write panel - woothemes_metabox_create
- woothemes_uploader_custom_fields
- woothemes_metabox_handle
- woothemes_metabox_add
- woothemes_metabox_header

-----------------------------------------------------------------------------------*/



/*-----------------------------------------------------------------------------------*/
// Custom fields for WP write panel
/*-----------------------------------------------------------------------------------*/

function woothemes_metabox_create($post,$callback) {
    global $post;

    $template_to_show = $callback['args'];

    $woo_metaboxes = get_option( 'woo_custom_template' );

    $output = '';
    $output .= '<table class="woo_metaboxes_table">'."\n";
    foreach ($woo_metaboxes as $woo_metabox) {
    	$woo_id = "woothemes_" . $woo_metabox["name"];
    	$woo_name = $woo_metabox["name"];

    	if (function_exists( 'woothemes_content_builder_menu')) {
    		$metabox_post_type_restriction = $woo_metabox['cpt'][$post->post_type];
    	} else {
    		$metabox_post_type_restriction = 'undefined';
    	}

    	if ( ($metabox_post_type_restriction != '') && ($metabox_post_type_restriction == 'true') ) {
    		$type_selector = true;
    	} elseif ($metabox_post_type_restriction == 'undefined') {
    		$type_selector = true;
    	} else {
    		$type_selector = false;
    	}

   		$woo_metaboxvalue = '';

    	if ($type_selector) {

    		if(
        	        $woo_metabox['type'] == 'text'
			OR      $woo_metabox['type'] == 'select'
			OR      $woo_metabox['type'] == 'select2'
			OR      $woo_metabox['type'] == 'checkbox'
			OR      $woo_metabox['type'] == 'textarea'
			OR      $woo_metabox['type'] == 'calendar'
			OR      $woo_metabox['type'] == 'time'
			OR      $woo_metabox['type'] == 'radio'
			OR      $woo_metabox['type'] == 'images') {

        	    	$woo_metaboxvalue = get_post_meta($post->ID,$woo_name,true);

				}

        	    if ( $woo_metaboxvalue == '' && isset( $woo_metabox['std'] ) ) {

        	        $woo_metaboxvalue = $woo_metabox['std'];
        	    }
        	    
				if($woo_metabox['type'] == 'info'){

        	        $output .= "\t".'<tr style="background:#f8f8f8; font-size:11px; line-height:1.5em;">';
        	        $output .= "\t\t".'<th class="woo_metabox_names"><label for="'. esc_attr( $woo_id ) .'">'.$woo_metabox['label'].'</label></th>'."\n";
        	        $output .= "\t\t".'<td style="font-size:11px;">'.$woo_metabox['desc'].'</td>'."\n";
        	        $output .= "\t".'</tr>'."\n";

        	    }
        	    elseif($woo_metabox['type'] == 'text'){

        	    	$add_class = ''; $add_counter = '';
        	        $output .= "\t".'<tr>';
        	        $output .= "\t\t".'<th class="woo_metabox_names"><label for="'.esc_attr( $woo_id ).'">'.$woo_metabox['label'].'</label></th>'."\n";
        	        $output .= "\t\t".'<td><input class="woo_input_text '.$add_class.'" type="'.$woo_metabox['type'].'" value="'.esc_attr( $woo_metaboxvalue ).'" name="'.$woo_name.'" id="'.esc_attr( $woo_id ).'"/>';
        	        $output .= '<span class="woo_metabox_desc">'.$woo_metabox['desc'] .' '. $add_counter .'</span></td>'."\n";
        	        $output .= "\t".'</tr>'."\n";

        	    }

        	    elseif ($woo_metabox['type'] == 'textarea'){

        	   		$add_class = ''; $add_counter = '';
        	        $output .= "\t".'<tr>';
        	        $output .= "\t\t".'<th class="woo_metabox_names"><label for="'.$woo_metabox.'">'.$woo_metabox['label'].'</label></th>'."\n";
        	        $output .= "\t\t".'<td><textarea class="woo_input_textarea '.$add_class.'" name="'.$woo_name.'" id="'.esc_attr( $woo_id ).'">' . esc_textarea(stripslashes($woo_metaboxvalue)) . '</textarea>';
        	        $output .= '<span class="woo_metabox_desc">'.$woo_metabox['desc'] .' '. $add_counter.'</span></td>'."\n";
        	        $output .= "\t".'</tr>'."\n";

        	    }

        	    elseif ($woo_metabox['type'] == 'calendar'){

        	        $output .= "\t".'<tr>';
        	        $output .= "\t\t".'<th class="woo_metabox_names"><label for="'.$woo_metabox.'">'.$woo_metabox['label'].'</label></th>'."\n";
        	        $output .= "\t\t".'<td><input class="woo_input_calendar" type="text" name="'.$woo_name.'" id="'.esc_attr( $woo_id ).'" value="'.esc_attr( $woo_metaboxvalue ).'">';
        	        $output .= '<span class="woo_metabox_desc">'.$woo_metabox['desc'].'</span></td>'."\n";
        	        $output .= "\t".'</tr>'."\n";

        	    }

        	    elseif ($woo_metabox['type'] == 'time'){

        	        $output .= "\t".'<tr>';
        	        $output .= "\t\t".'<th class="woo_metabox_names"><label for="'.esc_attr( $woo_id ).'">'.$woo_metabox['label'].'</label></th>'."\n";
        	        $output .= "\t\t".'<td><input class="woo_input_time" type="'.$woo_metabox['type'].'" value="'.esc_attr( $woo_metaboxvalue ).'" name="'.$woo_name.'" id="'.esc_attr( $woo_id ).'"/>';
        	        $output .= '<span class="woo_metabox_desc">'.$woo_metabox['desc'].'</span></td>'."\n";
        	        $output .= "\t".'</tr>'."\n";

        	    }

        	    elseif ($woo_metabox['type'] == 'select'){

        	        $output .= "\t".'<tr>';
        	        $output .= "\t\t".'<th class="woo_metabox_names"><label for="'.esc_attr( $woo_id ).'">'.$woo_metabox['label'].'</label></th>'."\n";
        	        $output .= "\t\t".'<td><select class="woo_input_select" id="'.esc_attr( $woo_id ).'" name="'. esc_attr( $woo_name ) .'">';
        	        $output .= '<option value="">Select to return to default</option>';

        	        $array = $woo_metabox['options'];

        	        if($array){

        	            foreach ( $array as $id => $option ) {
        	                $selected = '';

        	                if(isset($woo_metabox['default']))  {
								if($woo_metabox['default'] == $option && empty($woo_metaboxvalue)){$selected = 'selected="selected"';}
								else  {$selected = '';}
							}

        	                if($woo_metaboxvalue == $option){$selected = 'selected="selected"';}
        	                else  {$selected = '';}

        	                $output .= '<option value="'. esc_attr( $option ) .'" '. $selected .'>' . $option .'</option>';
        	            }
        	        }

        	        $output .= '</select><span class="woo_metabox_desc">'.$woo_metabox['desc'].'</span></td>'."\n";
        	        $output .= "\t".'</tr>'."\n";
        	    }
        	    elseif ($woo_metabox['type'] == 'select2'){

        	        $output .= "\t".'<tr>';
        	        $output .= "\t\t".'<th class="woo_metabox_names"><label for="'.esc_attr( $woo_id ).'">'.$woo_metabox['label'].'</label></th>'."\n";
        	        $output .= "\t\t".'<td><select class="woo_input_select" id="'.esc_attr( $woo_id ).'" name="'. esc_attr( $woo_name ) .'">';
        	        $output .= '<option value="">Select to return to default</option>';

        	        $array = $woo_metabox['options'];

        	        if($array){

        	            foreach ( $array as $id => $option ) {
        	                $selected = '';

        	                if(isset($woo_metabox['default']))  {
								if($woo_metabox['default'] == $id && empty($woo_metaboxvalue)){$selected = 'selected="selected"';}
								else  {$selected = '';}
							}

        	                if($woo_metaboxvalue == $id){$selected = 'selected="selected"';}
        	                else  {$selected = '';}

        	                $output .= '<option value="'. esc_attr( $id ) .'" '. $selected .'>' . $option .'</option>';
        	            }
        	        }

        	        $output .= '</select><span class="woo_metabox_desc">'.$woo_metabox['desc'].'</span></td>'."\n";
        	        $output .= "\t".'</tr>'."\n";
        	    }

        	    elseif ($woo_metabox['type'] == 'checkbox'){

        	        if($woo_metaboxvalue == 'true') { $checked = ' checked="checked"';} else {$checked='';}

        	        $output .= "\t".'<tr>';
        	        $output .= "\t\t".'<th class="woo_metabox_names"><label for="'.esc_attr( $woo_id ).'">'.$woo_metabox['label'].'</label></th>'."\n";
        	        $output .= "\t\t".'<td><input type="checkbox" '.$checked.' class="woo_input_checkbox" value="true"  id="'.esc_attr( $woo_id ).'" name="'. esc_attr( $woo_name ) .'" />';
        	        $output .= '<span class="woo_metabox_desc" style="display:inline">'.$woo_metabox['desc'].'</span></td>'."\n";
        	        $output .= "\t".'</tr>'."\n";
        	    }

        	    elseif ($woo_metabox['type'] == 'radio'){

        	    $array = $woo_metabox['options'];

        	    if($array){

        	    $output .= "\t".'<tr>';
        	    $output .= "\t\t".'<th class="woo_metabox_names"><label for="'.esc_attr( $woo_id ).'">'.$woo_metabox['label'].'</label></th>'."\n";
        	    $output .= "\t\t".'<td>';

        	        foreach ( $array as $id => $option ) {

        	            if($woo_metaboxvalue == $id) { $checked = ' checked';} else {$checked='';}

        	                $output .= '<input type="radio" '.$checked.' value="' . $id . '" class="woo_input_radio"  name="'. esc_attr( $woo_name ) .'" />';
        	                $output .= '<span class="woo_input_radio_desc" style="display:inline">'. $option .'</span><div class="woo_spacer"></div>';
        	            }
        	            $output .= "\t".'</tr>'."\n";
        	         }
        	    }
				elseif ($woo_metabox['type'] == 'images')
				{

				$i = 0;
				$select_value = '';
				$layout = '';

				foreach ($woo_metabox['options'] as $key => $option)
					 {
					 $i++;

					 $checked = '';
					 $selected = '';
					 if($woo_metaboxvalue != '') {
					 	if ($woo_metaboxvalue == $key) { $checked = ' checked'; $selected = 'woo-meta-radio-img-selected'; }
					 }
					 else {
					 	if ($option['std'] == $key) { $checked = ' checked'; }
						elseif ($i == 1) { $checked = ' checked'; $selected = 'woo-meta-radio-img-selected'; }
						else { $checked=''; }

					 }

						$layout .= '<div class="woo-meta-radio-img-label">';
						$layout .= '<input type="radio" id="woo-meta-radio-img-' . $woo_name . $i . '" class="checkbox woo-meta-radio-img-radio" value="'.esc_attr($key).'" name="'. $woo_name.'" '.$checked.' />';
						$layout .= '&nbsp;' . esc_html($key) .'<div class="woo_spacer"></div></div>';
						$layout .= '<img src="'.esc_url( $option ).'" alt="" class="woo-meta-radio-img-img '. $selected .'" onClick="document.getElementById(\'woo-meta-radio-img-'. esc_js($woo_metabox["name"] . $i).'\').checked = true;" />';
					}

				$output .= "\t".'<tr>';
				$output .= "\t\t".'<th class="woo_metabox_names"><label for="'.esc_attr( $woo_id ).'">'.$woo_metabox['label'].'</label></th>'."\n";
				$output .= "\t\t".'<td class="woo_metabox_fields">';
				$output .= $layout;
				$output .= '<span class="woo_metabox_desc">'.$woo_metabox['desc'].'</span></td>'."\n";
        	    $output .= "\t".'</tr>'."\n";

				}

        	    elseif($woo_metabox['type'] == 'upload')
        	    {
					if(isset($woo_metabox["default"])) $default = $woo_metabox["default"];
					else $default = '';

        	    	// Add support for the WooThemes Media Library-driven Uploader Module // 2010-11-09.
        	    	if ( function_exists( 'woothemes_medialibrary_uploader' ) ) {

        	    		$_value = $default;

        	    		$_value = get_post_meta( $post->ID, $woo_metabox["name"], true );

        	    		$output .= "\t".'<tr>';
	    	            $output .= "\t\t".'<th class="woo_metabox_names"><label for="'.$woo_metabox["name"].'">'.$woo_metabox['label'].'</label></th>'."\n";
	    	            $output .= "\t\t".'<td class="woo_metabox_fields">'. woothemes_medialibrary_uploader( $woo_metabox["name"], $_value, 'postmeta', $woo_metabox["desc"], $post->ID );
	    	            $output .= '</td>'."\n";
	    	            $output .= "\t".'</tr>'."\n";

        	    	} else {

	    	            $output .= "\t".'<tr>';
	    	            $output .= "\t\t".'<th class="woo_metabox_names"><label for="'.esc_attr( $woo_id ).'">'.$woo_metabox['label'].'</label></th>'."\n";
	    	            $output .= "\t\t".'<td class="woo_metabox_fields">'. woothemes_uploader_custom_fields($post->ID,$woo_name,$default,$woo_metabox["desc"]);
	    	            $output .= '</td>'."\n";
	    	            $output .= "\t".'</tr>'."\n";

        	        } // End IF Statement

        	    }
        }	// End IF Statement
    }

    $output .= '</table>'."\n\n";
    echo $output;
}



/*-----------------------------------------------------------------------------------*/
// woothemes_uploader_custom_fields
/*-----------------------------------------------------------------------------------*/

function woothemes_uploader_custom_fields($pID,$id,$std,$desc){

    // Start Uploader
    $upload = get_post_meta( $pID, $id, true);
	$href = cleanSource($upload);
	$uploader = '';
    $uploader .= '<input class="woo_input_text" name="'.$id.'" type="text" value="'.esc_attr($upload).'" />';
    $uploader .= '<div class="clear"></div>'."\n";
    $uploader .= '<input type="file" name="attachement_'.$id.'" />';
    $uploader .= '<input type="submit" class="button button-highlighted" value="Save" name="save"/>';
    if ( $href )
		$uploader .= '<span class="woo_metabox_desc">'.$desc.'</span></td>'."\n".'<td class="woo_metabox_image"><a href="'. $upload .'"><img src="'.get_template_directory_uri().'/thumb.php?src='.$href.'&w=150&h=80&zc=1" alt="" /></a>';

return $uploader;
}



/*-----------------------------------------------------------------------------------*/
// woothemes_metabox_handle
/*-----------------------------------------------------------------------------------*/

function woothemes_metabox_handle(){

    $pID = '';
    global $globals, $post;

    $woo_metaboxes = get_option( 'woo_custom_template' );

    // Sanitize post ID.

    if( isset( $_POST['post_ID'] ) ) {

		$pID = intval( $_POST['post_ID'] );

    } // End IF Statement

    // Don't continue if we don't have a valid post ID.

    if ( $pID == 0 ) {

    	return;

    } // End IF Statement

    $upload_tracking = array();

    if ( isset( $_POST['action'] ) && $_POST['action'] == 'editpost' ) {

        foreach ($woo_metaboxes as $woo_metabox) { // On Save.. this gets looped in the header response and saves the values submitted
            if($woo_metabox['type'] == 'text'
			OR $woo_metabox['type'] == 'calendar'
			OR $woo_metabox['type'] == 'time'
			OR $woo_metabox['type'] == 'select'
			OR $woo_metabox['type'] == 'select2'
			OR $woo_metabox['type'] == 'radio'
			OR $woo_metabox['type'] == 'checkbox'
			OR $woo_metabox['type'] == 'textarea'
			OR $woo_metabox['type'] == 'images' ) // Normal Type Things...
            {

				$var = $woo_metabox["name"];

				if ( isset( $_POST[$var] ) ) {

					// Sanitize the input.
					$posted_value = '';
					$posted_value = $_POST[$var];

				    // Get the current value for checking in the script.
				    $current_value = '';
				    $current_value = get_post_meta( $pID, $var, true );

					 // If it doesn't exist, add the post meta.
					if(get_post_meta( $pID, $var ) == "") {

						add_post_meta( $pID, $var, $posted_value, true );

					}
					// Otherwise, if it's different, update the post meta.
					elseif( $posted_value != get_post_meta( $pID, $var, true ) ) {

						update_post_meta( $pID, $var, $posted_value );

					}
					// Otherwise, if no value is set, delete the post meta.
					elseif($posted_value == "") {

						delete_post_meta( $pID, $var, get_post_meta( $pID, $var, true ) );

					} // End IF Statement

					/*
				    // If it doesn't exist, add the post meta.
					if ( $current_value == "" && $posted_value != '' ) {

						update_post_meta( $pID, $var, $posted_value );

					// Otherwise, if it's different, update the post meta.
					} elseif ( ( $posted_value != '' ) && ( $posted_value != $current_value ) ) {

						update_post_meta( $pID, $var, $posted_value );

					// Otherwise, if no value is set, delete the post meta.
					} elseif ( $posted_value == "" && $current_value != '' ) {

						delete_post_meta($pID, $var, $current_value );

					} // End IF Statement
					*/

				} elseif ( ! isset( $_POST[$var] ) && $woo_metabox['type'] == 'checkbox' ) {

					update_post_meta( $pID, $var, 'false' );

				} else {

					delete_post_meta( $pID, $var, $current_value ); // Deletes check boxes OR no $_POST

				} // End IF Statement

            } elseif( $woo_metabox['type'] == 'upload' ) { // So, the upload inputs will do this rather

				$id = $woo_metabox['name'];
				$override['action'] = 'editpost';

			    if(!empty($_FILES['attachement_'.$id]['name'])){ //New upload
			    $_FILES['attachement_'.$id]['name'] = preg_replace( '/[^a-zA-Z0-9._\-]/', '', $_FILES['attachement_'.$id]['name']);
			           $uploaded_file = wp_handle_upload($_FILES['attachement_' . $id ],$override);
			           $uploaded_file['option_name']  = $woo_metabox['label'];
			           $upload_tracking[] = $uploaded_file;
			           update_post_meta( $pID, $id, $uploaded_file['url'] );

			    } elseif ( empty( $_FILES['attachement_'.$id]['name'] ) && isset( $_POST[ $id ] ) ) {

			       	// Sanitize the input.
					$posted_value = '';
					$posted_value = $_POST[$id];

			        update_post_meta($pID, $id, $posted_value);

			    } elseif ( $_POST[ $id ] == '' )  {

			    	delete_post_meta( $pID, $id, get_post_meta( $pID, $id, true ) );

			    } // End IF Statement

			} // End IF Statement

               // Error Tracking - File upload was not an Image
               update_option( 'woo_custom_upload_tracking', $upload_tracking );

            } // End FOREACH Loop

        } // End IF Statement

} // End woothemes_metabox_handle()

/*-----------------------------------------------------------------------------------*/
// woothemes_metabox_add
/*-----------------------------------------------------------------------------------*/

function woothemes_metabox_add() {

	$woo_metaboxes = get_option( 'woo_custom_template' );

    if ( function_exists( 'add_meta_box') ) {

    	if ( function_exists( 'get_post_types') ) {
    		$custom_post_list = get_post_types();

    		// Get the theme name for use in multiple meta boxes.
    		$theme_name = get_option( 'woo_themename' );

			foreach ($custom_post_list as $type){

				$settings = array(
									'id' => 'woothemes-settings',
									'title' => $theme_name . __( ' Custom Settings', 'woothemes' ),
									'callback' => 'woothemes_metabox_create',
									'page' => $type,
									'priority' => 'normal',
									'callback_args' => ''
								);

				// Allow child themes/plugins to filter these settings.
				$settings = apply_filters( 'woothemes_metabox_settings', $settings, $type, $settings['id'] );

				if ( ! empty( $woo_metaboxes ) ) {
					add_meta_box( $settings['id'], $settings['title'], $settings['callback'], $settings['page'], $settings['priority'], $settings['callback_args'] );
				}

				//if(!empty($woo_metaboxes)) Temporarily Removed

			}
    	} else {
    		add_meta_box( 'woothemes-settings', $theme_name . ' Custom Settings','woothemes_metabox_create','post','normal' );
        	add_meta_box( 'woothemes-settings', $theme_name . ' Custom Settings','woothemes_metabox_create','page','normal' );
    	}

    }
}

/*-----------------------------------------------------------------------------------*/
// woothemes_metabox_header
/*-----------------------------------------------------------------------------------*/

function woothemes_metabox_header(){
?>
<script type="text/javascript">

    jQuery(document).ready(function(){

        jQuery( 'form#post').attr( 'enctype','multipart/form-data' );
        jQuery( 'form#post').attr( 'encoding','multipart/form-data' );

         //JQUERY DATEPICKER
		jQuery( '.woo_input_calendar').each(function (){
			jQuery( '#' + jQuery(this).attr( 'id')).datepicker({showOn: 'button', buttonImage: '<?php echo get_template_directory_uri(); ?>/library/functions/images/calendar.gif', buttonImageOnly: true});
		});

		//JQUERY TIME INPUT MASK
		jQuery( '.woo_input_time').each(function (){
			jQuery( '#' + jQuery(this).attr( 'id')).mask( "99:99" );
		});

		//JQUERY CHARACTER COUNTER
		jQuery( '.words-count').each(function(){
			var s = ''; var s2 = '';
		    var length = jQuery(this).val().length;
		    var w_length = jQuery(this).val().split(/\b[\s,\.-:;]*/).length;
			
		    if(length != 1) { s = 's';}
		    if(w_length != 1){ s2 = 's';}
		    if(jQuery(this).val() == ''){ s2 = 's'; w_length = '0';}

		    jQuery(this).parent().find( '.counter').html( length + ' character'+ s + ', ' + w_length + ' word' + s2);

		    jQuery(this).keyup(function(){
		    var s = ''; var s2 = '';
		        var new_length = jQuery(this).val().length;
		        var word_length = jQuery(this).val().split(/\b[\s,\.-:;]*/).length;

		        if(new_length != 1) { s = 's';}
		        if(word_length != 1){ s2 = 's'}
		        if(jQuery(this).val() == ''){ s2 == 's'; word_length = '0';}

		        jQuery(this).parent().find( '.counter').html( new_length + ' character' + s + ', ' + word_length + ' word' + s2);
		    });
		});

        jQuery( '.woo_metaboxes_table th:last, .woo_metaboxes_table td:last').css( 'border','0' );
        var val = jQuery( 'input#title').attr( 'value' );
        if(val == ''){
        jQuery( '.woo_metabox_fields .button-highlighted').after( "<em class='woo_red_note'>Please add a Title before uploading a file</em>" );
        };
		jQuery( '.woo-meta-radio-img-img').click(function(){
				jQuery(this).parent().find( '.woo-meta-radio-img-img').removeClass( 'woo-meta-radio-img-selected' );
				jQuery(this).addClass( 'woo-meta-radio-img-selected' );

			});
			jQuery( '.woo-meta-radio-img-label').hide();
			jQuery( '.woo-meta-radio-img-img').show();
			jQuery( '.woo-meta-radio-img-radio').hide();
        <?php //Errors
        $error_occurred = false;
        $upload_tracking = get_option( 'woo_custom_upload_tracking' );
        if(!empty($upload_tracking)){
        $output = '<div style="clear:both;height:20px;"></div><div class="errors"><ul>' . "\n";
            $error_shown == false;
            foreach($upload_tracking as $array )
            {
                 if(array_key_exists( 'error', $array)){
                        $error_occurred = true;
                        ?>
                        jQuery( 'form#post').before( '<div class="updated fade"><p>WooThemes Upload Error: <strong><?php echo $array['option_name'] ?></strong> - <?php echo $array['error'] ?></p></div>' );
                        <?php
                }
            }
        }

        delete_option( 'woo_upload_custom_errors' );
        ?>
    });

</script>
<style type="text/css">
.woo_input_text { margin:0 0 10px 0; background:#f4f4f4; color:#444; width:80%; font-size:11px; padding: 5px;}
.woo_input_select { margin:0 0 10px 0; background:#f4f4f4; color:#444; width:60%; font-size:11px; padding: 5px;}
.woo_input_checkbox { margin:0 10px 0 0; }
.woo_input_radio { margin:0 10px 0 0; }
.woo_input_radio_desc { font-size: 12px; color: #666 ; }
.woo_input_calendar { margin:0 0 10px 0; }
.woo_spacer { display: block; height:5px}
.woo_metabox_desc { font-size:10px; color:#aaa; display:block}
.woo_metaboxes_table{ border-collapse:collapse; width:100%}
.woo_metaboxes_table th,
.woo_metaboxes_table td{ border-bottom:1px solid #ddd; padding:10px 10px;text-align: left; vertical-align:top}
.woo_metabox_names { width:20%}
.woo_metabox_fields { width:70%}
.woo_metabox_image { text-align: right;}
.woo_red_note { margin-left: 5px; color: #c77; font-size: 10px;}
.woo_input_textarea { width:80%; height:120px;margin:0 0 10px 0; background:#f0f0f0; color:#444;font-size:11px;padding: 5px;}
.woo-meta-radio-img-img { border:3px solid #dedede; margin:0 5px 10px 0; display:none; cursor:pointer; border-radius: 3px; -moz-border-radius: 3px; -webkit-border-radius: 3px;}
.woo-meta-radio-img-img:hover, .woo-meta-radio-img-selected { border:3px solid #aaa; border-radius: 3px; -moz-border-radius: 3px; -webkit-border-radius: 3px; }
.woo-meta-radio-img-label { font-size:12px}
.woo_metabox_desc span.counter { color:green!important }
.woo_metabox_fields .controls input.upload { width:280px; padding-bottom:6px; }
.woo_metabox_fields .controls input.upload_button{ float:right; width:auto; border-color:#BBBBBB; cursor:pointer; height:16px; }
.woo_metabox_fields .controls input.upload_button:hover { width:auto; border-color:#666666; color:#000; }
.woo_metabox_fields .screenshot{margin:10px 0;float:left;margin-left:1px;position:relative;width:344px;}
.woo_metabox_fields .screenshot img{-moz-border-radius:4px;-webkit-border-radius:4px;-border-radius:4px;background:#FAFAFA;float:left;max-width:334px;border-color:#CCC #EEE #EEE #CCC;border-style:solid;border-width:1px;padding:4px;}
.woo_metabox_fields .screenshot .mlu_remove{background:url( "<?php echo get_template_directory_uri(); ?>/library/functions/images/ico-delete.png") no-repeat scroll 0 0 transparent;border:medium none;bottom:-4px;display:block;float:left;height:16px;position:absolute;left:-4px;text-indent:-9999px;width:16px;padding:0;}
.woo_metabox_fields .upload {background:none repeat scroll 0 0 #F4F4F4;color:#444444;font-size:11px;margin:0 0 10px;padding:5px;width:70%;}
.woo_metabox_fields .upload_button {-moz-border-radius:4px; -webkit-border-radius:4px;-border-radius:4px;}
.woo_metabox_fields .screenshot .no_image .file_link {margin-left: 20px;}
.woo_metabox_fields .screenshot .no_image .mlu_remove {bottom: 0px;}
</style>
<?php
 echo '<link rel="stylesheet" type="text/css" href="' . get_template_directory_uri() . '/library/functions/css/jquery-ui-datepicker.css" />';
}


function woo_custom_enqueue($hook) {
  	if ($hook == 'post.php' OR $hook == 'post-new.php' OR $hook == 'page-new.php' OR $hook == 'page.php') {
		add_action( 'admin_head', 'woothemes_metabox_header' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_register_script( 'jquery-ui-datepicker', get_template_directory_uri() . '/library/functions/js/ui.datepicker.js', array( 'jquery-ui-core' ));
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_register_script( 'jquery-input-mask', get_template_directory_uri() . '/library/functions/js/jquery.maskedinput-1.2.2.js', array( 'jquery' ));
		wp_enqueue_script( 'jquery-input-mask' );
  	}
}

add_action( 'admin_enqueue_scripts', 'woo_custom_enqueue', 10, 1 );
add_action( 'edit_post', 'woothemes_metabox_handle' );
add_action( 'admin_menu', 'woothemes_metabox_add' ); // Triggers Woothemes_metabox_create
?>