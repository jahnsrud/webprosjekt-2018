<?php
/**
 *	Laborator 1 Click Demo Content Importer
 *
 *	Version: 1.1
 *
 *	Developed by: Arlind
 *	URL: www.laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

define( 'LAB_1CL_DEMO_INSTALLER_PATH', str_replace( ABSPATH, '', dirname( __FILE__ ) . '/' ) );
define( 'LAB_1CL_DEMO_INSTALLER_STYLESHEET', kalium()->locateFileUrl( 'inc/lib/laborator/laborator-demo-content-importer/demo-content-importer.css' ) );
define( 'LAB_1CL_DEMO_INSTALLER_REMOTE_PATH', 'https://demo-content.kaliumtheme.com/' );


// Get Demo Content Packs
function lab_1cl_demo_installer_get_packs() {
	return array(
		array(
			// Pack Info
			'name'           => 'Main Demo',
			'desc'           => 'This will install Kalium main site demo content. It includes all theme features. All images are grey (takes 2-5 mins to install).',

			// Pack Data
			'thumb'          => 'demo-content/main/screenshot.png',
			'file'           => 'demo-content/main/content.xml',
			'products'		 => 'demo-content/main/products.xml',
			'options'        => 'demo-content/main/options.json',
			'layerslider'    => 'demo-content/main/layerslider.zip',
			'revslider'      => array(
									'demo-content/main/revslider-darkslider.zip',
									'demo-content/main/revslider-homepage.zip',
								),
			'custom_css'     => 'demo-content/main/css.json',
			'widgets'     	 => 'demo-content/main/widgets.wie',
			'typolab'     	 => 'demo-content/main/typolab.json',
			
			'frontpage'		 => 3359,
			'postspage'		 => 2937,
			'menus'			 => array( 'main-menu' => 'Main Menu' ),
			
			'requires'		 => array( 'portfolio-post-type' ),
			
			'url'			 => 'https://demo.kaliumtheme.com/main'
		),
		
		array(
			// Pack Info
			'name'           => 'Agency',
			'desc'           => 'This will install Kalium Agency site demo content in grey images format.',

			// Pack Data
			'thumb'          => 'demo-content/agency/screenshot.png',
			'file'           => 'demo-content/agency/content.xml',
			'products'		 => 'demo-content/main/products.xml',
			'options'        => 'demo-content/agency/options.json',
			'layerslider'    => '',
			'revslider'      => '',
			'custom_css'     => 'demo-content/agency/css.json',
			'widgets'     	 => 'demo-content/agency/widgets.wie',
			'typolab'     	 => 'demo-content/agency/typolab.json',
			
			'frontpage'		 => 3379,
			'postspage'		 => 2937,
			'menus'			 => array( 'main-menu' => 'Main Menu' ),
			
			'requires'		 => array( 'portfolio-post-type' ),
			
			'url'			 => 'https://demo.kaliumtheme.com/agency'
		),
		
		array(
			// Pack Info
			'name'           => 'Fashion',
			'desc'           => 'This will install Kalium Fashion site demo content with colored image placeholders.',

			// Pack Data
			'thumb'          => 'demo-content/fashion/screenshot.png',
			'file'           => 'demo-content/fashion/content.xml',
			'products'		 => '',
			'options'        => 'demo-content/fashion/options.json',
			'layerslider'    => '',
			'revslider'      => array(
									'demo-content/fashion/revslider-shop-women.zip',
									'demo-content/fashion/revslider-shop-men.zip',
									'demo-content/fashion/revslider-shop-shoes.zip',
									'demo-content/fashion/revslider-shop-accessories.zip',
									'demo-content/fashion/revslider-homepage-slider.zip',
									'demo-content/fashion/revslider-parallax-3.zip',
									'demo-content/fashion/revslider-homepage-full.zip',
									'demo-content/fashion/revslider-parallax-1.zip',
									'demo-content/fashion/revslider-parallax-2.zip',
								),
			'custom_css'     => 'demo-content/fashion/css.json',
			'widgets'     	 => 'demo-content/fashion/widgets.wie',
			'typolab'     	 => 'demo-content/fashion/typolab.json',
			
			'frontpage'		 => 36,
			'postspage'		 => 10,
			'menus'			 => array( 'main-menu' => 'Main Menu' ),
			
			'requires'		 => array( 'woocommerce' ),
			
			'url'			 => 'https://demo.kaliumtheme.com/fashion'
		),
		
		array(
			// Pack Info
			'name'           => 'Restaurant',
			'desc'           => 'This will install Kalium Restaurant site demo content with colored image placeholders.',

			// Pack Data
			'thumb'          => 'demo-content/restaurant/screenshot.jpg',
			'file'           => 'demo-content/restaurant/content.xml',
			'products'		 => '',
			'options'        => 'demo-content/restaurant/options.json',
			'layerslider'    => '',
			'revslider'      => array(
									'demo-content/restaurant/revslider-homepage.zip',
								),
			'custom_css'     => 'demo-content/restaurant/css.json',
			'widgets'     	 => 'demo-content/restaurant/widgets.wie',
			'typolab'     	 => 'demo-content/restaurant/typolab.json',
			
			'frontpage'		 => 14,
			'postspage'		 => 12,
			'menus'			 => array( 'main-menu' => 'Basic' ),
			
			'requires'		 => array(),
			
			'url'			 => 'https://demo.kaliumtheme.com/restaurant'
		),
		
		array(
			// Pack Info
			'name'           => 'Construction',
			'desc'           => 'This will install Kalium Construction site demo content with colored image placeholders.',

			// Pack Data
			'thumb'          => 'demo-content/construction/screenshot.jpg',
			'file'           => 'demo-content/construction/content.xml',
			'products'		 => '',
			'options'        => 'demo-content/construction/options.json',
			'layerslider'    => '',
			'revslider'      => array(
									'demo-content/construction/revslider-home.zip',
								),
			'custom_css'     => 'demo-content/construction/css.json',
			'widgets'     	 => 'demo-content/construction/widgets.wie',
			'typolab'     	 => 'demo-content/construction/typolab.json',
			
			'frontpage'		 => 14,
			'postspage'		 => 12,
			'menus'			 => array( 'main-menu' => 'Basic' ),
			
			'requires'		 => array( 'portfolio-post-type' ),
			
			'url'			 => 'https://demo.kaliumtheme.com/construction'
		),
		
		array(
			// Pack Info
			'name'           => 'Travel',
			'desc'           => 'This will install Kalium Travel site demo content with colored image placeholders.',

			// Pack Data
			'thumb'          => 'demo-content/travel/screenshot.png',
			'file'           => 'demo-content/travel/content.xml',
			'products'		 => '',
			'options'        => 'demo-content/travel/options.json',
			'layerslider'    => '',
			'revslider'      => array(
									'demo-content/travel/revslider-main-slider.zip',
								),
			'custom_css'     => 'demo-content/travel/css.json',
			'widgets'     	 => 'demo-content/travel/widgets.wie',
			'typolab'     	 => 'demo-content/travel/typolab.json',
			
			'frontpage'		 => 21,
			'postspage'		 => 10,
			'menus'			 => array( 'main-menu' => 'Basic' ),
			
			'requires'		 => array( 'portfolio-post-type' ),
			
			'url'			 => 'https://demo.kaliumtheme.com/travel'
		),
		
		array(
			// Pack Info
			'name'           => 'Architecture',
			'desc'           => 'This will install Kalium Architecture site demo content in grey images format.',

			// Pack Data
			'thumb'          => 'demo-content/architecture/screenshot.png',
			'file'           => 'demo-content/architecture/content.xml',
			'products'		 => 'demo-content/main/products.xml',
			'options'        => 'demo-content/architecture/options.json',
			'layerslider'    => '',
			'revslider'      => array(
									'demo-content/architecture/revslider-architecture.zip',
								),
			'custom_css'     => 'demo-content/architecture/css.json',
			'widgets'     	 => 'demo-content/architecture/widgets.wie',
			'typolab'     	 => 'demo-content/architecture/typolab.json',
			
			'frontpage'		 => 3390,
			'menus'			 => array( 'main-menu' => 'Main Menu' ),
			
			'requires'		 => array( 'portfolio-post-type' ),
			
			'url'			 => 'https://demo.kaliumtheme.com/architecture'
		),
		
		array(
			// Pack Info
			'name'           => 'Photography',
			'desc'           => 'This will install Kalium Photography site demo content in grey images format.',

			// Pack Data
			'thumb'          => 'demo-content/photography/screenshot.png',
			'file'           => 'demo-content/photography/content.xml',
			'products'		 => '',
			'options'        => 'demo-content/photography/options.json',
			'layerslider'    => '',
			'revslider'      => '',
			'custom_css'     => 'demo-content/photography/css.json',
			'widgets'     	 => 'demo-content/photography/widgets.wie',
			'typolab'     	 => 'demo-content/photography/typolab.json',
			
			'frontpage'		 => 3283,
			'postspage'		 => 2937,
			'menus'			 => array( 'main-menu' => 'Main Menu' ),
			
			'requires'		 => array( 'portfolio-post-type' ),
			
			'url'			 => 'https://demo.kaliumtheme.com/photography'
		),
		
		array(
			// Pack Info
			'name'           => 'Landing Page',
			'desc'           => 'This will install Kalium Landing page demo content in original images format.',

			// Pack Data
			'thumb'          => 'demo-content/landing/screenshot.png',
			'file'           => 'demo-content/landing/content.xml',
			'products'		 => '',
			'options'        => 'demo-content/landing/options.json',
			'layerslider'    => '',
			'revslider'      => array( 
									'demo-content/landing/revslider-landing.zip',
									'demo-content/landing/revslider-apple-watch.zip', 
								),
			'custom_css'     => 'demo-content/landing/css.json',
			'widgets'     	 => '',
			'typolab'     	 => 'demo-content/landing/typolab.json',
			
			'frontpage'		 => 2,
			'menus'			 => array( 'main-menu' => 'One Page Menu' ),
			
			'requires'		 => array(),
			
			'url'			 => 'https://demo.kaliumtheme.com/landing'
		),
		
		array(
			// Pack Info
			'name'           => 'Shop',
			'desc'           => 'This will install Kalium Shop site demo content in grey images format.',

			// Pack Data
			'thumb'          => 'demo-content/shop/screenshot.png',
			'file'           => 'demo-content/shop/content.xml',
			'products'		 => '',
			'options'        => 'demo-content/shop/options.json',
			'layerslider'    => '',
			'revslider'      => array(
									'demo-content/shop/revslider-shop_slider.zip'
								),
			'custom_css'     => 'demo-content/shop/css.json',
			'widgets'     	 => 'demo-content/shop/widgets.wie',
			'typolab'     	 => 'demo-content/shop/typolab.json',
			
			'frontpage'		 => 377,
			'menus'			 => array( 'main-menu' => 'Main Menu' ),
			
			'requires'		 => array( 'woocommerce' ),
			
			'url'			 => 'https://demo.kaliumtheme.com/shop'
		),
		
		array(
			// Pack Info
			'name'           => 'Education',
			'desc'           => 'This will install Kalium Education site demo content in grey images format.',

			// Pack Data
			'thumb'          => 'demo-content/education/screenshot.png',
			'file'           => 'demo-content/education/content.xml',
			'products'		 => '',
			'options'        => 'demo-content/education/options.json',
			'layerslider'    => '',
			'revslider'      => array( 
									'demo-content/education/revslider-contact.zip', 
									'demo-content/education/revslider-blog.zip', 
									'demo-content/education/revslider-homepage-slider.zip', 
									'demo-content/education/revslider-courses.zip', 
									'demo-content/education/revslider-news.zip',
								),
			'custom_css'     => 'demo-content/education/css.json',
			'widgets'     	 => 'demo-content/education/widgets.wie',
			'typolab'     	 => 'demo-content/education/typolab.json',
			
			'frontpage'		 => 20,
			'postspage'		 => 8,
			'menus'			 => array( 'main-menu' => 'Main Menu' ),
			
			'requires'		 => array( 'portfolio-post-type' ),
			
			'url'			 => 'https://demo.kaliumtheme.com/education',
			'new'			 => true
		),
		
		array(
			// Pack Info
			'name'           => 'Fitness',
			'desc'           => 'This will install Kalium Fitness site demo content in grey images format.',

			// Pack Data
			'thumb'          => 'demo-content/fitness/screenshot.png',
			'file'           => 'demo-content/fitness/content.xml',
			'products'		 => '',
			'options'        => 'demo-content/fitness/options.json',
			'layerslider'    => '',
			'revslider'      => array( 
									'demo-content/fitness/revslider-membership.zip', 
									'demo-content/fitness/revslider-homepage.zip' 
								),
			'custom_css'     => 'demo-content/fitness/css.json',
			'widgets'     	 => 'demo-content/fitness/widgets.wie',
			'typolab'     	 => 'demo-content/fitness/typolab.json',
			
			'frontpage'		 => 20,
			'postspage'		 => 13,
			'menus'			 => array( 'main-menu' => 'Main Menu' ),
			
			'requires'		 => array( 'portfolio-post-type', 'woocommerce' ),
			
			'url'			 => 'https://demo.kaliumtheme.com/fitness',
			'new'			 => true
		),
		
		array(
			// Pack Info
			'name'           => 'Freelancer',
			'desc'           => 'This will install Kalium Freelancer site demo content in grey images format.',

			// Pack Data
			'thumb'          => 'demo-content/freelancer/screenshot.png',
			'file'           => 'demo-content/freelancer/content.xml',
			'products'		 => 'demo-content/main/products.xml',
			'options'        => 'demo-content/freelancer/options.json',
			'layerslider'    => '',
			'revslider'      => '',
			'custom_css'     => 'demo-content/freelancer/css.json',
			'widgets'     	 => 'demo-content/freelancer/widgets.wie',
			'typolab'     	 => 'demo-content/freelancer/typolab.json',
			
			'frontpage'		 => 3283,
			'postspage'		 => 2937,
			'menus'			 => array( 'main-menu' => 'Main Menu' ),
			
			'requires'		 => array( 'portfolio-post-type' ),
			
			'url'			 => 'https://demo.kaliumtheme.com/freelancer'
		),
		
		array(
			// Pack Info
			'name'           => 'Blogging',
			'desc'           => 'This will install Kalium Blogging site demo content in grey images format.',

			// Pack Data
			'thumb'          => 'demo-content/blogging/screenshot.png',
			'file'           => 'demo-content/blogging/content.xml',
			'products'		 => 'demo-content/main/products.xml',
			'options'        => 'demo-content/blogging/options.json',
			'layerslider'    => '',
			'revslider'      => '',
			'custom_css'     => 'demo-content/blogging/css.json',
			'widgets'     	 => 'demo-content/blogging/widgets.wie',
			'typolab'     	 => 'demo-content/blogging/typolab.json',
			
			'menus'			 => array( 'main-menu' => 'Main Menu' ),
			
			'url'			 => 'https://demo.kaliumtheme.com/blogging'
		),
	);
}


// Importer Page
add_action( 'admin_menu', 'lab_1cl_demo_installer_menu' );

function lab_1cl_demo_installer_menu() {
	wp_register_style( 'lab-1cl-demo-installer', LAB_1CL_DEMO_INSTALLER_STYLESHEET, null, kalium()->getVersion() );
	wp_enqueue_style( 'lab-1cl-demo-installer' );
}

function lab_1cl_demo_installer_page() {
	
	// Change Media Download Status
	if (isset($_POST['lab_change_media_status'] ) ) {
		update_option('lab_1cl_demo_installer_download_media', kalium()->post('lab_1cl_demo_installer_download_media') ? true : false);
	}

	$lab_demo_content_url = site_url( str_replace( ABSPATH, '', dirname( __FILE__ ) ) . '/' );
	$lab_demo_content_url  = kalium()->locateFileUrl( 'inc/lib/laborator/laborator-demo-content-importer/' );

	wp_enqueue_script( 'thickbox' );
	wp_enqueue_style( 'thickbox' );

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	include 'demo-content-page.php';
}


function lab_1cl_demo_installer_get_pack($name) {
	foreach(lab_1cl_demo_installer_get_packs() as $pack) {
		if (sanitize_title($pack['name']) == $name) {
			return $pack;
		}
	}

	return null;
}


// Import Content Pack
function lab_1cl_demo_installer_admin_init() {

	if ( kalium()->url->get( 'page' ) == 'laborator-demo-content-installer' && ( $pack_name = kalium()->url->get( 'install-pack' ) ) ) {
		$pack = lab_1cl_demo_installer_get_pack( $pack_name );

		if ( $pack ) {
			if ( is_plugin_active( 'wordpress-importer/wordpress-importer.php' ) ) {
				deactivate_plugins( array( 'wordpress-importer/wordpress-importer.php' ) );
				update_option( 'lab_should_activate_wp_importer', true );

				wp_redirect( admin_url( 'admin.php?page=laborator-demo-content-installer&install-pack="' . sanitize_title( $pack_name ) ) );
				exit;
			}

			require 'demo-content-install-pack.php';
			die();
		}
	}
}

add_action( 'admin_init', 'lab_1cl_demo_installer_admin_init' );


// Save Custom CSS Options
function lab_1cl_demo_installer_custom_css_save($custom_css_vars) {
	foreach ( $custom_css_vars as $var_name => $value ) {
		update_option( $var_name, $value );
	}
}


// Get Packpage Contents to Install
function lab_1cl_demo_installer_pack_content_types($pack) {
	$active_plugins = apply_filters( 'active_plugins', get_option('active_plugins' ) );
	
	$full_path = ABSPATH . LAB_1CL_DEMO_INSTALLER_PATH;
	$content_packs = array();
	
	// WP Content
	if (isset($pack['file']) && $pack['file']) {
		$xml_file_size = '';
		
		$file_content_pack = array(
			'type'           => 'xml-wp-content',
			'title'          => 'WordPress Content',
			'description'    => 'This will import posts, pages, comments, custom fields, terms, navigation menus and custom posts.',
			'checked'		 => isset($pack['file_checked']) ? $pack['file_checked'] : true,
			'requires'		 => array(),
			'size'           => size_format($xml_file_size, 2)
		);
		
		if (isset($pack['requires']) && is_array($pack['requires']) && count($pack['requires'] ) ) {
			// Portfolio Post Type Plugin
			if ( in_array( 'portfolio-post-type', $pack['requires'] ) ) {
				$portfolio_plugin = 'portfolio-post-type/portfolio-post-type.php';
				
				if ( ! in_array( $portfolio_plugin, $active_plugins ) && ! is_plugin_active_for_network( $portfolio_plugin ) ) {
					$file_content_pack['checked'] = false;
					$file_content_pack['disabled'] = true;
					$file_content_pack['requires']['portfolio-post-type'] = 'This content pack includes portfolio items which requires Portfolio Post Type plugin, to install it go to <a href="'.esc_url(admin_url("themes.php?page=kalium-install-plugins" ) ).'" target="_blank">Appearance &raquo; Install Plugins</a> and then refresh this page.';
				}
			}
			
			// WooCommerce Plugin
			if (in_array('woocommerce', $pack['requires'] ) ) {
				$woocommerce_plugin = 'woocommerce/woocommerce.php';
				
				if ( ! in_array( $woocommerce_plugin, $active_plugins ) && ! is_plugin_active_for_network( $woocommerce_plugin ) ) {
					$file_content_pack['checked'] = false;
					$file_content_pack['disabled'] = true;
					$file_content_pack['requires']['woocommerce'] = 'This content pack includes shop products which requires WooCommerce plugin, to install it go to <a href="'.esc_url(admin_url("themes.php?page=kalium-install-plugins" ) ).'" target="_blank">Appearance &raquo; Install Plugins</a> and then refresh this page.';
				}
			}
		}
		
		$content_packs[] = $file_content_pack;
		
		// Download Media Attachments
		if ( ! isset($file_content_pack['disabled'] ) ) {
			$content_packs[] = array(
				'type'           => 'xml-wp-download-media',
				'title'          => 'Media Files',
				'description'    => 'This will download all media files presented in this demo content pack. Note: Images are in grey format.',
				'checked'		 => $file_content_pack['checked'],
				'requires'		 => array(),
				'size'           => ''
			);
		}
	}
	
	// Typolab
	if ( isset( $pack['typolab'] ) && $pack['typolab'] ) {
		$content_packs[] = array(
			'type'           => 'typolab',
			'title'          => 'Typography',
			'description'    => 'This will import font families and font sizes of this demo content.',
			'checked'		 => isset( $pack['typolab_checked'] ) ? $pack['typolab_checked'] : true,
			'disabled'		 => false,
			'requires'		 => array(),
			'size'           => 0
		);
	}
	
	// Widgets
	if ( isset( $pack['widgets'] ) && $pack['widgets'] ) {
		$content_packs[] = array(
			'type'           => 'widgets',
			'title'          => 'Widgets',
			'description'    => 'This will import default widgets presented in demo site of this content package.',
			'checked'		 => isset( $pack['widgets_checked'] ) ? $pack['widgets_checked'] : true,
			'disabled'		 => false,
			'requires'		 => array(),
			'size'           => 0
		);
	}
	
	// WooCommerce Products
	if ( isset($pack['products'] ) && $pack['products'] ) {
		$products_content_pack = array(
			'type'           => 'xml-products',
			'title'          => 'WooCommerce Products',
			'description'    => 'This will import default WooCommerce shop products and categories.',
			'checked'		 => isset( $pack['products_checked'] ) ? $pack['products_checked'] : false,
			'requires'		 => array(),
			'size'           => ''
		);
		
		$woocommerce_plugin = 'woocommerce/woocommerce.php';
		
		if ( ! in_array( $woocommerce_plugin, apply_filters( 'active_plugins', get_option('active_plugins' ) ) ) && ! is_plugin_active_for_network( $woocommerce_plugin ) ) {
			$products_content_pack['disabled'] = true;
			$products_content_pack['checked'] = false;
			$products_content_pack['requires']['woocommerce'] = 'This content pack includes shop products which requires WooCommerce plugin, to install it go to <a href="'.esc_url(admin_url("themes.php?page=kalium-install-plugins" ) ).'" target="_blank">Appearance &raquo; Install Plugins</a> and then refresh this page.';
		}
		
		$content_packs[] = $products_content_pack;
	}
	
	// Theme Options
	if ( isset( $pack['options'] ) && $pack['options'] ) {
		$theme_options_size = '';
		
		$content_packs[] = array(
			'type'           => 'theme-options',
			'title'          => 'Theme Options',
			'description'    => 'This will import theme options and will rewrite all current settings in Appearance &raquo; Theme Options.',
			'checked'		 => isset( $pack['options_checked'] ) ? $pack['options_checked'] : true,
			'requires'		 => array(),
			'size'           => size_format( $theme_options_size, 2 )
		);
	}
	
	// Custom CSS
	if (isset($pack['custom_css']) && $pack['custom_css']) {
		$custom_css_size = '';
		
		$content_packs[] = array(
			'type'           => 'custom-css',
			'title'          => 'Custom CSS',
			'description'    => 'This content pack contains custom styling which can be later accessed in <a href="'.esc_url(admin_url("admin.php?page=laborator_custom_css" ) ).'" target="_blank">Custom CSS</a>.',
			'checked'		 => isset($pack['custom_css_checked']) ? $pack['custom_css_checked'] : true,
			'requires'		 => array(),
			'size'           => size_format( $custom_css_size, 2 )
		);
	}
	
	// Revolution Slider
	if ( isset( $pack['revslider'] ) && $pack['revslider'] ) {
		$revslider_size = '';
		$revslider_activated = in_array( 'revslider/revslider.php', $active_plugins );
		
		$content_packs[] = array(
			'type'           => 'revslider',
			'title'          => 'Revolution Slider',
			'description'    => 'This will import all sliders presented in demo site of this content package.',
			'checked'		 => $revslider_activated ? ( isset( $pack['revslider_checked'] ) ? $pack['revslider_checked'] : true ) : false,
			'disabled'		 => ! $revslider_activated,
			'requires'		 => array(
				'revslider' => 'To import Revolution Slider content you must install and activate this plugin in <a href="' . esc_url( admin_url( 'themes.php?page=kalium-install-plugins' ) ) . '" target="_blank">Appearance &raquo; Install Plugins</a> and then refresh this page.'
			),
			'size'           => size_format( $revslider_size, 2 )
		);
	}
	
	// Layer Slider
	if ( isset( $pack['layerslider'] ) && $pack['layerslider'] ) {
		$layerslider_size = '';
		$layerslider_activated = in_array( 'LayerSlider/layerslider.php', $active_plugins );
		
		$content_packs[] = array(
			'type'           => 'layerslider',
			'title'          => 'Layer Slider',
			'description'    => 'This will import all sliders presented in demo site of this content package.',
			'checked'		 => isset( $pack['layerslider_checked'] ) ? $pack['layerslider_checked'] : false,
			'disabled'		 => ! $layerslider_activated,
			'requires'		 => array(
				'layerslider' => 'To import Layer Slider content you must install and activate this plugin in <a href="' . esc_url( admin_url( 'themes.php?page=kalium-install-plugins' ) ) . '" target="_blank">Appearance &raquo; Install Plugins</a> and then refresh this page.'
			),
			'size'           => size_format( $layerslider_size, 2 )
		);
	}
	
	return $content_packs;
}


// Import Content Pack
add_action('wp_ajax_lab_1cl_demo_install_package_content', 'lab_1cl_demo_install_package_content');

function lab_1cl_demo_install_package_content() {
	$resp = array(
		'success' => false,
		'errorMsg' => ''
	);
	
	$pack_name = kalium()->url->get( 'pack' );
	$source_details = kalium()->url->get( 'contentSourceDetails' );
	
	$pack = lab_1cl_demo_installer_get_pack($pack_name);
	
	// Content Source Install by Type
	switch ( $source_details['type'] ) {
		case 'xml-wp-content':
		case 'xml-products':
		
			// Run wordpress importer independently
			if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
				define( 'WP_LOAD_IMPORTERS', true );
				require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'wordpress-importer/wordpress-importer.php';
			}
			
			// Demo Content File (XML)
			if ( $source_details['type'] == 'xml-products' ) {
				$xml_file = lab_1cl_demo_installer_get_file_from_path( $pack['products'] );
			} else {
				$xml_file = lab_1cl_demo_installer_get_file_from_path( $pack['file'] );
			}

			try {			
				@set_time_limit(0);
	
				$wp_importer = new WP_Import();
				
				$wp_importer->fetch_attachments = isset( $source_details['downloadMedia'] ) && $source_details['downloadMedia'];
				$wp_importer->id = sanitize_title( $pack['name'] );
				
				// Download Shop Attachments by Default
				if ( $source_details['type'] == 'xml-products' ) {
					$wp_importer->fetch_attachments = true;
				}
				
				ob_start();
				$wp_importer->import( $xml_file );
				$content = ob_get_clean();
				
				$resp['imp'] = $wp_importer;
				$resp['success'] = true;
				
				// Small but important stuff to setup
				if ( $source_details['type'] == 'xml-wp-content' ) {
					
					// Set Frontpage and Posts page
					if ( isset( $pack['frontpage'] ) || isset( $pack['postspage'] ) ) {
						update_option( 'show_on_front', 'page' );
						
						if ( isset( $pack['frontpage'] ) ) {
							update_option( 'page_on_front', $pack['frontpage'] );
						}
						
						if ( isset( $pack['postspage'] ) ) {
							update_option( 'page_for_posts', $pack['postspage'] );
						}
					}
				
					// Menus
					if ( isset( $pack['menus'] ) && is_array( $pack['menus'] ) ) {
						$nav_menus = wp_get_nav_menus();
						
						foreach( $pack['menus'] as $menu_location => $menu_name ) {
						
							if ( is_array( $nav_menus ) ) {
		
								foreach( $nav_menus as $term ) {
									
									if ( strtolower( $menu_name ) == strtolower( $term->name ) ) {
										$nav_menu_locations = get_theme_mod( 'nav_menu_locations' );
										
										if ( ! is_array( $nav_menu_locations ) ) {
											$nav_menu_locations = array();
										}
										
										$nav_menu_locations[$menu_location] = $term->term_id;
										set_theme_mod( 'nav_menu_locations', $nav_menu_locations );
									}
								}
							}
						}
					}
					
					// Flush rewrite rules
					flush_rewrite_rules( true );
				}
			} catch( Exception $e ) {
				$resp['errorMsg'] = $e;
			}
			
			break;
		
		case "theme-options":
			
			$theme_options = lab_1cl_demo_installer_get_file_from_path( $pack['options'] );
			
			try {
				if ( $theme_options = file_get_contents( $theme_options ) ) {
					$smof_data = unserialize( base64_decode( $theme_options ) );
	
					$ignore_keys = array( '0', 'backups', 'license', 'theme_license_last_validation', 'nav_menu_locations', 'permalink_structure' );
					
					foreach ( $ignore_keys as $key_name ) {
						if ( @isset( $smof_data[ $key_name ] ) ) {
							unset( $smof_data[ $key_name ] );
						}
					}
					
					// Backup Nav Locations
					$nav_menu_locations = get_theme_mod( 'nav_menu_locations' );
					
					// Save Theme Options
					of_save_options( apply_filters( 'of_options_before_save', $smof_data ) );
					
					// Restore Nav Locations
					set_theme_mod( 'nav_menu_locations', $nav_menu_locations );
					
					$resp['success'] = true;
				} else {
					$resp['errorMsg'] = 'Invalid data serialization for Theme Options. Required format: Base64 Encoded';
				}
			} catch( Exception $e ) {
				$resp['errorMsg'] = $e;
			}
			
			break;
		
		case "custom-css":
			
			$custom_css = $pack['custom_css'];
			
			if ( $custom_css ) {
				$custom_css = lab_1cl_demo_installer_get_file_from_path( $custom_css );
			
				try {
					if ($custom_css = file_get_contents( $custom_css ) ) {
						$custom_css_options = json_decode( base64_decode( $custom_css ) );
						
						lab_1cl_demo_installer_custom_css_save( $custom_css_options );
						
						$resp['success'] = true;
					}
				}
				catch( Exception $e ) {
					$resp['errorMsg'] = $e;
				}
			}
			
			break;
		
		case "typolab":
			$typolab = lab_1cl_demo_installer_get_file_from_path( $pack['typolab'] );
			$typolab = maybe_unserialize( base64_decode( file_get_contents( $typolab ) ) );
			
			if ( ! is_string( $typolab ) ) {
				include_once( kalium()->locateFile( 'inc/lib/laborator/typolab/inc/classes/typolab-font-export-import.php' ) );
				$export_import_manager = new TypoLab_Font_Export_Import();
				
				if ( $export_import_manager->import( $typolab ) ) {
					$resp['success'] = true;
				} else {
					$resp['errorMsg'] = 'Typography import failed!';
				}
				
			} else {
				$resp['errorMsg'] = "Typography file doesn't contain valid export code!";
			}
			break;
		
		case "widgets":
			
			$widgets = lab_1cl_demo_installer_get_file_from_path( $pack['widgets'] );
			
			if ( ! function_exists( 'wie_process_import_file' ) ) {
				require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'widget-importer-exporter/widget-importer-exporter.php';
			}
			
			try {
				wie_process_import_file( $widgets );
				$resp['success'] = true;
			} catch( Exception $e ) {
				$resp['errorMsg'] = $e;
			}
			
			break;
		
		case "revslider":
		
			try {
				// Import Revolution Slider
				if ( $pack['revslider'] && class_exists( 'RevSlider' ) ) {
					if ( ! is_array( $pack['revslider'] ) ) {
						$pack['revslider'] = explode( ',', $pack['revslider'] );
					}
					
					foreach ( $pack['revslider'] as $revslider_path ) {
											
						$revslider = lab_1cl_demo_installer_get_file_from_path( $revslider_path );
						
						$rev = new RevSlider();
						
						ob_start();
						$rev->importSliderFromPost( true, true, $revslider );
						$content = ob_get_clean();
					}
					
					$resp['success'] = true;
				} else {
					$resp['errorMsg'] = 'Revolution Slider is not installed/activated and thus this content source couldn\'t be imported!';
				}
			} catch ( Exception $e ) {
				$resp['errorMsg'] = $e;
			}
			
			break;
		
		case "layerslider":
		
			try {
				// Import Layer Slider
				if ( $pack['layerslider'] && function_exists( 'ls_import_sliders' ) ) {
					$layerslider = lab_1cl_demo_installer_get_file_from_path( $pack['layerslider'] );
				
					include LS_ROOT_PATH . '/classes/class.ls.importutil.php';
				
					$import = new LS_ImportUtil( $layerslider, basename( $layerslider ) );
					
					$resp['success'] = true;
				} else {
					$resp['errorMsg'] = 'Layer Slider is not installed/activated and thus this content source couldn\'t be imported!';
				}
			}
			catch ( Exception $e ) {
				$resp['errorMsg'] = $e;
			}
			
			break;
	}
	
	
	echo json_encode( $resp );
	die();
}

function lab_1cl_demo_installer_get_file_from_path( $file_path ) {
	$local_path    = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
	$full_path     = $local_path . $file_path;
	$remote_path   = LAB_1CL_DEMO_INSTALLER_REMOTE_PATH . $file_path;
	
	if ( file_exists( $full_path ) ) {
		return $full_path;
	}
	
	try {
		$response = wp_remote_get( $remote_path );
		file_put_contents( $full_path, wp_remote_retrieve_body( $response ) );
	} catch( Exception $error ) {
		$full_path = download_url( $remote_path );
		
	}
	return download_url( $remote_path );
		
}