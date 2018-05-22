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


// Add Do-shortcode for text widgets
function execute_shortcodes_in_widget_text( $text ) {
	return do_shortcode( $text );
}

add_filter( 'widget_text', 'execute_shortcodes_in_widget_text' );


// Date Shortcode
function laborator_shortcode_date( $atts = array(), $content = '' ) {
	return date_i18n( get_option( 'date_format' ) );
}

if ( ! shortcode_exists( 'date' ) ) {
	add_shortcode( 'date', 'laborator_shortcode_date' );
}

// Shortcode for Social Networks [lab_social_networks]
function shortcode_lab_social_networks( $atts = array(), $content = '' ) {
	$custom_icon 		= get_data( 'social_network_custom_link_icon' );
	
	$social_order		= get_data( 'social_order' );
	$social_order_list	= apply_filters( 'kalium_social_networks_array', array(
		'fb'      => array( 
			'title'  => 'Facebook',
			'icon'   => 'fa fa-facebook'
		),
		'tw'      => array(
			'title'  => 'Twitter',
			'icon'   => 'fa fa-twitter'
		),
		'lin'     => array(
			'title'  => 'LinkedIn',
			'icon'   => 'fa fa-linkedin'
		),
		'yt'      => array(
			'title'  => 'YouTube',
			'icon'   => 'fa fa-youtube-play'
		),
		'vm'      => array(
			'title'  => 'Vimeo',
			'icon'   => 'fa fa-vimeo'
		),
		'drb'     => array(
			'title'  => 'Dribbble',
			'icon'   => 'fa fa-dribbble'
		),
		'ig'      => array(
			'title'  => 'Instagram',
			'icon'   => 'fa fa-instagram' 
		),
		'pi'      => array(
			'title'  => 'Pinterest',
			'icon'   => 'fa fa-pinterest' 
		),
		'gp'      => array(
			'title'  => 'Google+',
			'icon'   => 'fa fa-google-plus' 
		),
		'vk'      => array(
			'title'  => 'VKontakte',
			'icon'   => 'fa fa-vk' 
		),
		'fl'      => array(
			'title'  => 'Flickr',
			'icon'   => 'fa fa-flickr'
		),
		'be'      => array(
			'title'  => 'Behance',
			'icon'   => 'fa fa-behance' 
		),
		'fs'      => array(
			'title'  => 'Foursquare',
			'icon'   => 'fa fa-foursquare'
		),
		'sk'      => array(
			'title'  => 'Skype',
			'icon'   => 'fa fa-skype'
		),
		'tu'      => array(
			'title'  => 'Tumblr',
			'icon'   => 'fa fa-tumblr'
		),
		'da'      => array(
			'title'  => 'DeviantArt',
			'icon'   => 'fa fa-deviantart'
		),
		'gh'      => array(
			'title'  => 'GitHub',
			'icon'   => 'fa fa-github'
		),
		'sc'      => array(
			'title'  => 'SoundCloud',
			'icon'   => 'fa fa-soundcloud'
		),
		'hz'      => array(
			'title'  => 'Houzz',
			'icon'   => 'fa fa-houzz'
		),
		'px'      => array(
			'title'  => '500px',
			'icon'   => 'fa fa-500px',
			'prefix' => 'social',
		),
		'xi'      => array(
			'title'  => 'Xing',
			'icon'   => 'fa fa-xing'
		),
		'sp'      => array(
			'title'  => 'Spotify',
			'icon'   => 'fa fa-spotify'
		),
		'sn'      => array(
			'title'  => 'Snapchat',
			'icon'   => 'fa fa-snapchat-ghost',
			'dark'	 => true
		),
		'em'      => array(
			'title'  => __( 'Email', 'kalium' ),
			'icon'   => 'fa fa-envelope-o'
		),
		'yp'      => array(
			'title'  => 'Yelp',
			'icon'   => 'fa fa-yelp'
		),
		'ta'      => array(
			'title'  => 'TripAdvisor',
			'icon'   => 'fa fa-tripadvisor'
		),
		
		'custom'  => array(
			'title'  => get_data( 'social_network_custom_link_title' ), 			
			'href'   => get_data( 'social_network_custom_link_link' ),
			'icon'   => 'fa ' . ( $custom_icon ? "fa-{$custom_icon}" : 'fa-plus' ),
		),
	) );

	// Social Networks Class
	$class = 'social-networks';
	
	if ( isset( $atts['class'] ) ) {
		$class .= ' ' . $atts['class'];
	}
	
	// Rounded Social Networks
	if ( is_array( $atts ) && in_array( 'rounded', $atts ) ) {
		$class .= ' rounded';
	} else {
		$class .= ' textual';
	}
	
	// Colored Text
	if ( is_array( $atts ) && ( in_array( 'colored', $atts ) || 'hover' == get_array_key( $atts, 'colored' ) ) ) {
		
		if ( is_array( $atts ) && 'hover' == get_array_key( $atts, 'colored' ) ) {
			$class .= ' colored-hover';
		} else {
			$class .= ' colored';	
		}
	}	
	// Colored Background
	else if ( is_array( $atts ) && ( in_array( 'colored-bg', $atts ) || 'hover' == get_array_key( $atts, 'colored-bg' ) ) ) {
		
		if ( is_array( $atts ) && 'hover' == get_array_key( $atts, 'colored-bg' ) ) {
			$class .= ' colored-bg-hover';
		} else {
			$class .= ' colored-bg';
		}
	}
	
	$html = '<ul class="' . esc_attr( $class ) . '">';

	foreach ( $social_order['visible'] as $key => $title ) {
		
		if ( $key == 'placebo' ) {
			continue;
		}

		$sn = $social_order_list[ $key ];
		
		$href = get_data( "social_network_link_{$key}" );
		$class = sanitize_title( $title );
		
		// Prefixed
		if ( isset( $sn['prefix'] ) ) {
			$class = "{$sn['prefix']}-" . $class;
		}
		
		if ( $key == 'custom' ) {
			$title   = $sn['title'];
			$href    = $sn['href'];
			$class 	 = 'custom';
		}
		
		$title_span = $title;
		
		if ( isset( $atts['class'] ) && strpos( $atts['class'], 'rounded' ) >= 0 ) {
			$title_span = $title;
		}
		
		$link_target = get_data( 'social_networks_target_attr', '_blank' );
		
		if ( is_email( $href ) ) {
			$link_target = '_self';
			$subject = get_data( 'social_network_link_em_subject' );
		
			$href = "mailto:{$href}";
			
			if ( $subject ) {
				$href .= '?subject=' . esc_attr( $subject );
			}
		}
		
		// Dark Class
		if ( ! empty( $sn['dark'] ) ) {
			$class .= ' dark';
		}
			
		$html .= '<li>';
			$html .= '<a href="' . $href . '" target="' . $link_target . '" class="' . $class . '" title="' . $title . '">';
				$html .= '<i class="' . $sn['icon'] . '"></i>';
				$html .= '<span class="name">' . apply_filters( 'kalium_social_networks_name' , $title_span, $title ) . '</span>';
			$html .= '</a>';
		$html .= '</li>';
	}

	$html .= '</ul>';


	return apply_filters( 'shortcode_social_networks_shortcode', $html );

}

