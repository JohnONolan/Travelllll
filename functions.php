<?php

// Include fucking awesome ninja stuff
//--------------------------------------------------------------------------------
require_once ( get_template_directory() . '/library/functions/admin-init.php' );
require_once ( get_template_directory() . '/departures/departure.php' );


// Enqueue Scripts / Remove Scripts
//--------------------------------------------------------------------------------
function t5_scripts() {
   wp_register_script(
   		'fitvid',
   		get_template_directory_uri() . '/library/js/fitvid.js',
       	array('jquery'),
       	'1.0'
       	);
   wp_enqueue_script('fitvid');
}
add_action('wp_enqueue_scripts', 't5_scripts');

if ( !is_admin() ) {
	function t5_cleanup() {
		wp_deregister_script( 'l10n' );
		wp_deregister_script( 'jquery-ui-core' );
		wp_deregister_script( 'jquery-ui-tabs' );
		wp_deregister_script( 'jquery-ui-widget' );
		wp_deregister_script( 'shortcodes' );
	}
	add_action( 'init', 't5_cleanup' ); 
}


// Set up menus
//--------------------------------------------------------------------------------
register_nav_menus( array(
	'secondary' => 'Top Navigation',
	'primary' => 'Main Navigation',
	'footer1' => 'Footer Column 1',
	'footer2' => 'Footer Column 2',
	'footer3' => 'Footer Column 3'
) );


// Set up widgets
//--------------------------------------------------------------------------------
register_sidebar( array(
	'name' => 'Main Sidebar',
	'id' => 'main-sidebar',
	'description' => 'Main sidebar widget area, used on all pages.',
	'before_widget' => '<div id="%1$s" class="widget-container %2$s"><section class="widget">',
	'after_widget' => '</section></div>',
	'before_title' => '<h3 class="widget-title bar"><span>',
	'after_title' => '</span></h3>'
) );


// Set up post thumbnails
//--------------------------------------------------------------------------------

add_theme_support('post-thumbnails');
add_image_size('trending', 360, 220, true);
add_image_size('loop', 642, 220, true);
add_image_size('type-photo', 642, 427, true);
add_image_size('grid', 306, 203, true);
add_image_size('thumb', 116, 80, true);
add_image_size('essay', 978, 978, false);

/*Custom fuction to output thumbnail caption*/
function t5_post_thumbnail_caption() {
	global $post;
	$thumb_id = get_post_thumbnail_id($post->id);
	$args = array(
		'post_type' => 'attachment',
		'post_status' => null,
		'post_parent' => $post->ID,
		'include'  => $thumb_id
	); 
	$thumbnail_image = get_posts($args);
	if ($thumbnail_image && isset($thumbnail_image[0]) && !empty($thumbnail_image[0]->post_excerpt)) { 
		echo "<figcaption>" . $thumbnail_image[0]->post_excerpt . "</figcaption>";
	}
}

/*Add thumbnails to RSS feeds*/
function t5_post_thumbnail_feeds($content) {
	global $post;
	if(has_post_thumbnail($post->ID)) {
		$content = '<div>' . get_the_post_thumbnail( $post->ID, 'type-photo' ) . '</div>' . $content;
	}
	return $content;
}
add_filter('the_excerpt_rss', 't5_post_thumbnail_feeds');
add_filter('the_content_feed', 't5_post_thumbnail_feeds');

/*Dear WordPress, stop adding fucking inline styles to galleries*/
add_filter('gallery_style', create_function('$a', 'return preg_replace("%<style type=\'text/css\'>(.*?)</style>%s", "", $a);'));


// Trending posts widget
//--------------------------------------------------------------------------------

register_widget('T5_Trending_Posts');
class T5_Trending_Posts extends WP_Widget {
	function T5_Trending_Posts() {
		parent::WP_Widget(false, $name = 'T5 Trending Posts');	
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php 
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
	}

