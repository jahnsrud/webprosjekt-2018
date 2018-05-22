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

// Get element from array by key (fail safe)
function get_array_key( $arr, $key ) {
	if ( ! is_array( $arr ) ) {
		return null;
	}
	
	return isset( $arr[ $key ] ) ? $arr[ $key ] : null;
}


// Print attribute values based on boolean value
function when_match( $bool, $str = '', $otherwise_str = '', $echo = true ) {
	$str = trim( $bool ? $str : $otherwise_str );
	
	if ( $str ) {
		$str = ' ' . $str;
		
		if ( $echo ) {
			echo $str;
			return '';
		}
	}
	
	return $str;
}


// Get Theme Options data
$theme_options_data = get_theme_mods();

function get_data( $var = null, $default = '' ) {
	global $theme_options_data;
	
	if ( $var == null ) {
		return apply_filters( 'get_theme_options', $theme_options_data );
	}

	if ( isset( $theme_options_data[ $var ] ) ) {
		$value = $theme_options_data[ $var ];
		
		// Treat numeric values as "number"
		if ( is_numeric( $value ) ) {
			if ( is_int( $value ) ) {
				$value = intval( $value );
		 	} elseif ( is_float( $value ) ) {
				$value = floatval( $value );
			} elseif ( is_double( $value ) ) {
				$value = doubleval( $value );
			}								
		}
		 
		return apply_filters( "get_data_{$var}", $value );
	}

	return apply_filters( "get_data_{$var}", $default );
}


// Compress Text Function
function compress_text( $buffer ) {
	/* remove comments */
	$buffer = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer );
	/* remove tabs, spaces, newlines, etc. */
	$buffer = str_replace( array( "\r\n", "\r", "\n", "\t", '	', '	', '	' ), '', $buffer );
	return $buffer;
}


// Share Network Story
function share_story_network_link( $network, $post_id = null, $class = '', $icon = false ) {
	$post = get_post( $post_id );
	$excerpt = wp_trim_words( kalium_clean_excerpt( $post->post_excerpt, true ), 40, '&hellip;' );

	$title     = esc_attr( get_the_title( $post_id ) );
	$excerpt   = esc_attr( $excerpt );
	$permalink = esc_attr( get_permalink( $post_id ) );	

	$networks = array(
		'fb'          => array(
			'url'        => 'https://www.facebook.com/sharer.php?u=' . $permalink,
			'tooltip'    => 'Facebook',
			'icon'       => 'facebook'
		),

		'tw'          => array(
			'url'        => 'https://twitter.com/share?text=' . $title,
			'tooltip'    => 'Twitter',
			'icon'       => 'twitter'
		),

		'gp'          => array(
			'url'        => 'https://plus.google.com/share?url=' . $permalink,
			'tooltip'    => 'Google+',
			'icon'       => 'google-plus'
		),

		'tlr'         => array(
			'url'        => 'http://www.tumblr.com/share/link?url=' . $permalink . '&name=' . $title . '&description=' . $excerpt,
			'tooltip'    => 'Tumblr',
			'icon'       => 'tumblr'
		),

		'lin'         => array(
			'url'        => 'https://linkedin.com/shareArticle?mini=true&amp;url=' . $permalink . '&amp;title=' . $title,
			'tooltip'    => 'LinkedIn',
			'icon'       => 'linkedin'
		),

		'pi'          => array(
			'url'        => 'https://pinterest.com/pin/create/button/?url=' . $permalink . '&amp;description=' . $title . '&' . ( $post_id ? ( 'media=' . wp_get_attachment_url( get_post_thumbnail_id( $post_id ) ) ) : '' ),
			'tooltip'    => 'Pinterest',
			'icon'       => 'pinterest'
		),

		'vk'          => array(
			'url'        => 'https://vkontakte.ru/share.php?url=' . $permalink . '&title=' . $title . '&description=' . $excerpt,
			'tooltip'    => 'VKontakte',
			'icon'       => 'vk'
		),

		'em'          => array(
			'url'        => 'mailto:?subject=' . $title . '&body=' . esc_attr( sprintf( __( 'Check out what I just spotted: %s', 'kalium' ), $permalink ) ),
			'tooltip'    => __( 'Email', 'kalium' ),
			'icon'       => 'envelope-o'
		),

		'pr'          => array(
			'url'        => 'javascript:window.print();',
			'tooltip'    => __( 'Print', 'kalium' ),
			'icon'       => 'print'
		),
	);

	$network_entry = $networks[ $network ];
	$new_window = $network ? false : true;
	?>
	<a class="<?php echo esc_attr( trim( "{$network_entry['icon']} {$class}" ) ); ?>" href="<?php echo $network_entry['url']; ?>"<?php when_match( $new_window, 'target="_blank"' ); ?>>
		<?php if ( $icon ) : ?>
			<i class="icon fa fa-<?php echo esc_attr( $network_entry['icon'] ); ?>"></i>
		<?php else : ?>
			<?php echo esc_html( $network_entry['tooltip'] ); ?>
		<?php endif; ?>
	</a>
	<?php
}