add_shortcode( 'lab_social_networks', 'shortcode_lab_social_networks' );


// Excerpt Length & More
function laborator_supershort_excerpt_length() {
	return 18;
}


// Replace Shop/Archive Page Settings
function kalium_replace_shop_archive_object( $post ) {
		
	// Replace Query Object for WooCommerce Shop Archive
	if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_category() || is_product_tag() ) ) {
		$post = get_post( get_option( 'woocommerce_shop_page_id' ) );
	}
	
	return $post;
}

add_filter( 'kalium_replace_shop_archive_object', 'kalium_replace_shop_archive_object' );


// Body Class
function laborator_header_spacing() {
	global $wp_query;
	
	$qo = apply_filters( 'kalium_replace_shop_archive_object', get_queried_object() );
	
	$header_position = get_data( 'header_position' );
	$header_spacing = get_data( 'header_spacing' );
	
	if ( is_paged() ) {
		return;
	}
	
	if ( is_singular() ) {
		$post_id = get_the_ID();
	} else if ( $qo instanceof WP_Post ) {
		$post_id = $qo->ID;
	}
	
	// Custom Post
	if ( isset( $post_id ) ) {
		// Header Position
		$page_header_position = kalium()->acf->get_field( 'header_position', $post_id );
		$page_header_spacing = kalium()->acf->get_field( 'header_spacing', $post_id );
		
		if ( ! empty( $page_header_position ) && $page_header_position != 'inherit' ) {
			$header_position = $page_header_position;
			$header_spacing = $page_header_spacing;
			
			add_filter( 'get_data_header_position', kalium_hook_return_value( $header_position ) );
			add_filter( 'get_data_header_spacing', kalium_hook_return_value( $header_spacing ) );
		}
		
		// Footer Visibility
		$footer_visibility = kalium()->acf->get_field( 'footer_visibility', $post_id );
		
		if ( in_array( $footer_visibility, array( 'show', 'hide' ) ) ) {
			add_filter( 'kalium_show_footer', ( $footer_visibility == 'hide' ? '__return_false' : '__return_true' ), 10 );
		}
		
		// Fixed Footer
		$fixed_footer = kalium()->acf->get_field( 'fixed_footer', $post_id );
		
		if ( in_array( $fixed_footer, array( 'normal', 'fixed', 'fixed-fade', 'fixed-slide' ) ) ) {
			
			if ( $fixed_footer == 'normal' ) {
				$fixed_footer = '';
			}
			
			add_filter( 'get_data_footer_fixed', kalium_hook_return_value( $fixed_footer ) );
		}
	}
	
	// Header Position
	if ( $header_position == 'absolute' && ! post_password_required( $qo ) ) {
		define( 'HEADER_ABSOLUTE_SPACING', intval( $header_spacing ) );
		add_filter( 'body_class', 'laborator_header_spacing_body_class' );
	}
}

