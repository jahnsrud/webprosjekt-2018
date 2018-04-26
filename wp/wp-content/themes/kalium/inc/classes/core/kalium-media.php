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

// VideoJS media element
include_once kalium()->locateFile( 'inc/classes/core/media/kalium-videojs.php' );

class Kalium_Media {
	
	/**
	 * Media library in use
	 */
	public $media_library;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		
		switch ( apply_filters( 'kalium_media_library', 'videojs' ) ) {
			
			// VideoJS
			case 'videojs':
				$this->media_library = new Kalium_VideoJS();
				break;
		}
	}
	
	/**
	 * Enqueue media library
	 */
	public function enqueueMediaLibrary() {
		
		if ( $this->media_library ) {
			$this->media_library->enqueue();
		}
	}

	/**
	 * Check if the url is a youtube video
	 */
	public function isYouTube( $url ) {
		return preg_match( '#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#', $url );
	}
	
	/**
	 * Check if the url is a vimeo video
	 */
	public function isVimeo( $url ) {
		return preg_match( '#https?://(.+\.)?vimeo\.com/.*#i', $url );
	}
	
	/**
	 * Parse video or audio shortcode
	 */
	public function parseMedia( $source, $atts = array() ) {
		
		if ( $this->media_library ) {
			return $this->media_library->parseMedia( $source, $atts );
		}
		
		return $source;
	}
	
	/**
	 * Embed YouTube (default player)
	 */
	public function embedYouTube( $source, $atts = array() ) {
		
		if ( ! $this->isYouTube( $source ) ) {
			return $source;
		}
		
		// Defaults
		$atts = array_merge( wp_embed_defaults( $source ), $atts );
		
		// Autoplay
		$autoplay = get_data( 'videojs_player_autoplay' );
		
		if ( empty( $atts['autoplay'] ) ) {
			if ( 'on-viewport' == $autoplay ) {
				$autoplay_on_viewport = true;
			} else if ( 'yes' == $autoplay ) {
				$atts['autoplay'] = true;
			}
		}
		
		// Video URL
		$url = wp_parse_url( $source );
		$url_args = wp_parse_args( get_array_key( $url, 'query' ) );
		
		// YouTube video args
		$youtube_args = array_merge( array(
			'rel' => 0,
			'controls' => 1,
			'showinfo' => 0,
			'enablejsapi' => 1
		), $atts );
		
		// Playlist
		if ( isset( $url_args['list'] ) ) {
			$youtube_args['listType'] = 'playlist';
		}
		
		// Replace custom query arguments added in video URL
		if ( ! empty( $url_args ) ) {
			$youtube_args = array_merge( $youtube_args, $url_args );
		}
		
		// Video source
		$youtube_url = sprintf( 'https://www.youtube.com/embed/%s?%s', $url_args['v'], http_build_query( $youtube_args ) );
		
		// Embed
		$embed = sprintf( '<iframe src="%s" width="%d" height="%d" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>', esc_attr( $youtube_url ), $atts['width'], $atts['height'] );
		
		// Embed classes
		$embed_classes = array( 'video', 'video-youtube' );
		
		if ( isset( $autoplay_on_viewport ) ) {
			$embed_classes[] = 'autoplay-on-viewport';
		}
		
		return kalium()->images->aspectRatioWrap( $embed, array(
			'width' => $atts['width'],
			'height' => $atts['height'],
		), array( 'class' => $embed_classes ) );
	}
	
	/**
	 * Embed Vimeo (default player)
	 */
	public function embedVimeo( $source, $atts = array() ) {
		
		if ( ! $this->isVimeo( $source ) ) {
			return $source;
		}
			
		// Enqueue Vimeo handling library
		wp_enqueue_script( 'vimeo-player' );
		
		// Defaults
		$atts = array_merge( wp_embed_defaults( $source ), $atts );
		
		// Autoplay
		$autoplay = get_data( 'videojs_player_autoplay' );
		
		if ( empty( $atts['autoplay'] ) ) {
			if ( 'on-viewport' == $autoplay ) {
				$autoplay_on_viewport = true;
			} else if ( 'yes' == $autoplay ) {
				$atts['autoplay'] = true;
			}
		}
		
		// Video URL
		$url = wp_parse_url( $source );
		$url_args = wp_parse_args( get_array_key( $url, 'query' ) );
		
		$vimeo_id = '';
		
		if ( preg_match( '#^\/(?<vimeo_id>[^\/\?]+)#', $url['path'], $matches ) ) {
			$vimeo_id = $matches['vimeo_id'];
		}
		
		// Vimeo video args
		$vimeo_args = array_merge( array(
			'title' => 0,
			'byline' => 0,
			'portrait' => 0,
		), $atts );
		
		// Replace custom query arguments added in video URL
		if ( ! empty( $url_args ) ) {
			$vimeo_args = array_merge( $vimeo_args, $url_args );
		}
		
		// Video source
		$vimeo_url = sprintf( 'https://player.vimeo.com/video/%s?%s', $vimeo_id, http_build_query( $vimeo_args ) );
		
		// Emed
		$embed = sprintf( '<iframe src="%s" width="%d" height="%d" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>', $vimeo_url, $atts['width'], $atts['height'] );
		
		// Embed classes
		$embed_classes = array( 'video', 'video-vimeo' );
		
		if ( isset( $autoplay_on_viewport ) ) {
			$embed_classes[] = 'autoplay-on-viewport';
		}
		
		return kalium()->images->aspectRatioWrap( $embed, array(
			'width' => $atts['width'],
			'height' => $atts['height'],
		), array( 'class' => $embed_classes ) );
	}
}