	function widget($args, $instance) {
		extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        ?>
            <?php echo $before_widget; ?>
            <?php if ( $title ) echo $before_title . $title . $after_title; ?>
            <?php 
            function t5_filter_where_pop( $where = '' ) {
                $where .= " AND post_date > '" . date('Y-m-d', strtotime('-7 days')) . "'";
                return $where;
            }
            add_filter( 'posts_where', 't5_filter_where_pop' );
            $args = array( 
		        'posts_per_page' => 5, 
		        'orderby' => 'comment_count', 
		        'order' => 'DESC',
		        'ignore_sticky_posts' => 1
		    );
            $trending_posts = new WP_Query( $args ); 
            if( $trending_posts->have_posts() ) : ?>
            <ul id="t5-trending-posts">
                <?php 
                $i = 0; 
                $total_comments = 999;
                while( $trending_posts->have_posts() ) : $trending_posts->the_post(); 
                if($i == 0) $total_comments = get_comments_number();
                	if (get_comments_number() != 0) {
                		$width = ((get_comments_number()/$total_comments) * 100) -15;
                	}
                ?>
                <li class="comment-<?php echo $i; ?>" style="width:<?php echo ($width >= 55) ? $width : 55; ?>%">
                    <span class="counter"><?php echo $i+1; ?>.</span>
					<span class="title"><a href="<?php the_permalink(); ?>?utm_source=travelllll&utm_medium=widget&utm_campaign=trending"><?php the_title(); ?></a></span>
                    <?php comments_popup_link( '0', '1', '%', 'comments' ); ?>
                </li>
                <?php $i++; endwhile; ?>
            </ul>
            <?php endif; ?>
            <?php 
            remove_filter( 'posts_where', 't5_filter_where_pop' );
            echo $after_widget; 
            ?>
        <?php
	}
    
}


// Most popular posts widget
//--------------------------------------------------------------------------------

register_widget('T5_MostPopular_Posts');
class T5_MostPopular_Posts extends WP_Widget {
	function T5_MostPopular_Posts() {
		parent::WP_Widget(false, $name = 'T5 Most Popular Posts');	
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php 
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
	}

	function widget($args, $instance) {
		extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        ?>
            <?php echo $before_widget; ?>
            <?php if ( $title ) echo $before_title . $title . $after_title; ?>
            <?php 
            $args = array( 
		        'posts_per_page' => 5, 
		        'orderby' => 'comment_count', 
		        'order' => 'DESC',
		        'ignore_sticky_posts' => 1,
		        'tag__not_in' => '449'
		    );
            $t5popular_posts = new WP_Query( $args ); 
            if( $t5popular_posts->have_posts() ) : ?>
            <ul id="t5-mostpopular-posts">
                <?php while( $t5popular_posts->have_posts() ) : $t5popular_posts->the_post(); ?>
                <li>                	
					<div class="title">
						<a href="<?php the_permalink(); ?>?utm_source=travelllll&utm_medium=widget&utm_campaign=popular" rel="nofollow"><?php the_post_thumbnail( 'thumb' ); ?></a>
						<a href="<?php the_permalink(); ?>?utm_source=travelllll&utm_medium=widget&utm_campaign=popular"><?php the_title(); ?></a><a class="count" href="<?php comments_link(); ?>?utm_source=travelllll&utm_medium=widget&utm_campaign=popular" rel="nofollow"><?php comments_number('0', '1', '%'); ?> <span class="screen-reader-text">Comments</span></a>
						<div class="clearfix"></div>
					</div>
                    
                </li>
                <?php endwhile; ?>
            </ul>
            <a class="more" style="color: #3DADF5;font-weight: normal;" href="<?php bloginfo( 'url' ); ?>/must-read/">See more popular posts &raquo;</a>
            <?php endif; echo $after_widget; 
	}
    
}


// Our favourite posts widget
//--------------------------------------------------------------------------------

register_widget('T5_Favourite_Posts');
class T5_Favourite_Posts extends WP_Widget {
	function T5_Favourite_Posts() {
		parent::WP_Widget(false, $name = 'T5 Favourite Posts');	
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php 
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
	}

	function widget($args, $instance) {
		extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        ?>
            <?php echo $before_widget; ?>
            <?php if ( $title ) echo $before_title . $title . $after_title; ?>
            <?php 
            $args = array( 
		        'posts_per_page' => 5, 
		        'orderby' => 'rand', 
		        'ignore_sticky_posts' => 1,
		        'tag_slug__in' => 'favourite',
		        'tag__not_in' => '449'
		    );
            $t5popular_posts = new WP_Query( $args ); 
            if( $t5popular_posts->have_posts() ) : ?>
            <ul id="t5-favourite-posts">
                <?php while( $t5popular_posts->have_posts() ) : $t5popular_posts->the_post(); ?>
                <li>                	
					<div class="title">
						<a href="<?php the_permalink(); ?>?utm_source=travelllll&utm_medium=widget&utm_campaign=favourites" rel="nofollow"><?php the_post_thumbnail( 'thumb' ); ?></a>
						<a href="<?php the_permalink(); ?>?utm_source=travelllll&utm_medium=widget&utm_campaign=favourites"><?php the_title(); ?></a><a class="count" href="<?php comments_link(); ?>?utm_source=travelllll&utm_medium=widget&utm_campaign=favourites" rel="nofollow"><?php comments_number('0', '1', '%'); ?> <span class="screen-reader-text">Comments</span></a>
						<div class="clearfix"></div>
					</div>
                    
                </li>
                <?php endwhile; ?>
            </ul>
            <a class="more" style="color: #3DADF5;font-weight: normal;" href="<?php bloginfo( 'url' ); ?>/tag/favourite/">See more of our favourites &raquo;</a>
            <?php endif; echo $after_widget; 
	}
    
}