function laborator_header_spacing_body_class( $classes ) {
	
	if ( defined( 'HEADER_ABSOLUTE_SPACING' ) ) {
		$classes[] = 'header-absolute';
		
		$header_spacing = str_replace( 'px', '', HEADER_ABSOLUTE_SPACING );
		generate_custom_style( '.wrapper', "padding-top: {$header_spacing}px !important", '', true );
	}
	
	return $classes;
}

add_action( 'wp', 'laborator_header_spacing' );

// Full-width Header
function kalium_header_footer_fullwidth() {
	
	$qo = apply_filters( 'kalium_replace_shop_archive_object', get_queried_object() );
	
	if ( $qo instanceof WP_Post ) {
		$post_id = $qo->ID;
		$header_fullwidth = kalium()->acf->get_field( 'header_fullwidth', $post_id );
		$footer_fullwidth = kalium()->acf->get_field( 'footer_fullwidth', $post_id );
		
		if ( in_array( $header_fullwidth, array( 'yes', 'no' ) ) ) {
			add_filter( 'get_data_header_fullwidth', $header_fullwidth == 'yes' ? '__return_true' : '__return_false' );
		}
		
		if ( in_array( $footer_fullwidth, array( 'yes', 'no' ) ) ) {
			add_filter( 'get_data_footer_fullwidth', $footer_fullwidth == 'yes' ? '__return_true' : '__return_false' );
		}
	}
}

add_action( 'wp', 'kalium_header_footer_fullwidth' );


