<?php
/**
 *	Kalium WordPress Theme
 *
 *	Core Theme Functions
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 *	Get template from Kalium theme
 */
function kalium_get_template( $file, $args = array() ) {

	// Templates prefix
	$file = sprintf( 'templates/%s', $file );
	
	// Locate template file
	$located = locate_template( $file, false );
	
	// Apply filters to current template file
	$template_file = apply_filters( 'kalium_get_template', $located, $file, $args );
	
	// File does not exists
	if ( ! file_exists( $template_file ) ) {
		kalium_doing_it_wrong( __FUNCTION__, sprintf( '%s does not exist.', '<code>' . $file . '</code>' ), '2.1' );
		return;
	}
	
	// Filter arguments by "kalium_get_template-filename.php"
	$args = apply_filters( "kalium_get_template-{$file}", $args );
	
	// Extract arguments (to use in template file)
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}
	
	// Actions before parsing template
	do_action( 'kalium_get_template_before', $located, $file, $args );
	
	include( $template_file );
	
	// Actions after parsing template
	do_action( 'kalium_get_template_after', $located, $file, $args );
}

/**
 *	Doing it wrong, the Kalium way
 */
function kalium_doing_it_wrong( $function, $message, $version ) {
	$message .= ' Backtrace: ' . wp_debug_backtrace_summary();

	if ( defined( 'DOING_AJAX' ) ) {
		do_action( 'doing_it_wrong_run', $function, $message, $version );
		error_log( "{$function} was called incorrectly. {$message}. This message was added in version {$version}." );
	} else {
		_doing_it_wrong( $function, $message, $version );
	}
}

/**
 * Get attachment image – the Kalium's way
 */
function kalium_get_attachment_image( $attachment_id, $size = 'thumbnail', $atts = null, $placeholder_atts = null ) {
	return kalium()->images->getImage( $attachment_id, $size, $atts, $placeholder_atts );
}

/**
 * Get ACF field value
 */
function kalium_get_field( $field_key, $post_id = false, $format_value = true ) {
	return kalium()->acf->get_field( $field_key, $post_id, $format_value );
}

/**
 * Generate infinite scroll pagination object for JavaScript
 */
function kalium_infinite_scroll_pagination_js_object( $id, $args = array() ) {
	
	if ( ! empty( $id ) ) {
		// Defaults
		extract( array_merge( array(
			
			// Total items
			'total_items' => 1,
			
			// Posts per page
			'posts_per_page' => 10,
			
			// Fetched ID's,
			'fetched_items' => array(),
			
			// Base query
			'base_query' => array(),
			
			// WP Ajax Action
			'action' => 'kalium_endless_pagination_get_paged_items',
			
			// Loop handler
			'loop_handler' => '',
			
			// Posts loop template function (PHP)
			'loop_template' => '',
			
			// JS Callback Function
			'callback' => '',
			
			// Selectors
			'trigger_element' => sprintf( '.pagination--infinite-scroll-show-more[data-endless-pagination-id="%s"]', esc_attr( $id ) ),
			
			// Container Element
			'container_element' => sprintf( '#%s', esc_attr( $id ) ),
			
			// Auto-reveal
			'auto_reveal' => false,
			
			// Extra arguments
			'args' => array(),
			
		), $args ) );
		
		// Remove unnecessary keys from query
		foreach ( array( 'pagename', 'page_id', 'name', 'portfolio', 'preview' ) as $query_arg ) {
			if ( isset( $base_query[ $query_arg ] ) ) {
				unset( $base_query[ $query_arg ] );
			}
		}
		
		// Instance object
		$infinite_scroll_obj_data = array(
			// Query to use
			'baseQuery' => $base_query,
			
			// Extra Query Filter Args
			'queryFilter' => null,
			
			// Pagination info
			'pagination' => array(
				'totalItems'    => $total_items,
				'perPage'       => $posts_per_page,
				'fetchedItems'  => $fetched_items,
			),
			
			// WP AJAX Action
			'action' => $action,
			
			// Loop handler
			'loopHandler' => $loop_handler,
			
			// Loop template
			'loopTemplate' => $loop_template,
			
			// JavaScript Callback
			'callback' => $callback,
			
			// Triggers
			'triggers' => array(
				
				// CSS Selector
				'selector' => $trigger_element,
				
				// Items container (where to append results)
				'container' => $container_element,
				
				// Auto Reveal
				'autoReveal' => $auto_reveal,
				
				// Classes added on events
				'classes' => array(
					
					// Ready
					'isReady' => 'pagination--infinite-scroll-has-items',
					
					// Loading
					'isLoading' => 'pagination--infinite-scroll-is-loading',
					
					// Pagination reached the end
					'allItemsShown' => 'pagination--infinite-scroll-all-items-shown'
				),
			),
			
			// Extra arguments
			'args' => $args
		);
		
	
		?>
		<script>
		var infiniteScrollPaginationInstances = infiniteScrollPaginationInstances || {};
		infiniteScrollPaginationInstances['<?php echo sanitize_title( $id ); ?>'] = <?php echo json_encode( apply_filters( 'kalium_infinite_scroll_object', $infinite_scroll_obj_data, $id ) ); ?>;
		</script>
		<?php
	}
}

