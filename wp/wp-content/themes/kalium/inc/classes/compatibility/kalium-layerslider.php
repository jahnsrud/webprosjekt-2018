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

class Kalium_LayerSlider {
	
	/**
	 * Required plugin/s for this class
	 */
	public static $plugins = array( 'LayerSlider/layerslider.php' );
	
	/**
	 * Class instructor, define necesarry actions
	 */
	public function __construct() {
		if ( 'kalium-install-plugins' == kalium()->url->get( 'page' ) ) {
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'fixUpdatePackageNotAvailable' ) );
		}
	}
	
	/**
	 * Fix for "Update package not available"
	 */
	public function fixUpdatePackageNotAvailable( $transient ) {
		$plugin = 'LayerSlider/layerslider.php';
		
		if ( ! empty( $transient->response ) && isset( $transient->response[ $plugin ] ) ) {
			$layerslider = & $transient->response[ $plugin ];
			
			if ( empty( $layerslider->package ) ) {
				$layerslider->package = $GLOBALS['tgmpa']->plugins['LayerSlider']['source'];
				
				// Activate plugin again if it was previously active
				$is_active = kalium()->helpers->isPluginActive( $plugin );
				
				if ( $is_active ) {
					add_action( 'admin_footer', array( $this, 'activateLayerSliderPlugin' ) );
				}
			}
		}
		
		return $transient;
	}
	
	/**
	 * Activate LayerSlider plugin
	 *
	 * @type action
	 */
	public function activateLayerSliderPlugin() {
		activate_plugin( 'LayerSlider/layerslider.php' );
	}
}