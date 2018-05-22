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


// Base Functionality
function laborator_init() {
	$version = kalium()->getVersion();
	
	// Disable resource caching when version debug is enabled
	if ( defined( 'KALIUM_VERSION_DEBUG' ) ) {
		$version .= sprintf( '.%d', date( 'dhi' ) );
	}
	
	// Styles
	wp_register_style( 'bootstrap', kalium()->assetsUrl( 'css/bootstrap.css' ), null, null );
	wp_register_style( 'main', kalium()->assetsUrl( 'css/main.css' ), null, $version );

	// Icons used in Admin
	wp_register_style( 'font-awesome', kalium()->assetsUrl( 'css/fonts/font-awesome/font-awesome-admin.css' ), null, $version );
	wp_register_style( 'font-flaticons', kalium()->assetsUrl( 'css/fonts/flaticons-custom/flaticon-admin.css' ), null, $version );
	wp_register_style( 'font-lineaicons', kalium()->assetsUrl( 'css/fonts/linea-iconfont/linea-iconfont-admin.css' ), null, $version );

	wp_register_style( 'style', kalium()->locateFileUrl( 'style.css' ), null, $version );


	// Scripts
	wp_register_script( 'bootstrap', kalium()->assetsUrl( 'js/bootstrap.min.js' ), null, null, true );
	wp_register_script( 'kalium-main', kalium()->assetsUrl( 'js/main.min.js' ), null, $version, false );
		

		// Nivo Lightbox
		if ( apply_filters( 'kalium_enable_nivo_lightbox', true ) ) {
			wp_register_script( 'nivo-lightbox', kalium()->assetsUrl( 'js/nivo-lightbox/nivo-lightbox.min.js' ), null, null, true );
			wp_register_style( 'nivo-lightbox', kalium()->assetsUrl( 'js/nivo-lightbox/nivo-lightbox.min.css' ), null, null );
			wp_register_style( 'nivo-lightbox-default', kalium()->assetsUrl( 'js/nivo-lightbox/themes/default/default.css' ), array( 'nivo-lightbox' ), null);
		}
		
		// Slick Carousel
		wp_register_script( 'slick', kalium()->assetsUrl( 'js/slick/slick.min.js' ), null, null, true );
		wp_register_style( 'slick', kalium()->assetsUrl( 'js/slick/slick.css' ), null, null );
		
		// Fluid Box
		if ( apply_filters( 'kalium_enable_fluidbox', true ) ) {
			wp_register_script( 'fluidbox', kalium()->assetsUrl( 'js/fluidbox/jquery.fluidbox.min.js' ), null, null, true );
			wp_register_style( 'fluidbox', kalium()->assetsUrl( 'js/fluidbox/css/fluidbox.min.css' ), null, null );
		}
		
		// Light Gallery
		if ( apply_filters( 'kalium_enable_lightgallery', true ) ) {
			wp_register_script( 'light-gallery', kalium()->assetsUrl( 'js/light-gallery/lightgallery-all.min.js' ), null, $version, true );
			
			wp_register_style( 'light-gallery', kalium()->assetsUrl( 'js/light-gallery/css/lightgallery.min.css' ), null, $version );
			wp_register_style( 'light-gallery-transitions', kalium()->assetsUrl( 'js/light-gallery/css/lg-transitions.min.css' ), $version, null );
		}
		
		
		// Admin JS & CSS
		wp_register_script( 'admin-js', kalium()->assetsUrl( 'js/admin/main.min.js' ), null, null );
		wp_register_style( 'admin-css', kalium()->assetsUrl( 'css/admin/main.css' ), null, $version );
		
		// Product Activation Library
		wp_register_script( 'laborator-product-activation', kalium()->assetsUrl( 'js/admin/laborator-product-activation.min.js' ), null, null );
		
		// CSS Loaders
		wp_register_style( 'css-loaders', kalium()->assetsUrl( 'css/admin/css-loaders.css' ), null, $version );


	// Google Maps
	$google_api_key = kalium_get_google_api();
	
	// Google API Key for ACF
	add_filter( 'acf/fields/google_map/api', 'kalium_google_api_key_acf', 10 );
	
	if ( false == is_admin() ) {
		wp_register_script( 'google-maps', '//maps.googleapis.com/maps/api/js?key=' . $google_api_key, null, null, true );
	}
}

add_action( 'init', 'laborator_init' );


// Theme Demo
$theme_demo_file = kalium()->locateFile( 'theme-demo/theme-demo.php' );

if ( file_exists( $theme_demo_file ) && is_readable( $theme_demo_file ) ) {
	require_once $theme_demo_file;
}

// Setup post data for archives
function kalium_setup_postdata_for_pages() {
	setup_postdata( get_queried_object() );
}

add_action( 'template_redirect', 'kalium_setup_postdata_for_pages' );