/**
 * Get Post Ids from WP_Query
 */
function kalium_get_post_ids_from_query( $query ) {
	$ids = array();
	
	foreach ( $query->posts as $post ) {
		if ( is_object( $post ) ) {
			$ids[] = $post->ID;
		} else if ( is_numeric( $post ) ) {
			$ids[] = $post;
		}
	}
	
	return $ids;
}

/**
 * Get enabled options (SMOF Theme Options array)
 */
function kalium_get_enabled_options( $items ) {
	$enabled = array();
	
	if ( isset( $items['visible'] ) ) {
		foreach ( $items['visible'] as $item_id => $item ) {
			
			if ( $item_id == 'placebo' ) {
				continue;
			}
			
			$enabled[ $item_id ] = $item;
		}
	}
	
	return $enabled;
}

/**
 * Extract aspect ratio from string
 */
function kalium_extract_aspect_ratio( $str = '' ) {
	$ratio = array();
	
	if ( ! empty( $str ) && preg_match( '/^(?<w>[0-9]+)(:|x)(?<h>[0-9]+)$/', trim( $str ), $matches ) ) {		

		return array(
			'width' => $matches['w'],
			'height' => $matches['h']
		);
	}
	
	return array();
}

/**
 * Wrap image with image placeholder element
 */
function kalium_image_placeholder_wrap_element( $image ) {
	$ratio = '';
	
	// If its not an image, do not process
	if ( false === strpos( $image, '<img' ) ) {
		return $image;
	}
	
	// Generate aspect ratio
	if ( preg_match_all( '#(width|height)=(\'|")?(?<dimensions>[0-9]+)(\'|")?#i', $image, $image_dimensions ) && 2 == count( $image_dimensions['dimensions'] ) ) {
		$ratio = 'padding-bottom:' . kalium()->images->calculateAspectRatio( $image_dimensions['dimensions'][0], $image_dimensions['dimensions'][1] ) . '%';
	}
	
	// Lazy loading
	if ( preg_match( '(class=(\'|")[^"]+)', $image, $class_attr ) ) {
		$image = str_replace( $class_attr[0], $class_attr[0] . ' lazyload', $image );
	}
	
	return sprintf( '<span class="image-placeholder" style="%2$s">%1$s</span>', $image, $ratio );
}

/**
 * Kalium image placeholders style
 *
 * @type action
 */
function kalium_image_placeholder_set_style() {
	// Placeholder color
	$background_color = get_data( 'image_loading_placeholder_bg' );
	
	if ( ! empty( $background_color ) ) {
		kalium()->images->setPlaceholderColor( $background_color );
	}
	
	// Placeholder gradient color
	if ( get_data( 'image_loading_placeholder_use_gradient' ) ) {
		kalium()->images->setPlaceholderGradient( $background_color, get_data( 'image_loading_placeholder_gradient_bg' ), get_data( 'image_loading_placeholder_gradient_type' ) );
	}
	
	// Placeholder dominant color
	if ( get_data( 'image_loading_placeholder_dominant_color' ) ) {
		kalium()->images->useDominantColor();
	}
	
	// Set loader types
	switch ( get_data( 'image_loading_placeholder_type' ) ) {
		
		// Preselected
		case 'preselected':
			// Select spinner to use
			$spinner_id = get_data( 'image_loading_placeholder_preselected_loader' );
			
			kalium()->images->setLoadingSpinner( $spinner_id, array(
				'holder'    => 'span',
				'alignment' => get_data( 'image_loading_placeholder_preselected_loader_position' ),
				'spacing'   => get_data( 'image_loading_placeholder_preselected_spacing' ),
				'color'     => get_data( 'image_loading_placeholder_preselected_loader_color' ),
				'scale'     => intval( get_data( 'image_loading_placeholder_preselected_size' ) ) / 100,
			) );
			break;
			
		// Custom preloader
		case 'custom':
			$loader_image = get_data( 'image_loading_placeholder_custom_image' );
			
			if ( $loader_image ) {
				$loader_image_width = get_data( 'image_loading_placeholder_custom_image_width' );
				$loader_position    = get_data( 'image_loading_placeholder_custom_loader_position' );
				$loader_spacing     = get_data( 'image_loading_placeholder_custom_spacing' );
				
				kalium()->images->setCustomPreloader( $loader_image, array(
					'width' => $loader_image_width,
					'alignment' => $loader_position,
					'spacing' => $loader_spacing
				) );
			}
			break;
	}
	
}