// Skin Compiler
function laborator_custom_skin_generate( $data, $force_regenerate = false ) {
	if ( ! $force_regenerate ) {
		if ( ! defined( 'DOING_AJAX' ) ) {
			return $data;
		} elseif ( ! in_array( $_REQUEST['action'], array( 'of_ajax_post_action', 'lab_1cl_demo_install_package_content' ) ) ) {
			return $data;
		}
	} else {
		$data = get_data();
	}
	
	
	if ( isset( $data['use_custom_skin'] ) && $data['use_custom_skin'] ) {
		update_option( 'kalium_skin_custom_css', '' );
	
		$colors = array();
		
		$custom_skin_bg_color         = $data['custom_skin_bg_color'];
		$custom_skin_link_color       = $data['custom_skin_link_color'];
		$custom_skin_headings_color   = $data['custom_skin_headings_color'];
		$custom_skin_paragraph_color  = $data['custom_skin_paragraph_color'];
		$custom_skin_footer_bg_color  = $data['custom_skin_footer_bg_color'];
		$custom_skin_borders_color    = $data['custom_skin_borders_color'];
		
		$custom_skin_bg_color         = $custom_skin_bg_color 			? 	kalium_format_color_value( $custom_skin_bg_color ) 			: '#FFFFFF';
		$custom_skin_link_color       = $custom_skin_link_color 		? 	kalium_format_color_value( $custom_skin_link_color )		: '#F6364D';
		$custom_skin_headings_color   = $custom_skin_headings_color 	? 	kalium_format_color_value( $custom_skin_headings_color )	: '#F6364D';
		$custom_skin_paragraph_color  = $custom_skin_paragraph_color 	? 	kalium_format_color_value( $custom_skin_paragraph_color )	: '#777777';
		$custom_skin_footer_bg_color  = $custom_skin_footer_bg_color	? 	kalium_format_color_value( $custom_skin_footer_bg_color )	: '#FAFAFA';
		$custom_skin_borders_color    = $custom_skin_borders_color 		? 	kalium_format_color_value( $custom_skin_borders_color )		: '#EEEEEE';
		
		$files = array(
			kalium()->locateFile( "assets/less/other-less/lesshat.less" ) => "include",
			kalium()->locateFile( "assets/less/skin-generator.less" )     => "parse",
		);
		
		$vars = array(
			'bg-color'   => $custom_skin_bg_color,
			'link-color' => $custom_skin_link_color,
			'heading'    => $custom_skin_headings_color,
			'paragraph'  => $custom_skin_paragraph_color,
			'footer'     => $custom_skin_footer_bg_color,
			'border'     => $custom_skin_borders_color,
		);
		
		$css_style = kalium_generate_less_style( $files, $vars );
		
		update_option( 'kalium_skin_custom_css', $css_style );
		kalium_generate_custom_skin_file();
	}
	
	return $data;
}

add_filter( 'of_options_before_save', 'laborator_custom_skin_generate' );


/* Removed in 2.2
// Font Compiler (Deprecated)
function laborator_custom_font_generate( $data ) {
	if ( ! defined( 'DOING_AJAX' ) )
	{
		return $data;
	} elseif ( ! in_array( $_REQUEST['action'], array( 'of_ajax_post_action', 'lab_1cl_demo_install_package_content' ) ) ) {
		return $data;
	}
	
	if ( isset( $data['use_custom_font'] ) && $data['use_custom_font'] ) {
		update_option( 'kalium_font_custom_css', '' );
		
		$default_font_family = '"Karla", Arial, sans-serif';
		
		$font_primary             = $data['font_primary'];
		$font_primary_weight      = $data['font_primary_weight'];
		$font_primary_transform   = $data['font_primary_transform'];
		
		$font_heading             = $data['font_heading'];
		$font_heading_weight      = $data['font_heading_weight'];
		$font_heading_transform   = $data['font_heading_transform'];
		
		$font_primary   = in_array( $font_primary, array( 'none' ) ) ? $default_font_family : "'{$font_primary}', sans-serif";
		$font_heading   = in_array( $font_heading, array( 'none' ) ) ? $default_font_family : "'{$font_heading}', sans-serif";
		
		$files = array(
			kalium()->locateFile( "assets/less/typo-generator.less" ) => "parse",
		);
		
		// Custom Fonts 
		if ( $data['custom_primary_font_url'] && $data['custom_primary_font_name'] ) {
			$font_primary            = $data['custom_primary_font_name'];
			$font_primary_weight     = $data['custom_primary_font_weight'];
			$font_primary_transform  = $data['custom_primary_font_transform'];
		}
		
		if ( $data['custom_heading_font_url'] && $data['custom_heading_font_name'] ) {
			$font_heading              = $data['custom_heading_font_name'];
			$font_heading_weight       = $data['custom_heading_font_weight'];
			$font_heading_transform    = $data['custom_heading_font_transform'];
		}
		
		$vars = array(
			'primary-font'           => $font_primary,
			'primary-font-weight'    => $font_primary_weight,
			'primary-transform'      => $font_primary_transform,
			
			'heading-font'           => $font_heading,
			'heading-font-weight'    => $font_heading_weight,
			'heading-transform'      => $font_heading_transform,
		);
		
		$css_style = kalium_generate_less_style( $files, $vars );
		
		update_option( 'kalium_font_custom_css', $css_style );
	}
	
	return $data;
}

add_filter( 'of_options_before_save', 'laborator_custom_font_generate' );
*/


