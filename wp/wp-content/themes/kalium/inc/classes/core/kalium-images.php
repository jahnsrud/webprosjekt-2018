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

class Kalium_Images {
	
	/**
	 * Content before image
	 */
	public $before_image = '';
	
	/**
	 * Content after image
	 */
	public $after_image = '';
	
	/**
	 * Lazy loading
	 */
	public $lazy_load = true;
	
	/**
	 * Placeholder background color
	 */
	public $placeholder_background = '';
	
	/**
	 * Placeholder gradient
	 */
	public $placeholder_gradient = null;
	
	/**
	 * Placeholder dominant color
	 */
	public $placeholder_dominant_color = false;
	
	/**
	 * Constructor
	 */
	public function __construct() {
	}
	
	/**
	 * Get image
	 */
	public function getImage( $attachment_id, $size = 'thumbnail', $atts = null, $placeholder_atts = array() ) {
		
		// Image
		if ( is_numeric( $attachment_id ) ) {
			$image = wp_get_attachment_image( $attachment_id, $size, false, $atts );

			if ( ! $image ) {
				return '';
			}
		} else {
			$local_image = $this->getLocalImage( $attachment_id );
			
			// Local image found
			if ( $local_image ) {
				$image = $local_image['image'];
			} else {
				return '';
			}
		}
		
		$image_atts = kalium()->helpers->parseAttributes( $image );
		$image_atts = $image_atts['attributes'];
		
		// If height is missing...
		if ( empty( $image_atts['height'] ) && ! empty( $image_atts['src'] ) ) {
			$local_image = $this->getLocalImage( $image_atts['src'] );
			
			if ( ! empty( $local_image['height'] ) ) {
				$image_atts['height'] = $local_image['height'];
			}
		}
		
		// Image options
		$image_opts = array(
			'class' => 'img-' . $attachment_id,
			'width' => $image_atts['width'],
			'height' => $image_atts['height'],
		);
		
		// Lazy load
		if ( $this->lazy_load ) {
					
			// Image element
			foreach ( array( 'src', 'srcset', 'sizes' ) as $attr ) {
				if ( ! empty( $image_atts[ $attr ] ) ) {
					$image_atts[ 'data-' . $attr ] = $image_atts[ $attr ];
					unset( $image_atts[ $attr ] );
				}
			}
			
			// Lazyload class
			if ( empty( $image_atts['class'] ) ) {
				$image_atts['class'] = '';
			}
			
			$image_atts['class'] .= ' lazyload';
		}
		
		// Dominant image background color
		if ( $this->placeholder_dominant_color ) {
			if ( is_numeric( $attachment_id ) ) {
				$dominant_color = $this->getDominantColor( $attachment_id );
			} else if ( ! empty( $local_image ) ) {
				$dominant_color = $this->getDominantColorFromFile( $local_image );
			}
			
			if ( $dominant_color ) {
				$image_opts['background'] = $dominant_color;
			}
		}
		
		// Image
		$image = $this->buildImageElement( $image_atts );
		
		return $this->aspectRatioWrap( $image, $image_opts, $placeholder_atts );
	}
	
	/**
	 * Get image by URL
	 */
	public function getLocalImage( $image_url ) {
		$uploads_dir = wp_upload_dir();
		$site_url = site_url();
		
		$image = array();
		$baseurl = $uploads_dir['baseurl'];
		
		// Extract from tag
		if ( preg_match( '#<img[^>]+>#', $image_url, $matches ) ) {
			$element = kalium()->helpers->parseAttributes( $matches[0] );
			$element_attributes = $element['attributes'];
			
			if ( ! empty( $element_attributes['src'] ) ) {
				$image_url = $element_attributes['src'];
				
				if ( ! empty( $element_attributes['alt'] ) ) {
					$alt = $element_attributes['alt'];
				}
			}
		}
		
		// Check for local image
		if ( false !== strpos( $image_url, $site_url ) ) {
			$image_relative_path = ltrim( str_replace( $site_url, '', $image_url ), '\/' );
			$image_full_path = ABSPATH . $image_relative_path;
			
			if ( file_exists( $image_full_path ) ) {
				$image_size = @getimagesize( $image_full_path );
				
				if ( is_array( $image_size ) && isset( $image_size[0] ) && isset( $image_size[1] ) ) {
					$image_element = sprintf( '<img src="%s" width="%d" height="%d" alt="%s" />', $image_url, $image_size[0], $image_size[1], esc_attr( isset( $alt ) ? $alt : basename( $image_url ) ) );
					
					$image = array(
						'src' => $image_url,
						'path' => $image_full_path,
						'width' => $image_size[0],
						'height' => $image_size[1],
						'image' => $image_element
					);
				}
			}
		} else {
			// Remote image
			$image_hash = 'img_' . md5( $image_url );
			$image_sizes = get_option( 'kalium_remote_images_sizes', array() );
			
			if ( empty( $image_sizes[ $image_hash ] ) ) {
				$image_size = @getimagesize( $image_url );
				
				if ( is_array( $image_size ) ) {
					$image_size['time'] = time();
					$image_sizes[ $image_hash ] = $image_size;
					
					update_option( 'kalium_remote_images_sizes', $image_sizes );
				}
			} else {
				$image_size = $image_sizes[ $image_hash ];
			}
			
			if ( is_array( $image_size ) && isset( $image_size[0] ) && isset( $image_size[1] ) ) {
				$image_element = sprintf( '<img src="%s" width="%d" height="%d" alt="%s" />', $image_url, $image_size[0], $image_size[1], esc_attr( basename( $image_url ) ) );
					
				$image = array(
					'src' => $image_url,
					'path' => '',
					'width' => $image_size[0],
					'height' => $image_size[1],
					'image' => $image_element
				);
			}
		}

		
		return $image;
	}
	
