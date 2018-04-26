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

class Kalium_WPBakery {
	
	/**
	 * Required plugin/s for this class
	 */
	public static $plugins = array( 'js_composer/js_composer.php' );
	
	/**
	 * Class instructor, define necesarry actions
	 */
	public function __construct() {
		$this->template_redirect_priority = 100;
		
		// Deprecated row wrapper
		if ( apply_filters( 'kalium_wpbakery_use_deprecated_page_builder_row_wrapper', false ) ) {
			add_filter( 'vc_shortcode_output', '_deprecated_kalium_vc_row_parent_wrapper', 100, 3 );
			add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, '_deprecated_kalium_vc_row_remove_custom_css_class', 10, 3 );
		}
		// Row wrapper 
		else {
			add_filter( 'vc_shortcode_output', array( $this, 'vcRow' ), 100, 3 );
			
			// Inner row full-width support
			add_filter( 'vc_after_init', array( $this, 'vcInnerRowParams' ), 100 );
			add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, array( $this, 'vcInnerRowClass' ), 100,3  );
		}
	}
	
	/**
	 * Row wrapper
	 *
	 * @type filter
	 */
	public function vcRow( $output, $object, $atts ) {
		
		static $use_container;
		
		if ( ! isset( $use_container ) ) {
			$use_container = is_page() || ( is_singular( 'portfolio' ) && 'type-7' ==  kalium_get_field( 'item_type' ) );
		}
		
		// VC Section and Row
		if ( in_array( $object->settings( 'base' ), array( 'vc_section', 'vc_row' ) ) ) {
			$row_container_classes = array( 'vc-row-container' );
				
			// Row width
			if ( empty( $atts['full_width'] ) ) {
				
				// Applied to valid pages or post types only
				if ( $use_container ) {
					$row_container_classes[] = 'container';
				}
			}
			// Stretch row
			else if ( 'stretch_row' == $atts['full_width'] ) {
				
				// Applied to valid pages or post types only
				if ( $use_container ) {
					$row_container_classes[] = 'container';
				}
			}
			// Stretch row and content
			else if ( 'stretch_row_content' == $atts['full_width'] ) {
				$row_container_classes[] = 'vc-row-container--stretch-content';
			}
			// Stretch row and content (no spaces)
			else if ( 'stretch_row_content_no_spaces' == $atts['full_width'] ) {
				$row_container_classes[] = 'vc-row-container--stretch-content-no-spaces';
			}
			
			// Custom classes
			if ( ! empty( $atts['el_class'] ) ) {
				$classes = explode( ' ', $atts['el_class'] );
				
				foreach ( $classes as $class ) {
					$row_container_classes[] = "parent--{$class}";
				}
			}
			
			// Wrap the row
			$output = sprintf( '<div class="%2$s">%1$s</div>', $output, kalium()->helpers->showClasses( $row_container_classes ) );
		}
		
		return $output;
	}
	
	/**
	 * Inner row params
	 *
	 * @type action
	 */
	public function vcInnerRowParams() {
		$container_type = array(
			'type' => 'dropdown',
			'heading' => 'Container type',
			'param_name' => 'container_type',
			'std' => 'fixed',
			'value' => array(
				'Fluid container' => 'fluid',
				'Fixed container'  => 'fixed',
			),
			'description' => 'Fluid container will expand to 100% of column size, while fixed container will keep defined screen sizes and aligned on center.',
			'weight' => 1
		);
		
		vc_add_param( 'vc_row_inner', $container_type );
	}
	
	/**
	 * Inner row class
	 *
	 * @type filter
	 */
	public function vcInnerRowClass( $classes, $base, $atts ) {
		
		// Row stretch class
		if ( 'vc_row' == $base ) {
			
			// Stretched row
			if ( ! empty( $atts['full_width'] ) && 'stretch_row' == $atts['full_width'] ) {
				$classes .= ' row-stretch';
				
			}
		}
		// Inner row
		elseif ( 'vc_row_inner' == $base ) {
			
			// Fixed container
			if ( empty( $atts['container_type'] ) || 'fixed' == $atts['container_type'] ) {
				
				// Applied to pages only
				if ( is_page() ) {
					$classes .= ' container-fixed';
				}
			}
		}
		
		return $classes;
	}
	
	/**
	 * Deregister isotope
	 *
	 * @type action
	 */
	public function template_redirect() {
		wp_deregister_script( 'isotope' );
	}
}

/**
 * Kalium Visual Composer row wrapper
 *
 * (Deprecated)
 */
function _deprecated_kalium_vc_row_parent_wrapper( $output, $object, $atts ) {

	// Applied to rows only
	if ( in_array( $object->settings( 'base' ), array( 'vc_row', 'vc_row_inner' ) ) ) {
		$classes = array( 'vc-parent-row' );
		
		// Row width
		$full_width = get_array_key( $atts, 'full_width' );
		
		if ( $full_width ) {
			$classes[] = "row-{$full_width}";
		} else {
			$classes[] = "row-default";
		}
		
		// Custom CSS for row
		$css = get_array_key( $atts, 'css' );
		
		if ( $css ) {
			$classes[] = vc_shortcode_custom_css_class( $css );
		}
		
		// Columns gap
		$gap = get_array_key( $atts, 'gap' );
		
		if ( $gap ) {
			$classes[] = "columns-gap-{$gap}";
		}
	
		// Container wrap
		$output = sprintf( '<div class="%s">%s</div>', implode( ' ', $classes ), $output );
	}
	
	return $output;
}

/**
 * Since custom CSS class is applied to row parent, remove it from rows
 *
 * (Deprecated)
 */
function _deprecated_kalium_vc_row_remove_custom_css_class( $classes, $base, $atts ) {
	
	if ( 'vc_row' == $base ) {
		if ( strpos( $classes, 'vc_custom_' ) ) {
			$classes = preg_replace( '/\s+vc_custom_[0-9]+/', '', $classes );
		}
	}
	
	return $classes;
}
