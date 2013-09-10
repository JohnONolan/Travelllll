<?php

/*-----------------------------------------------------------------------------------*/
/* Load the required Framework Files */
/*-----------------------------------------------------------------------------------*/

$functions_path = get_template_directory() . '/library/functions/';

require_once ( $functions_path . 'theme-options.php' );
require_once ( $functions_path . 'admin-functions.php' );				// Custom functions and plugins
require_once ( $functions_path . 'admin-custom.php' );					// Custom fields
require_once ( $functions_path . 'admin-medialibrary-uploader.php' ); 	// Framework Media Library Uploader Functions // 2010-11-05.
require_once ( $functions_path . 'admin-shortcodes.php' );				// Shortcodes
require_once ( $functions_path . 'admin-shortcode-generator.php' ); 	// Framework Shortcode generator // 2011-01-21.
require_once ( $functions_path . 'theme-js.php' );						// Tabbed theme options on edit post screen

?>