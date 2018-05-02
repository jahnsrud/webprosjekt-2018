<?php
/**
 *	Kalium WordPress Theme
 *
 *	Core Template Functions
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Theme default widgets
 */
if ( ! function_exists( 'kalium_widgets_init' ) ) {
	
	function kalium_widgets_init() {
		// Widget wrappers
		$before_widget = '<div id="%1$s" class="widget %2$s">';
		$after_widget  = '</div>';
		
		// Core widgets
		$widgets = array(
			// Blog Sidebar
			array(
				'id' => 'blog_sidebar',
				'name' => 'Blog Archive',
			),
			// Sidebar on single post
			array(
				'id' => 'blog_sidebar_single',
				'name' => 'Single Post',
			),
			// Footer Sidebar
			array(
				'id' => 'footer_sidebar',
				'name' => 'Footer',
			),
			// Top Menu Sidebar
			array(
				'id' => 'top_menu_sidebar',
				'name' => 'Top Menu',
			),
			// Sidebar Menu Widgets
			array(
				'id' => 'sidebar_menu_sidebar',
				'name' => 'Sidebar Menu',
			),
			// Shop Sidebar
			array(
				'id' => 'shop_sidebar',
				'name' => 'Shop Archive',
			),
			// Sidebar on single post
			array(
				'id' => 'shop_sidebar_single',
				'name' => 'Single Product',
			),
		);
		
		// Load sidebars (when the plugin is inactive)
		if ( ( $custom_sidebars = get_option( 'cs_sidebars', null ) ) && false == kalium()->helpers->isPluginActive( 'custom-sidebars/customsidebars.php' ) ) {
			foreach ( $custom_sidebars as $widget ) {
				$widgets[] = array(
					'id' => $widget['id'],
					'name' => $widget['name'],
					'description' => 'Inherited from Custom Sidebars plugin'
				);
			}
		}
		
		// Kalium Widgets Filter
		$widgets = apply_filters( 'kalium_widgets_array', $widgets );
		
		// Initialize widgets
		foreach ( $widgets as $widget ) {
			register_sidebar( array(
				'id' => $widget['id'],
				'name' => $widget['name'],
				'before_widget' => $before_widget,
				'after_widget' => $after_widget,
				'description' => get_array_key( $widget, 'description' )
			) );
		}
	}
}

/**
 * Handler function for Endless Pagination via AJAX
 */
if ( ! function_exists( 'kalium_endless_pagination_get_paged_items' ) ) {
	
	function kalium_endless_pagination_get_paged_items() {
		$response = array(
			'hasMore' => false,
			'hasItems' => false,
			'hasQueryFilter' => false,
		);
		
		$action        = kalium()->post( 'action' );
		$loop_handler  = kalium()->post( 'loopHandler' );
		$loop_template = kalium()->post( 'loopTemplate' );
		$base_query    = kalium()->post( 'baseQuery' );
		$args          = kalium()->post( 'args' );
		$pagination    = kalium()->post( 'pagination' );
		$query_filter  = kalium()->post( 'queryFilter' );
		
		// Execute attached "pre" actions
		do_action( 'kalium_endless_pagination_pre_get_paged_items', $args );
		
		// Query
		$fetched_ids    = array_map( 'absint', $pagination['fetchedItems'] );
		$posts_per_page = absint( $pagination['perPage'] );
		$total_items    = absint( $pagination['totalItems'] );
		
		$wp_query_args  = (array) $base_query;
		
		// Extra query filter
		if ( ! empty( $query_filter ) && is_array( $query_filter ) ) {
			$wp_query_args = array_merge( $wp_query_args, $query_filter );
			
			$response['hasQueryFilter'] = true;
		}
		
		// Set pagination data
		$wp_query_args = array_merge( $wp_query_args, array(
			'post_status' => 'publish',
			'posts_per_page' => $posts_per_page,
			'post__not_in' => $fetched_ids,
		) );
		
		// Custom loop handler
		if ( $loop_handler && kalium_infinite_scroll_valid_handler( $loop_handler ) ) {
			wp_send_json_success( call_user_func( $loop_handler, $posts_per_page, $total_items, $fetched_ids, $wp_query_args ) );
		}
		
		query_posts( $wp_query_args );
		
		// Load items
		if ( have_posts() ) {
			$new_fetched_ids = array();
			
			ob_start();
			
			// Posts loop
			while ( have_posts() ) {
				the_post();
				
				// Fetched ID
				$new_fetched_ids[] = get_the_id();
				
				// Loop template
				if ( function_exists( $loop_template ) && kalium_infinite_scroll_valid_handler( $loop_template ) ) {
					call_user_func( $loop_template );
				}
			}
			
			// Reset query
			wp_reset_postdata();
			wp_reset_query();
			
			$response['fetchedItems'] = $new_fetched_ids;
			$response['items']        = ob_get_clean();
			$response['hasMore']      = count( $fetched_ids ) + count( $new_fetched_ids ) < $total_items;
			$response['hasItems']     = true;
		}
		
		wp_send_json_success( $response );
	}
}

/**
 * Get widgets of specific sidebar
 */