	/**
	 * Build image style
	 */
	private function buildImageStyle( $style ) {
		$style_arr = array();
		
		foreach ( $style as $prop => $value ) {
			$style_arr[] = sprintf( '%s:%s', $prop, $value );
		}
		
		return implode( ';', $style_arr );
	}
	
	/**
	 * Build image element
	 */
	private function buildImageElement( $atts ) {
		// Build atts
		$atts_build = array();
		
		foreach ( $atts as $attr_id => $attr_value ) {
			$atts_build[] = sprintf( '%s="%s"', $attr_id, $attr_value );
		}
		
		return sprintf( '<img %s />', implode( ' ', $atts_build ) );
	}
	
	/**
	 * Set lazy loading state
	 */
	public function setLazyLoading( $on = true ) {
		$this->lazy_load = $on;
	}
	
	/**
	 * Set placeholder color
	 */
	public function setPlaceholderColor( $color ) {
		$this->placeholder_background = $color;
	}
	
	/**
	 * Set gradients
	 */
	public function setPlaceholderGradient( $start_color = '', $end_color = '', $type = 'linear' ) {
		$this->placeholder_gradient = array(
			'start' => $start_color,
			'end' => $end_color,
			'type' => $type
		);
	}
	
	/**
	 * Use dominant color
	 */
	public function useDominantColor( $use = true ) {
		$this->placeholder_dominant_color = $use;
	}
	
	/**
	 * Set loading spinner
	 */
	public function setLoadingSpinner( $spinner_id, $args = array() ) {
		// Spinner
		$spinner = new Kalium_Image_Loading_Spinner( $spinner_id, $args );
		
		if ( $spinner->getSpinner() ) {
			$this->before_image = $spinner;
		}
	}
	
	/**
	 * Set custom preloader
	 */
	public function setCustomPreloader( $attachment_id, $args ) {
		// Custom preloader
		$preloader = new Kalium_Image_Custom_Preloader( $attachment_id, $args );
		
		if ( $preloader->getPreloaderImage() ) {
			$this->before_image = $preloader;
		}
	}
	
	/**
	 * Calculate aspect ratio
	 */
	public function calculateAspectRatio( $width, $height ) {
		return number_format( $height / $width * 100, 8 );
	}
	
