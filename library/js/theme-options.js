/*-------------------------------------------------------------------------------------

FILE INFORMATION

Description: Theme-specific JavaScript calls.
Date Created: 2011-06-22.
Author: Cobus, Matty.
Since: 1.0.0


TABLE OF CONTENTS

- Setup options tabber.
- Add classes, as woo_metabox_create() doesn't cater for classes at this time.
- Logic for toggling theme options.

-------------------------------------------------------------------------------------*/

jQuery(document).ready(function(){

/*-----------------------------------------------------------------------------------*/
/* Setup options tabber. */
/*-----------------------------------------------------------------------------------*/

	// Add our tabs UL, to be filled presently.
	jQuery( '#woothemes-settings .woo_metaboxes_table' ).before( '<ul id="woothemes-settings-tabs"></ul>' );
	
	// Add ID to main meta box table, to avoid selector conflicts below.
	jQuery( '#woothemes-settings .woo_metaboxes_table' ).attr( 'id', 'woo_metaboxes_table_main' );
	
	// Detect the headings and work with them.
	jQuery( '#woothemes-settings label[for$="_heading"]' ).each( function () {
	
		var parentRow = jQuery( this ).parents( 'tr' );
		
		// Add classes in preparation for the split.
		parentRow.addClass( 'heading' );
		
		// Create DIV tags for each section.
		var sectionId = jQuery( this ).attr( 'for' );
		sectionId = sectionId.replace( '_heading', '_section' );
		
		parentRow.attr( 'section', sectionId );
		
		jQuery( '#woothemes-settings #woo_metaboxes_table_main' ).before( '<div id="' + sectionId + '"><table class="woo_metaboxes_table"><tbody></tbody></table></div>' );
		
		jQuery( '#woothemes-settings #woothemes-settings-tabs' ).append( '<li><a href="#' + sectionId + '">' + jQuery( this ).text() + '</a></li>' );

	});
	
	// Detect the headings and work with them.
	jQuery( '#woothemes-settings .heading' ).each( function () {
	
		var rowObj = jQuery( this );

		// Begin splitting the rows into the various sections.
		rowObj.nextUntil( '.heading' ).each( function () {
			var sectionId = rowObj.attr( 'section' );
			
			jQuery( 'div#' + sectionId + ' table tbody' ).append( jQuery( this ) );
		});

	});
	
	// Remove the main table, as it's no longer needed.
	jQuery( '#woo_metaboxes_table_main' ).remove();
	
	// After all the DOM manipulation, we set up our jQueryUI tabs. :)
	
	jQuery( '#woothemes-settings-tabs' ).parents( '.inside' ).tabs();

/*-----------------------------------------------------------------------------------*/
/* Add classes, as woo_metabox_create() doesn't cater for classes at this time. */
/*-----------------------------------------------------------------------------------*/

jQuery( '#woothemes__enable_gallery' ).parents( 'tr' ).addClass( 'collapsed' );

jQuery( '#woothemes__gallery_position' ).parents( 'tr' ).addClass( 'hidden' );

jQuery( '#woothemes__gallery_position' ).parents( 'tr' ).addClass( 'last' );

/*-----------------------------------------------------------------------------------*/
/* Logic for toggling theme options. */
/*-----------------------------------------------------------------------------------*/

	jQuery( '.woo_metaboxes_table .collapsed' ).each( function(){
		jQuery(this).find( 'input:checked' ).parents( 'tr' ).nextAll().each(
		function(){
				if ( jQuery(this).hasClass( 'last' ) ) {
					jQuery(this).removeClass( 'hidden' );
					return false;
				}
				jQuery(this).filter( '.hidden' ).removeClass( 'hidden' );
			});
	});
					
	jQuery( '.woo_metaboxes_table .collapsed input:checkbox' ).click( function () {
		
		if ( jQuery( this ).attr( 'checked' ) == true ) {
			jQuery( this ).parents( 'tr' ).nextAll().each( function () {
				jQuery( this ).removeClass( 'hidden' );
				return false;
			});
		} else {
			jQuery( this ).parents( 'tr' ).nextAll().each( function () {
				jQuery( this ).addClass( 'hidden' );
				return false;
			});			
		}
	});
	
	
/*-----------------------------------------------------------------------------------*/
/* Remove last table row borders from single page theme options box */
/*-----------------------------------------------------------------------------------*/
	
	jQuery('.ui-tabs-panel tr:last-child').each(function(){
		jQuery(this).addClass('last-row');
	});

}); // End jQuery()