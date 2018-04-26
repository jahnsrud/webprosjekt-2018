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

$current_dir = dirname( __FILE__ );

return apply_filters( 'kalium_load_classes', array(
	// Helpers and utls (order matters)
	'Kalium_Helpers'           => $current_dir . '/core/kalium-helpers.php',
	'Kalium_URL'  			   => $current_dir . '/utility/kalium-url.php',
	
	// Core classes
	'Kalium_Theme_License'     => $current_dir . '/core/kalium-theme-license.php',
	'Kalium_Theme_Upgrader'    => $current_dir . '/core/kalium-theme-upgrader.php',
	'Kalium_Version_Upgrades'  => $current_dir . '/core/kalium-version-upgrades.php',
	'Kalium_Translations' 	   => $current_dir . '/core/kalium-translations.php',
	'Kalium_Images' 	   	   => $current_dir . '/core/kalium-images.php',
	'Kalium_Media' 	   	   	   => $current_dir . '/core/kalium-media.php',
	
	// Plugin compatibility
	'Kalium_WooCommerce'   	   => $current_dir . '/compatibility/kalium-woocommerce.php',
	'Kalium_WPBakery'   	   => $current_dir . '/compatibility/kalium-wpbakery.php',
	'Kalium_LayerSlider'   	   => $current_dir . '/compatibility/kalium-layerslider.php',
	'Kalium_ACF'   	   		   => $current_dir . '/compatibility/kalium-acf.php',
	
	// Utility classes
	'Laborator_System_Status'  => $current_dir . '/utility/laborator-system-status.php',
) );