// Recommended posts widget
//--------------------------------------------------------------------------------

register_widget('T5_Recommended_Posts');
class T5_Recommended_Posts extends WP_Widget {
	function T5_Recommended_Posts() {
		parent::WP_Widget(false, $name = 'T5 Recommended Posts');	
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php 
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
	}

	function widget($args, $instance) {
		extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        ?>
            <?php echo $before_widget; ?>
            <?php if ( $title ) echo $before_title . $title . $after_title; ?>
            <?php 
            $args = array( 
		        'posts_per_page' => 1, 
		        'ignore_sticky_posts' => 1,
		        'tag' => 'recommended'
		    );
            $recommended_posts = new WP_Query( $args );
            if( $recommended_posts->have_posts() ) : while( $recommended_posts->have_posts() ) : $recommended_posts->the_post();
            	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'trending' );
            	$url = $thumb['0'];
            ?>
            <a id="t5-recommended-posts" href="<?php the_permalink(); ?>?utm_source=travelllll&utm_medium=widget&utm_campaign=recommended" style="background: url(<?php echo $url ?>) top center no-repeat;">
            	<span><?php the_title(); ?></span>
            </a>
            <?php endwhile; endif; ?>
            <?php 
            echo $after_widget; 
            ?>
        <?php
	}
    
}


// Popular video posts widget
//--------------------------------------------------------------------------------

register_widget('T5_Popvid_Posts');
class T5_Popvid_Posts extends WP_Widget {
	function T5_Popvid_Posts() {
		parent::WP_Widget(false, $name = 'T5 Popular Video Posts');	
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php 
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
	}

	function widget($args, $instance) {
		extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        ?>
            <?php echo $before_widget; ?>
            <?php if ( $title ) echo $before_title . $title . $after_title; ?>
            <?php 
            function t5_filter_where_popvids( $where = '' ) {
                $where .= " AND post_date > '" . date('Y-m-d', strtotime('-30 days')) . "'";
                return $where;
            }
            add_filter( 'posts_where', 't5_filter_where_popvids' );
            $args = array( 
                'posts_per_page' => 1, 
                'orderby' => 'comment_count', 
                'order' => 'DESC',
                'ignore_sticky_posts' => 1,
                'content_type' => 'videos'
            );
            $popvid_posts = new WP_Query( $args );
            if( $popvid_posts->have_posts() ) : while( $popvid_posts->have_posts() ) : $popvid_posts->the_post();
            	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'grid' );
            	$url = $thumb['0'];
            ?>
            <a id="t5-popvids-posts" href="<?php the_permalink(); ?>?utm_source=travelllll&utm_medium=widget&utm_campaign=videos">
            	<span class="vidthumb" style="background:url(<?php echo $url ?>) top center no-repeat;"><span class="overlay"></span><span class="play"></span></span>
            	<span class="title"><?php the_title(); ?></span>
            </a>
            <?php endwhile; endif; ?>
            <?php
            remove_filter( 'posts_where', 't5_filter_where_popvids' );
            echo $after_widget; 
            ?>
        <?php
	}
    
}


// Custom Excerpt
//--------------------------------------------------------------------------------

/*This lets us call a new function with a specific length*/
function t5_excerpt($limit) {
  $excerpt = explode(' ', get_the_content(), $limit);
  if (count($excerpt)>=$limit) {
    array_pop($excerpt);
    $excerpt = implode(" ",$excerpt).' ...';
  } else {
    $excerpt = implode(" ",$excerpt);
  }	
  $excerpt = strip_tags($excerpt, '');
  $excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
  echo $excerpt;
}

