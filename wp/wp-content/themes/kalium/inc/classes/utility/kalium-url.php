<?php
/**
 *	URL library
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class Kalium_URL {
	
	/**
	 * $_GET request method
	 */
	public function get( $var, $isset = false ) {
		if ( ! isset( $_REQUEST[ $var ] ) ) {
			return null;
		}
		
		return $isset ? true : $_REQUEST[ $var ];
	}
}