	/**
	 * Image placeholder - wspect ratio wrapper
	 */
	public function aspectRatioWrap( $element, $opts = array(), $atts = array() ) {
		
		// Options
		$opts = array_merge( array(
			'width' => 1,
			'height' => 1
		), $opts );
		
		// Placeholder classes
		$placeholder_classes = array( 'image-placeholder' );
		
		// Style
		$image_style = array();
		
		// Define proportional image height
		$image_style['padding-bottom'] = $this->calculateAspectRatio( $opts['width'], $opts['height'] ) . '%';
		
		// Background color
		if ( $this->placeholder_background ) {
			$image_style['background-color'] = $this->placeholder_background;
		}
		
		// Custom background image
		if ( ! empty( $opts['background'] ) ) {
			$image_style['background-color'] = $opts['background'];
		}
		
		// Gradient color
		if ( $this->placeholder_gradient ) {
			$start = $this->placeholder_gradient['start'];
			$end = $this->placeholder_gradient['end'];
			$type = $this->placeholder_gradient['type'];
			
			$gradient_color = $this->getGradientBackground( $start, $end, $type );
			
			if ( $gradient_color ) {
				$image_style['background-image'] = $gradient_color;
			}
		}
		
		// Placeholder attributes
		$placeholder_atts = array(
			'class' => '',
			'style' => $this->buildImageStyle( $image_style )
		);
		
		// Extend attributes
		if ( is_array( $atts ) && ! empty( $atts ) ) {
			foreach ( $atts as $attr_id => $attr_value ) {
				if ( 'class' == $attr_id ) {
					$new_classes = is_array( $attr_value ) ? $attr_value : explode( ' ', $attr_value );
					$placeholder_classes = array_unique( array_merge( $placeholder_classes, $new_classes ) );
				} else if ( 'style' !== strtolower( $attr_id ) ) {
					$placeholder_atts[ $attr_id ] = $attr_value;
				}
			}
		}
		
		// Placeholder classes
		$placeholder_atts['class'] = kalium()->helpers->showClasses( $placeholder_classes );
		
		// Create placeholder attributes array
		$placeholder_atts_str = array();
		
		foreach ( $placeholder_atts as $attr_id => $attr_value ) {
			$placeholder_atts_str[] = sprintf( '%s="%s"', esc_attr( $attr_id ), esc_attr( $attr_value ) );
		}
		
		// Video wrapper start
		$placeholder_wrapper = sprintf( '<span %s>', implode( ' ', $placeholder_atts_str ) );
		
			// Before
			$placeholder_wrapper .= apply_filters( 'kalium_images_before_image', $this->before_image );
			
			// Content element
			$placeholder_wrapper .= $element;
			
			// After
			$placeholder_wrapper .= apply_filters( 'kalium_images_after_image', $this->after_image );
			
		// Video wrapper end	
		$placeholder_wrapper .= '</span>';
		
		return $placeholder_wrapper;
	}
	
	/**
	 * Get Dominant Image Color
	 */
	private function getDominantColor( $attachment_id ) {
		$dominant_color = '';		
		$metadata = wp_get_attachment_metadata( $attachment_id );
		
		// Retrieve dominant color
		if ( empty( $metadata['image_meta']['kalium_dominant_color'] ) ) {
			
			require_once kalium()->locateFile( 'inc/lib/class-dominantcolors.php' );
			$dominant_colors = kalium_get_dominant_colors( $attachment_id, array( 'colorsNum' => 1 ) );
			
			if ( ! empty( $dominant_colors['foundColors'] ) ) {
				$dominant_color = $dominant_colors['foundColors'][0];
				$metadata['image_meta']['kalium_dominant_color'] = $dominant_color;
				wp_update_attachment_metadata( $attachment_id, $metadata );
			}
		}
		
		// Assign Dominant color
		if ( ! empty( $metadata['image_meta']['kalium_dominant_color'] ) ) {
			$dominant_color = $metadata['image_meta']['kalium_dominant_color'];
		}
		
		return $dominant_color;
	}
	
	/**
	 * Get Dominant omage color from direct file
	 */
	private function getDominantColorFromFile( $image ) {
		
		if ( empty( $image['path'] ) ) {
			return null;
		}
		
		$image_hash = 'img_' . md5( $image['path'] );
		$dominant_colors = get_option( 'kalium_images_dominant_colors', array() );
		
		if ( isset( $dominant_colors[ $image_hash ] ) ) {
			return $dominant_colors[ $image_hash ];
		} else {
			require_once kalium()->locateFile( 'inc/lib/class-dominantcolors.php' );
			
			$colors = new DominantColors( $image['path'], array( 'colorsNum' => 1 ) );
			$colors_most_dominant = $colors->getDominantColors();
			
			if ( ! empty( $colors_most_dominant['foundColors'] ) ) {
				$dominant_color = $colors_most_dominant['foundColors'][0];
			}
			
			// Set dominant color
			$dominant_colors[ $image_hash ] = isset( $dominant_color ) ? $dominant_color : '';
			
			update_option( 'kalium_images_dominant_colors', $dominant_colors );
			return $dominant_color;
		}
	}
	
	/**
	 * Get gradient colors
	 */
	private function getGradientBackground( $start, $end, $type = 'linear' ) {
		$gradient_color = '';
		
		if ( $start && $end ) {
			if ( 'radial' == $type ) {
				$gradient_color = "radial-gradient(circle, {$start}, {$type} 60%)";
			} else {
				$gradient_color = "linear-gradient(to bottom, {$start}, {$end})";
			}
		}
		
		return $gradient_color;
	}
}


