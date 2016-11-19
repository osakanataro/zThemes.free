<?php
/**
 * WordPress 4.3 产生大量定时作业修复方法
 */
remove_action( 'admin_init', '_wp_check_for_scheduled_split_terms' );

/**
 * Enabled link manager (>=WordPress 3.5)
 */
add_filter( 'pre_option_link_manager_enabled', '__return_true' );  

/**
 * Secure WordPress by removing version
 */
remove_action('wp_head', 'wp_generator');

/**
 * Secure WordPress by hiding login errors
 */
function hide_login_errors($errors) { return 'login error'; }
add_filter('login_errors', 'hide_login_errors', 10, 1);

/**
 * disabled all automatic updater.
 */
add_filter( 'automatic_updater_disabled', '__return_true' );

/*
 * Make theme available for translation.
 * Translations can be filed in the /languages/ directory.
 * If you're building a theme based on Twenty Sixteen, use a find and replace
 * to change 'zsimple' to the name of your theme in all the template files
 */
#load_theme_textdomain( 'zsimple', get_template_directory() . '/languages' );

/**
 * Add default posts and comments RSS feed links to head.
 */
add_theme_support( 'automatic-feed-links' );

/**
 * This theme uses post thumbnails
 */
add_theme_support( 'post-thumbnails' );
/**
 * post_thumbnail by zwwooooo
 */
function zoo_post_thumbnail($size = 'thumbnail', $return = 'img', $use_default = false, $custom_default = '' ){
	global $post;
	
	if( has_post_thumbnail() ){
		$timthumb_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $size);
		$post_timthumb_src = $timthumb_src[0];
	} elseif ($auto_thumb_img_src = zoo_auto_thumb_img_src()) {
			$post_timthumb_src = $auto_thumb_img_src;
	} elseif ($use_default == true) {
		$post_timthumb_src =  get_template_directory_uri() . '/img/thumb_default.png';
		if ($custom_default) $post_timthumb_src = $custom_default;
	} else {
		$post_timthumb_src = '';
	}

	if ($post_timthumb_src) {
		if ($return == 'img') {
			return '<img src="'.$post_timthumb_src.'" alt="'.$post->post_title.'" />';
		} else {
			return $post_timthumb_src;
		}
	}
}
/**
 * Auto Thumbnail
 */
function zoo_auto_thumb_img_src($size = 'thumbnail') {
	global $post;
	$args = array(
		'numberposts' => 1,
		'order'=> 'ASC',
		'post_mime_type' => 'image',
		'post_parent' => $post->ID,
		'post_status' => null,
		'post_type' => 'attachment'
	);
 
	$attachments = get_children($args);
	$imageUrl = '';
 
	if($attachments) {
		$image = array_pop($attachments);
		$imageSrc = wp_get_attachment_image_src($image->ID, $size);
		$imageUrl = $imageSrc[0];
	}

	return $imageUrl;
}


/*
 * Let WordPress manage the document title.
 * By adding theme support, we declare that this theme does not use a
 * hard-coded <title> tag in the document head, and expect WordPress to
 * provide it for us.
 */
add_theme_support( 'title-tag' );
/**
 * zsimple Title Tag (Themes are REQUIRED to use 'wp_title' filter, to filter wp_title() )
 */
add_filter( 'wp_title', 'zsimple_wp_title', 10, 2 );
function zsimple_wp_title($title, $sep) {
	global $paged, $page;
	if ( is_feed() )
		return $title;
	// Add the site name.
	$site_title = get_bloginfo( 'name' );
	$title .= $site_title;
	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";
	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 ) {
		$havepage = sprintf( ' - Page %s ', max( $paged, $page ) );
	}

	$title_split=explode(' | ', $title);
	if ( is_category() ) {
		$title = 'Catetory Archives: ' . $title_split[0] . $havepage . " $sep " .  $title_split[1];
	} elseif ( is_tag() ) {
		$title = 'Tag Archives: ' . $title_split[0] . $havepage . " $sep " .  $title_split[1];
	} elseif ( is_search() ) {
		$title = 'Search Results for: ' . $title_split[0] . $havepage . " $sep " .  $title_split[2];
	} elseif ( is_date() ) {
		if(is_day()) {
			$date = date('M',get_the_date('U')).get_the_date(' jS, Y');
		} elseif(is_year()) {
			$date = get_the_date('Y');
		} else {
			$date = date('M',get_the_date('U')).get_the_date(', Y');
		}
		$title = 'Date Archives: ' . $date . $havepage . " $sep " .  $site_title;
	} elseif ( is_author() ) {
		$title = 'Author Archives: ' . $title_split[0] . $havepage . " $sep " .  $title_split[1];
	} else {
		$title = "$title" . $havepage;
	}
	
	return $title;
}


/**
 * WP nav menu
 */
if (function_exists('wp_nav_menu')) {
	register_nav_menus(array(
		'zsimple_primary' => 'zSimple Primary Navigation'
	));
}
// Custom wp_list_pages
function zsimple_wp_list_pages(){
	echo wp_list_pages('title_li=');
}
function zsimple_wp_list_categories(){
	echo wp_list_categories('title_li=');
}


/**
 * Widgetized Sidebar.
 */
function zsimple_widgets_init() {
	register_sidebar(array(
		'name' => __('Primary Widget Area','zsimple'),
		'id' => 'primary-widget-area',
		'description' => __('The primary widget area','zsimple'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	));
	register_sidebar(array(
		'name' => __('Singular Widget Area','zsimple'),
		'id' => 'singular-widget-area',
		'description' => __('The singular widget area','zsimple'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	));
	register_sidebar(array(
		'name' => __('Not Singular Widget Area','zsimple'),
		'id' => 'not-singular-widget-area',
		'description' => __('Not the singular widget area','zsimple'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	));
}
add_action( 'widgets_init', 'zsimple_widgets_init' );

/**
 * Custom Widgets
 */
require( dirname( __FILE__ ).'/custom-widgets.php' );
