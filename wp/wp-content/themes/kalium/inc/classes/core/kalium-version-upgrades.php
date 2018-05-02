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

class Kalium_Version_Upgrades {
	
	/**
	 *	Recent versions of the theme
	 *	Defined only by developer
	 */
	private $version_history = array( '2.0', '2.0.5', '2.2' );
	
	public function __construct() {
	}
	
	/**
	 *	Check for version upgrades callbacks
	 */
	public function admin_init() {
		// Execute callbacks for version upgrades
		$version_upgrades = array_unique( get_option( 'kalium_version_upgrades', array() ) );
		$current_version = kalium()->getVersion();
		$_current_version = str_replace( '.', '_', $current_version );
		
		$this->version_history[] = $current_version;
		
		sort( $version_upgrades );
		
		foreach ( $this->version_history as $previous_version ) {

			if ( ! in_array( $previous_version, $version_upgrades ) && version_compare( $current_version, $previous_version, '>' ) ) {
				// Version upgrade function name callback
				$current_version_callback_fn = 'version_upgrade_' . $_current_version;
				
				// Native version upgrade callback
				if ( method_exists( $this, $current_version_callback_fn ) ) {
					$this->$current_version_callback_fn( $previous_version );
				}
				
				// Execute version upgrade actions
				do_action( 'kalium_version_upgrade', $current_version, $previous_version );
				do_action( 'kalium_version_upgrade_' . $_current_version, $previous_version );
				
				// Register this version upgrade
				$version_upgrades[] = $previous_version;
				update_option( 'kalium_version_upgrades', $version_upgrades );
			}
		}
	}
	
	/**
	 * Upgrading to version 2.3
	 */
	public static function version_upgrade_2_3() {
		
		// WooCommerce settings
		$shop_product_columns = get_data( 'shop_product_columns' );
		
		update_option( 'woocommerce_catalog_columns', kalium_get_number_from_word( 'decide' == $shop_product_columns ? 4 : $shop_product_columns ) );
		update_option( 'woocommerce_catalog_rows', str_replace( 'rows-', '', get_data( 'shop_products_per_page' ) ) );
		
		// Refresh Envato Hosted license
		remove_theme_mod( 'purchase_code_request_verification' );
	}
}