/*This adjusts the length of the default WordPress excerpt*/
function t5_trim_excerpt($text) {
	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content('');

		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text, '<p><a>');
		$excerpt_length = apply_filters('excerpt_length', 80);
		$excerpt_more = apply_filters('excerpt_more', ' ' . ' <a href="'. get_permalink($post->ID) . '" class="more-link"><span class="screen-reader-text">Continue Reading</span></a>');
		$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
		if ( count($words) > $excerpt_length ) {
			array_pop($words);
			$text = implode(' ', $words);
			$text = $text . $excerpt_more;
		} else {
			$text = implode(' ', $words);
		}
	}
	return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
}

remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 't5_trim_excerpt');


/**
 * Tests if any of a post's assigned categories are descendants of target categories
 *
 * @param int|array $cats The target categories. Integer ID or array of integer IDs
 * @param int|object $_post The post. Omit to test the current post in the Loop or main query
 * @return bool True if at least 1 of the post's categories is a descendant of any of the target categories
 * @see get_term_by() You can get a category by name or slug, then pass ID to this function
 * @uses get_term_children() Passes $cats
 * @uses in_category() Passes $_post (can be empty)
 * @version 2.7
 * @link http://codex.wordpress.org/Function_Reference/in_category#Testing_if_a_post_is_in_a_descendant_category
 */
function post_is_in_descendant_category( $cats, $_post = null )
{
	foreach ( (array) $cats as $cat ) {
		// get_term_children() accepts integer ID only
		$descendants = get_term_children( (int) $cat, 'category');
		if ( $descendants && in_category( $descendants, $_post ) )
			return true;
	}
	return false;
}

// Remove HTML from category descriptions
//--------------------------------------------------------------------------------
remove_filter('term_description','wpautop');


// Customise user profiles
//--------------------------------------------------------------------------------
add_action('admin_head', 'admin_del_color_options');
	function admin_del_color_options() {
   		global $_wp_admin_css_colors;
   		$_wp_admin_css_colors = 0;
}
add_filter('user_contactmethods','hide_profile_fields',10,1);
function hide_profile_fields( $contactmethods ) {
	unset($contactmethods['aim']);
	unset($contactmethods['jabber']);
	unset($contactmethods['yim']);
	unset($contactmethods['twitter']);
	return $contactmethods;
}
		
function t5_contactmethods( $contactmethods ) {
	$contactmethods['twitter'] = 'Twitter URL';
	$contactmethods['facebook'] = 'Facebook URL';
	$contactmethods['gplus'] = 'Google+ URL';
	return $contactmethods;
}
add_filter('user_contactmethods','t5_contactmethods',10,1);


// Customise admin capabilities. Hey mega-lomaniac, you're no Jesus. (Yes I am).
//--------------------------------------------------------------------------------
add_action( 'admin_menu', 't5_remove_menu_pages' );

function t5_remove_menu_pages() {

	remove_menu_page( 'link-manager.php' );
	remove_menu_page( 'admin.php?page=polls' );
	remove_menu_page( 'admin.php?page=ratings' );
	remove_menu_page( 'index.php?page=akismet-stats-display' );
	
	if ( !current_user_can( 'administrator' ) ) {
		remove_menu_page( 'tools.php' );
		remove_submenu_page( 'index.php', 'search-meter/search-meter.php' );
	}
	
	if ( !current_user_can( 'editor' ) && !current_user_can( 'manager' ) && !current_user_can( 'administrator' ) ) {
		remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
		remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
		remove_meta_box('dashboard_recent_drafts', 'dashboard', 'normal');
		remove_meta_box('dashboard_scheduled_posts', 'dashboard', 'normal');
		remove_menu_page( 'upload.php' );
		remove_menu_page('edit-comments.php');
		
	    add_action('wp_dashboard_setup', 't5_author_dashboard_widgets');
		    function t5_author_dashboard_widgets() {
			    global $wp_meta_boxes;
			    wp_add_dashboard_widget('author_info_widget', 'Author Information', 'author_info_widget');
		    }
		    function author_info_widget() {
		    	echo '<p>Hi there! Thanks for writing for Travelllll.com! Over to the left hand side of this screen you\'ll find the main menu. From here you can view a list of all the posts on Travelllll, write a new post, or edit your user profile. Please make sure that you fill out your profile so that you\'re credited correctly at the end of all your posts.</p><p>If you have any technical difficulties with using WordPress, please speak to your commissioning editor.</p>';
	    }
	}

}

// Add CSS files to admin based on capability
if ( !current_user_can( 'administrator' ) && is_user_logged_in() ) {
	wp_register_style('t5-editor-css', content_url('/themes/travelllll/library/styles/editor.css'), false, '9001');
	wp_enqueue_style('t5-editor-css');
}