// Get Excerpt
function laborator_get_excerpt( $text ) {
	$excerpt_length  = apply_filters( 'excerpt_length', 55 );
	$excerpt_more	 = apply_filters( 'excerpt_more', ' [&hellip;]' );
	$text			 = apply_filters( 'the_excerpt', apply_filters( 'get_the_excerpt', wp_trim_words( $text, $excerpt_length, $excerpt_more ) ) );

	return $text;
}

// Aspect Ratio Element Generator
$as_element_id = 1;

function laborator_generate_as_element( $size ) {
	global $as_element_id;
	
	if ( isset( $size['width'] ) ) {
		$size[0] = $size['width'];
	}
	
	if ( isset( $size['height'] ) ) {
		$size[1] = $size['height'];
	}

	if ( $size[0] == 0 ) {
		return null;
	}

	$element_id = "arel-" . $as_element_id;
	$element_css = 'padding-bottom: ' . kalium()->images->calculateAspectRatio( $size[0], $size[1] ) . '% !important;';
	
	$as_element_id++;

	if ( defined( 'DOING_AJAX' ) ) {
		$element_id .= '-' . time() . mt_rand( 100, 999 );
	}

	generate_custom_style( ".{$element_id}", $element_css );

	return $element_id;
}

// Custom Style Generator
$bottom_styles = array();

function generate_custom_style( $selector, $props = '', $media = '', $footer = false ) {
	global $bottom_styles;

	$css = '';
		
		// Selector Start
		$css .= $selector . ' {' . PHP_EOL;

			// Selector Properties
		$css .= str_replace( ';', ';' . PHP_EOL, $props );

		$css .= PHP_EOL . '}';
		// Selector End
		
		// Media Wrap
		if ( trim( $media ) ) {
			if ( strpos( $media, '@' ) == false ) {
				$css = "@media {$media} { {$css} }";
			} else {
				$css = "{$media} { {$css} }";
			}
		}

	if ( ! $footer || defined( 'DOING_AJAX' ) ) {
		echo "<style>{$css}</style>";
		return;
	}

	$bottom_styles[] = $css;
}


// User IP
function get_the_user_ip() {
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return $ip;
}


// Get SVG
function laborator_get_svg( $svg_path, $id = null, $size = array( 24, 24 ), $is_asset = true ) {
	if ( $is_asset ) {
		$svg_path = get_template_directory() . '/assets/' .  $svg_path;
	}

	if ( ! $id ) {
		$id = sanitize_title( basename( $svg_path ) );
	}

	if ( is_numeric( $size ) ) {
		$size = array( $size, $size );
	}

	ob_start();

	echo file_get_contents( $svg_path );

	$svg = ob_get_clean();

	$svg = preg_replace(
		array(
			'/^.*<svg/s',
			'/id=".*?"/i',
			'/width=".*?"/',
			'/height=".*?"/'
		),
		array(
			'<svg', 'id="' . $id . '"',
			'width="' . $size[0] . 'px"',
			'height="' . $size[0] . 'px"'
		),
		$svg
	);

	return $svg;
}