/**
 * Kalium Image Loading Spinner
 */
class Kalium_Image_Loading_Spinner {
	
	/**
	 * Spinner ID
	 */
	public $spinner_id = '';
	
	/**
	 * Stored args
	 */
	public $args = array();
	
	/**
	 * Constructor
	 */
	public function __construct( $spinner_id, $args = array() ) {
		// Spinner
		$this->spinner_id = $spinner_id;
		
		// Args
		$args = shortcode_atts( array(
			'holder' => 'span',
			'alignment' => 'center',
			'spacing' => '',
			'color' => '',
			'scale' => ''
		), $args );
		
		$this->args = $args;
		
		// Generate HTML
		$this->html = $this->getHTML();
		$this->css = $this->getCSS();
		$this->css_parsed = false;
	}
	
	/**
	 * Get Spinner
	 */
	public function getSpinner() {
		$spinners = self::getSpinners();
		return isset( $spinners[ $this->spinner_id ] ) ? $spinners[ $this->spinner_id ] : null;
	}
	
	/**
	 * Get CSS
	 */
	public function getCSS() {
		$css = array();
		$spinner_id = $this->spinner_id;
		$args = $this->args;
		
		// Spacing
		$spacing = $this->args['spacing'];
		
		if ( $spacing >= 0 ) {
			$css[] = sprintf( '.image-placeholder > .loader { %s }', "left:{$args['spacing']}px;right:{$args['spacing']}px;top:{$args['spacing']}px;bottom:{$args['spacing']}px;" );
		}
		
		// Scale
		$scale = $args['scale'];
		
		if ( $scale ) {
			$transform = "scale3d({$args['scale']},{$args['scale']},1)";
			$css[] = sprintf( '.image-placeholder > .loader .loader-row .loader-size { %s }', "transform:{$transform};-webkit-transform:{$transform};-moz-transform:{$transform};" );
		}
		
		// Color
		$color = $args['color'];
		
		if ( $color ) {

			$loaders_selectors = array(
				'background-color' => array(
					'.ball-scale > div',
					'.ball-scale-multiple > div',
					'.ball-scale-random > div',
					'.ball-clip-rotate-pulse > div:first-child',
					'.line-scale > div',
					'.line-scale-party > div',
					'.line-scale-pulse-out > div',
					'.line-scale-pulse-out-rapid > div',
					'.ball-pulse-sync > div',
					'.ball-pulse > div',
					'.ball-beat > div',
					'.ball-rotate > div',
					'.ball-rotate > div:before', 
					'.ball-rotate > div:after',
					'.ball-spin-fade-loader > div',
					'.line-spin-fade-loader > div',
					'.ball-grid-pulse > div',
					'.ball-grid-beat > div',
					'.pacman > div:nth-child(3)', 
					'.pacman > div:nth-child(4)', 
					'.pacman > div:nth-child(5)', 
					'.pacman > div:nth-child(6)',
					'.square-spin > div',
					'.ball-pulse-rise > div',
					'.cube-transition > div',
					'.ball-zig-zag > div',
					'.ball-zig-zag-deflect > div',
					'.square-spin > span'
				),
				'background-image' => array(
					'.semi-circle-spin > div' => 'linear-gradient(transparent 0%, transparent 70%, {color} 30%, {color} 100%)'	
				),
				'border-color' => array(
					'.ball-clip-rotate > div',
					'.ball-scale-ripple > div',
					'.ball-scale-ripple-multiple > div',
					'.ball-clip-rotate-multiple > div',
					'.ball-triangle-path > div',
					'.double-circle-rotate > span',
					'.circle-pulse > span',
				),
				'border-top-color' => array(
					'.ball-clip-rotate-pulse > div:last-child',
					'.ball-clip-rotate-multiple > div:last-child',
					'.pacman > div:first-of-type',
					'.pacman > div:nth-child(2)',
				),
				'border-bottom-color' => array(
					'.ball-clip-rotate-pulse > div:last-child',
					'.ball-clip-rotate-multiple > div:last-child',
					'.triangle-skew-spin > div',
					'.pacman > div:first-of-type',
					'.pacman > div:nth-child(2)',
					'.ball-clip-rotate > div' => 'transparent',
					'.double-circle-rotate > span' => 'transparent',
				),
				'border-left-color' => array(
					'.pacman > div:first-of-type',
					'.pacman > div:nth-child(2)'
				),
				'stroke' => array(
					'.modern-circular .circular .path'
				)
			);
			
			foreach ( $loaders_selectors as $css_property => $selectors ) {
				
				foreach ( $selectors as $key => $selector ) {
					if ( is_string( $key ) ) {
						$id = explode( ' ', $key );
						$id = str_replace( '.', '', $id[0] );
						
						if ( $id == $spinner_id ) {
							$props = $css_property . ':' . str_replace( '{color}', $args['color'], $selector );
							$css[] = sprintf( '%s { %s }', $key, $props );
						}
					} else {
						$id = explode( ' ', $selector );
						$id = str_replace( '.', '', $id[0] );
						
						if ( $id == $spinner_id ) {
							$css[] = sprintf( '%s { %s }', $selector, $css_property . ':' . $args['color'] );
						}
					}
				}
			}
		}
		
		return '<style>' . implode( PHP_EOL, $css ) . '</style>';
	}
	