if ( !current_user_can( 'editor' ) && is_user_logged_in() ) {
	wp_register_style('t5-editor-css', content_url('/themes/travelllll/library/styles/editor.css'), false, '9001');
	wp_enqueue_style('t5-editor-css');
	wp_register_style('t5-contributor-css', content_url('/themes/travelllll/library/styles/contributor.css'), false, '9001');
	wp_enqueue_style('t5-contributor-css');
}


// Remove dashboard widgets
//--------------------------------------------------------------------------------
add_action('admin_init', 't5_remove_dashboard_widgets');
function t5_remove_dashboard_widgets() {

	remove_meta_box('dashboard_primary', 'dashboard', 'normal');   // wordpress blog
	remove_meta_box('dashboard_secondary', 'dashboard', 'normal');   // other wordpress news
	remove_meta_box('dashboard_quick_press', 'dashboard', 'normal');
	remove_meta_box('yoast_db_widget', 'dashboard', 'normal');
	remove_meta_box('meandmymac_rss_widget', 'dashboard', 'normal');

}


// Custom taxonomy for content types
//--------------------------------------------------------------------------------
add_action( 'init', 'register_taxonomy_content_type' );

function register_taxonomy_content_type() {

    $labels = array( 
        'name' => _x( 'Content Types', 'content type' ),
        'singular_name' => _x( 'Content Type', 'content type' ),
        'search_items' => _x( 'Search Content Types', 'content type' ),
        'popular_items' => _x( 'Popular Content Types', 'content type' ),
        'all_items' => _x( 'All Content Types', 'content type' ),
        'parent_item' => _x( 'Parent Content Type', 'content type' ),
        'parent_item_colon' => _x( 'Parent Content Type:', 'content type' ),
        'edit_item' => _x( 'Edit Content Type', 'content type' ),
        'update_item' => _x( 'Update Content Type', 'content type' ),
        'add_new_item' => _x( 'Add New Content Type', 'content type' ),
        'new_item_name' => _x( 'New Content Type Name', 'content type' ),
        'separate_items_with_commas' => _x( 'Separate content types with commas', 'content type' ),
        'add_or_remove_items' => _x( 'Add or remove content types', 'content type' ),
        'choose_from_most_used' => _x( 'Choose from the most used content types', 'content type' ),
        'menu_name' => _x( 'Content Types', 'content type' ),
    );

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_tagcloud' => true,
        'hierarchical' => true,
        'rewrite' => false,
        'query_var' => true
    );
    register_taxonomy( 'content_type', array('post'), $args );
    
    $terms = array( 
    	'photos', 
    	'interviews', 
    	'news', 
    	'opinion', 
    	'podcast', 
    	'videos',
    	'features',
    	'reviews', 
    	'alerts', 
    	'guides', 
    	'links', 
    	'discussions', 
    	'reports', 
    	'live', 
    	'how-to',
    	'essays'
    );
	add_rewrite_tag( '%content_type%', '(' . implode( '|', $terms ) . ')' );
	add_permastruct( 'content_type', '%content_type%', false );

}


// Set default taxonomy terms
//--------------------------------------------------------------------------------
function t5_default_taxonomy_terms( $post_id, $post ) {
    if ( 'publish' === $post->post_status ) {
        $defaults = array(
            'content_type' => array( 'news' ),
            'status' => array( 'active' ),
            );
        $taxonomies = get_object_taxonomies( $post->post_type );
        foreach ( (array) $taxonomies as $taxonomy ) {
            $terms = wp_get_post_terms( $post_id, $taxonomy );
            if ( empty( $terms ) && array_key_exists( $taxonomy, $defaults ) ) {
                wp_set_object_terms( $post_id, $defaults[$taxonomy], $taxonomy );
            }
        }
    }
}
add_action( 'save_post', 't5_default_taxonomy_terms', 100, 2 );


// Add Taxonomies to post_class()
//--------------------------------------------------------------------------------
add_action( 'post_class', 'onolan_smash_post_classes', 10, 3 );
function onolan_smash_post_classes( $classes, $extra_classes, $post_id ) {
   $post = get_post( $post_id );
   if ( is_object_in_taxonomy( $post->post_type, 'content_type' ) ) {
       foreach ( (array) get_the_terms( $post->ID, 'content_type' ) as $term )
           $classes[] = 'content-' . sanitize_html_class( $term->slug, $term->term_id );
   }
   return $classes;
}

