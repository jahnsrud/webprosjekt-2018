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

class Kalium_VideoJS {
	
	/**
	 * Constructor
	 */
	public function __construct() {
		
		// Init
		add_action( 'init', array( $this, 'init' ), 10 );

		// Video and audio processing library
		add_filter( 'wp_video_shortcode_library', array( $this, 'library' ), 10 );
		add_filter( 'wp_audio_shortcode_library', array( $this, 'library' ), 10 );
		
		// Video and audio element classes
		add_filter( 'wp_video_shortcode_class', array( $this, 'classes' ), 10 );
		add_filter( 'wp_audio_shortcode_class', array( $this, 'classes' ), 10 );
		
		// Embed defaults
		add_filter( 'embed_defaults', array( $this, 'embedDefaults' ), 10, 2 );
		
		// Default media parameters
		add_filter( 'kalium_embed_video_atts', array( $this, 'mediaParameters' ), 10, 2 );
		add_filter( 'kalium_embed_audio_atts', array( $this, 'mediaParameters' ), 10, 2 );
		
		// Youtube and Vimeo handlers
		if ( ! $this->isInEditMode() ) {
			add_filter( 'embed_oembed_html', array( $this, 'youtubeHandler' ), 10, 4 );
			add_filter( 'embed_oembed_html', array( $this, 'vimeoHandler' ), 10, 4 );
			
			// Video shortcode
			add_filter( 'wp_video_shortcode', array( $this, 'videoHandler' ), 10, 4 );
			
			// Audio shortcode
			add_filter( 'wp_audio_shortcode', array( $this, 'audioHandler' ), 10, 4 );
		}
	}
	
	/**
	 * Init
	 */
	public function init() {
		
		// Video JS Share option enabling from theme options
		if ( 'yes' == get_data( 'videojs_share' ) ) {
			add_filter( 'kalium_videojs_share', '__return_true' );
		}

		// Video JS
		wp_register_script( 'video-js', kalium()->assetsUrl( 'js/video-js/video.min.js' ), null, null, true );
		wp_register_style( 'video-js', kalium()->assetsUrl( 'js/video-js/video-js.min.css' ), null, null );
		
		wp_register_script( 'video-js-youtube', kalium()->assetsUrl( 'js/video-js-youtube.js' ), array( 'video-js' ), null, true );
		
		// Vimeo Player
		wp_register_script( 'vimeo-player', kalium()->assetsUrl( 'js/vimeo/player.min.js' ), null, null, true );
		
		// Video JS Share
		wp_register_script( 'video-js-share', kalium()->assetsUrl( 'js/video-js-share/videojs-share.min.js' ), array( 'video-js' ), null, true );
		wp_register_style( 'video-js-share', kalium()->assetsUrl( 'js/video-js-share/videojs-share.css' ), array( 'video-js' ), null, null );
		
		// Use native YouTube player
		if ( 'native' == get_data( 'youtube_player' ) ) {
			add_filter( 'kalium_videojs_native_youtube_player', '__return_true', 100 );
		}
	}
	
	/**
	 * Is in edit mode (backend)
	 */
	private function isInEditMode() {
		global $pagenow;
		return defined( 'DOING_AJAX' ) && in_array( kalium()->url->get( 'action' ), array( 'parse-embed', 'parse-media-shortcode' ) ) || ( is_admin() && in_array( $pagenow, array( 'post.php' ) ) );
	}
	
	/**
	 * Enqueue library
	 */
	public function enqueue() {		
		static $videojs_imported;
		
		if ( $videojs_imported ) {
			return;
		}
		
		$videojs_imported = true;
		
		// VideoJS
		wp_enqueue_script( 'video-js' );
		wp_enqueue_style( 'video-js' );
		
		// Youtube extension
		wp_enqueue_script( 'video-js-youtube' );
		
		// Vimeo
		wp_enqueue_script( 'vimeo-player' );
		
		// Share option
		if ( apply_filters( 'kalium_videojs_share', false ) ) {
			$share_options = apply_filters( 'kalium_videojs_share_options', array(
			  'socials' => array( 'fb', 'tw', 'reddit', 'gp', 'messenger', 'linkedin', 'telegram', 'whatsapp', 'viber', 'vk', 'ok', 'mail' ),
			  'fbAppId' => ''
			) );
			
			$videojs_share_options = sprintf( 'var videojs_share_options = %s;', json_encode( $share_options ) );
			
			// Share extension
			wp_enqueue_script( 'video-js-share' );
			wp_enqueue_style( 'video-js-share' );
			
			// Share extension options
			wp_add_inline_script( 'video-js-share', $videojs_share_options );
		}
	}
	