// Enqueue Scritps and other stuff
function laborator_wp_enqueue_scripts() {
	
	// Styles
	wp_enqueue_style( 'bootstrap' );
	wp_enqueue_style( 'main' );
	
	// Somebody don't want to include style.css of the theme
	if( get_data( 'do_not_enqueue_style_css' ) != true ) {
		wp_enqueue_style( 'style' );
	}

	// Scripts
	wp_enqueue_script( array( 'jquery' ) );

	// Custom Skin
	if ( get_data( 'use_custom_skin' ) ) {
		if ( false == apply_filters( 'kalium_use_filebased_custom_skin', kalium_use_filebased_custom_skin() ) ) {
			if ( '' != get_option( 'permalink_structure' ) && true != get_data( 'theme_skin_include_alternate' ) ) {
				wp_enqueue_style( 'custom-skin', home_url( 'skin.css' ), null, null );
			} else {
				wp_enqueue_style( 'custom-skin', home_url( '?custom-skin=1' ), null, null );
			}
		}
	}
	
	// Single Post
	if ( is_single() ) {
		wp_enqueue_script( array( 'fluidbox' ) );
		wp_enqueue_style( 'fluidbox' );
		
		if ( comments_open() ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	/* Removed in 2.2
	// Fonts
	laborator_load_font(); // (Deprecated)
	*/
}

add_action( 'wp_enqueue_scripts', 'laborator_wp_enqueue_scripts' );


// Custom Skin (used only
function kalium_custom_skin_parse_css() {
	$kalium_skin_custom_css = get_option( 'kalium_skin_custom_css' );
	
	header( 'Content-Type: text/css; charset: UTF-8' );
	header( 'Content-Length: ' . strlen( $kalium_skin_custom_css ) );
	header( 'Cache-Control: public, max-age=2592000' );
	header( 'Date: ' . gmdate( 'D, d M Y H:i:s \G\M\T', time() ) );
	header( 'Expires: ' . gmdate( 'D, d M Y H:i:s \G\M\T', time() + ( 60 * 60 * 24 * 30 ) ) );
	header( 'ETag: ' . md5( $kalium_skin_custom_css ) );
	header( 'X-Cache: HIT' );
	
	echo $kalium_skin_custom_css;
	die();
}

if ( isset( $_GET['custom-skin'] ) ) {
	kalium_custom_skin_parse_css();
}


// Custom Skin for Pretty Permalinks
function lab_custom_skin_rewrite() {
	global $wp_rewrite;
	
	if ( '' !== $wp_rewrite->permalink_structure ) {	
		add_rewrite_rule( 'skin.css', 'index.php?custom-skin=use', 'top' );
		add_rewrite_tag( '%custom-skin%', 'use' );
	}
}

add_action( 'init', 'lab_custom_skin_rewrite', 10 );


// Show Custom Skin on Pretty Permalinks
function lab_custom_skin_template_redirect() {
	global $wp_rewrite;
	
	if ( '' !== $wp_rewrite->permalink_structure ) {	
		
		if ( ! in_array( 'skin.css', array_keys( $wp_rewrite->rules ) ) ) {
			flush_rewrite_rules();
		}
		
		if ( get_query_var( 'custom-skin' ) == 'use' ) {
			kalium_custom_skin_parse_css();
		}
	}
}

add_action( 'wp', 'lab_custom_skin_template_redirect', 10 );


// Custom Skin Canonical Redirect Disable
function lab_custom_skin_redirect_canonical( $redirect_url ) {
	
	if ( get_query_var( 'custom-skin' ) == 'use' ) {
		return false;
	}
	
	return $redirect_url;
}

add_filter( 'redirect_canonical', 'lab_custom_skin_redirect_canonical' );


// Get Google API Key
function kalium_get_google_api() {
	return apply_filters( 'kalium_google_api_key', get_data( 'google_maps_api' ) );
}

// Get Google API Key Array for ACF
function kalium_google_api_key_acf() {
	$api = array(
		'libraries'   => 'places',
		'key'         => kalium_get_google_api(),
	);
	
	return $api;
}


// Print scripts in the header
function laborator_wp_print_scripts() {
?>
<script type="text/javascript">
var ajaxurl = ajaxurl || '<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ); ?>';
<?php if ( defined( 'ICL_LANGUAGE_CODE' ) ) : ?>
var icl_language_code = <?php echo json_encode( ICL_LANGUAGE_CODE ); ?>;
<?php endif; ?>
</script>
<?php
}

add_action( 'wp_print_scripts', 'laborator_wp_print_scripts' );
	
	

// Parse Footer Styles
function kalium_parse_bottom_styles() {
	global $bottom_styles;

	if ( ! count( $bottom_styles ) ) {
		return;
	}
	
	echo "<style>\n" . compress_text( implode( PHP_EOL . PHP_EOL, $bottom_styles ) ) . "\n</style>"; 	
}

add_action( 'wp_footer', 'kalium_parse_bottom_styles' );



// Append content to the footer
function laborator_append_content_to_footer_parse_content() {
	global $lab_footer_html;
	echo implode( PHP_EOL, $lab_footer_html );
}

add_action( 'wp_footer', 'laborator_append_content_to_footer_parse_content' );


// Theme Options Link in Admin Bar
function kalium_get_plugin_updates_requires() {
	global $tgmpa;
	
	// Plugin Updates Notification
	$plugin_updates = 0;
	$updates_notification = '';
	
	if ( $tgmpa instanceof TGM_Plugin_Activation && method_exists( $tgmpa, 'is_tgmpa_complete' ) && ! $tgmpa->is_tgmpa_complete() ) {
		// Plugins
		$plugins = $tgmpa->plugins;
		
		foreach ( $tgmpa->plugins as $slug => $plugin ) {
			if ( $tgmpa->is_plugin_active( $slug ) && true == $tgmpa->does_plugin_have_update( $slug ) ) {
				$plugin_updates++;
			}
		}
	}
	
	if ( $plugin_updates > 0 ) {
		$updates_notification = " <span class=\"lab-update-badge\">{$plugin_updates}</span>";
	}
	
	return array( $plugin_updates, $updates_notification );
}

function laborator_modify_admin_bar( $wp_admin_bar ) {
	
	list( $plugin_updates, $updates_notification ) = kalium_get_plugin_updates_requires();
	
	$icon = '<i class="wp-menu-image dashicons-before dashicons-admin-generic laborator-admin-bar-menu"></i>';
	
	// Add Admin Bar Menu Links
	$wp_admin_bar->add_menu( array( 
		'id'      => 'laborator-options',
		'title'   => $icon . wp_get_theme(),
		'href'    => is_admin() ? home_url() : admin_url( 'admin.php?page=laborator_options' ),
		'meta'	  => array( 'target' => is_admin() ? '_blank' : '_self' )
	) );
	
	$wp_admin_bar->add_menu( array( 
		'parent'  => 'laborator-options',
		'id'      => 'laborator-options-theme',
		'title'   => 'Theme Options',
		'href'    => admin_url( 'admin.php?page=laborator_options' )
	) );
	
	$wp_admin_bar->add_menu( array( 
		'parent'  => 'laborator-options',
		'id'      => 'laborator-options-typolab',
		'title'   => 'Typography',
		'href'    => admin_url( 'admin.php?page=typolab' )
	) );
		
	if ( $plugin_updates > 0 ) {
		$wp_admin_bar->add_menu( array( 
			'parent'  => 'laborator-options',
			'id'      => 'install-plugins',
			'title'   => 'Update Plugins' . $updates_notification,
			'href'    => admin_url( 'themes.php?page=kalium-install-plugins' )
		) );
	}
	
	$wp_admin_bar->add_menu( array( 
		'parent'  => 'laborator-options',
		'id'      => 'laborator-custom-css',
		'title'   => 'Custom CSS',
		'href'    => admin_url( 'admin.php?page=laborator_custom_css' )
	) );
	
	$wp_admin_bar->add_menu( array( 
		'parent'  => 'laborator-options',
		'id'      => 'laborator-demo-content',
		'title'   => 'Demo Content',
		'href'    => admin_url( 'admin.php?page=laborator-demo-content-installer' )
	) );
	
	$wp_admin_bar->add_menu( array( 
		'parent'  => 'laborator-options',
		'id'      => 'laborator-help',
		'title'   => 'Theme Help',
		'href'    => admin_url( 'admin.php?page=laborator-docs' )
	) );
	
	$wp_admin_bar->add_menu( array( 
		'parent'  => 'laborator-options',
		'id'      => 'laborator-themes',
		'title'   => 'Browse Our Themes',
		'href'    => 'https://themeforest.net/user/Laborator/portfolio?ref=Laborator',
		'meta'	  => array( 'target' => '_blank' )
	) );
	
	// Network Admin Links
	if ( ! is_admin() ) {
		$wp_admin_bar->add_menu( array(
			'parent' => 'site-name',
			'id'     => 'site-name-themeoptions',
			'title'  => 'Theme Options',
			'href'   => admin_url( 'admin.php?page=laborator_options' ),
		) );
		
		$wp_admin_bar->add_menu( array(
			'parent' => 'site-name',
			'id'     => 'site-name-typolab',
			'title'  => 'Typography',
			'href'   => admin_url( 'admin.php?page=typolab' ),
		) );
	}
}

add_action( 'admin_bar_menu', 'laborator_modify_admin_bar', 10000 );


// Custom JavaScript in Head and Footer
function laborator_wp_head() {
	
	// Custom JavaScript in Header
	$user_custom_js_head = get_data( 'user_custom_js_head' );
	
	if ( ! empty( $user_custom_js_head ) ) {
		
		if ( ! preg_match( "/\<\w+/", $user_custom_js_head ) ) {
			$user_custom_js_head = '<script> ' . $user_custom_js_head . ' </script>';
		}
		
		echo $user_custom_js_head;
	}
}

add_action( 'wp_head', 'laborator_wp_head', 100 );


// Footer actions on Kalium
function laborator_wp_footer() {
	
	// Kalium main JS file main.min.js
	wp_enqueue_script( 'kalium-main' );
}

add_action( 'wp_footer', 'laborator_wp_footer' );

// Custom User JavaScript print in the end
function kalium_print_user_custom_js() {

	// Custom JavaScript in Footer
	$user_custom_js = get_data( 'user_custom_js' );
	
	if ( ! empty( $user_custom_js ) ) {
		
		if ( ! preg_match( "/\<\w+/", $user_custom_js ) ) {
			$user_custom_js = '<script> ' . $user_custom_js . ' </script>';
		}
		
		echo $user_custom_js;
	}
	
}

add_action( 'wp_print_footer_scripts', 'kalium_print_user_custom_js' );


// Fav Icon
function laborator_favicon() {
	$favicon_image = get_data( 'favicon_image' );
	$apple_touch_icon = get_data( 'apple_touch_icon' );

	if ( ! has_site_icon() && ( $favicon_image || $apple_touch_icon ) ) {
		
		if ( is_numeric( $favicon_image ) ) {
			$favicon_image = wp_get_attachment_image_src( $favicon_image, 'full' );
			
			if ( $favicon_image ) {
				$favicon_image = $favicon_image[0];
			}
		}
		
		if ( is_numeric( $apple_touch_icon ) ) {
			$apple_touch_icon = wp_get_attachment_image_src( $apple_touch_icon, 'full' );
			
			if ( $apple_touch_icon ) {
				$apple_touch_icon = $apple_touch_icon[0];
			}
		}
		
		if ( $favicon_image ) {
			$favicon_image = str_replace( array( 'http:', 'https:' ), '', $favicon_image );
		}
		
		if ( $apple_touch_icon ) {
			$apple_touch_icon = str_replace( array( 'http:', 'https:' ), '', $apple_touch_icon );
		}
	?>
	<?php /*<!-- Favicons -->*/ ?>
	<?php if ( $favicon_image ) : ?>
	<link rel="shortcut icon" href="<?php echo esc_attr( $favicon_image ); ?>">
	<?php endif; ?>
	<?php if ( $apple_touch_icon ) : ?>
	<link rel="apple-touch-icon-precomposed" href="<?php echo esc_attr( $apple_touch_icon ); ?>">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo esc_attr( $apple_touch_icon ); ?>">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo esc_attr( $apple_touch_icon ); ?>">
	<?php endif; ?>
	<?php
	}
}

add_action( 'wp_head', 'laborator_favicon' );


// Third party plugins
function kalium_register_required_plugins() {
	
	$plugins = array(

		array(
			'name'               => 'Portfolio Post Type',
			'slug'               => 'portfolio-post-type',
			'required'           => false,
			'version'            => '',
		),

		array(
			'name'               => 'WooCommerce',
			'slug'               => 'woocommerce',
			'required'           => false,
			'version'            => '',
		),

		array(
			'name'               => 'WPBakery Page Builder',
			'slug'               => 'js_composer',
			'source'             => get_template_directory() . '/inc/thirdparty-plugins/js_composer.zip',
			'required'           => false,
			'version'            => '5.4.7',
		),

		array(
			'name'               => 'Revolution Slider',
			'slug'               => 'revslider',
			'source'             => get_template_directory() . '/inc/thirdparty-plugins/revslider.zip',
			'required'           => false,
			'version'            => '5.4.7.1',
		),

		array(
			'name'               => 'Layer Slider',
			'slug'               => 'LayerSlider',
			'source'             => get_template_directory() . '/inc/thirdparty-plugins/layersliderwp.zip',
			'required'           => false,
			'version'            => '6.7.1',
			'minimum_version'	 => null,
			'force_deactivation' => true
		),

		array(
			'name'               => 'Advanced Custom Fields',
			'slug'               => 'advanced-custom-fields',
			'required'           => true,
		),

		array(
			'name'               => 'ACF Repeater',
			'slug'               => 'acf-repeater',
			'version'            => '2.0.1',
			'source'             => get_template_directory() . '/inc/thirdparty-plugins/acf-repeater.zip',
			'required'           => true,
		),

		array(
			'name'               => 'ACF Flexible Content',
			'slug'               => 'acf-flexible-content',
			'version'            => '2.0.1',
			'source'             => get_template_directory() . '/inc/thirdparty-plugins/acf-flexible-content.zip',
			'required'           => true,
		),

		array(
			'name'               => 'ACF Gallery',
			'slug'               => 'acf-gallery',
			'version'            => '2.0.1',
			'source'             => get_template_directory() . '/inc/thirdparty-plugins/acf-gallery.zip',
			'required'           => true,
		),

	);
	
	// Size Guide Plugin
	if ( is_shop_supported() ) {
		$plugins[] = array(
			'name'       => 'WooCommerce Product Size Guide',
			'slug'       => 'sizeguide',
			'source'     => get_template_directory() . '/inc/thirdparty-plugins/sizeguide.zip',
			'required'   => false,
			'version'	 => '2.6',
		);
	}
	
	
	// Plugins Updater and Installer Message
	$update_themes = get_site_transient( 'update_themes' );
	$message = '<p style="color: #888">If any of theme required plugins has new update after <strong>Kalium ' . kalium()->getVersion() .  '</strong> was released, we will include the latest version of that plugin in the next theme update.</p>';
	
	if ( isset( $update_themes->response['kalium'] ) && version_compare( kalium()->getVersion(), $update_themes->response['kalium']['new_version'], '<' ) ) {
		$new_version = $update_themes->response['kalium'];
		
		$message = '<div class="notice notice-warning">
			<p>A new <strong>' . THEMENAME .'</strong> update is available! To get latest updates of premium plugins you need to update the theme first.</p>
			<p style="font-size: 12px;color: #888">Your current theme version is <strong>' . kalium()->getVersion() . '</strong>, latest version available is <strong>' . $new_version['new_version'] . '</strong>. <a href="' . admin_url( 'update-core.php' ) . '">Click here to update the theme to newest version &raquo;</a></p>
		</div>';
	}
	
	$config = array(
		'id'           => 'kalium',
		'default_path' => '',
		'menu'         => 'kalium-install-plugins',
		'has_notices'  => true,
		'dismissable'  => true,
		'dismiss_msg'  => '',
		'is_automatic' => false,
		'message'      => $message,
	);

	tgmpa( $plugins, $config );
}

add_action( 'tgmpa_register', 'kalium_register_required_plugins' );


// Remove greensock from LayerSlider because it causes theme incompatibility issues
function layerslider_remove_greensock() {
	wp_dequeue_script( 'greensock' );
	wp_dequeue_script( 'layerslider-greensock' );
}

add_action( 'wp_enqueue_scripts', 'layerslider_remove_greensock' );


// Coming Soon Mode
function laborator_coming_soon_mode() {
	global $current_user;

	$maintenance_mode  = get_data( 'maintenance_mode' );
	$coming_soon_mode  = get_data( 'coming_soon_mode' );

	$manage_options    = current_user_can( 'manage_options' );

	if ( $coming_soon_mode && $manage_options == false || kalium()->url->get( 'view-coming-soon' ) ) {
		get_template_part( 'coming-soon-mode' );
		die();
	}

	if ( $maintenance_mode && $manage_options == false || kalium()->url->get( 'view-maintenance' ) ) {
		get_template_part( 'maintenance-mode' );
		die();
	}
}

add_action( 'template_redirect', 'laborator_coming_soon_mode' );

// Like Feature
function laborator_update_like_count() {
	$output    = array(
		'liked' => false,
		'count' => 0
	);

	$post_id   = intval( $_GET['post_id'] );
	$user_ip   = get_the_user_ip();

	if ( filter_var( $post_id, FILTER_VALIDATE_INT ) ) {
		$the_post = get_post( $post_id );

		if ( $the_post ) {
			$likes = $the_post->post_likes;
			$likes = is_array( $likes ) ? $likes : array();

			if ( ! in_array( $user_ip, $likes ) ) {
				// Like Post
				$output['liked'] = true;

				$likes[] = $user_ip;
				$output['count'] = count( $likes );

				update_post_meta( $post_id, 'post_likes', $likes );
			} else {
				// Unlike Post
				$output['liked'] = false;

				$key = array_search( $user_ip, $likes );

				if ( false !== $key ) {
					unset( $likes[ $key ] );
				}

				$output['count'] = count( $likes );

				update_post_meta( $post_id, 'post_likes', $likes );
			}
			
			if ( function_exists( 'wp_cache_post_change' ) ) {
				wp_cache_post_change( $post_id );
			}
		}

	}

	echo json_encode( $output );

	exit();
}

add_action( 'wp_ajax_laborator_update_likes', 'laborator_update_like_count' );
add_action( 'wp_ajax_nopriv_laborator_update_likes', 'laborator_update_like_count' );

// Page Custom CSS
function kalium_custom_page_css_wp() {
	
	$qo = apply_filters( 'kalium_replace_shop_archive_object', get_queried_object() );
	
	if ( $qo instanceof WP_Post ) {
		$page_custom_css = trim( $qo->page_custom_css );
	}
	
	if ( ! defined( 'PAGE_CUSTOM_CSS' ) && ! empty( $page_custom_css ) ) {
		$post_id = $qo->ID;
		$page_custom_css = str_replace( '.post-ID', ".page-id-{$post_id}", $page_custom_css );
		
		define( 'PAGE_CUSTOM_CSS', $page_custom_css );
	}
}

function kalium_custom_page_css() {
	if ( defined( 'PAGE_CUSTOM_CSS' ) ) {
		echo '<style>' . PAGE_CUSTOM_CSS . '</style>';
	}
}

add_action( 'wp', 'kalium_custom_page_css_wp' );
add_action( 'get_footer', 'kalium_custom_page_css' );


// Search Results Exclude Post Types Filter
function kalium_search_pre_get_posts( $query ) {
	global $s;
	
	if ( $query->is_main_query() && $query->is_search && false == is_admin() ) {	
		$exclude_post_types = array_filter( get_data( 'exclude_search_post_types', array() ) );
		$allowed_post_types = get_post_types( array( 'public' => true, 'exclude_from_search' => false ) );
		
		if ( isset( $allowed_post_types['attachment'] ) ) {
			unset( $allowed_post_types['attachment'] );
		}
		
		$allowed_post_types = array_values( array_diff_key( $allowed_post_types, $exclude_post_types ) );
		
		$query->set( 'post_type', $allowed_post_types );
	}
}

add_action( 'pre_get_posts', 'kalium_search_pre_get_posts' );

// Google Meta Theme Color (Phone)
function kalium_google_theme_color() {
	if ($google_theme_color = get_data( 'google_theme_color' ) ) {
	?>
	<meta name="theme-color" content="<?php echo esc_attr( $google_theme_color ); ?>">
	<?php
	}
}

add_action( 'wp_head', 'kalium_google_theme_color' );


// Revolution Slider set as Theme
if ( function_exists( 'set_revslider_as_theme' ) ) {
	set_revslider_as_theme();
}


// Page Options – Logo and Menu
function kalium_check_for_custom_logo_in_page() {
	$post = apply_filters( 'kalium_replace_shop_archive_object', get_queried_object() );
	
	if ( ! ( $post && $post instanceof WP_Post ) ) {
		return;
	}
	
	$post_id = $post->ID;
	
	$custom_logo           = kalium()->acf->get_field( 'custom_logo', $post_id );
	$custom_menu_skin      = kalium()->acf->get_field( 'custom_menu_skin', $post_id );
	$sticky_menu_on_page   = kalium()->acf->get_field( 'sticky_menu_on_page', $post_id );
	$custom_sticky_logo	   = kalium()->acf->get_field( 'custom_sticky_logo', $post_id );
	$sticky_menu_skin	   = kalium()->acf->get_field( 'sticky_menu_skin', $post_id );
	
	if ( $custom_logo && is_numeric( $custom_logo ) ) {
		$custom_logo_width = kalium()->acf->get_field( 'custom_logo_width' );
		
		add_filter( 'get_data_use_uploaded_logo', '__return_true' );
		add_filter( 'get_data_custom_logo_image', kalium_hook_return_value( $custom_logo ) );
		
		if ( is_numeric( $custom_logo_width ) && $custom_logo_width > 0 ) {
			add_filter( 'get_data_custom_logo_max_width', kalium_hook_return_value( $custom_logo_width ) );
		}
	}
	
	if ( $custom_sticky_logo ) {
		add_filter( 'get_data_sticky_header_logo', kalium_hook_return_value( $custom_sticky_logo ) );
	}
	
	if ( $custom_menu_skin && in_array( $custom_menu_skin, array( 'menu-skin-main', 'menu-skin-dark', 'menu-skin-light' ) ) ) {
		add_filter( 'get_data_menu_full_bg_skin', kalium_hook_return_value( $custom_menu_skin ) );
		add_filter( 'get_data_menu_standard_skin', kalium_hook_return_value( $custom_menu_skin ) );
		add_filter( 'get_data_menu_top_skin', kalium_hook_return_value( $custom_menu_skin ) );
		add_filter( 'get_data_menu_sidebar_skin', kalium_hook_return_value( $custom_menu_skin ) );
	}
	
	// Overwrite sticky header activation in single page
	$sticky_menu_on_page_enable_disable = in_array( $sticky_menu_on_page, array( 'enable', 'disable' ) );
	
	if ( $sticky_menu_on_page && $sticky_menu_on_page_enable_disable ) {
		add_filter( 'get_data_sticky_header', ( $sticky_menu_on_page == 'enable' ? '__return_true' : '__return_false' ) );
	}
	
	// Custom sticky header skin
	if ( get_data( 'sticky_header' ) && $sticky_menu_skin && 'default' !== $sticky_menu_skin ) {
		add_filter( 'get_data_sticky_header_skin', kalium_hook_return_value( $sticky_menu_skin ) );
	}
}

add_action( 'wp', 'kalium_check_for_custom_logo_in_page' );


// Go to Top Feature
function kalium_go_to_top_link() {
	$activate_when = get_data( 'footer_go_to_top_activate' );
	$button_type   = get_data( 'footer_go_to_top_type' );
	$position      = get_data( 'footer_go_to_top_position' );
	
	$type = 'pixels';
	
	if ( strpos( $activate_when, '%' ) ) {
		$type = 'percentage';
	} else if ( trim( strtolower( $activate_when ) ) == 'footer' ) {
		$type = 'footer';
	}
	
	?>
	<a href="#top" class="go-to-top<?php echo $button_type == 'circle' ? ' rounded' : ''; echo ' position-' . $position; ?>" data-type="<?php echo $type; ?>" data-val="<?php echo in_array( $type, array( 'pixels', 'percentage' ) ) ? intval( $activate_when ) : esc_attr( $activate_when ); ?>">
		<i class="flaticon-bottom4"></i>
	</a>
	<?php
}

if ( get_data( 'footer_go_to_top' ) ) {
	add_action( 'wp_footer', 'kalium_go_to_top_link' );
}

// Is Holiday Season
function laborator_is_holiday_season() {
	$time       = time();
	$date_start = date( 'Y' ) . '-12-13';
	$date_end   = ( date( 'Y' ) + 1 ) . '-01-04';
	
	return strtotime( $date_start ) <= $time && strtotime( $date_end ) >= $time;
}


// Holiday Season Wishes (13 dec – 05 jan)
function laborator_holidays_wishes_css() {
	global $pagenow;
	
	if ( $pagenow == 'themes.php' && kalium()->url->get( 'page' ) == 'theme-options' ) {
		return;
	}
	
	if ( laborator_is_holiday_season() ) {
		$x = is_rtl() ? 'left' : 'right';
		echo "<style type='text/css'> #laborator-holidays { float: $x; padding-$x: 15px; padding-top: 8px; margin: 0; font-size: 11px; } #laborator-holidays ~ #of_container { clear: both; margin-top: 35px; }</style>";
	
		add_action( 'admin_notices', 'laborator_holidays_wishes' );
	}
}

function laborator_holidays_wishes() {
	wp_enqueue_style( 'font-awesome' );
	echo "<p id='laborator-holidays'>Happy Holiday Season from <strong>Laborator</strong> team <i class=\"fa fa-tree\"></i></p>";
}

add_action( 'admin_head', 'laborator_holidays_wishes_css' );
// End: Holiday Season Wishes


// Open Graph Meta
function kalium_wp_head_open_graph_meta() {
	global $post;
	
	// Only show if open graph meta is allowed
	if ( ! apply_filters( 'kalium_open_graph_meta', true ) ) {
		return;
	}
	
	// Do not show open graph meta on single posts
	if ( ! is_singular() ) {
		return;
	}

	$featured_image = $post_thumb_id = '';
	
	if ( has_post_thumbnail( $post->ID ) ) {
		$post_thumb_id = get_post_thumbnail_id( $post->ID );
		$featured_image = wp_get_attachment_image_src( $post_thumb_id, 'original' );
	}
	
	// Excerpt, clean styles
	$excerpt = kalium_clean_excerpt( get_the_excerpt(), true );

	?>

	<meta property="og:type" content="article"/>
	<meta property="og:title" content="<?php echo esc_attr( get_the_title() ); ?>"/>
	<meta property="og:url" content="<?php echo esc_url( get_permalink() ); ?>"/>
	<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"/>
	
	<?php if ( $excerpt ) : ?>
	<meta property="og:description" content="<?php echo esc_attr( $excerpt ); ?>"/>
	<?php endif; ?>

	<?php if ( is_array( $featured_image ) ) : ?>
	<meta property="og:image" content="<?php echo $featured_image[0]; ?>"/>
	<link itemprop="image" href="<?php echo $featured_image[0]; ?>" />
	
		<?php if ( apply_filters( 'kalium_meta_google_thumbnail', true ) ) : $thumb = wp_get_attachment_image_src( $post_thumb_id, 'thumbnail' ); ?>
		<!--
		  <PageMap>
		    <DataObject type="thumbnail">
		      <Attribute name="src" value="<?php echo $thumb[0]; ?>"/>
		      <Attribute name="width" value="<?php echo $thumb[1]; ?>"/>
		      <Attribute name="height" value="<?php echo $thumb[2]; ?>"/>
		    </DataObject>
		  </PageMap>
		-->
		<?php endif; ?>
	
	<?php endif;
}

add_action( 'wp_head', 'kalium_wp_head_open_graph_meta', 5 );


// Portfolio Pagination in Archive Page
function kalium_portfolio_user_pagination( $query ) {
	if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'portfolio' ) ) {
		$portfolio_args = kalium_get_portfolio_query( array( 'no_query' => true ) );
		$query->set( 'posts_per_page', $portfolio_args['per_page'] );
	}
}