/**
 * Return single value in WP Hook
 */
function kalium_hook_return_value( $value ) {
	$returnable = new Kalium_WP_Hook_Value( $value );
	return array( $returnable, 'returnValue' );
}

/**
 * Merge array value in WP Hook
 */
function kalium_hook_merge_array_value( $value, $key = '' ) {
	$returnable = new Kalium_WP_Hook_Value();
	$returnable->array_value = $value;
	$returnable->array_key = $key;
	
	return array( $returnable, 'mergeArrayValue' );
}

/**
 * Call user function in WP Hook
 */
function kalium_hook_call_user_function( $function_name ) {
	
	// Function arguments
	$function_args = func_get_args();
	
	// Remove the function name argument
	array_shift( $function_args );
	
	$returnable = new Kalium_WP_Hook_Value();
	$returnable->function_name = $function_name;
	$returnable->function_args = $function_args;
	
	return array( $returnable, 'callUserFunction' );
}

/**
 * Define debug mode in body class
 *
 * @type filter
 */
function kalium_check_debug_bode_body_class( $classes ) {
	if ( defined( 'KALIUM_DEBUG' ) ) {
		$classes[] = 'kalium-debug';
	}
	
	return $classes;
}

/**
 * Enqueue media library
 */
function kalium_enqueue_media_library() {
	kalium()->media->enqueueMediaLibrary();
}

/**
 * Clean excerpt
 */
function kalium_clean_excerpt( $content, $strip_tags = false ) {
	$content = strip_shortcodes( $content );
	$content = preg_replace( '#<style.*?>(.*?)</style>#i', '', $content );
	$content = preg_replace( '#<script.*?>(.*?)</script>#i', '', $content );
	
	return $strip_tags ? strip_tags( $content ) : $content;
}

/**
 * Convert an english word to number
 */
function kalium_get_number_from_word( $word ) {
	
	if ( is_numeric( $word ) ) {
		return $word;
	}
	
	switch ( $word ) {
		case 'ten' 	 : return 10; break;
		case 'nine'  : return 9; break;
		case 'eight' : return 8; break;
		case 'seven' : return 7; break;
		case 'six' 	 : return 6; break;
		case 'five'  : return 5; break;
		case 'four'	 : return 4; break;
		case 'three' : return 3; break;
		case 'two' 	 : return 2; break;
		case 'one'	 : return 1; break;
	}
	
	return 0;
}


/**
 * Format color value
 */
function kalium_format_color_value( $color ) {
	$color_formatted = '#';
	
	if ( preg_match( '#\#?([a-f0-9]+)#', $color, $matches ) ) {
		$color = strtolower( $matches[1] );
		$color_len = strlen( $color );
		
		if ( 3 == $color_len || 6 == $color_len ) {
			$color_formatted .= $color;
		} else if ( $color_len < 6 ) {
			$last = substr( $color, -1, 1 );
			$color_formatted .= $color . str_repeat( $last, 6 - $color_len );
		} else if ( $color_len > 6 ) {
			$color_formatted .= substr( $color, 0, 6 );
		}
	} else {
		$color_formatted .= 'ffffff';
	}
	
	return $color_formatted;
}

/**
 * Infinite scroll pagination – valid handler checker
 */
function kalium_infinite_scroll_valid_handler( $handler_fn ) {
	$valid_handlers = array(
		'kalium_blog_loop_post_template',
		'Kalium_WooCommerce::paginationHandler'
	);
	
	$valid = apply_filters( 'kalium_infinite_scroll_valid_handler', in_array( $handler_fn, $valid_handlers ), $handler_fn );
	
	return $valid;
}
