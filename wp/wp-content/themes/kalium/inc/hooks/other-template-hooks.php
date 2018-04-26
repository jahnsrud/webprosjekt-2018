<?php
/**
 *	Kalium WordPress Theme
 *
 *	Other Template Hooks
 *	
 *	Laborator.co
 *	www.laborator.co
 */

/**
 * Remove multiple current menu items with hashtags
 */
add_filter( 'nav_menu_css_class', 'kalium_unique_hashtag_url_base_menu_item', 10, 2 ); 
add_filter( 'wp_nav_menu_args', 'kalium_unique_hashtag_url_base_reset', 10 ); 

/**
 * Fix for Post Link Plus when WPML is active
 */
add_filter( 'kalium_post_link_plus_result', 'kalium_post_link_plus_result_mapper', 10 );