add_action( 'pre_get_posts', 'kalium_portfolio_user_pagination' );


// Image Loading Placeholder
function kalium_image_loading_placeholder_static_color() {
	return;
	$loading_placeholder_bg_color          = get_data( 'image_loading_placeholder_bg' );
	$loading_placeholder_use_gradient      = get_data( 'image_loading_placeholder_use_gradient' );
	$loading_placeholder_gradient_color    = get_data( 'image_loading_placeholder_gradient_bg' );
	$loading_placeholder_gradient_type     = get_data( 'image_loading_placeholder_gradient_type' );
	$loading_placeholder_dominant_color	   = get_data( 'image_loading_placeholder_dominant_color' );
	
	if ( $loading_placeholder_bg_color && ! $loading_placeholder_dominant_color ) {
		
		if ( $loading_placeholder_use_gradient && $loading_placeholder_gradient_color ) {
			$gradient_color = '';
			
			if ( 'radial' == $loading_placeholder_gradient_type ) {
				$gradient_color = "radial-gradient(circle, {$loading_placeholder_bg_color}, {$loading_placeholder_gradient_color} 60%)";
			} else {
				$gradient_color = "linear-gradient(to bottom, {$loading_placeholder_bg_color}, {$loading_placeholder_gradient_color})";
			}
			
			generate_custom_style( '.image-placeholder, .image-placeholder-bg', "background: {$gradient_color}" );
		} else {
			generate_custom_style( '.image-placeholder, .image-placeholder-bg', 'background-color: ' . $loading_placeholder_bg_color );
		}
	} else if ( $loading_placeholder_dominant_color ) {
		add_filter( 'kalium_image_loading_placeholder_dominant_color', '__return_true' );
	}
}