// Less Generator
function kalium_generate_less_style( $files = array(), $vars = array() ) {
	try {
		@ini_set( 'memory_limit', '256M' );
		
		if ( ! class_exists( 'Less_Parser' ) ) {
			include_once kalium()->locateFile( 'inc/lib/lessphp/Less.php' );
		}
		
		$skin_generator = file_get_contents( kalium()->locateFile( 'assets/less/skin-generator.less' ) );
		
		// Compile Less
		$less_options = array(
			'compress' => true
		);
		
		$css = '';
				
		$less = new Less_Parser( $less_options );
		
		foreach ( $files as $file => $type ) {
			if ( $type == 'parse' ) {
				$css_contents = file_get_contents( $file );
				
				// Replace Vars
				foreach ( $vars as $var => $value ) {
					if ( trim( $value ) ) {
						$css_contents = preg_replace( "/(@{$var}):\s*.*?;/", '$1: ' . $value . ';', $css_contents );
					}
				}
				
				$less->parse( $css_contents );
			} else {
				$less->parseFile( $file );
			}
		}
		
		$css = $less->getCss();
	} catch( Exception $e ) {
	}
	
	return $css;
}


// Escape script tag
function laborator_esc_script( $str = '' ) {
	$str = str_ireplace( array( '<script', '</script>' ), array( '&lt;script', '&lt;/script&gt;' ), $str );
	return $str;
}


// Shop Supported
function is_shop_supported() {
	return kalium()->helpers->isPluginActive( 'woocommerce/woocommerce.php' );
}