	/**
	 * Shortcode library
	 */
	public function library() {
		return 'video-js';
	}
	
	/**
	 * Embed defaults
	 */
	public function embedDefaults( $defaults, $url ) {
		
		if ( kalium()->media->isYouTube( $url ) || kalium()->media->isVimeo( $url ) ) {
			return array( 'width' => 560, 'height' => 315 );
		}
		
		return $defaults;
	}
	
	/**
	 * Media parameters
	 */
	public function mediaParameters( $atts, $media_element ) {
		$preload = get_data( 'videojs_player_preload' );
		$autoplay = get_data( 'videojs_player_autoplay' );
		$loop = 'yes' == get_data( 'videojs_player_loop' );
		
		// Preload
		$atts['preload'] = $preload;
		
		// Autoplay
		if ( empty( $atts['autoplay'] ) ) {
			if ( 'on-viewport' == $autoplay ) {
				$atts['data-autoplay'] = 'on-viewport';
			} else if ( 'yes' == $autoplay ) {
				$atts['autoplay'] = 'autoplay';
			}
		}
		
		// Loop
		if ( $loop && empty( $atts['loop'] ) ) {
			$atts['loop'] = 'loop';
		}
		
		return $atts;
	}
	
	/**
	 * Shortcode class
	 */
	public function classes( $_classes ) {
		$classes = array( 'video-js-el' );
		$classes[] = 'vjs-default-skin';
		
		if ( 'minimal' == get_data( 'videojs_player_skin' ) ) {
			$classes[] = 'vjs-minimal-skin';
		}
		
		return trim( $_classes . ' ' . implode( ' ', $classes ) );
	}
	
	/**
	 * YouTube video handler
	 */
	public function youtubeHandler( $cache, $url, $atts, $post_id ) {
		
		// Use native YouTube player
		if ( apply_filters( 'kalium_videojs_native_youtube_player', false ) ) {
			return kalium()->media->embedYouTube( $url, $atts );
		}

		if ( kalium()->media->isYouTube( $url ) ) {
			
			// Enqueue VideoJS library including YouTube Extension
			$this->enqueue();
			
			// YouTube video attributes
			$youtube_atts = array(
				'data-vsetup' => array(
					'techOrder' => array( 'youtube' ),
					'sources' => array(
						array(
							'type' => 'video/youtube',
							'src' => $url
						)
					),
					'youtube' => array(
						'iv_load_policy' => 1,
						'ytControls' => 3,
						
						'customVars' => array(
							'wmode' => 'transparent',
							'controls' => 0
						)
					)
				),
			);
			
			$youtube_video = new Kalium_VideoJS_Media_Element( 'video', array_merge( $atts, $youtube_atts ) );
			
			return $youtube_video->getElement();
		}
		
		return $cache;
	}
	
	/**
	 * Vimeo video handler
	 */
	public function vimeoHandler( $cache, $url, $atts, $post_id ) {

		if ( kalium()->media->isVimeo( $url ) ) {
			
			return kalium()->media->embedVimeo( $url, $atts );
		}
		
		return $cache;
	}
	