// Placeholder Background
if ( get_data( 'image_loading_placeholder_bg' ) ) {

	if ( get_data( 'image_loading_placeholder_dominant_color' ) ) {
		add_filter( 'kalium_image_loading_placeholder_dominant_color', '__return_true' );	
	} else {
		add_action( 'wp_head', 'kalium_image_loading_placeholder_static_color', 100 );
	}
}
	

// Blog Page Content
function kalium_blog_page_content_for_vc( $query ) {
	
	if ( ! is_admin() && $query->is_posts_page ) {
		add_action( 'kalium_header_main_heading_title_before', 'kalium_blog_archive_vc_content' );
	}
}

function kalium_blog_archive_vc_content() {
	$blog_page = get_queried_object();
	$is_vc_container = preg_match( "/\[vc_row.*?\]/i", $blog_page->post_content );
	
	
	if ( $is_vc_container ) {
		?>
		<div class="vc-container">
			<?php echo apply_filters( 'the_content', $blog_page->post_content ); ?>
		</div>
		<?php
	}
}

add_action( 'pre_get_posts', 'kalium_blog_page_content_for_vc' );

// Create Font Size Groups
function kalium_font_size_groups() {
	// Headings
	$headings = array( 
		'H1' => 'h1', 
		'H2' => 'h2', 
		'H3' => 'h3', 
		'H4' => 'h4', 
		'H5' => 'h5', 
		'H6' => 'h6' 
	);
	TypoLab_Font_Sizes::addFontSizeGroup( 'Headings', 'Set font size for the headings and page titles.', $headings );
	
	// Paragraphs
	$paragraphs = array( 
		'P' => 'body, p' 
	);
	TypoLab_Font_Sizes::addFontSizeGroup( 'Paragraphs', 'Set font size for paragraphs and general body class.', $paragraphs );
	
	// Standard Menu
	$first_level   = '.main-header.menu-type-standard-menu .standard-menu-container div.menu>ul>li>a, .main-header.menu-type-standard-menu .standard-menu-container ul.menu>li>a';
	$submenu_level = '.main-header.menu-type-standard-menu .standard-menu-container div.menu>ul ul li a, .main-header.menu-type-standard-menu .standard-menu-container ul.menu ul li a';
	
	$standard_menu = array( 
		'Main Menu Items' => $first_level, 
		'Sub Menu Items'  => $submenu_level 
	);
	TypoLab_Font_Sizes::addFontSizeGroup( 'Standard Menu', 'Set font size for menu and submenu items for Standard Menu type.', $standard_menu );
	
	// Fullscreen Menu
	$first_level	= '.main-header.menu-type-full-bg-menu .full-screen-menu nav ul li a';
	$submenu_level	= '.main-header.menu-type-full-bg-menu .full-screen-menu nav div.menu>ul ul li a, .main-header.menu-type-full-bg-menu .full-screen-menu nav ul.menu ul li a';
	
	$fullscreen_menu = array( 
		'Main Menu Items' => $first_level, 
		'Sub Menu Items'  => $submenu_level 
	);
	TypoLab_Font_Sizes::addFontSizeGroup( 'Fullscreen Menu', 'Set font size for menu and submenu items for Fullscreen Menu type.', $fullscreen_menu );
	
	// Top Menu
	$first_level   = '.top-menu-container .top-menu ul li a';
	$submenu_level = '.top-menu div.menu>ul>li ul>li>a, .top-menu ul.menu>li ul>li>a';
	$widgets_title = '.top-menu-container .widget h3';
	$widgets_text  = '.top-menu-container .widget, .top-menu-container .widget p, .top-menu-container .widget div';
	
	$top_menu = array( 
		'Main Menu Items' => $first_level, 
		'Sub Menu Items'  => $submenu_level,
		'Widgets Title'   => $widgets_title,
		'Widgets Content' => $widgets_text 
	);
	TypoLab_Font_Sizes::addFontSizeGroup( 'Top Menu', 'Set font size for menu and submenu items for Top Menu type.', $top_menu );
	
	// Sidebar Menu
	$first_level   = '.sidebar-menu-wrapper .sidebar-menu-container .sidebar-main-menu div.menu>ul>li>a, .sidebar-menu-wrapper .sidebar-menu-container .sidebar-main-menu ul.menu>li>a';
	$submenu_level = '.sidebar-menu-wrapper .sidebar-menu-container .sidebar-main-menu div.menu>ul li ul li:hover>a, .sidebar-menu-wrapper .sidebar-menu-container .sidebar-main-menu ul.menu li ul li>a';
	$widgets_title = '.sidebar-menu-wrapper .sidebar-menu-container .sidebar-menu-widgets .widget .widget-title';
	$widgets_text  = '.sidebar-menu-wrapper .widget, .sidebar-menu-wrapper .widget p, .sidebar-menu-wrapper .widget div';
	
	$sidebar_menu = array( 
		'Main Menu Items' => $first_level, 
		'Sub Menu Items'  => $submenu_level,
		'Widgets Title'   => $widgets_title,
		'Widgets Content' => $widgets_text 
	);
	TypoLab_Font_Sizes::addFontSizeGroup( 'Sidebar Menu', 'Set font size for menu and submenu items for Sidebar Menu type.', $sidebar_menu );
	
	// Mobile Menu
	$first_level   = '.mobile-menu-wrapper .mobile-menu-container div.menu>ul>li>a, .mobile-menu-wrapper .mobile-menu-container ul.menu>li>a, .mobile-menu-wrapper .mobile-menu-container .cart-icon-link-mobile-container a, .mobile-menu-wrapper .mobile-menu-container .search-form input';
	$submenu_level = '.mobile-menu-wrapper .mobile-menu-container div.menu>ul>li ul>li>a, .mobile-menu-wrapper .mobile-menu-container ul.menu>li ul>li>a';
	
	$mobile_menu_menu = array( 
		'Main Menu Items' => $first_level, 
		'Sub Menu Items' => $submenu_level 
	);
	TypoLab_Font_Sizes::addFontSizeGroup( 'Mobile Menu', 'Set font size for menu and submenu items for Mobile Menu type.', $mobile_menu_menu );
	
	// Portfolio
	$portfolio_title               = '.portfolio-holder .thumb .hover-state .info h3, .portfolio-holder .item-box .info h3';
	$portfolio_categories          = '.portfolio-holder .thumb .hover-state .info p, .portfolio-holder .item-box .info h3';
	$portfolio_title_single        = '.single-portfolio-holder .title h1, .single-portfolio-holder.portfolio-type-5 .portfolio-description-container .portfolio-description-showinfo h3';
	$portfolio_subtiles            = '.single-portfolio-holder .section-title p';
	$portfolio_content             = '.portfolio-description-showinfo p, .single-portfolio-holder .details .project-description p, .gallery-item-description .post-formatting p';
	$portfolio_services_title      = '.single-portfolio-holder .details .services h3';
	$portfolio_services_content    = '.single-portfolio-holder .details .services ul li';
	$portfolio_viewsite_link       = '.single-portfolio-holder .details .link';
	
	$portfolio = array(
		'Titles'              => $portfolio_title,
		'Categories'          => $portfolio_categories,
		'Single Title'        => $portfolio_title_single,
		'Subtitles'           => $portfolio_subtiles,
		'Portfolio Content'	  => $portfolio_content,
		'Services Title'      => $portfolio_services_title,
		'Services Content'    => $portfolio_services_content,
		'Launch Link'         => $portfolio_viewsite_link,
	);
	TypoLab_Font_Sizes::addFontSizeGroup( 'Portfolio', 'Set font sizes for portfolio section.', $portfolio );
	
	// Shop
	if ( is_shop_supported() ) {
		$shop_title           = '.woocommerce .product .item-info h3 a, .woocommerce .product .item-info .price ins, .woocommerce .product .item-info .price>.amount';
		$shop_title_single    = '.woocommerce .item-info h1, .woocommerce .single-product .summary .single_variation_wrap .single_variation>.price>.amount, .woocommerce .single-product .summary div[itemprop=offers]>.price>.amount';
		$shop_categories      = '.woocommerce .product.catalog-layout-transparent-bg .item-info .product-category a';
		$shop_product_content = '.woocommerce .item-info p, .woocommerce .item-info .product_meta, .woocommerce .single-product .summary .variations .label label';
		$shop_buttons         = '.woocommerce .item-info .group_table .button, .woocommerce .item-info form.cart .button';
		
		$shop = array(
			'Titles'          => $shop_title,
			'Single Title'    => $shop_title_single,
			'Categories'      => $shop_categories,
			'Product Content' => $shop_product_content,
			'Buttons'         => $shop_buttons,
		);
		TypoLab_Font_Sizes::addFontSizeGroup( 'Shop', 'Set font sizes for shop section.', $shop );	
	}
	
	// Blog
	$post_title_loop	= '.blog-posts .box-holder .post-info h2, .wpb_wrapper .lab-blog-posts .blog-post-entry .blog-post-content-container .blog-post-title';
	$post_title_single	= '.single-blog-holder .blog-title h1';
	$post_excerpt 		= '.blog-post-excerpt p, .post-info p';
	$post_content 		= '.blog-content-holder .post-content';
	
	$blog = array( 
		'Titles'          => $post_title_loop, 
		'Single Title'    => $post_title_single, 
		'Post Excerpt'    => $post_excerpt, 
		'Post Content'    => $post_content 
	);
	TypoLab_Font_Sizes::addFontSizeGroup( 'Blog', 'Set font sizes for blog titles and content.', $blog );
	
	// Footer
	$widgets_title = '.site-footer .footer-widgets .widget h1, .site-footer .footer-widgets .widget h2, .site-footer .footer-widgets .widget h3';
	$widgets_text  = '.site-footer .footer-widgets .widget .textwidget, .site-footer .footer-widgets .widget p';
	$copyrights    = '.copyrights, .site-footer .footer-bottom-content a, .site-footer .footer-bottom-content p';
	
	$footer = array( 
		'Widgets Title'   => $widgets_title, 
		'Widgets Content' => $widgets_text, 
		'Copyrights'      => $copyrights 
	);
	TypoLab_Font_Sizes::addFontSizeGroup( 'Footer', 'Set font sizes for footer elements.', $footer );
	
}

