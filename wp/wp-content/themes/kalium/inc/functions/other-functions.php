<?php
/**
 *	Kalium WordPress Theme
 *
 *	Other Functions
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Homepage hashtags links fix
 */
function kalium_unique_hashtag_url_base_menu_item( $classes, $item ) {
	$url = $item->url;
		
	// Only hashtag links
	if ( false !== strpos( $url, '#' ) ) {
		$url_md5 = ( preg_replace( '/#.*/', '', $url ) );
		
		// Skip first item only
		if ( ! isset( $GLOBALS['kalium_hashtag_links'][ $url_md5 ] ) ) {
			$GLOBALS['kalium_hashtag_links'][ $url_md5 ] = true;
			return $classes;
		}
					
		$remove_classes = array( 'current_page_item', 'current-menu-item', 'current-menu-ancestor', 'current_page_ancestor' );
		
		foreach ( $remove_classes as $class_to_remove ) {
			$current_item_index = array_search( $class_to_remove, $classes );
			
			if ( false !== $current_item_index ) {
				unset( $classes[ $current_item_index ] );
			}
		}
	}
	
	return $classes;
}

/**
 * Homepage hashtags reset skipped item
 */
function kalium_unique_hashtag_url_base_reset( $args ) {
	$GLOBALS['kalium_hashtag_links'] = array();
	return $args;
}

/**
 * Post link plus mapper for WPML plugin
 *
 * @type filter
 */
function kalium_post_link_plus_result_mapper( $results ) {
	
	if ( kalium()->helpers->isPluginActive( 'sitepress-multilingual-cms/sitepress.php' ) ) {
		$results_new = array();
		
		foreach ( $results as $result ) {
			$results_new[] = get_post( apply_filters( 'wpml_object_id', $result->ID, $result->post_type, true ) );
		}
		
		return $results_new;
	}
	
	return $results;
}