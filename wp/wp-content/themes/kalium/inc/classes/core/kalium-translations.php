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

class Kalium_Translations {
	
	/**
	 * Translations repot
	 */
	private $repository = 'https://api.github.com/repos/arl1nd/Kalium-Translations';
	
	/**
	 * If there is a Kalium translation being loaded
	 */
	private $has_translation = false;
	
	/**
	 * Translation update data
	 */
	private $translation_data;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		
		// Check if there are existing translations installed
		add_action( 'load_textdomain', array( $this, 'loadTextDomain' ), 100, 2 );
	}
	
	/**
	 * Admin init
	 *
	 * @type actiom
	 */
	public function after_setup_theme() {
		
		// Retrieve translation updates
		$this->retrieveTranslationUpdates();
			
		// Check for translation updates
		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'checkTranslationUpdates' ), 100 );
		add_filter( 'pre_set_transient_update_themes', array( $this, 'checkTranslationUpdates' ), 100 );
		
		// Delete translations cache after update
		add_action( 'upgrader_process_complete', array( $this, 'cleanTranslationsUpdates' ), 10, 2 );
	}
	
	/**
	 * Retrieve translation updates and cache them for a while
	 */
	private function retrieveTranslationUpdates() {
		global $pagenow;
		
		if ( ! is_admin() ) {
			return;
		}
		
		$translation_data = get_option( 'kalium_upgrader_translations', array(
			'last_check' => 0,
			'available_translations' => array(),
			'translation_updates' => array(),
		) );
		
		$check_interval = 172800; // 2 days
		
		// Force check updates (lower interval)
		if ( 'update-core.php' == $pagenow && kalium()->get( 'force-check', true ) ) {
			$check_interval = 300; // 5 minutes
		}
		
		// Check for updates
		if ( $translation_data['last_check'] < ( time() - $check_interval ) ) {
		
			$translation_data['available_translations'] = $translation_data['translation_updates'] = array();
			
			// Current locale
			$locale = get_locale();
			
			// If translation doesn't exists
			if ( false === $this->has_translation ) {
				$translation = $this->getRemoteTranslation( $locale );
				
				if ( $translation ) {
					$translation_data['available_translations'][ $locale ] = array(
						'type'       => 'theme',
						'slug'       => 'kalium',
						'language'   => $locale,
						'package'    => $translation->download_url,
						'version'	 => kalium()->getVersion(),
						'autoupdate' => 0
					);
				}
			}
			// Check for translation updates
			else {
				global $l10n;
		
				$kalium_l10n = get_array_key( $l10n, 'kalium' );
				
				if ( $kalium_l10n && ! empty( $kalium_l10n->headers['X-Translation-Version'] ) ) {
										
					$current_translation_version = $kalium_l10n->headers['X-Translation-Version'];
					$remote_translation_version = $this->getRemoteTranslationVersion( $locale );
					
					if ( $remote_translation_version && version_compare( $remote_translation_version, $current_translation_version, '>' ) ) {
						
						$translation_data['translation_updates'][ $locale ] = array(							
							'type'       => 'theme',
							'slug'       => 'kalium',
							'language'   => $locale,
							'package'    => sprintf( 'https://raw.githubusercontent.com/arl1nd/Kalium-Translations/master/%s.zip', $locale ),
							'version'	 => $remote_translation_version,
							'autoupdate' => 0
						);
					}
				}
			}
		
			// Set last checked
			$translation_data['last_check'] = time();
			
			// Update data
			update_option( 'kalium_upgrader_translations', $translation_data );
		}
		
		// Set translation update data
		$this->translation_data = $translation_data;
	}
	
	/**
	 * Clean translation updates
	 */
	public function cleanTranslationsUpdates( $upgrader, $data ) {
		
		if ( ! empty( $data['type'] ) && 'translation' == $data['type'] ) {
			
			$translation_data = get_option( 'kalium_upgrader_translations', array(
				'last_check' => 0,
				'available_translations' => array(),
				'translation_updates' => array(),
			) );
			
			$translation_data['available_translations'] = $translation_data['translation_updates'] = array();
			
			update_option( 'kalium_upgrader_translations', $translation_data );
		}
	}
	
	/**
	 * Load text domain
	 *
	 * @type action
	 */
	public function loadTextDomain( $domain, $mofile ) {
		
		if ( 'kalium' == $domain && file_exists( $mofile ) && false !== strpos( $mofile, 'wp-content/languages/themes') ) {
			$this->has_translation = true;
		}
	}
	
	/**
	 * Check for translation updates
	 */
	public function checkTranslationUpdates( $transient ) {
		
		// Translation updates
		$translation_data = $this->translation_data;
		$translation_updates = array();
		
		foreach ( array( 'available_translations', 'translation_updates' ) as $translation_type ) {
			if ( ! empty( $translation_data[ $translation_type ] ) ) {
				$translation_updates = array_merge( $translation_updates, $translation_data[ $translation_type ] );
			}
		}
		
		if ( ! empty( $translation_updates ) ) {
			
			foreach ( $translation_updates as $translation_update ) {
				if ( version_compare( $translation_update['version'], kalium()->getVersion(), '<=' ) ) {
					$transient->translations[] = $translation_update;
				}
			}
		}
		
		return $transient;
	}
	
	/**
	 * Get avaialble translation for current locale
	 *
	 * @return (object) $response (name, path, sha, size, url, html_url, git_url, download_url, type, content, encoding, _links)
	 */
	private function getRemoteTranslation( $locale ) {
		$contents_url = sprintf( '%s/contents/%s.zip', $this->repository, esc_attr( $locale ) );
		
		$request = wp_remote_get( $contents_url );
		$request_body = wp_remote_retrieve_body( $request );
		
		if ( ! is_wp_error( $request ) ) {
			$response = json_decode( $request_body );
			
			if ( ! empty( $response->download_url ) ) {
				return $response;
			}
		}
		
		return null;
	}
	
	/**
	 * Get remote translation locale version
	 *
	 * @return (string) $version
	 */
	private function getRemoteTranslationVersion( $locale ) {
		$contents_url = sprintf( '%1$s/contents/%2$s/kalium-%2$s.po', $this->repository, esc_attr( $locale ) );
		
		$request = wp_remote_get( $contents_url );
		$request_body = wp_remote_retrieve_body( $request );
		
		if ( ! is_wp_error( $request ) ) {
			$response = json_decode( $request_body );
			
			if ( ! empty( $response->content ) ) {
				$content = base64_decode( $response->content );
				
				// Get translation version
				if ( preg_match( '#X-Translation-Version:\s*([0-9\.]+)#', $content, $matches ) ) {
					return $matches[1];
				}
			}
		}
		
		return null;
	}
}