add_action( 'typolab_add_font_size_groups', 'kalium_font_size_groups' );

// Mobile menu breakpoint
function kalium_mobile_menu_breakpoint() {
	$breakpoint = kalium_get_mobile_menu_breakpoint();
	$breakpoint_one = $breakpoint + 1;
	
	$media_min = "screen and (min-width:{$breakpoint_one}px)";
	$media_max = "screen and (max-width:{$breakpoint}px)";
	
	echo "<script>var mobile_menu_breakpoint = {$breakpoint};</script>";
	
	// Hide elements outside of mobile menu breakpoint
	$breakpoint_outside_hide = array();
	$breakpoint_outside_hide[] = '.mobile-menu-wrapper';
	$breakpoint_outside_hide[] = '.mobile-menu-overlay';
	$breakpoint_outside_hide[] = '.standard-menu-container .menu-bar-hidden-desktop';
	
	generate_custom_style( implode( ',', $breakpoint_outside_hide ), 'display: none;', $media_min );
	
	// Hide elements inside of mobile menu breakpoint
	$breakpoint_inside_hide = array();
	$breakpoint_inside_hide[] = '.standard-menu-container > div';
	$breakpoint_inside_hide[] = '.standard-menu-container > nav';
	$breakpoint_inside_hide[] = '.main-header.menu-type-standard-menu .standard-menu-container div.menu>ul';
	$breakpoint_inside_hide[] = '.main-header.menu-type-standard-menu .standard-menu-container ul.menu';
	$breakpoint_inside_hide[] = '.menu-cart-icon-container';
	
	generate_custom_style( implode( ',', $breakpoint_inside_hide ), 'display: none;', $media_max  );
}

