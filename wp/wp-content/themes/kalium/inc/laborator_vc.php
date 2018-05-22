<?php
/**
 *	Kalium WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

// If its not active disable these functions
$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

if ( is_array( $active_plugins ) && ! in_array( 'js_composer/js_composer.php', $active_plugins ) && function_exists( 'vc_map' ) == false ) {
	return;
} else if ( function_exists( 'vc_map' ) == false ) {
	return;
}

// Set Visual Composer As Theme Mode
function laborator_visual_composer_init() {
    vc_set_as_theme();
}

add_action( 'vc_before_init', 'laborator_visual_composer_init' );

// Text column text formatting
function kalium_vc_text_column_formatting( $classes, $base, $atts ) {
	
	if ( 'vc_column_text' == $base ) {
		
		$classes .= ' post-formatting ';
	}
	
	return $classes;
}

add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'kalium_vc_text_column_formatting', 10, 3 );

// Widget skin class for VC elements
function kalium_vc_widget_sidebar_classes( $classes, $base, $atts ) {
	
	if ( 'vc_widget_sidebar' == $base ) {
		$classes .= sprintf( ' widget-area %s', implode( ' ', kalium_set_widgets_classes() ) );
	}
	
	return $classes;
}

add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'kalium_vc_widget_sidebar_classes', 10, 3 );


// Default Post Types
function kalium_vc_default_post_types() {
	
	if ( function_exists( 'vc_set_default_editor_post_types' ) ) {	
		$list = array( 'page', 'portfolio' );
		vc_set_default_editor_post_types( $list );	
	}
}

add_action( 'vc_before_init', 'kalium_vc_default_post_types' );


// General Attributes
$laborator_vc_general_params = array(
	
	// Reveal Effect (Extended)
	'reveal_effect_x' => array(
		'type'        => 'dropdown',
		'heading'     => 'Reveal Effect',
		'param_name'  => 'reveal_effect',
		'std'         => 'fadeInLab',
		'value'       => array(
			'None'                           => 'none',
			'Fade In'                        => 'fadeIn',
			'Slide and Fade'                 => 'fadeInLab',
			'Fade In (one by one)'           => 'fadeIn-one',
			'Slide and Fade (one by one)'    => 'fadeInLab-one',
		),
		'description' => 'Set reveal effect for this element.'
	),
);


// Support for Shortcodes
$lab_vc_templates_path = kalium()->locateFile( 'inc/lib/vc/' );

$lab_vc_shortcodes = array(
	// Portfolio
	'lab_portfolio_items',
	'lab_masonry_portfolio',
	'lab_dribbble_gallery',
	'lab_portfolio_share_like',
	
	// Other
	'lab_team_members', 
	'lab_service_box', 
	'lab_heading', 
	'lab_scroll_box', 
	'lab_clients', 
	'lab_vc_social_networks', 
	'lab_message', 
	'lab_button',
	'lab_contact_form',
	'lab_google_map',
	'lab_text_autotype',
	'lab_blog_posts',
	'lab_divider',
	'lab_pricing_table',
);

if ( is_shop_supported() ) {
	$lab_vc_shortcodes[] = 'lab_products_carousel';
}

foreach ( $lab_vc_shortcodes as $shortcode_template ) {
	include_once $lab_vc_templates_path . $shortcode_template . '/init.php';
}


// Customizations
require_once $lab_vc_templates_path . 'custom-font-icons.php';
require_once $lab_vc_templates_path . 'custom-image-gallery.php';

// Reveal Effect Params Generator
function lab_vc_reveal_effect_params( $effect, $duration = '', $delay = '' ) {
	
	if ( $effect == '' || $effect == 'none' ) {
		return;
	}
	
	$atts = '';
	
	if ( $duration ) {	
		$atts .= ' data-wow-duration="' . $duration . 's"';
	}
	
	if ( $delay ) {
		$atts .= ' data-wow-delay="' . $delay . 's"';
	}
	
	return $atts;
}

// VC Tabs 4.7
function lab_vc_tta_tabs_setup() {
	
	$new_param = array( 'Theme Styled (if selected, other style settings will be ignored)' => 'theme-styled' );
	$new_param_minimal = array( 'Theme Styled Minimal (if selected, other style settings will be ignored)' => 'theme-styled-minimal' );
	
	$tabs_param        = WPBMap::getParam( 'vc_tta_tabs', 'style' );
	$accordion_param   = WPBMap::getParam( 'vc_tta_accordion', 'style' );
	
	if ( ! is_array( $tabs_param ) || ! is_array( $accordion_param ) ) {
		return;
	}
	
	$tabs_param['value']       = array_merge( $new_param, $new_param_minimal, $tabs_param['value'] );
	$accordion_param['value']  = array_merge( $new_param, $accordion_param['value'] );

	vc_update_shortcode_param( 'vc_tta_tabs', $tabs_param );
	vc_update_shortcode_param( 'vc_tta_accordion', $accordion_param );
}

add_action( 'vc_after_mapping', 'lab_vc_tta_tabs_setup' );


// Kalium VC Query Builder
function kalium_vc_query_builder( $query ) {
	
	if ( class_exists( 'VcLoopQueryBuilder' ) ) {
		
		if ( ! class_exists( 'KaliumVcLoopQueryBuilder' ) ) {
			class KaliumVcLoopQueryBuilder extends VcLoopQueryBuilder {
				public function getQueryArgs() {
					return $this->args;
				}
			}
		}
		
		$query = new KaliumVcLoopQueryBuilder( VcLoopSettings::parseData( $query ) );
		
		return $query->getQueryArgs();
	}
	
	return array();
}