	/**
	 * Video shortcode
	 */
	public function videoHandler( $output, $atts, $video, $post_id ) {
		global $wp_embed;
		
		// Youtube or vimeo videos
		if ( ! empty( $atts['src'] ) && ( kalium()->media->isYouTube( $atts['src'] ) || kalium()->media->isVimeo( $atts['src'] ) ) ) {
			return $wp_embed->autoembed( $atts['src'] );
		}
		
		// Enqueue VideoJS library
		$this->enqueue();
		
		// Video attributes
		if ( preg_match( '/<video[^>]+>(.*?)<\/video>/', $output, $matches ) ) {
			
			// VSetup attribut
			$atts['data-vsetup'] = array();
			
			// Ommit empty attributes
			foreach ( array( 'poster', 'loop', 'autoplay', 'preload' ) as $attr_id ) {
				if ( empty( $atts[ $attr_id ] ) ) {
					unset( $atts[ $attr_id ] );
				}
			}
			
			// Remove unsupported attributes
			foreach ( array( 'm4v', 'webm', 'ogv', 'flv' ) as $attr_id ) {
				if ( isset( $atts[ $attr_id ] ) ) {
					unset( $atts[ $attr_id ] );
				}
			}
			
			$video_element = new Kalium_VideoJS_Media_Element( 'video', $atts, $matches[1] );
			
			return $video_element->getElement();
		}
		
		return $cache;
	}
	
	/**
	 * Audio shortcode
	 */
	public function audioHandler( $html, $atts, $audio, $post_id ) {
		
		// Enqueue VideoJS library
		$this->enqueue();
		
		// Audio attributes
		if ( preg_match( '/<audio[^>]+>(.*?)<\/audio>/', $html, $matches ) ) {
			
			// VSetup attribut
			$atts['data-vsetup'] = array();
			
			// Remove empty attributes
			foreach ( array( 'poster', 'loop', 'autoplay', 'preload' ) as $attr_id ) {
				if ( empty( $atts[ $attr_id ] ) ) {
					unset( $atts[ $attr_id ] );
				}
			}
			
			// Remove unsupported attributes
			foreach ( array( 'mp3', 'ogg', 'flac', 'm4a', 'wav' ) as $attr_id ) {
				if ( isset( $atts[ $attr_id ] ) ) {
					unset( $atts[ $attr_id ] );
				}
			}
			
			// Default aspect ratio
			$atts['width'] = 16;
			$atts['height'] = 3;
			
			$audio_element = new Kalium_VideoJS_Media_Element( 'audio', $atts, $matches[1] );
			
			return $audio_element->getElement();
		}
		
		return $html;
	}
	
	/**
	 * Parse video or audio shortcode
	 */
	public function parseMedia( $source, $atts = '' ) {
		$urls = wp_extract_urls( $source );

		// Match video shortcodes
		if ( preg_match( '#\[video[^\]]+\](\[\/video\])?#i', $source, $matches ) ) {
			$shortcode_video = do_shortcode( $matches[0] );
			
			if ( preg_match( '#(?<element><video[^>]+>)(?<content>.*?)(<\/video>)#i', $shortcode_video, $video_markup ) ) {
				$element = str_replace( ' src ', ' ', $video_markup['element'] );
				
				// Parse attributes
				$element_arr = kalium()->helpers->parseAttributes( $element );
				$atts = array_merge( $element_arr['attributes'], $atts );
				
				$video_element = new Kalium_VideoJS_Media_Element( 'video', $atts, $video_markup['content'] );
				
				return $video_element->getElement();
			}
			else if ( preg_match( '#<iframe.*?src#i', $shortcode_video ) ) {
				return $shortcode_video;
			}
			
		}
		
		// Match audio shortcodes
		else if ( preg_match( '#\[audio[^\]]+\](\[\/audio\])?#i', $source, $matches ) ) {
			
			if ( preg_match( '#(?<element><audio[^>]+>)(?<content>.*?)(<\/audio>)#i', do_shortcode( $matches[0] ), $audio_markup ) ) {
				$element = str_replace( ' src ', ' ', $audio_markup['element'] );
				
				// Parse attributes
				$element_arr = kalium()->helpers->parseAttributes( $element );
				$atts = array_merge( $element_arr['attributes'], $atts );
				
				$audio_element = new Kalium_VideoJS_Media_Element( 'audio', $atts, $audio_markup['content'] );
				
				return $audio_element->getElement();
			}
		}
		
		// Match Video or Audio URL
		else if ( ! empty( $urls[0] ) ) {
			// URL
			$url = $urls[0];
			
			// YouTube or Vimeo video
			if ( kalium()->media->isYouTube( $url ) || kalium()->media->isVimeo( $url ) ) {
				$video_shortcode = sprintf( '[video src="%s"][/video]', esc_attr( $url ) );
				return $this->parseMedia( $video_shortcode, $atts );				
			}
			// Check other video types
			else {
				$video_types = wp_get_video_extensions();
				$audio_types = wp_get_audio_extensions();
				$type = wp_check_filetype( $url, wp_get_mime_types() );
				
				// Video URL
				if ( in_array( $type['ext'], $video_types ) ) {
					$shortcode = sprintf( '[video %s="%s"][/video]', esc_attr( $type['ext'] ), esc_attr( $url ) );
					return $this->parseMedia( $shortcode, $atts );
				} 
				// Audio URL
				else if ( in_array( $type['ext'], $audio_types ) ) {
					$shortcode = sprintf( '[audio %s="%s"][/audio]', esc_attr( $type['ext'] ), esc_attr( $url ) );
					return $this->parseMedia( $shortcode, $atts );
				}
			}
		}
		
		return null;
	}
}