// Remove Plugin Notices
if ( defined( 'LS_PLUGIN_BASE' ) ) {
	remove_action( 'after_plugin_row_' . LS_PLUGIN_BASE, 'layerslider_plugins_purchase_notice', 10 );
}


// General Body Class Filter
function laborator_body_class( $classes ) {
	if ( get_data( 'theme_borders' ) ) {
		$classes[] = 'has-page-borders';
	}
	
	if ( get_data( 'footer_fixed' ) ) {
		$classes[] = 'has-fixed-footer';
	}
	
	return $classes;
}

add_filter( 'body_class', 'laborator_body_class' );


// Portfolio Like Share Options
function shortcode_lab_portfolio_like_share() {
	ob_start();
	include locate_template( 'tpls/portfolio-single-like-share.php' );
	return ob_get_clean();
}

add_shortcode( 'lab_portfolio_like_share', 'shortcode_lab_portfolio_like_share' );

// Like Button
function kalium_shortcode_ajax_like_button( $atts ) {
	global $post;
	
	$id = $post->ID;
	
	// Custom ID
	if ( isset( $atts['id'] ) ) {
		$id = $atts['id'];
	}
	
	$likes = get_post_likes( $id );

	// Like Icon Class
	$like_icon_default = 'fa-heart-o';
	$like_icon_liked = 'fa-heart';
	
	ob_start();
	?>
	<a href="#" class="like-btn" data-id="<?php echo $id; ?>">
		<i class="icon fa <?php echo $likes['liked'] ? $like_icon_liked : $like_icon_default; ?>"></i>
		<span class="counter like-count"><?php echo esc_html( $likes['count'] ); ?></span>
	</a>
	<?php
	
	return ob_get_clean();
}

add_shortcode( 'kalium_ajax_like_button', 'kalium_shortcode_ajax_like_button' );

// Share buttons
function kalium_shortcode_portfolio_share_buttons( $atts ) {
	global $post;
	
	$id = $post->ID;
	
	// Custom ID
	if ( isset( $atts['id'] ) ) {
		$id = $atts['id'];
		
	}
	
	// Portfolio share networks
	$share_networks = get_data( 'portfolio_share_item_networks' );
	
	ob_start();
	?>
	<div class="social-links">
		
		<?php
			foreach ( $share_networks['visible'] as $network_id => $network ) {
		
				if ( 'placebo' == $network_id ) {
					continue;
				}
		
				share_story_network_link( $network_id, $id, 'social-share-icon', true );
		
			}
		?>
	
	</div>
	<?php
	
	return ob_get_clean();
}

add_shortcode( 'kalium_portfolio_share_buttons', 'kalium_shortcode_portfolio_share_buttons' );


// Current Portfolio Menu Item Highlight (Bug fix)
function portfolio_current_nav_class( $classes, $item ) {
	
	if ( ! isset( $item->url ) ) {
		return $item;
	}
	
	$path_info = pathinfo( $item->url );
	
	if ( $path_info['filename'] == get_data( 'portfolio_prefix_url_slug', 'portfolio' ) ) {
		$classes[] = 'current-menu-item current_page_item';
	}
	
    return $classes;
}

if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
	$req_path_info = pathinfo( $_SERVER['REQUEST_URI'] );
	
	if ( ! empty( $req_path_info['filename'] ) && $req_path_info['filename'] == get_data( 'portfolio_prefix_url_slug', 'portfolio' ) ) {
		add_filter( 'nav_menu_css_class', 'portfolio_current_nav_class', 10, 2 );
	}
}

