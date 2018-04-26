<?php
/**
 *	Kalium WordPress Theme
 *
 *	Core Actions and Filters
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

/**
 * Handle endless pagination (global)
 */
add_action( 'wp_ajax_kalium_endless_pagination_get_paged_items', 'kalium_endless_pagination_get_paged_items', 10 );
add_action( 'wp_ajax_nopriv_kalium_endless_pagination_get_paged_items', 'kalium_endless_pagination_get_paged_items', 10 );

/**
 * Register widgets
 */
add_action( 'widgets_init', 'kalium_widgets_init', 10 );

/**
 * Sidebar skin
 */
add_filter( 'kalium_widget_area_classes', 'kalium_set_widgets_classes', 10 );

/**
 * Custom sidebars plugin args
 */
add_filter( 'cs_sidebar_params', 'kalium_custom_sidebars_params', 10 );

/**
 * Password protected post form
 */
add_filter( 'the_password_form', 'kalium_the_password_form', 10 );

/**
 * Default excerpt length and more dots
 */
add_filter( 'excerpt_length', 'kalium_get_default_excerpt_length', 10 );
add_filter( 'excerpt_more', 'kalium_get_default_excerpt_more', 100 );

/**
 * Footer classes
 */
add_filter( 'kalium_footer_class', 'kalium_get_footer_classes', 10 );

/**
 * Image placeholder set style
 */
add_action( 'init', 'kalium_image_placeholder_set_style', 10 );

/**
 * Version upgrade hooks
 */
add_action( 'version_upgrade_2_3', array( 'Kalium_Version_Upgrades', 'version_upgrade_2_3' ), 10 );
