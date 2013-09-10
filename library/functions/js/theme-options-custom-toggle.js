/*-------------------------------------------------------------------------------------

FILE INFORMATION

Description: Custom toggle logic for "Theme Options".
Date Created: 2011-06-27.
Author: Cobus, Matty.
Since: 1.0.0


TABLE OF CONTENTS

- Logic for toggling of main theme options.
- Logic for toggling of "headlines" section theme options.

-------------------------------------------------------------------------------------*/

jQuery(document).ready(function(){

/*-----------------------------------------------------------------------------------*/
/* Logic for toggling of main theme options. */
/*-----------------------------------------------------------------------------------*/

	var showValue = 'alignleft';
	var elementName = 'woo_header_align';
	var toggleElements = 'input[name="woo_header_left_layout"]';
	var hiddenElements = '';
	var headlinesOptions = 'input[name="woo_header_left_headlines_tag"], select[name="woo_header_left_headlines_number"]';
	
	if ( jQuery( 'input[name="woo_header_left_layout"]:checked' ).val() == 'headlines' ) {
		toggleElements += ', input[name="woo_header_left_headlines_tag"], select[name="woo_header_left_headlines_number"]';
	} else {
		hiddenElements = headlinesOptions;
	}
	
	// Hide elements to be hidden.
	jQuery( hiddenElements ).parents( '.section' ).hide();
	
	// Toggle the main elements on load.
	jQuery( 'input[name="' + elementName + '"]:checked' ).each( function () {
		
		if ( jQuery( this ).val() == showValue ) {
			jQuery( toggleElements ).parents( '.section' ).show();
		} else {
			jQuery( toggleElements ).parents( '.section' ).hide();
		}
		
	});
	
	// Toggle the main elements on click.
	jQuery( 'input[name="' + elementName + '"]' ).click( function () {
		
		if ( jQuery( this ).val() == showValue ) {
			jQuery( toggleElements ).parents( '.section' ).show();
			
			if ( jQuery( 'input[name="woo_header_left_layout"]:checked' ).val() == 'headlines' ) {
				jQuery( headlinesOptions ).parents( '.section' ).show();
			} else {
				jQuery( headlinesOptions ).parents( '.section' ).hide();
			}
		} else {
			jQuery( toggleElements ).parents( '.section' ).hide();
		}
		
	});
	
	// Toggle the headlines options on click.
	jQuery( 'input[name="' + 'woo_header_left_layout' + '"]' ).click( function () {
		
		if ( jQuery( this ).val() == 'headlines' && jQuery( 'input[name="woo_header_align"]:checked' ).val() == 'alignleft' ) {
			jQuery( headlinesOptions ).parents( '.section' ).show();
		} else {
			jQuery( headlinesOptions ).parents( '.section' ).hide();
		}
		
	});
	
/*-----------------------------------------------------------------------------------*/
/* Logic for toggling of "headlines" section theme options. */
/*-----------------------------------------------------------------------------------*/

}); // End jQuery()

/*-----------------------------------------------------------------------------------*/
/* function - woo_toggle_specified_theme_options() */
/*-----------------------------------------------------------------------------------*/

function woo_toggle_specified_theme_options ( elementName, showValue ) {} // End woo_toggle_specified_theme_options()