	/**
	 * Get HTML spinner
	 */
	public function getHTML() {
		$spinner_id = $this->spinner_id;
		$spinner = $this->getSpinner();
		$args = $this->args;
		
		// Classes
		$spinner_classes = array( 'loader' );
		
		if ( $args['alignment'] ) {
			$spinner_classes[] = "align-{$args['alignment']}";
		}
		
		$spinner_html = '<' . $args['holder'] . ' class="' . implode( ' ', $spinner_classes ) . '" data-id="' . $spinner_id . '">';
			
			$spinner_html .= '<' . $args['holder'] . ' class="loader-row">';
			
			if ( $args['scale'] ) {
				$spinner_html .= '<' . $args['holder'] . ' class="loader-size">';
			}
						
			$spinner_html .= '<' . $args['holder'] . ' class="loader-inner ' . $spinner_id . '">';
				
				if ( isset( $spinner['markup'] ) ) {
					$spinner_html .= $spinner['markup'];
				} else {
					$spinner_html .= str_repeat( '<span></span>', $spinner['layers'] );
				}
				
			$spinner_html .= '</' . $args['holder'] . '>';
			

			if ( $args['scale'] ) {
				$spinner_html .= '</' . $args['holder'] . '>';
			}
			
			$spinner_html .= '</' . $args['holder'] . '>';
		
		$spinner_html .= '</' . $args['holder'] . '>';
		
		return $spinner_html;
	}
	
	/**
	 * Registered spinners
	 */
	public static function getSpinners() {
		$loading_spinners = array(
			'double-circle-rotate'		    => array( 'name' => 'Double Circle Rotate', 		'layers' => 2 ),
			'modern-circular'				=> array( 'name' => 'Modern Circular Loader', 		'markup' => '<svg class="circular" viewBox="25 25 50 50"> <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10"/> </svg>' ),
			'circle-pulse'					=> array( 'name' => 'Circle Pulse', 				'layers' => 1 ),
			
			'ball-clip-rotate'              => array( 'name' => 'Ball Clip Rotate', 			'layers' => 1 ),
			'ball-scale'                    => array( 'name' => 'Ball Scale', 					'layers' => 1 ),
			'ball-scale-multiple'           => array( 'name' => 'Ball Scale Multiple', 			'layers' => 3 ),
			'ball-scale-ripple'             => array( 'name' => 'Ball Scale Ripple', 			'layers' => 1 ),
			'ball-scale-ripple-multiple'    => array( 'name' => 'Ball Scale Ripple Multiple', 	'layers' => 3 ),
			'ball-scale-random'             => array( 'name' => 'Ball Scale Random', 			'layers' => 3 ),
			'ball-clip-rotate-pulse'        => array( 'name' => 'Ball Clip Rotate Pulse', 		'layers' => 2 ),
			'ball-clip-rotate-multiple'     => array( 'name' => 'Ball Clip Rotate Multiple', 	'layers' => 2 ),
			
			'line-scale'                    => array( 'name' => 'Line Scale', 					'layers' => 5 ),
			'line-scale-party'              => array( 'name' => 'Line Scale Party', 			'layers' => 4 ),
			'line-scale-pulse-out'          => array( 'name' => 'Line Scale Pulse Out', 		'layers' => 5 ),
			'line-scale-pulse-out-rapid'    => array( 'name' => 'Line Scale Pulse Out Rapid', 	'layers' => 5 ),
			
			'ball-pulse-sync'               => array( 'name' => 'Ball Pulse Sync', 				'layers' => 3 ),
			'ball-pulse'                    => array( 'name' => 'Ball Pulse', 					'layers' => 3 ),
			
			
			'ball-beat'                     => array( 'name' => 'Ball Beat', 					'layers' => 3 ),
			'ball-rotate'                   => array( 'name' => 'Ball Rotate', 					'layers' => 1 ),
			'ball-spin-fade-loader'         => array( 'name' => 'Ball Spin Fade Loader', 		'layers' => 8 ),
			'line-spin-fade-loader'         => array( 'name' => 'Line Spin Fade Loader', 		'layers' => 8 ),
			'ball-grid-pulse'               => array( 'name' => 'Ball Grid Pulse', 				'layers' => 9 ),
			'ball-grid-beat'                => array( 'name' => 'Ball Grid Beat', 				'layers' => 9 ),
			
			'triangle-skew-spin'            => array( 'name' => 'Triangle Skew Spin', 			'layers' => 1 ),
			'pacman'                        => array( 'name' => 'Pacman', 						'layers' => 5 ),
			'semi-circle-spin'              => array( 'name' => 'Semi Circle Spin', 			'layers' => 1 ),
			
			
			'square-spin'                   => array( 'name' => 'Square Spin', 					'layers' => 1 ),
			'ball-pulse-rise'               => array( 'name' => 'Ball Pulse Rise', 				'layers' => 5 ),
			'cube-transition'               => array( 'name' => 'Cube Transition', 				'layers' => 2 ),
			'ball-zig-zag'                  => array( 'name' => 'Ball Zig Zag', 				'layers' => 2 ),
			'ball-zig-zag-deflect'          => array( 'name' => 'Ball Zig Zag Deflect', 		'layers' => 2 ),
			'ball-triangle-path'            => array( 'name' => 'Ball Triangle Path', 			'layers' => 3 ),
		);
		
		return $loading_spinners;
	}
	
