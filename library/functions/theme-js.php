<?php

/*-----------------------------------------------------------------------------------*/
/* Theme Admin JavaScript */
/*-----------------------------------------------------------------------------------*/

if ( is_admin() ) { add_action( 'admin_print_scripts', 'woothemes_add_admin_javascript' ); }
if ( is_admin() ) { add_action( 'admin_print_styles', 'woothemes_add_admin_css' ); }

if ( ! function_exists( 'woothemes_add_admin_javascript' ) ) {
	function woothemes_add_admin_javascript() {
		global $pagenow;
		
		if ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) {
			wp_enqueue_script( 'woo-post-meta-options', get_template_directory_uri() . '/library/functions/js/theme-options.js', array( 'jquery', 'jquery-ui-tabs' ), '1.0.0' );
		}
		
		if ( $pagenow == 'admin.php' || get_query_var( 'page' ) == 'woothemes' ) {
			wp_enqueue_script( 'woo-theme-options-custom-toggle', get_template_directory_uri() . '/library/functions/js/theme-options-custom-toggle.js', array( 'jquery' ), '1.0.0' );
		}
		
	} // End woothemes_add_admin_javascript()
}

if ( ! function_exists( 'woothemes_add_admin_css' ) ) {
	function woothemes_add_admin_css() {
		wp_enqueue_style( 'woo-post-meta-options', get_template_directory_uri() . '/library/functions/css/meta-options.css' );
	} // End woothemes_add_admin_css()
}
?>