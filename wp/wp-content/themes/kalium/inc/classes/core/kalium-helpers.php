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

class Kalium_Helpers {
	
	/**
	 *	Admin notices to show
	 */
	private static $admin_notices = array();
	
	
	public function __construct() {
		$this->admin_init_priority = 1000;
	}
	
	/**
	 *	Execute admin actions
	 *
	 *	@type action
	 */
	public function admin_init() {
		// Show defined admin notices
		if ( count( self::$admin_notices ) ) {
			add_action( 'admin_notices', array( & $this, 'showAdminNotices' ), 1000 );
		}
	}
	
	/**
	 *	Add admin notice
	 */
	public static function addAdminNotice( $message, $type = 'success', $dismissible = true ) {
		
		switch ( $type ) {
			case 'success':
			case 'error':
			case 'warning':
				break;
			
			default:
				$type = 'info';
		}
		
		self::$admin_notices[] = array( 
			'message'        => $message,
			'type'           => $type,
			'dismissible'    => $dismissible ? true : false
		);
	}
	
	/**
	 *	Let to Num
	 */
	public static function letToNum( $size ) {
		$l   = substr( $size, -1 );
		$ret = substr( $size, 0, -1 );
		switch ( strtoupper( $l ) ) {
			case 'P':
				$ret *= 1024;
			case 'T':
				$ret *= 1024;
			case 'G':
				$ret *= 1024;
			case 'M':
				$ret *= 1024;
			case 'K':
				$ret *= 1024;
		}
		return $ret;
	}
	
	/**
	 *	Show defined admin notices
	 */
	public function showAdminNotices() {
		foreach ( self::$admin_notices as $i => $notice ) {
			?>			
			<div class="laborator-notice notice notice-<?php echo $notice['type']; echo $notice['dismissible'] ? ' is-dismissible' : ''; ?>">
				<?php echo wpautop( $notice['message'] ); ?>
			</div>
			<?php
		}
		
	}
	
	/**
	 *	Get SVG dimensions from viewBox
	 */
	public function getSVGDimensions( $file ) {
		$width = $height = 1;
		
		// Get attached file
		if ( is_numeric( $file ) ) {
			$file = get_attached_file( $file );
		}
		
		if ( function_exists( 'simplexml_load_file' ) ) {
			$svg = simplexml_load_file( $file );
			
			if ( isset( $svg->attributes()->viewBox ) ) {
				$view_box = explode( ' ', (string) $svg->attributes()->viewBox );
				$view_box = array_values( array_filter( array_map( 'absint', $view_box ) ) );
				
				if ( count( $view_box ) > 1 ) {
					return array( $view_box[0], $view_box[1] );
				}
			}
		}
		
		return array( $width, $height );
	}
	
	/**
	 *	Safe JSON for numeric checks
	 */
	public function safeEncodeJSON( $arr ) {
		// Check for older version of php
		if ( function_exists( 'phpversion' ) && version_compare( phpversion(), '5.3.3', '<' ) ) {
			return json_encode( $arr );
		}
		
		return json_encode( $arr, JSON_NUMERIC_CHECK );
	}
	
	/**
	 *	Add Body Class
	 */
	public function addBodyClass( $classes = '' ) {
		if ( ! is_array( $classes ) ) {
			$classes = explode( ' ', $classes );
		}
		
		$classes = array_map( 'esc_attr', $classes );
		
		add_filter( 'body_class', kalium_hook_merge_array_value( implode( ' ', $classes ) ) );
	}
	
	/**
	 *	Show CSS classes attributes
	 */
	public function showClasses( $classes, $echo = false ) {
		if ( ! is_array( $classes ) ) {
			$classes = array( $classes );
		}
		
		$classes = implode( ' ', array_map( 'esc_attr', $classes ) );
		
		if ( $echo ) {
			echo $classes;
		}
		
		return $classes;
	}
	
	/**
	 *	Check if file is SVG extension
	 */
	public function isSVG( $file ) {
		$file_info = pathinfo( $file );
		return 'svg' == strtolower( get_array_key( $file_info, 'extension' ) );
	}
	
	/**
	 *	Active Plugins
	 */
	public function isPluginActive( $plugin ) {
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) );
		$active_sitewide_plugins = apply_filters( 'active_sitewide_plugins', get_site_option( 'active_sitewide_plugins', array() ) );
		$plugins = array_merge( $active_plugins, $active_sitewide_plugins );
		
		return in_array( $plugin, $plugins ) || isset( $plugins[ $plugin ] );
	}
	
	/**
	 * Build HTML element
	 */
	public function buildDOMElement( $tag_name, $attributes = array(), $content = '' ) {
		// Check for parsed element
		if ( is_array( $tag_name ) ) {
			
			if ( ! empty( $tag_name['element'] ) && isset( $tag_name['attributes'] ) && isset( $tag_name['content'] ) ) {
				$attributes = $tag_name['attributes'];
				$content = $tag_name['content'];
				$tag_name = $tag_name['element'];
			} else {
				$tag_name = $attributes = $content = '';
			}
		}
		
		// If no tag is present
		if ( empty( $tag_name ) ) {
			return '';
		}
		
		// Self closing tags
		$self_closing_tags = array( 'img', 'br' );
		
		// Attributes build
		$attributes_str = array();
		
		foreach ( $attributes as $attribute_name => $attribute_value ) {
			$attributes_str[] = sprintf( ' %s="%s"', esc_attr( $attribute_name ), esc_attr( is_string( $attribute_value ) ? $attribute_value : json_encode( $attribute_value ) ) );
		}
		
		// Self closing tag
		if ( in_array( strtolower( $tag_name ), $self_closing_tags ) ) {
			$element = sprintf( '<%s%s />', $tag_name, implode( ' ', $attributes_str ) );
		} else {
			$element = sprintf( '<%1$s%2$s>%3$s</%1$s>', $tag_name, implode( ' ', $attributes_str ), $content );
		}
		
		return $element;
	}
	
	/**
	 * Parse attributes from an HTML element
	 */
	public function parseAttributes( $input ) {
		$results = array(
			'element' => '',
			'attributes' => array(),
			'content' => '',
		);
		
		// Find nearest match
		if ( preg_match( '#^(<)(?<element>[a-z0-9\-._:]+)((\s)+(?<attributes>.*?))?((>)(?<content>[\s\S]*?)((<)\/\2(>))|(\s)*\/?(>))$#im', $input, $matches ) ) {
			// Tag name and content
			$results['element'] = $matches['element'];
			$results['content'] = $matches['content'];
			
			// Attributes
			if ( ! empty( $matches['attributes'] ) && preg_match_all( '#(?<attribute_name>[a-z0-9\-]+)=("|\')(?<attribute_value>[^"\']+)("|\')#im', $matches['attributes'], $matched_attributes ) ) {
				
				foreach ( $matched_attributes['attribute_name'] as $i => $attribute_name ) {
					$results['attributes'][ $attribute_name ] = $matched_attributes['attribute_value'][ $i ];
				}
			}
		}
		
		return $results;
	}
}