	/**
	 * Get spinner (static context)
	 */
	public static function getSpinnerById( $id, $args = array() ) {
		$spinner = new self( $id, $args );
		return $spinner;
	}
	
	/**
	 * Output in the screen
	 */
	public function __toString() {
		if ( ! $this->css_parsed ) {
			$this->css_parsed = true;
			return $this->css . $this->html;
		}
		
		return $this->html;
	}
}

/**
 * Kalium Image Custom Preloader
 */
class Kalium_Image_Custom_Preloader {
	
	/**
	 * Preloader ID
	 */
	public $attachment_id = '';
	
	/**
	 * Constructor
	 */
	public function __construct( $attachment_id, $args = array() ) {
		// Spinner
		$this->attachment_id = $attachment_id;
		
		// Args
		$args = shortcode_atts( array(
			'width' => '',
			'alignment' => 'center',
			'spacing' => ''
		), $args );
		
		$this->args = $args;
		
		// Generate HTML
		$this->html = $this->getHTML();
		$this->css = $this->getCSS();
		$this->css_parsed = false;
	}
	
	/**
	 * Get preloader image
	 */
	public function getPreloaderImage() {
		return wp_get_attachment_image( $this->attachment_id, 'full' );
	}
	
	/**
	 * Get CSS
	 */
	public function getCSS() {
		$css = array();
		$args = $this->args;
		
		// Spacing
		$spacing = $this->args['spacing'];
		
		if ( $spacing >= 0 ) {
			$css[] = sprintf( '.image-placeholder > .custom-preloader-image { %s }', "padding:{$args['spacing']}px;" );
		}
		
		// Width
		$width = $this->args['width'];
		
		if ( $width >= 0 ) {
			$css[] = sprintf( '.image-placeholder > .custom-preloader-image { %s }', "width:{$args['width']}px;" );
		}
		
		return '<style>' . implode( PHP_EOL, $css ) . '</style>';
	}
	
	/**
	 * Get HTML
	 */
	public function getHTML() {
		$args = $this->args;
		
		$classes = array( 'custom-preloader-image' );
		$classes[] = 'align-' . $args['alignment'];
		
		$image = $this->getPreloaderImage();
		
		return sprintf( '<span class="%s">%s</span>', implode( ' ', $classes ), $image );
	}
	
	/**
	 * Output in the screen
	 */
	public function __toString() {
		if ( ! $this->css_parsed ) {
			$this->css_parsed = true;
			return $this->css . $this->html;
		}
		
		return $this->html;
	}
}
