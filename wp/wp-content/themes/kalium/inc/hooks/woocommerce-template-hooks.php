<?php
/**
 *	Kalium WordPress Theme
 *
 *	WooCommerce Template Hooks
 *	
 *	Laborator.co
 *	www.laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * WooCommerce init
 */
add_action( 'woocommerce_init', 'kalium_woocommerce_init' );

/**
 * Disable WooCommerce styles
 */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * Archive wrapper
 */
add_action( 'woocommerce_before_main_content', 'kalium_woocommerce_archive_wrapper_start', 20 );
add_action( 'woocommerce_after_main_content', 'kalium_woocommerce_archive_wrapper_end', 5 );

/**
 * Results count and archive description
 */
add_action( 'woocommerce_before_main_content', 'kalium_display_woocommerce_archive_description', 10 );
add_action( 'kalium_woocommerce_archive_description', 'woocommerce_result_count', 20 );

/**
 * Remove certain actions from shop archive page
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

/**
 * Remove default result counter and products order dropdown
 */
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

/**
 * Remove default product details added by WooCommerce
 */
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

/**
 * Remove Link from Products
 */
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

/**
 * Single product wrapper
 */
add_action( 'woocommerce_before_single_product', 'kalium_woocommerce_single_product_wrapper_start', 1 );
add_action( 'woocommerce_after_single_product', 'kalium_woocommerce_single_product_wrapper_end', 1000 );

/**
 * Change the order of product details on single page
 */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 29 );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 21 );

/**
 * Site wide store notice
 */
#remove_action( 'wp_footer', 'woocommerce_demo_store' );
#add_action( 'kalium_before_header', 'woocommerce_demo_store', 10 );

/**
 * Archive products header
 */
add_action( 'woocommerce_before_main_content', 'kalium_woocommerce_archive_header', 15 );
add_action( 'kalium_woocommerce_archive_header', 'kalium_woocommerce_archive_header_display', 10, 2 );

/**
 * Product loop start
 */
remove_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' );
add_filter( 'woocommerce_before_shop_loop', 'kalium_woocommerce_maybe_show_product_categories' );

/**
 * Add "shop-categories" class for products container ([product_categories])
 */
add_filter( 'do_shortcode_tag', 'kalium_woocommerce_product_categories_shortcode_wrap', 100, 2 );

/**
 * Replace category thumbnail in shop loop
 */
remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );
add_action( 'woocommerce_before_subcategory_title', 'kalium_woocommerce_subcategory_thumbnail', 10 );

/**
 * Loop product images
 */
add_action( 'woocommerce_before_shop_loop_item_title', 'kalium_woocommerce_catalog_loop_thumbnail', 10 );

/**
 * Loop pagination args
 */
add_filter( 'woocommerce_pagination_args', 'kalium_woocommerce_pagination_args', 10 );

/**
 * Remove Product Description
 */
add_filter( 'woocommerce_product_description_heading', '__return_empty_string' );
		
/**
 * Infinite pagination setup
 */
add_action( 'woocommerce_after_shop_loop', 'kalium_woocommerce_infinite_scroll_pagination', 9 );

/**
 * My Account Wrapper
 */
add_action( 'woocommerce_before_my_account', 'kalium_woocommerce_before_my_account' );
add_action( 'woocommerce_after_my_account', 'kalium_woocommerce_after_my_account' );

/**
 * Support multi currency in AJAX mode for paged products page
 */
add_filter( 'wcml_multi_currency_ajax_actions', 'kalium_wcml_multi_currency_ajax_actions' );

/**
 * Double variation image fix
 */
add_filter( 'woocommerce_available_variation', 'kalium_woocommerce_variation_remove_featured_image', 1, 2 );

/**
 * Review rating
 */
add_action( 'woocommerce_product_get_rating_html', 'kalium_woocommerce_product_get_rating_html', 10, 3 );

/**
 * Product rating
 */
add_action( 'kalium_woocommerce_single_product_rating_stars', 'kalium_woocommerce_single_product_rating_stars', 10 );

/**
 * Payment method title
 */
add_action( 'woocommerce_review_order_before_payment', 'kalium_woocommerce_review_order_before_payment_title', 10 );

/**
 * Review product form
 */
add_filter( 'woocommerce_product_review_comment_form_args', 'kalium_woocommerce_product_review_comment_form_args', 10 );

/**
 * Login page heading
 */
add_action( 'woocommerce_before_customer_login_form', 'kalium_woocommerce_my_account_login_page_heading', 10 );

/**
 * Add to cart link (loop)
 */
add_filter( 'woocommerce_loop_add_to_cart_link', 'kalium_woocommerce_loop_add_to_cart_link', 10, 3 );

/**
 * Cart fragments
 */
add_filter( 'woocommerce_add_to_cart_fragments', 'kalium_woocommerce_woocommerce_add_to_cart_fragments', 10 );

/**
 * Account navigation
 */
add_action( 'woocommerce_before_account_navigation', 'kalium_woocommerce_before_account_navigation' );
add_action( 'woocommerce_after_account_navigation', 'kalium_woocommerce_after_account_navigation' );

/**
 * Orders and downloads page titles
 */
add_action( 'woocommerce_before_account_orders', 'kalium_woocommerce_before_account_orders', 10 );
add_action( 'woocommerce_before_account_downloads', 'kalium_woocommerce_before_account_downloads', 10 );

/**
 * WooCommerce fields
 */
add_filter( 'woocommerce_form_field_args', 'kalium_woocommerce_woocommerce_form_field_args', 10, 3 );

/**
 * Related products and Upsells columns count
 */
add_filter( 'woocommerce_related_products_columns', 'kalium_woocommerce_related_products_columns', 10 );
add_filter( 'woocommerce_upsells_columns', 'kalium_woocommerce_related_products_columns', 10 );

/**
 * Related products to show
 */
add_filter( 'woocommerce_output_related_products_args', 'kalium_woocommerce_related_products_args', 10 );

/**
 * Return to shop after cart item adding (option enabled in Woo)
 */
add_filter( 'woocommerce_continue_shopping_redirect', 'kalium_woocommerce_continue_shopping_redirect_to_shop', 10 );

/**
 * Replace cart remove link icon
 */
add_filter( 'woocommerce_cart_item_remove_link', 'kalium_woocommerce_woocommerce_cart_item_remove_link' );

/**
 * Bacs details
 */
add_action( 'woocommerce_thankyou_bacs', 'kalium_woocommerce_bacs_details_before', 1 );
add_action( 'woocommerce_thankyou_bacs', 'kalium_woocommerce_bacs_details_after', 100 );

/**
 * Show rating below top rated products widget
 */
add_action( 'woocommerce_widget_product_item_end', 'kalium_woocommerce_top_rated_products_widget_rating', 10 );

/**
 * Ordering dropdown for products loop
 */
add_filter( 'kalium_woocommerce_shop_loop_ordering', 'kalium_woocommerce_shop_loop_ordering_dropdown', 10, 2 );

/**
 * Single product images wrapper  
 */
add_filter( 'woocommerce_before_single_product_summary', 'kalium_woocommerce_single_product_images_wrapper_start', 2 );
add_filter( 'woocommerce_before_single_product_summary', 'kalium_woocommerce_single_product_images_wrapper_end', 1000 );

/**
 * Product flash badges
 */
add_action( 'kalium_woocommerce_product_badge', 'kalium_woocommerce_product_badges', 10, 2 );