if ( ! function_exists( 'kalium_get_widgets' ) ) {
	
	function kalium_get_widgets( $sidebar_id, $class = '' ) {
		$classes = array( 'widget-area' );
		
		if ( is_array( $class ) ) {
			$classes = array_merge( $classes, $class );
		} else if ( ! empty( $class ) ) {
			$classes[] = $class;
		}
		
		?>
		<div class="<?php echo implode( ' ', array_map( 'sanitize_html_class', apply_filters( 'kalium_widget_area_classes', $classes, $sidebar_id ) ) ); ?>" role="complementary">
			
			<?php
				// Show sidebar widgets
				dynamic_sidebar( $sidebar_id );
			?>
			
		</div>
		<?php
	}
}

/**
 * Set sidebar skin class/es
 */
if ( ! function_exists( 'kalium_get_widgets_classes' ) ) {
	
	function kalium_set_widgets_classes( $classes = array() ) {
		$skin = get_data( 'sidebar_skin' );
		
		if ( in_array( $skin, array( 'bordered', 'background-fill' ) ) ) {
			$classes[] = sprintf( 'widget-area--skin-%s', $skin );
		}
		
		return $classes;
	}
}

/**
 * Custom sidebars params
 */
if ( ! function_exists( 'kalium_custom_sidebars_params' ) ) {
	
	function kalium_custom_sidebars_params( $sidebar ) {
		// Widget wrappers
		$before_widget = '<div id="%1$s" class="widget %2$s">';
		$after_widget  = '</div>';
		$before_title  = '<h3 class="widget-title">';
		$after_title   = '</h3>';
		
		$sidebar['before_widget'] = $before_widget;
		$sidebar['after_widget'] = $after_widget;
		$sidebar['before_title'] = $before_title;
		$sidebar['after_title'] = $after_title;
	
		return $sidebar;
	}
}

/**
 * Password protected post form
 */
if ( ! function_exists( 'kalium_the_password_form' ) ) {
	
	function kalium_the_password_form( $output ) {
		$output = str_replace( 'type="submit"', sprintf( 'type="submit" %s', 'class="button button-small"' ), $output );
		return $output;
	}
}

/**
 * Kalium get default excerpt length
 */
if ( ! function_exists( 'kalium_get_default_excerpt_length' ) ) {
	
	function kalium_get_default_excerpt_length() {
		return apply_filters( 'kalium_get_default_excerpt_length', 55 );
	}
}

/**
 * Excerpt more dots
 */
if ( ! function_exists( 'kalium_get_default_excerpt_more' ) ) {
	
	function kalium_get_default_excerpt_more() {
		return apply_filters( 'kalium_get_default_excerpt_more', '&hellip;' );
	}
}

/**
 * Footer class function
 */
if ( ! function_exists( 'kalium_footer_class' ) ) {
	
	function kalium_footer_class( $_classes = array() ) {
		
		// Classes
		$classes = array( 'site-footer', 'main-footer' );
		
		// Extra classes
		if ( ! empty( $_classes ) && is_array( $_classes ) ) {
			$classes = array_merge( $classes, $_classes );
		}
		
		echo sprintf( 'class="%s"', kalium()->helpers->showClasses( apply_filters( 'kalium_footer_class', $classes ) ) );
	}
}

/**
 * Assign footer classes
 *
 * @type filter
 */
if ( ! function_exists( 'kalium_get_footer_classes' ) ) {
	
	function kalium_get_footer_classes( $classes ) {
		
		$fixed        = get_data( 'footer_fixed' );
		$full_width   = get_data( 'footer_fullwidth' );
		$style        = get_data( 'footer_style' );
		$bottom_style = get_data( 'footer_bottom_style' );
		
		$classes[] = 'footer-bottom-' . $bottom_style;
		
		if ( $fixed ) {
			$classes[] = 'fixed-footer';
			
			if ( $fixed == 'fixed-fade' ) {
				$classes[] = 'fixed-footer-fade';
			}
			else if ( $fixed == 'fixed-slide' ) {
				$classes[] = 'fixed-footer-slide';
			}
		}
		
		if ( $style ) {
			$classes[] = 'site-footer-' . $style;
			$classes[] = 'main-footer-' . $style; // Deprecated
		}
		
		// Full-width footer
		if ( $full_width ) {
			$classes[] = 'footer-fullwidth';
		}
		
		return $classes;
	}
}

/**
 * Show classes attribute array
 */
if ( ! function_exists( 'kalium_class_attr' ) ) {

	function kalium_class_attr( $classes, $echo = true ) {
		
		$class = sprintf( 'class="%s"', kalium()->helpers->showClasses( $classes ) );
		
		if ( $echo ) {
			echo $class;
			return '';
		}
		
		return $class;
	}
}



/**
 * Get nav menu
 */
if ( ! function_exists( 'kalium_nav_menu' ) ) {
	
	function kalium_nav_menu( $menu_location = 'main-menu' ) {
		
		if ( $menu_location == '' || $menu_location == '-' ) {
			return '';
		}
		
		$args = array(
			'container'       => '',
			'theme_location'  => $menu_location,
			'echo'            => false,
			'link_before'     => '<span>',
			'link_after'      => '</span>',
		);
		
		if ( is_numeric( $menu_location ) ) {
			$args['menu'] = $menu_location;
			unset( $args['theme_location'] );
		}
		
		return apply_filters( 'kalium_nav_menu', wp_nav_menu( $args ), $args );
	}
}