add_action( 'wp_head', 'kalium_mobile_menu_breakpoint' );


// Upgrading to Kalium v2.0 Changes
function kalium_version_upgrade_to_2_0( $previous_version ) {
	global $wpdb;
	
	$new_options = array();
	
	// Sticky Header Options Migrate
	$new_options['sticky_header']                  = get_data( 'header_sticky_menu' );
	
	$new_options['sticky_header_support_mobile']   = get_data( 'header_sticky_mobile' );
	$new_options['sticky_header_autohide']         = get_data( 'header_sticky_autohide' );
	
	$new_options['sticky_header_background_color'] = get_data( 'header_sticky_bg' );
	$new_options['sticky_header_vertical_padding'] = get_data( 'header_sticky_vpadding' );
	
	$new_options['sticky_header_skin']             = get_data( 'header_sticky_menu_skin' );
	
	$new_options['sticky_header_border']           = get_data( 'header_sticky_border' );
	$new_options['sticky_header_border_color']     = get_data( 'header_sticky_border_color' );
	$new_options['sticky_header_border_width']     = get_data( 'header_sticky_border_width' );
	
	$new_options['sticky_header_shadow_color']     = get_data( 'header_sticky_shadow_color' );
	$new_options['sticky_header_shadow_width']     = get_data( 'header_sticky_shadow_width' );
	$new_options['sticky_header_shadow_blur']      = get_data( 'header_sticky_shadow_blur' );
	
	$new_options['sticky_header_logo']      	   = get_data( 'header_sticky_custom_logo' );
	$new_options['sticky_header_logo_width']       = get_data( 'header_sticky_logo_width' );
	
	// Typekit Font Variable Had Wrong Name
	$new_options['use_typekit_font'] 			   = get_data( 'use_tykekit_font' );
	
	// Default Sticky Options
	if ( $new_options['sticky_header'] ) {
		$new_options['sticky_header_animate_duration'] = false;
		$new_options['sticky_header_support_desktop']  = true;
		$new_options['sticky_header_support_tablet']   = true;
	}
	
	
	/* Move RSLS Logos to Section Logo Switch */	
	// Get posts with RSLS
	$result = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key = 'revolution_slider_logo_switch' AND meta_value = '1'" );
	
	// RSLS groups
	$rsls_groups = array();
	
	foreach ( $result as $post ) {
		$post_id = $post->post_id;

		$post_rsls_entries = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key LIKE 'revolution_slider_custom_logos_%' AND post_id = '$post_id'" );
		$rsls_group_entry = array();
		
		if ( ! isset( $rsls_groups[ $post_id ] ) ) {
			$rsls_groups[ $post_id ] = array();
		}
		
		foreach ( $post_rsls_entries as $rsls_entry ) {
			$key = preg_replace( "/revolution_slider_custom_logos_[0-9]+_/", '', $rsls_entry->meta_key );
			$rsls_group_entry[ $key ] = $rsls_entry->meta_value;
		}
		
		$rsls_groups[ $post_id ][] = $rsls_group_entry;
	}
	
	// Add to new field name
	foreach ( $rsls_groups as $post_id => $rsls_entries ) {
		$post_meta = array();
		
		$post_meta['section_logo_switch'] = true; 
		$post_meta['_section_logo_switch'] = 'field_58382d561e98d';
		
		$post_meta["logo_switch_sections"] = count( $rsls_entries );
		$post_meta["_logo_switch_sections"] = 'field_58382e9f2830e';
		
		foreach ( $rsls_entries as $_index => $rsls_entry ) {
			$rsls_entry = (object) $rsls_entry;
			$move_post_meta_values = array();
			
			// Activate Logo Switch Section
			$post_meta["section_logo_switch"] = true;
			$post_meta["_section_logo_switch"] = 'field_58382d561e98d';
			
			$post_meta["_logo_switch_sections_{$_index}_switch_type"]         = 'field_58382f5bd4c4a';
			$post_meta["logo_switch_sections_{$_index}_switch_type"]          = 'revslider';
			
			$post_meta["_logo_switch_sections_{$_index}_revslider"]           = 'field_583832b3d951e';
			$post_meta["logo_switch_sections_{$_index}_revslider"]            = $rsls_entry->slider;
			
			$post_meta["_logo_switch_sections_{$_index}_logo"]                = 'field_583830b862c12';
			$post_meta["logo_switch_sections_{$_index}_logo"]                 = $rsls_entry->logo;
			
			$post_meta["_logo_switch_sections_{$_index}_logo_width"]          = 'field_583833540e070';
			$post_meta["logo_switch_sections_{$_index}_logo_width"]           = $rsls_entry->logo_width;
			
			$post_meta["_logo_switch_sections_{$_index}_menu_skin"]           = 'field_583830f762c13';
			$post_meta["logo_switch_sections_{$_index}_menu_skin"]            = $rsls_entry->menu_skin;
			
			$post_meta["_logo_switch_sections_{$_index}_transparent_style"]   = 'field_5838316262c14';
			$post_meta["logo_switch_sections_{$_index}_transparent_style"]    = $rsls_entry->transparent_background || $rsls_entry->no_bottom_border;
		}
	
		foreach ( $post_meta as $key => $val ) {
			update_post_meta( $post_id, $key, $val );
		}
	}
	/* End: Move RSLS Logo to Section Logo Switch */	
	

	// Save smof data	
	of_save_options( $new_options );
}

add_action( 'kalium_version_upgrade_2_0', 'kalium_version_upgrade_to_2_0', 10 );

// Upgrading to Kalium v2.0.6
function kalium_version_upgrade_to_2_0_6( $previous_version ) {
	$new_options = array(
		'submenu_dropdown_indicator' => get_data( 'menu_standard_menu_dropdown_caret' )
	);
	
	of_save_options( $new_options );
}

add_action( 'kalium_version_upgrade_2_0_6', 'kalium_version_upgrade_to_2_0_6', 10 );

// LayerSlider ready
function layerslider_disable_autoupdates() {
	$GLOBALS['lsAutoUpdateBox'] = false;
}

add_action( 'layerslider_ready', 'layerslider_disable_autoupdates' );

// Sidekick Configuration
define( 'SK_PRODUCT_ID', 454 );
define( 'SK_ENVATO_PARTNER', 'iZmD68ShqUyvu7HzjPWPTzxGSJeNLVxGnRXM/0Pqxv4=' );
define( 'SK_ENVATO_SECRET', 'RqjBt/YyaTOjDq+lKLWhL10sFCMCJciT9SPUKLBBmso=' );