// Portfolio Post Type Args
function portfolio_posttype_args( $args ) {
	
	// URL Slug for Portfolio Works
	$portfolio_prefix_url_slug = sanitize_title( get_data( 'portfolio_prefix_url_slug' ) );
	
	if ( $portfolio_prefix_url_slug ) {
		$args['rewrite']['slug'] = $portfolio_prefix_url_slug;
	}
	
	return $args;
}

add_filter( 'portfolioposttype_args', 'portfolio_posttype_args', 1000 );


// Portfolio Category Args
function portfolio_category_tax_args( $args ) {
	
	// URL Slug for Portfolio Category
	$portfolio_category_prefix_url_slug = sanitize_title( get_data( 'portfolio_category_prefix_url_slug' ) );
	
	if ( $portfolio_category_prefix_url_slug ) {
		$args['rewrite']['slug'] = $portfolio_category_prefix_url_slug;
	} else {
		$args['rewrite']['slug'] = 'portfolio-category';
	}
	
	return $args;
}

add_filter( 'portfolioposttype_category_args', 'portfolio_category_tax_args', 1000 );


// Proportional Image Height on Blog
function kalium_blog_thumbnail_size_proportional( $size ) {
	return 'large';
}


// Ninja Forms Support
function kalium_ninja_forms_display_field_class( $field_class, $field_id, $field_row ) {
	global $ninja_forms_fields;
	
	switch( $field_row['type'] ) {
		
		case '_submit':
		case '_timed_submit':
			$field_class .= ' btn btn-default';
			break;
		
		// Break Rule
		case '_hr':
			break;
			
		// Text Description
		case '_desc':
			break;
		
		// Checkbox & Radio
		case '_checkbox':
		case '_radio':
			break;
			
		// Text inputs
		default:
			$field_class .= ' form-control';
	}
	
	return $field_class;
}

add_filter( 'ninja_forms_display_field_class', 'kalium_ninja_forms_display_field_class', 10, 3 );


// Footer Visibility
$footer_visibility = get_data( 'footer_visibility', true ) ? '__return_true' : '__return_false';

add_filter( 'kalium_show_footer', $footer_visibility, 1 );


// LayerSlider hide Notice
add_filter( 'option_layerslider-authorized-site', '__return_true', 1000 );


// File Based Custom Skin
function kalium_use_filebased_custom_skin_filter( $use ) {
	// Generate Skin Hash (Prevent Cache Issues)
	if ( $use ) {
		$skin_colors_vars = array( 'custom_skin_bg_color', 'custom_skin_link_color', 'custom_skin_link_color', 'custom_skin_headings_color', 'custom_skin_paragraph_color', 'custom_skin_footer_bg_color', 'custom_skin_borders_color' );
		$skin_colors_hash = '';
		
		foreach ( $skin_colors_vars as $var ) {
			$skin_colors_hash .= get_data( $var );
		}
		
		$skin_colors_hash = md5( kalium()->getVersion() . $skin_colors_hash );
		

		// Eneuque skin		
		$custom_skin_filename = kalium_get_custom_skin_filename();
		
		if ( defined( 'KALIUM_VERSION_DEBUG' ) ) {
			$skin_colors_hash = md5( $skin_colors_hash . time() );
		}
		
		if ( is_child_theme() ) {
			wp_enqueue_style( 'custom-skin', get_stylesheet_directory_uri() . '/' . $custom_skin_filename, null, $skin_colors_hash );
		} else {
			wp_enqueue_style( 'custom-skin', get_stylesheet_directory_uri() . '/assets/css/' . $custom_skin_filename, null, $skin_colors_hash );
		}
	}
}

add_filter( 'kalium_use_filebased_custom_skin', 'kalium_use_filebased_custom_skin_filter', 10 );


// Portfolio Loop Thumbnail Custom Sizes
function kalium_portfolio_loop_custom_thumbnail_size( $size, $type ) {
	if ( 'type-1' == $type && ( $custom_size = get_data( 'portfolio_thumbnail_size_1' ) ) ) {
		return $custom_size;
	} elseif ( 'type-2' == $type && ( $custom_size = get_data( 'portfolio_thumbnail_size_2' ) ) ) {
		return $custom_size;
	}
	
	return $size;
}