// Is ACF Pro Activated
function is_acf_pro_activated() {
	return is_array( get_option( 'active_plugins' ) ) && in_array( 'advanced-custom-fields-pro/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
}


// Show Menu Bar (Hambuger Icon)
function kalium_menu_icon_or_label() {
	$menu_hamburger_custom_label = get_data( 'menu_hamburger_custom_label' );
	
	if ( $menu_hamburger_custom_label ) {
		
		$label_show_text  = get_data( 'menu_hamburger_custom_label_text' );
		$label_close_text = get_data( 'menu_hamburger_custom_label_close_text' );
		$icon_position    = get_data( 'menu_hamburger_custom_icon_position', 'left' );
		
		?>
		<span class="show-menu-text icon-<?php echo esc_attr( $icon_position ); ?>"><?php echo $label_show_text; ?></span>
		<span class="hide-menu-text"><?php echo $label_close_text; ?></span>
		
		<span class="ham"></span>
		<?php
		
	} else {	
		?>
		<span class="ham"></span>
		<?php
	}
}


// Generate Unique ID
function laborator_unique_id( $prepend = 'el-' ) {
	$uniqueid = $prepend . ( function_exists( 'uniqid' ) ? uniqid() : '' ) . time() . mt_rand( 10000, 99999 );
	return $uniqueid;
}


// Get Available Terms for current WP_Query object
function laborator_get_available_terms_for_query( $args, $taxonomy = 'category', $ignore_paged_var = true ) {
	
	// Remove pagination argument
	if ( $ignore_paged_var && isset( $args['paged'] ) ) {
		unset( $args['paged'] );
	}
	
	$posts = new WP_Query( array_merge( $args, array(
		'fields'          => 'ids',
		'posts_per_page'  => -1
	) ) );
	
	$post_ids = $posts->posts;
	$term_ids = array(); // Terms IDs Array
	
	$object_terms = wp_get_object_terms( $post_ids, $taxonomy );
	
	// In case when taxonomy doesn't exists
	if ( is_wp_error( $object_terms ) ) {
		return array();
	}
	
	if ( ! empty( $object_terms ) ) {
		foreach ( $object_terms as $term ) {
			$term_ids[] = $term->term_id;
		}
	}
	
	// Order Terms
	if ( is_array( $object_terms ) && isset( $object_terms[0] ) && $object_terms[0] instanceof WP_Term && isset( $object_terms[0]->term_order ) ) {
		uasort( $object_terms, 'kalium_sort_terms_taxonomy_order_fn' );
	}
	
	// Fix Missing Parent Categories
	foreach ( $object_terms as & $term ) {
		if ( ! in_array( $term->parent, $term_ids ) ) {
			$term->parent = 0;
		}
	}
	
	return $object_terms;
}

function kalium_sort_terms_taxonomy_order_fn( $a, $b ) {
	return $a->term_order > $b->term_order ? 1 : -1;
}


// Append content to the footer
$lab_footer_html = array();

function laborator_append_content_to_footer( $str ) {
	global $lab_footer_html;
	
	if ( defined( 'DOING_AJAX' ) ) {
		echo $str;
	} else {
		$lab_footer_html[] = $str;
	}
}

// Get Custom Skin File Name
function kalium_get_custom_skin_filename() {
	if ( is_multisite() ) {
		return apply_filters( 'kalium_multisite_custom_skin_name', 'custom-skin-' . get_current_blog_id() . '.css', get_current_blog_id() );
	}
	
	return apply_filters( 'kalium_custom_skin_name', 'custom-skin.css' );
}

// File Based Custom Skin
function kalium_use_filebased_custom_skin() {
	$custom_skin_filename = kalium_get_custom_skin_filename();
	$custom_skin_path_full = get_stylesheet_directory() . '/assets/css/' . $custom_skin_filename;
	
	if ( is_child_theme() ) {
		$custom_skin_path_full = get_stylesheet_directory() . '/' . $custom_skin_filename;
	}
	
	// Create skin file in case it does not exists
	if ( file_exists( $custom_skin_path_full ) === false ) {
		@touch( $custom_skin_path_full );
	}
	
	if ( is_writable( $custom_skin_path_full ) === true ) {
		
		if ( ! trim( @file_get_contents( $custom_skin_path_full ) ) ) {
			return laborator_custom_skin_generate( null, true );
		}
		
		return true;
	}
	
	return false;
}


// Generate Custom Skin File
function kalium_generate_custom_skin_file() {
	$custom_skin_filename = kalium_get_custom_skin_filename();
	$custom_skin_path = get_stylesheet_directory() . '/assets/css/' . $custom_skin_filename;
	
	if ( is_child_theme() ) {
		$custom_skin_path = get_stylesheet_directory() . '/' . $custom_skin_filename;
	}
	
	if ( is_writable( $custom_skin_path ) ) {
		$kalium_skin_custom_css = get_option( 'kalium_skin_custom_css' );
		
		$fp = @fopen( $custom_skin_path , 'w' );
		@fwrite( $fp, $kalium_skin_custom_css );
		@fclose( $fp );
		
		return true;
	}
	
	return false;
}


// Default Value Set for Visual Composer Loop Parameter Type
function kalium_vc_loop_param_set_default_value( & $query, $field, $value = '' ) {
	
	if ( ! preg_match( '/(\|?)' . preg_quote( $field ) . ':/', $query ) ) {
		$query .= "|{$field}:{$value}";
	}
	
	return ltrim( '|', $query );
}


// Get Post Likes
function get_post_likes( $post_id = null ) {
	global $post;

	$user_ip   = get_the_user_ip();
	$the_post  = $post_id ? get_post( $post_id ) : $post;
	$likes     = $the_post->post_likes;

	if ( ! is_array( $likes ))
		$likes = array();

	$output    = array(
		'liked' => in_array($user_ip, $likes),
		'count' => count( $likes )
	);

	return $output;
}


// Immediate Return Function (Deprecated)
function laborator_immediate_return_fn( $value ) {
	/**
	 * New PHP 7.2 compatible anonymous function
	 */
	return kalium_hook_return_value( $value );
}

// Get attachment sizes and srcset
function kalium_image_get_srcset_and_sizes_from_attachment( $attachment_id, $image = null, $image_size = 'original' ) {
	$srcset = $sizes = array();
	
	if ( $image != false ) {
		$size_array = array( absint( $image[1] ), absint( $image[2] ) );
		$image_metadata = wp_get_attachment_metadata( $attachment_id );
		
		$srcset = wp_calculate_image_srcset( $size_array, $image[0], $image_metadata, $attachment_id );
		$sizes = wp_calculate_image_sizes( $size_array, $image[0], $image_metadata, $attachment_id );
	}
	
	return array( $srcset, $sizes );
}


// Get Sticky Header Options
function kalium_get_sticky_header_options() {
	$options = array(
		'type'              => 'classic', 		// Sticky type: classic, autohide
		
		'wrapper'           => '.wrapper', 		// Wrapper element (if empty, body will be used)
		'container'         => '.main-header',	// Header container
		
		'logoContainer'     => '.header-logo', 	// Header logo
		
		'spacer'            => true, 			// Header spacer element
		
		'initialOffset'     => 0, 				// Initial offset for scene animation
		
		'debugMode'			=> false, 			// Debug mode
		
		'animateDuration'	=> true, 			// Animate scenes with scroll duration
		
		// Responsive breakpoints for sticky menu
		'breakpoints' => array(
			'desktop'  => array( 992, null ),
			'tablet'   => array( 768, 992 ),
			'mobile'   => array( null, 768 )
		),
		
		// Skin
		'skin' => array(
			
			// Defined skins
			'classes' => array( 
				'menu-skin-main',
				'menu-skin-dark',
				'menu-skin-light' 
			),
			
			// Current skin applied to header
			'current' => '',
			
			// Skin to use when sticky is active
			'active' => '',
		),
		
		// Sticky Header Scenes
		'scenes' => array(
			'paddingSceneOptions'    => null,
			'backgroundSceneOptions' => null,
			'logoSceneOptions'       => null,
		),
		
		// Autohide Menu Options
		'autohide' => array(
			'duration' => 0.3,	
			'easing'   => 'Sine.easeInOut',
			'css'      => array(),
		)
	);
	
	// Sticky Type
	if ( get_data( 'sticky_header_autohide' ) ) {
		$options['type'] = 'autohide';
	}
	
	// Header spacer
	if ( 'absolute' == get_data( 'header_position' ) ) {
		$options['spacer'] = false;
	}
	
	// Initial Offfset
	if ( $initial_offset = get_data( 'header_sticky_initial_offset' ) ) {
		$options['initialOffset'] = $initial_offset;
	}
	
	// Animate duration
	$options['animateDuration'] = ! get_data( 'sticky_header_animate_duration' );
	
	// Autohide Options
	$autohide_duration = get_data( 'sticky_header_autohide_duration' );
	
	$options['autohide']['duration'] = is_numeric( $autohide_duration ) ? $autohide_duration : 0.3;
	$options['autohide']['easing'] = get_data( 'sticky_header_autohide_easing' ) . '.' . get_data( 'sticky_header_autohide_easing_type' );
	$options['autohide']['css'] = array( 'autoAlpha' => 0 );
	
	switch ( get_data( 'sticky_header_autohide_animation_type' ) ) {
		case 'fade-slide-top':
			$options['autohide']['css'] = array(
				'y' => '-25%'
			);
			break;
			
		case 'fade-slide-bottom':
			$options['autohide']['css'] = array(
				'y' => '25%'
			);
			break;
	}
	
	// Sticky Skin
	switch ( get_data( 'main_menu_type' ) ) {
		case 'full-bg-menu':
			$options['skin']['current'] = get_data( 'menu_full_bg_skin' );
			break;
			
		case 'standard-menu':
			$options['skin']['current'] = get_data( 'menu_standard_skin' );
			break;
			
		case 'top-menu':
			$options['skin']['current'] = get_data( 'menu_top_skin' );
			break;
			
		case 'sidebar-menu':
			$options['skin']['current'] = get_data( 'menu_sidebar_skin' );
			break;
	}
	
	$options['skin']['active'] = get_data( 'sticky_header_skin' );
	
	// Sticky Duration
	$sticky_duration = get_data( 'header_sticky_duration', 0.3 );
	$sticky_easing = 'Sine.easeInOut';
	
	// Disabled on desktop
	if ( ! get_data( 'sticky_header_support_desktop' ) ) {
		unset( $options['breakpoints']['desktop'] );
	}
	
	// Disabled on tablets
	if ( ! get_data( 'sticky_header_support_tablet' ) ) {
		unset( $options['breakpoints']['tablet'] );
	}
	
	// Disabled on mobile
	if ( ! get_data( 'sticky_header_support_mobile' ) ) {
		unset( $options['breakpoints']['mobile'] );
	}
	
	// Sample Scene
	$scene_obj = array(
		'scene' => array(),
		'tween' => array(
			'easing' => $sticky_easing,
			'css'    => array()
		),
	);
	
	// Tween Duration for Scenes
	if ( $sticky_duration ) {
		$scene_obj['tween']['duration'] = $sticky_duration;
	}
	
	// Logo Scene
	$custom_logo               = get_data( 'custom_logo_image' );
	$custom_logo_width         = get_data( 'custom_logo_max_width' );
	$custom_logo_width_mobile  = get_data( 'custom_logo_mobile_max_width' );
	
	$sticky_logo               = get_data( 'sticky_header_logo' );
	$sticky_logo_width         = get_data( 'sticky_header_logo_width' );
	
	
	$sticky_logo_scene = array_merge( $scene_obj, array(
		'logo' => array()
	) );
	
	if ( ! $sticky_logo ) {
		$sticky_logo = $custom_logo;
	}
	
	if ( $sticky_logo ) {
		$sticky_logo_img = wp_get_attachment_image_src( $sticky_logo, 'original' );
		
		if ( is_array( $sticky_logo_img ) && ! empty( $sticky_logo_img[0] ) ) {
			
			$sticky_logo_width = $sticky_logo_width ? $sticky_logo_width : ( $custom_logo_width ? $custom_logo_width : $sticky_logo_img[1] );
			$sticky_logo_height = ( $sticky_logo_width / $sticky_logo_img[1] ) * $sticky_logo_img[2];
			
			$sticky_logo_scene['logo'] = array_merge( $sticky_logo_scene['logo'], array(
				'src'    => $custom_logo !== $sticky_logo ? $sticky_logo_img[0] : '',
				'width'  => round( $sticky_logo_width ),
				'height' => round( $sticky_logo_height ),
			) );
		}
	}
	
	$options['scenes']['logoSceneOptions'] = $sticky_logo_scene;
	
	// Background Scene
	$sticky_bg_scene = array_merge_recursive( $scene_obj, array(
		'tween' => array(
			'css' => array()
		)
	) );
	
	$sticky_bg_color = get_data( 'sticky_header_background_color' );
	
	if ( $sticky_bg_color ) {
		$sticky_bg_scene['tween']['css']['backgroundColor'] = $sticky_bg_color;
	}
	
	// Apply Border and/or Shadow for Background Scene
	if ( get_data( 'sticky_header_border' ) ) {	
		$sticky_border_color = get_data( 'sticky_header_border_color' );
		$sticky_border_width = get_data( 'sticky_header_border_width' );
		
		$sticky_shadow_color = get_data( 'sticky_header_shadow_color' );
		$sticky_shadow_width = get_data( 'sticky_header_shadow_width' );
		$sticky_shadow_blur  = get_data( 'sticky_header_shadow_blur' );
		
		// Border
		if ( $sticky_border_color && $sticky_border_width ) {
			$sticky_bg_scene['tween']['css']['borderBottomColor'] = $sticky_border_color;
			
			// Transparent Border
			generate_custom_style( 'header.main-header', "border-bottom: {$sticky_border_width} solid rgba(255,255,255,0)" );
		}
		
		// Shadow
		if ( $sticky_shadow_color && ( $sticky_shadow_width || $sticky_shadow_blur ) ) {
			$sticky_bg_scene['tween']['css']['boxShadow'] = "{$sticky_shadow_color} 0px {$sticky_shadow_width} {$sticky_shadow_blur} {$sticky_shadow_width}";
			
			// Transparent Shadow
			generate_custom_style( 'header.main-header', "box-shadow: rgba(255,255,255,0) 0px {$sticky_shadow_width} {$sticky_shadow_blur} {$sticky_shadow_width}" );
		}
	}
	
	$options['scenes']['backgroundSceneOptions'] = $sticky_bg_scene;
		
	// Padding Scene
	$vertical_padding = get_data( 'sticky_header_vertical_padding' );
	
	if ( '' !== $vertical_padding ) {
		$sticky_padding_scene = array_merge_recursive( $scene_obj, array(
			'tween' => array(
				'css' => array(
					'paddingTop' => "{$vertical_padding}px",
					'paddingBottom' => "{$vertical_padding}px",
				)
			)
		) );
		
		$options['scenes']['paddingSceneOptions'] = $sticky_padding_scene;
	}
	
	// Animation Chaining
	switch ( get_data( 'sticky_header_animation_chaining' ) ) {
		// Padding -> Background, Logo
		case 'padding-bg_logo':
			$options['scenes']['paddingSceneOptions']['scene']    = array( 'startAt' => 0.00, 'endAt' => 0.50 );
			$options['scenes']['backgroundSceneOptions']['scene'] = array( 'startAt' => 0.50, 'endAt' => 1.00 );
			$options['scenes']['logoSceneOptions']['scene']       = array( 'startAt' => 0.50, 'endAt' => 1.00 );
			break;
			
		// Background, Logo -> Padding
		case 'bg_logo-padding':
			$options['scenes']['paddingSceneOptions']['scene']    = array( 'startAt' => 0.50, 'endAt' => 1.00 );
			$options['scenes']['backgroundSceneOptions']['scene'] = array( 'startAt' => 0.00, 'endAt' => 0.50 );
			$options['scenes']['logoSceneOptions']['scene']       = array( 'startAt' => 0.00, 'endAt' => 0.50 );
			break;
			
		// Logo, Padding -> Background
		case 'logo_padding-bg':
			$options['scenes']['paddingSceneOptions']['scene']    = array( 'startAt' => 0.00, 'endAt' => 0.50 );
			$options['scenes']['backgroundSceneOptions']['scene'] = array( 'startAt' => 0.50, 'endAt' => 1.00 );
			$options['scenes']['logoSceneOptions']['scene']       = array( 'startAt' => 0.00, 'endAt' => 0.50 );
			break;
			
		// Background -> Logo, Padding
		case 'bg-logo_padding':
			$options['scenes']['paddingSceneOptions']['scene']    = array( 'startAt' => 0.50, 'endAt' => 1.00 );
			$options['scenes']['backgroundSceneOptions']['scene'] = array( 'startAt' => 0.00, 'endAt' => 0.50 );
			$options['scenes']['logoSceneOptions']['scene']       = array( 'startAt' => 0.50, 'endAt' => 1.00 );
			break;
		
		// Padding -> Background -> Logo
		case 'padding-bg-logo':
			$options['scenes']['paddingSceneOptions']['scene']    = array( 'startAt' => 0.00, 'endAt' => 0.33 );
			$options['scenes']['backgroundSceneOptions']['scene'] = array( 'startAt' => 0.33, 'endAt' => 0.66 );
			$options['scenes']['logoSceneOptions']['scene']       = array( 'startAt' => 0.66, 'endAt' => 1.00 );
			break;
			
		// Background -> Logo -> Padding
		case 'bg-logo-padding':
			$options['scenes']['paddingSceneOptions']['scene']    = array( 'startAt' => 0.66, 'endAt' => 1.00 );
			$options['scenes']['backgroundSceneOptions']['scene'] = array( 'startAt' => 0.00, 'endAt' => 0.33 );
			$options['scenes']['logoSceneOptions']['scene']       = array( 'startAt' => 0.33, 'endAt' => 0.66 );
			break;
			
		// Logo -> Background -> Padding
		case 'logo-bg-padding':
			$options['scenes']['paddingSceneOptions']['scene']    = array( 'startAt' => 0.66, 'endAt' => 1.00 );
			$options['scenes']['backgroundSceneOptions']['scene'] = array( 'startAt' => 0.33, 'endAt' => 0.66 );
			$options['scenes']['logoSceneOptions']['scene']       = array( 'startAt' => 0.00, 'endAt' => 0.33 );
			break;
	}
	
	// Debug mode
	if ( defined( 'KALIUM_DEBUG' ) ) {
		$options['debugMode'] = true;
	}
	
	return apply_filters( 'kalium_sticky_header_options', $options );
}


// Logo Switch Sections
function kalium_get_logo_switch_sections() {
	$sections = array();
	
	if ( is_singular() && kalium()->acf->get_field( 'section_logo_switch' ) ) {
		$sections = kalium()->acf->get_field( 'logo_switch_sections' );
		
		foreach ( $sections as $i => $section ) {
			
			// Revolution slider height
			if ( 'revslider' == $section['switch_type'] && class_exists( 'RevSliderSlider' ) ) {
				$slider = new RevSliderSlider();
				$slider->initByID( $section['revslider'] );
				
				$sections[ $i ]['slider_height'] = $slider->getParam( 'height' );
			}
		}
	}
	
	return apply_filters( 'kalium_sticky_logo_switch_sections', $sections );
}


// Null Funcion
function kalium_null_function() {}


// Header Search Field
function kalium_header_search_field( $skin = '' ) {
	if ( ! get_data( 'header_search_field' ) ) {
		return;
	}
	
	$animation = get_data( 'header_search_field_icon_animation' );
	?>						
	<div class="header-search-input <?php echo esc_attr( $skin ); ?>">
		<form role="search" method="get" action="<?php echo home_url(); ?>">
		
			<div class="search-field">
				<span><?php _e( 'Search site...', 'kalium' ); ?></span>
				<input type="search" value="" autocomplete="off" name="s" />
			</div>
		
			<div class="search-icon">
				<a href="#" data-animation="<?php echo $animation; ?>">
					<?php echo laborator_get_svg( 'images/icons/search.svg', null, array( 24, 24 ), true ); ?>
				</a>
			</div>
		</form>
		
	</div>
	<?php
}

// Enqueue Lightbox Gallery
function kalium_enqueue_lightbox_library() {
	wp_enqueue_script( 'light-gallery' );
	wp_enqueue_style( 'light-gallery' );
	wp_enqueue_style( 'light-gallery-transitions' );
}

// Enqueue Slick Gallery
function kalium_enqueue_slick_slider_library() {
	wp_enqueue_script( 'slick' );
	wp_enqueue_style( 'slick' );
}

// Mobile menu breakpoint
function kalium_get_mobile_menu_breakpoint() {
	$breakpoint = get_data( 'menu_mobile_breakpoint' );
	
	if ( ! $breakpoint || ! is_numeric( $breakpoint ) ) {
		$breakpoint = 769;
	}
	
	return $breakpoint;
}

// Get Open SSL Version
function get_openssl_version_number( $openssl_version_number = null ) {
	if ( is_null( $openssl_version_number ) ) {
		$openssl_version_number = OPENSSL_VERSION_NUMBER;
	}
	
	$openssl_numeric_identifier = str_pad( (string) dechex( $openssl_version_number ), 8, '0', STR_PAD_LEFT );

	$openssl_version_parsed = array();
	$preg = '/(?<major>[[:xdigit:]])(?<minor>[[:xdigit:]][[:xdigit:]])(?<fix>[[:xdigit:]][[:xdigit:]])';
	$preg.= '(?<patch>[[:xdigit:]][[:xdigit:]])(?<type>[[:xdigit:]])/';
	
	preg_match_all( $preg, $openssl_numeric_identifier, $openssl_version_parsed );
	
	$openssl_version = false;
	
	if ( ! empty( $openssl_version_parsed ) ) {
		$openssl_version  = intval( $openssl_version_parsed['major'][0] ).'.';
		$openssl_version .= intval( $openssl_version_parsed['minor'][0] ) .'.';
		$openssl_version .= intval( $openssl_version_parsed['fix'][0] );
		$patchlevel_dec   = hexdec( $openssl_version_parsed['patch'][0] );
	}
	
	return $openssl_version;
}
