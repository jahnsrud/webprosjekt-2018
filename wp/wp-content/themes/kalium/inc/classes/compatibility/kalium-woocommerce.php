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

class Kalium_WooCommerce {
	
	/**
	 * Required plugin/s for this class
	 */
	public static $plugins = array( 'woocommerce/woocommerce.php' );
	
	/**
	 * Class instructor, define necesarry actions
	 */
	public function __construct() {
	}
	
	/**
	 * After setup theme
	 *
	 * @type action
	 */
	public function after_setup_theme() {
		
		// Add theme support for WooCommerce
		add_theme_support( 'woocommerce', array(
			'single_image_width'    => 820,
			'thumbnail_image_width' => 550,
			
			'product_grid'          => array(
				'default_rows'    => 4,
				'min_rows'        => 1,
				'max_rows'        => 10,
				
				'default_columns' => 3,
				'min_columns'     => 1,
				'max_columns'     => 6,
			),
		) );
		
		add_theme_support( 'wc-product-gallery-slider' );
		
		if ( '0' !== get_data( 'shop_single_product_image_zoom' ) ) {
			add_theme_support( 'wc-product-gallery-zoom' );
		}
		
		if ( '0' !== get_data( 'shop_single_product_image_lightbox' ) ) {
			add_theme_support( 'wc-product-gallery-lightbox' );
		}
	}
	
	/**
	 * Init
	 *
	 * @type action
	 */
	public function init() {
		
		// Category thumbnails
		$this->defineCategoryThumbnails();
		
		// Use image resizer in AJAX requests for infinite pagination
		if ( class_exists( 'WC_Regenerate_Images' ) ) {
			add_action( 'kalium_woocommerce_infinite_scroll_pagination_before_query', array( kalium()->woocommerce, 'maybeResizeImages' ), 10 );
		}
	}
	
	/**
	 * Image resizer (WC >=3.3)
	 */
	public function maybeResizeImages() {
		if ( class_exists( 'WC_Regenerate_Images' ) ) {
			add_filter( 'wp_get_attachment_image_src', array( 'WC_Regenerate_Images', 'maybe_resize_image' ), 10, 4 );
		}
	}
	
	/**
	 * Category image thumbnail
	 */
	public function defineCategoryThumbnails() {
		
		// Category image size dimensions
		$shop_category_image_size = get_data( 'shop_category_image_size' );
		$shop_category_thumb_width = 500;
		$shop_category_thumb_height = 290;
		$shop_category_thumb_crop = true;
		
		// Custom defined size
		if ( preg_match_all( '/^([0-9]+)x?([0-9]+)?x?(0|1)?$/', $shop_category_image_size, $shop_category_image_dims ) ) {	
			$shop_category_thumb_width = intval( $shop_category_image_dims[1][0] );
			$shop_category_thumb_height	= intval( $shop_category_image_dims[2][0] );
			$shop_category_thumb_crop = intval( $shop_category_image_dims[3][0] ) == 1;
			
			if ( $shop_category_thumb_width == 0 || $shop_category_thumb_height == 0 ) {
				$shop_category_thumb_crop = false;
			}
		}
		
		add_image_size( 'shop-category-thumb', $shop_category_thumb_width, $shop_category_thumb_height, $shop_category_thumb_crop );
	}
	
	/**
	 * Pagination handler
	 */
	public static function paginationHandler( $posts_per_page, $total_items, $fetched_ids, $wp_query_args ) {
		global $post;
		
		$response = array();
		
		// Kalium WooCommerce
		$kalium_woocommerce = kalium()->woocommerce;
		
		// Query vars
		$kalium_woocommerce->custom_query_args = $wp_query_args;
		
		// New ids fetched
		$kalium_woocommerce->fetched_new_ids = array();
		
		// Custom query function
		$custom_query_function = array( $kalium_woocommerce, 'customQuery' );
		$fetch_new_ids_function = array( $kalium_woocommerce, 'fetchNewIds' );
		
		// Assign custom query
		add_filter( 'woocommerce_shortcode_products_query', $custom_query_function, 10, 3 );
		add_action( 'woocommerce_before_shop_loop_item', $fetch_new_ids_function, 1 );
		
		// Execute actions before products query
		do_action( 'kalium_woocommerce_infinite_scroll_pagination_before_query' );
		
		// Products
		$products = new WC_Shortcode_Products();
		
		// Products content
		$products_content = $products->get_content();
		
		// Unassign custom query hooks
		remove_filter( 'woocommerce_shortcode_products_query', $custom_query_function, 10, 3 );
		remove_action( 'woocommerce_before_shop_loop_item', $fetch_new_ids_function, 1 );
		
		$response['fetchedItems'] = $kalium_woocommerce->fetched_new_ids;
		$response['items']        = $products_content;
		$response['hasMore']      = count( $fetched_ids ) + count( $kalium_woocommerce->fetched_new_ids ) < $total_items;
		$response['hasItems']     = true;
		
		return $response;
	}
	
	/**
	 * Custom query for pagination
	 */
	public function customQuery( $query ) {
		return array_merge( $query, $this->custom_query_args );
	}
	
	/**
	 * Fetched news ids for pagination
	 */
	public function fetchNewIds() {
		$this->fetched_new_ids[] = get_the_id();
	}
}