function t5_body_class( $classes ) {
	global $post;
	if ( is_object_in_taxonomy( $post->post_type, 'content_type' ) ) {
       foreach ( (array) get_the_terms( $post->ID, 'content_type' ) as $term )
           $classes[] = 'single-content-' . sanitize_html_class( $term->slug, $term->term_id );
   }
   return $classes;
}
add_filter('body_class', 't5_body_class');


// Custom Comment Output
//--------------------------------------------------------------------------------
function t5_comment( $comment, $args, $depth ) {
	$GLOBALS ['comment'] = $comment; ?>
	
	<?php if ( '' == $comment->comment_type ) : ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<section id="comment-<?php comment_ID(); ?>">
	    	<div class="author-wrap">
    	    	<section class="comment-author vcard">
        			<?php echo get_avatar( $comment, 40 ); ?>
        		</section>
            </div>
    		<section class="comment-body">
    			<section class="comment-author-name">
    				<?php comment_author_link() ?> 
	    			<?php if ( 
	    					$comment->comment_author_email == "john@travelllll.com" ||
	    					$comment->comment_author_email == "alastair@travelllll.com" || 
	    					$comment->comment_author_email == "rich@travelllll.com" || 
	    					$comment->comment_author_email == "lezaan@travelllll.com"
	    					) {
	    					echo '<span class="staff">Staff</span>';
	    				}
	    			?>
	    			<span class="comment-meta commentmetadata"><?php comment_date('F jS') ?></span>
    			</section>
    		    <?php comment_text(); ?>
    		    <?php if ( $comment->comment_approved == '0' ) : ?>
        			<em>Your comment is awaiting moderation.</em>
        			<br />
        		<?php endif; ?>
    		    <div class="reply">
        			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
        		</div>
        		<div class="clearfix"></div>
    		</section>
    		<div class="clearfix"></div>
	    </section><!--comment-<?php comment_ID(); ?>-->
	    <div class="clearfix"></div>
	    <?php $email = ''; ?>

	<?php else : ?>
	<li class="post pingback">
		<p>Pingback:<?php comment_author_link(); ?><?php edit_comment_link ( 'edit', '&nbsp;&nbsp;', '' ); ?></p>
	<?php endif;
}

// Custom Comment Author Link (Using hAtom)
function t5_comment_author_link() {
	global $comment;
	$url    = get_comment_author_url();
	$author = get_comment_author();
 
	if ( empty( $url ) || 'http://' == $url )
		$return = "<span class='fn'>$author</span>";
	else
		$return = "<a href='$url' rel='external nofollow' class='fn url'>$author</a>";
	return $return;
}
add_filter('get_comment_author_link', 't5_comment_author_link');


// Sponsored Post Background
//--------------------------------------------------------------------------------
function t5_sponsored_background() {

	$t5_sponsored_background = get_post_custom_values( 'sponsored_background' );
	$t5_sponsored_background_padding = get_post_custom_values( 'sponsored_background_padding' );
	$t5_sponsored_background_color = get_post_custom_values( 'sponsored_background_color' );
	
	if( !isset( $t5_sponsored_background_padding[0] ) || $t5_sponsored_background_padding[0] == '' ) { $t5_sponsored_background_padding[0] = '200'; }
	if( !isset( $t5_sponsored_background_color[0] ) || $t5_sponsored_background_color[0] == '' ) { $t5_sponsored_background_color[0] = 'fff'; }
	
	if( $t5_sponsored_background[0] !== '' ) {
		echo '
		<style type="text/css">
			#main {
				padding-top: ' . $t5_sponsored_background_padding[0] . 'px;
				padding-bottom: 60px;
				background: #' . $t5_sponsored_background_color[0] . ' url(' . $t5_sponsored_background[0] . ') top center fixed repeat-x;
			}
			#main div.wrap {
				background: #fff;
				padding: 10px 0;
				box-shadow: #fff 10px 0, #fff -10px 0;
				border-radius:3px;
			}
			#main #content {
				margin-right:0;
				padding:10px 0 0 10px;
				border:none;
			}
			#main #sidebar {
				right:9px;
				top:13px
			}
			.roadblockheader, .headerads, .endofcontentad, #breadcrumbs, #trending {display:none;}
		</style>' 
		. "\n";
	}
	
}

// Detect Old Posts to Serve Ads
//--------------------------------------------------------------------------------
function t5_is_old_post($days = 7) {
	$days = (int) $days;
	$offset = $days*60*60*24;
	if ( get_post_time() < date('U') - $offset )
		return true;
	return false;
}