class Kalium_VideoJS_Media_Element {
	
	/**
	 * Element type
	 */
	public $type = 'video';
	
	/**
	 * Classes
	 */
	public $class_name = '';
	
	/**
	 * Element attributes
	 */
	public $atts = array();
	
	/**
	 * Element content
	 */
	public $content = '';
	
	/**
	 * Element width
	 */
	public $width = 1;
	
	/**
	 * Element height
	 */
	public $height = 1;
	
	/**
	 * Construct
	 */
	public function __construct( $type, $atts = array(), $content = '' ) {
		
		// Element type
		$this->type = 'audio' == $type ? 'audio' : 'video';
	
		// Element classes
		$default_classes = apply_filters( "wp_{$type}_shortcode_class", '' );
		
		// Filter attributes
		$this->atts = apply_filters( "kalium_embed_{$type}_atts", $atts, $this );

		// Filter content
		$this->content = apply_filters( "kalium_embed_{$type}_content", $content, $this );
		
		// Classes
		$this->class_name = $default_classes;
		
		if ( ! empty( $atts['class'] ) ) {
			$extra_classes = is_array( $atts['class'] ) ? $atts['class'] : explode( ' ', $atts['class'] );
			$this->class_name = kalium()->helpers->showClasses( array_unique( array_merge( explode( ' ', $this->class_name ), $extra_classes ) ) );
		}
		
		// Width and height (used to generate aspect ratio element)
		if ( ! empty( $atts['width'] ) ) {
			$this->width = $atts['width'];
		}
		
		if ( ! empty( $atts['height'] ) ) {
			$this->height = $atts['height'];
		}
	}
	
	/**
	 * Build embed element
	 */
	public function buildElement( $tag_name, $atts, $content = '' ) {
		
		$atts_str = array();
		
		foreach ( $atts as $attr_name => $attr_value ) {
			
			if ( is_array( $attr_value ) ) {
				$attr_value = json_encode( $attr_value );
			}
			
			if ( ! is_numeric( $attr_name ) ) {
				if ( $attr_value ) {
					$atts_str[] = sprintf( '%1$s="%2$s"', esc_attr( $attr_name ), esc_attr( $attr_value ) );
				} else {
					$atts_str[] = esc_attr(  $attr_name );
				}
			}
		}
		
		$element = sprintf( '<%1$s %2$s>%3$s</%1$s>', $tag_name, implode( ' ', $atts_str ), $content );
		
		return $element;
	}
	
	/**
	 * Get DOM Element
	 */
	public function getElement() {
		
		// Clean attributes
		foreach ( array( 'class' ) as $attr_id ) {
			if ( isset( $this->atts[ $attr_id ] ) ) {
				unset( $this->atts[ $attr_id ] );
			}
		}
		
		// Attributes
		$atts = array_merge( array(
			'controls' => '',
			'class' => $this->class_name,
		), $this->atts );
		
		// Width and height
		$width = $this->width;
		$height = $this->height;
		
		// Element
		$element = $this->buildElement( $this->type, $atts, $this->content );

		return kalium()->images->aspectRatioWrap( $element, array( 'width' => $width, 'height' => $height ), array( 'class' => 'video' ) );
	}
	
	/**
	 * To string
	 */
	public function __toString() {
		return $this->getElement();
	}

}