// Portfolio Head Title Meta Tag
function portfolioposttype_args_head_title( $args ) {
	$args['labels']['name'] = get_data( 'portfolio_title' );
	return $args;
}

if ( get_data( 'portfolio_title' ) ) {
	add_filter( 'portfolioposttype_args', 'portfolioposttype_args_head_title' );
}

// Disabled comments on blog posts
if ( 'hide' == get_data( 'blog_comments' ) ) {
	add_filter( 'kalium_blog_enable_comments', '__return_false' );
}


// Remove Dot from Social Networks
function kalium_social_networks_name_remove_dot( $name ) {
	return preg_replace( '/\.$/', '', $name );
}

// Default Font
function kalium_set_default_font( $fonts, $valid_fonts, $published_fonts ) {
	$use_default_font = ! get_data( 'use_custom_font' ) && empty( $fonts ) && ! is_admin() && ! defined( 'DOING_AJAX' ) && $valid_fonts && $published_fonts;
	
	if ( apply_filters( 'kalium_typolab_use_default_font', $use_default_font ) ) {
		
		$default_font = array(
			'id' => 'default-font',
			'source' => 'google',
			'options' => array(
				'data' => ( (object) array(
					 'family' => 'Roboto',
					 'variants' =>  array( '100', '100italic', '300', '300italic', 'regular', 'italic', '500', '500italic', '700', '700italic', '900', '900italic', ),
					 'subsets' => array( 'greek', 'latin-ext', 'cyrillic', 'vietnamese', 'latin', 'greek-ext', 'cyrillic-ext', ),
				) ),
				'selectors' => array(
					array(
						'selector' => 'h1, h2, h3, h4, h5, h6',
						'variant' => '300',
						'font-sizes' => array( 'general' => '', 'desktop' => '', 'tablet' => '', 'mobile' => '', 'unit' => 'px', ),
					),
					array(
						'selector' => 'body, p',
						'variant' => '300',
						'font-sizes' => array( 'general' => '', 'desktop' => '', 'tablet' => '', 'mobile' => '', 'unit' => 'px', ),
					),
				),
			),
			'valid'          => true,
			'family'         => 'Roboto',
			'variants'       => array( '300', '300italic', ),
			'subsets'        => array( 'latin', ),
			'font_status'    => 'published',
			'font_placement' => '',
		);
		
		$fonts[] = $default_font;
	}
	
	return $fonts;
}

add_filter( 'typolab_get_fonts', 'kalium_set_default_font', 10, 3 );


// Disable Kalium Open Graph data generation when Yoast is enabled
if ( defined( 'WPSEO_VERSION' ) ) {
	$social = WPSEO_Options::get_option( 'wpseo_social' );
	
	if ( isset( $social['opengraph'] ) ) {
		add_filter( 'kalium_open_graph_meta', '__return_false' );
	}
}


// Fix image dimensions issue with SVG files
function kalium_fix_svg_size_for_images( $image, $attachment_id = null ) {
	
	if ( kalium()->helpers->isSVG( $image[0] ) && ! ( $image[1] && $image[2] ) ) {
		$svg_dimensions = kalium()->helpers->getSVGDimensions( $attachment_id );
		$image[1] = $svg_dimensions[0];
		$image[2] = $svg_dimensions[1];
	}
	
	return $image;
}

add_filter( 'wp_get_attachment_image_src', 'kalium_fix_svg_size_for_images', 10, 2 );


// Jetpack remove YouTube and Vimeo embbed
function kalium_jetpack_remove_youtube_vimeo_shortcodes( $shortcodes ) {
	$jetpack_shortcodes_dir = WP_CONTENT_DIR . '/plugins/jetpack/modules/shortcodes/';
	
	$shortcodes_to_unload = array( 'youtube.php', 'vimeo.php' );
	
	foreach ( $shortcodes_to_unload as $shortcode ) {
		if ( $key = array_search( $jetpack_shortcodes_dir . $shortcode, $shortcodes ) ) {
			unset( $shortcodes[ $key ] );
		}
	}
	
	return $shortcodes;
}

add_filter( 'jetpack_shortcodes_to_include', 'kalium_jetpack_remove_youtube_vimeo_shortcodes', 10 );
