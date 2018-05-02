<?php
/**
 *	Custom Image Gallery
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

// Add support for Light Gallery Lightbox
function kalium_vc_image_gallery_add_lightbox_support() {	
	// Support for Light Gallery element
	$lightgallery_param = array(
		'type'        => 'checkbox',
		'heading'     => 'Use <strong>Light Gallery</strong> lightbox',
		'param_name'  => 'use_light_gallery',
		'description' => 'Use Kalium\'s built-in lightbox for gallery images.',
		'value'       => array( 'Yes' => 'yes' ),
	);
	
	vc_add_param( 'vc_media_grid', $lightgallery_param );
	vc_add_param( 'vc_masonry_media_grid', $lightgallery_param );
	
	// Gallery element
	$lightgallery_param_gallery_el = array_merge( $lightgallery_param, array(
		'description' => 'Use Kalium\'s built-in lightbox for gallery images. Note: Gallery type should be set to <strong>Image grid</strong>.',
		'weight' => 1,
		'dependency' => array(
			'element' => 'type',
			'value' => array( 'image_grid' ),
		),
	) );
	
	vc_add_param( 'vc_gallery', $lightgallery_param_gallery_el );
}

add_action( 'vc_after_init', 'kalium_vc_image_gallery_add_lightbox_support' );


// Default filters for Visual Composer element classes
function kalium_vc_shortcodes_css_class_filter( $classes, $base, $atts ) {	
	
	// Light gallery support for VC Masonry Media Grid
	if ( in_array( $base, array( 'vc_masonry_media_grid', 'vc_media_grid', 'vc_gallery' ) ) && 'yes' == get_array_key( $atts, 'use_light_gallery' ) ) {
		$classes .= ' light-gallery--enabled';
		
		// Enqueue light gallery library
		kalium_enqueue_lightbox_library();
	}
	
	return $classes;
}

add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'kalium_vc_shortcodes_css_class_filter', 10, 3 );
