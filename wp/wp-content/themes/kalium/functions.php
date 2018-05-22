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

// Theme options (soon to be deprecated)
require_once get_template_directory() . '/inc/lib/smof/smof.php';

// Load classes
require_once get_template_directory() . '/inc/classes/kalium-main.php';

// Initalize Kalium instance
Kalium::instance();

// Theme functions
require_once kalium()->locateFile( 'inc/functions/core-functions.php' );
require_once kalium()->locateFile( 'inc/functions/blog-functions.php' );
require_once kalium()->locateFile( 'inc/functions/other-functions.php' );

// Template functions
require_once kalium()->locateFile( 'inc/functions/template/core-template-functions.php' );
require_once kalium()->locateFile( 'inc/functions/template/blog-template-functions.php' );
require_once kalium()->locateFile( 'inc/functions/template/other-template-functions.php' );

// Hooks
require_once kalium()->locateFile( 'inc/hooks/core-template-hooks.php' );
require_once kalium()->locateFile( 'inc/hooks/blog-template-hooks.php' );
require_once kalium()->locateFile( 'inc/hooks/other-template-hooks.php' );

// WooCommerce functions, template functions and hooks
if ( kalium()->helpers->isPluginActive( 'woocommerce/woocommerce.php' ) ) {	
	require_once kalium()->locateFile( 'inc/functions/woocommerce-functions.php' );
	require_once kalium()->locateFile( 'inc/functions/template/woocommerce-template-functions.php' );
	require_once kalium()->locateFile( 'inc/hooks/woocommerce-template-hooks.php' );
}

// Core files
require_once kalium()->locateFile( 'inc/laborator_functions.php' );
require_once kalium()->locateFile( 'inc/laborator_actions.php' );
require_once kalium()->locateFile( 'inc/laborator_filters.php' );
require_once kalium()->locateFile( 'inc/laborator_portfolio.php' );
require_once kalium()->locateFile( 'inc/laborator_vc.php' );
require_once kalium()->locateFile( 'inc/laborator_thumbnails.php' );

// ACF Custom fields
require_once kalium()->locateFile( 'inc/acf-fields.php' );

// Libraries and plugins to use in theme
#require_once kalium()->locateFile( 'inc/lib/dynamic_image_downsize.php' );
require_once kalium()->locateFile( 'inc/lib/acf-revslider-field.php' );
require_once kalium()->locateFile( 'inc/lib/class-tgm-plugin-activation.php' );
require_once kalium()->locateFile( 'inc/lib/post-link-plus.php' );
require_once kalium()->locateFile( 'inc/lib/laborator/laborator_custom_css.php' );
require_once kalium()->locateFile( 'inc/lib/laborator/typolab/typolab.php' );

// Admin related plugins
if ( is_admin() ) {
	require_once kalium()->locateFile( 'inc/lib/laborator/laborator-acf-grouped-metaboxes/laborator-acf-grouped-metaboxes.php' );
	require_once kalium()->locateFile( 'inc/lib/laborator/laborator-demo-content-importer/laborator_demo_content_importer.php' );
}
