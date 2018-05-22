<?php
/**
 *	TypoLab Fonts
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class TypoLab {
	
	/**
	 *	
	 */
	public static $settings_var = 'typolab_fonts';
	
	/**
	 *	TypoLab Path
	 */
	public static $typolab_path;
	
	/**
	 *	Fonts Path
	 */
	public static $fonts_path;
	
	/**
	 *	Fonts URL
	 */
	public static $fonts_url;
	
	/**
	 *	Assets URL to TypoLab
	 */
	public static $typolab_assets_url = '';
	
	/**
	 *	Registered/Supported Font Sources
	 */
	private static $font_sources = array(
		'google' => array(
			'name' => 'Google Fonts',
			'description' => "Google's free font directory is one of the most exciting developments in web typography in a very long time.\n\nGoogle Fonts catalog are published under licenses that allow you to use them on any website, whether it’s commercial or personal.\n\nChoose between <strong>800+</strong> available fonts to use with your site."
		),
		'font-squirrel' => array(
			'name' => 'Font Squirrel',
			'description' => "Font Squirrel is a collection of free fonts for commercial use.\n\nApart from Google fonts, Font Squirrel requires to download and install fonts in order to use them. Installation process is automatic, just hit the <strong>Download</strong> button.\n\nChoose between <strong>750+</strong> available fonts to use with your site."
		),
		'premium' => array(
			'name' => 'Premium Fonts',
			'description' => "Premium fonts worth of <strong>$149</strong> (per site) are available for Laborator customers only.\n\nIt has the same installation procedures as Font Squirrel, you need to download and install fonts that you want to use in this site.\n\nTheme activation is required in order to install fonts from this source."
		),
		'typekit' => array(
			'name' => 'TypeKit',
			'description' => "Typekit is a subscription service for fonts which you can use on a website.\n\nInstead of licensing individual fonts, you can sign up for the plan that best suits your needs and get a library of fonts from which to choose.\n\nTo import TypeKit fonts in your site, simply enter the <strong>Kit ID</strong> and you are all set."
		),
		'custom-font' => array(
			'name' => 'Custom Font',
			'description' => "If you can't find the right font from above sources then Custom Fonts got covered you.\n\nTo import a custom font, simply enter the stylesheet URL that includes @font-face's and specify font variant names."
		),
	);
	
	/**
	 *	Font Preview String
	 */
	public static $font_preview_str = 'Mist enveloped the ship three hours out from port.';
	
	/**
	 *	Font Preview Size
	 */
	public static $font_preview_size = 16;
	
	/**
	 *	TypoLab Execute on Frontend
	 */
	public static $typolab_enabled = true;
	
	/**
	 *	Default font import code placement
	 */
	public static $font_placement = 'head';
	
	/**
	 *	Font combining
	 */
	public static $font_combining = true;
	
	/**
	 *	Responsive Sizes
	 */
	public static $viewport_breakpoints = array(
		'general' => array( null, null ),
		'desktop' => array( 992, 1200 ),
		'tablet'  => array( 768, 992 ),
		'mobile'  => array( null, 768 ),
	);
	
	/**
	 *	List of Not Installed Fonts
	 */
	private static $missing_fonts = array();
	
	/**
	 *	Initialize TypoLab
	 */
	public function __construct() {
		// TypoLabPath
		self::$typolab_path = dirname( __FILE__ );
		
		// TypoLab Assets URL
		self::$typolab_assets_url = kalium()->locateFileUrl( 'inc/lib/laborator/typolab/assets' );
		
		// Fonts Path
		$uploads = wp_upload_dir();
		$fonts_path = $uploads['basedir'] . '/typolab-fonts/';
		$fonts_url = $uploads['baseurl'] . '/typolab-fonts/';
		
		self::$fonts_path = apply_filters( 'laborator_typolab_font_squirrel_fonts_dir', $fonts_path );
		self::$fonts_url = apply_filters( 'laborator_typolab_font_squirrel_fonts_url', $fonts_url );
		
		
		// TypoLab Font Providers
		$current_path = dirname( __FILE__ );
		
		include_once( $current_path . '/inc/classes/typolab-google-fonts.php' );
		include_once( $current_path . '/inc/classes/typolab-font-squirrel.php' );
		include_once( $current_path . '/inc/classes/typolab-premium-fonts.php' );
		include_once( $current_path . '/inc/classes/typolab-typekit-fonts.php' );
		include_once( $current_path . '/inc/classes/typolab-custom-font.php' );
		include_once( $current_path . '/inc/classes/typolab-font-sizes.php' );
		include_once( $current_path . '/inc/classes/typolab-font-loader.php' );
		
		// Start font loader
		$font_loader = new TypoLab_Font_Loader();
		
		// Other Actions
		add_action( 'admin_menu', array( $this, 'typographyMenuItem' ) );
		add_action( 'admin_init', array( $this, 'typoLabAdminInit' ) );
		
		// Export/Import Manager
		add_action( 'wp_ajax_typolab-export-import-manager', array( $this, 'fontExportImportManager' ) );
	}
	
	/**
	 *	Get Settings
	 */
	public static function getSetting( $var = null, $default = '' ) {
		$typolab_settings = get_option( self::$settings_var, array() );
		
		// Get All Vars
		if ( is_null( $var ) ) {
			return $typolab_settings;
		}
		
		// Get Single Var
		if ( isset( $typolab_settings[ $var ] ) ) {
			return $typolab_settings[ $var ];
		}
		
		return $default;
	}
	
	/**
	 *	Save Variable in Settings Array
	 */
	public static function setSetting( $var, $value = '' ) {
		$settings = self::getSetting();
		
		$settings[ $var ] = $value;
		
		update_option( self::$settings_var, $settings );
	}
	
	/**
	 *	Get Font Settings
	 */
	public static function getFontSettings() {
		return self::getSetting( 'font_settings', array() );
	}
	
	/**
	 *	Silence is golden file maker
	 */
	public static function silenceIsGolden( $directory ) {
		$silence = '<!-- Silence is golden. -->';
		
		if ( is_array( $directory ) ) {
			$res = array();
			
			foreach ( $directory as $dir ) {
				$res[] = self::silenceIsGolden( $dir );
			}
			
			return count( array_filter( $res ) ) == count( $res );
		}
		
		return @file_put_contents( rtrim( $directory ) . '/index.html', $silence );
	}
	
	/**
	 *	Download Font
	 */
	public static function downloadFont( $font_package_url, $provider = '' ) {
		// File.php is required
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		
		$fonts_path = $fonts_path_main = rtrim( self::$fonts_path, '/' );
		
		if ( $provider ) {
			$fonts_path .= "/" . rtrim( $provider, '/' );
		}
		
		$uploads = wp_upload_dir();
		$directory_no_writable_str = 'Directory: <em>' . $fonts_path . '</em>';
		
		// Create TypoLab Directory
		if ( wp_mkdir_p( $fonts_path ) ) {
			@set_time_limit(0);
			
			// Insert an index.html to disable indexing of typolab folder
			self::silenceIsGolden( array( $fonts_path_main, $fonts_path ) );
			
			$downloaded_font = download_url( $font_package_url );
			$font_package_file = $fonts_path . '/downloaded-font-' . time() . '.zip';
			
			// Stop on error...
			if ( is_wp_error( $downloaded_font ) ) {
				return $downloaded_font;
			}
			
			// Move downloaded files to the typolab path
			if ( @file_put_contents( $font_package_file, file_get_contents( $downloaded_font ) ) ) {			
				
				// Delete TMP File
				@unlink( $downloaded_font );
				
				return $font_package_file;
			} else {
				$error = new WP_Error();
				$error->add( 'TypoLab-DirectoryNotWritable', 'Font cannot be saved in your server, make sure directory is writable!' );
				$error->add( 'TypoLab-DirectoryNotWritable', $directory_no_writable_str );
				
				return $error;
			}
			
		} else {
			$error = new WP_Error();
			$error->add( 'TypoLab-DirectoryNotWritable', 'Fonts directory is not writable!' );
			$error->add( 'TypoLab-DirectoryNotWritable', $directory_no_writable_str );
			
			return $error;
		}
		
		return null;
	}
	
	/**
	 * Init WP_Filesystem
	 */
	public static function initFilesystem() {
		global $wp_filesystem;
		
		// Temporary fix for FS_METHOD ftpext
		if ( 'ftpext' == get_filesystem_method() ) {
			add_filter( 'filesystem_method', 'TypoLab::setDirectFSMethod', 100 );
		}
		
		// Start Unzipping the packge
		WP_Filesystem();
	}
	
	/**
	 * Set direct method for WP_Filesystem
	 */
	public static function setDirectFSMethod( $method ) {
		return 'direct';
	}
	
	/**
	 *	Unzip Font Package (ZIP)
	 */
	public static function unzipFont( $font_package_file, $destination, $delete_package_after_is_done = false ) {
		global $wp_filesystem;
		
		// File.php is required
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		
		$file_name = basename( $font_package_file );
		
		// Check if zip package exists
		if ( false === file_exists( $font_package_file ) ) {
			return new WP_Error( "TypoLab-FileNotExists", "File <em>{$file_name}</em> doesn't exists!" );
		}
		
		// Init WP Filesystem
		TypoLab::initFilesystem();
		
		$unzip = unzip_file( $font_package_file, $destination );
		
		// For some reasons extract was not successful
		if ( is_wp_error( $unzip ) ) {
			return $unzip;
		}
		
		// Delete ZIP package after extraction is complete
		if ( $delete_package_after_is_done ) {
			@unlink( $font_package_file );
		}
		
		return $destination;
	}
	
	/**
	 *	Delete Combined Files
	 */
	public static function deleteCombinedFiles() {
		$font_import_files = self::getSetting( 'combined_font_import_files', array() );
		
		foreach ( $font_import_files as $hosted_font ) {
			@unlink( $hosted_font['path'] );
		}
		
		self::setSetting( 'combined_font_import_files', array() );
	}
	
	/**
	 *	Generate Unique Font ID
	 */
	public static function newId() {
		$id = self::getSetting( 'id_iterator', 1 );
		self::setSetting( 'id_iterator', $id + 1 );
		return "font-{$id}";
	}
	
	/**
	 *	"Any" item object
	 */
	public function anyItem() {
		return array(
			'value' => '',
			'text' => ':: Any ::'
		);
	}
	
	/**
	 *	Register TypoLab Required Resources
	 */
	public function typoLabAdminInit() {
		// Register TypoLab Resources
		wp_register_style( 'typolab-main', self::$typolab_assets_url . '/css/typolab.min.css', null, '1.0', false );
		wp_register_script( 'typolab-main', self::$typolab_assets_url . '/js/typolab-main.min.js', null, '1.0', true );
		
		// Get Available Post Entries for Post Type
		add_action( 'wp_ajax_typolab_get_post_type_entries', array( & $this, 'ajaxGetPostsForPostType' ) );
		add_action( 'wp_ajax_typolab_get_taxonomy_entries', array( & $this, 'ajaxGetTaxonomies' ) );
		
		// Switch Pages and Process Actions
		if ( 'typolab' == kalium()->url->get( 'page' ) ) {
			wp_enqueue_style( 'typolab-main' );
			wp_enqueue_style( 'font-awesome' );
			wp_enqueue_script( 'typolab-main' );
			
			// Tooltips
			wp_enqueue_style( 'tooltipster-bundle', 'https://cdn.jsdelivr.net/jquery.tooltipster/4.1.4/css/tooltipster.bundle.min.css', null, '4.1.4' );
			wp_enqueue_script( 'tooltipster-bundle', 'https://cdn.jsdelivr.net/jquery.tooltipster/4.1.4/js/tooltipster.bundle.min.js', null, '4.1.4' );
			
			$action = kalium()->url->get( 'typolab-action' );
			$font_id = kalium()->url->get( 'font-id' );
			
			// Edit Font Page
			if ( 'edit-font' == $action ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
				
				// Save Font
				if ( kalium()->post( 'save_font_changes' ) && check_admin_referer( 'typolab-save-font-changes' ) ) {
					$this->saveFontChanges();
				}
			}
			// Delete Font from List
			else if ( 'delete-font' == $action ) {
				if ( $this->deleteFont( $font_id ) ) {
					Kalium_Helpers::addAdminNotice( 'Font has been deleted.', 'information' );
				}
			}
			// Delete Combined Files
			else if ( 'delete-combined-files' == $action ) {
				self::deleteCombinedFiles();
				Kalium_Helpers::addAdminNotice( 'Combined files are deleted successfully' );
			}
			// Delete Custom Font Sizes Group
			else if ( 'delete-size-group' == $action ) {
				$delete_group_id = kalium()->url->get( 'group-id' );

				if ( TypoLab_Font_Sizes::deleteCustomFontGroup( $delete_group_id ) ) {
					Kalium_Helpers::addAdminNotice( 'Custom font sizes group has been deleted.', 'info' );
				}
			}
			
			
			// Font Sizes Page
			if ( 'font-sizes' == kalium()->url->get( 'typolab-page' ) ) {
				// Save Font Sizes
				if ( kalium()->post( 'save_font_sizes' ) && check_admin_referer( 'typolab-save-font-sizes' ) ) {
					$this->saveFontSizeChanges();
				}
			}
			// Font Settings Page
			else if ( 'settings' == kalium()->url->get( 'typolab-page' ) ) {
				
				// Delete Font Files
				if ( $downloaded_font = kalium()->url->get( 'delete-font-files' ) ) {
					$delete_font = explode( ',', $downloaded_font );
					$delete_font_id = isset( $delete_font[1] ) ? $delete_font[1] : '';
					$font_to_delete = null;
					
					switch ( $delete_font[0] ) {
						// Delete "Premium Font" entry from database of downloaded fonts
						case 'premium-fonts':
							$premium_fonts_downloads = TypoLab_Premium_Fonts::getDownloadedFonts();
							
							if ( isset( $premium_fonts_downloads[ $delete_font_id ] ) ) {
								$font_to_delete = $premium_fonts_downloads[ $delete_font_id ];
								
								TypoLab_Premium_Fonts::removeDownloadedFont( $delete_font_id );
							}
							break;
							
						// Delete "Font Squirrel" entry from database of downloaded fonts
						case 'font-squirrel':
							$font_squirrel_downloads = TypoLab_Font_Squirrel::getDownloadedFonts();
							
							if ( isset( $font_squirrel_downloads[ $delete_font_id ] ) ) {
								$font_to_delete = $font_squirrel_downloads[ $delete_font_id ];
								
								TypoLab_Font_Squirrel::removeDownloadedFont( $delete_font_id );
							}
							break;
					}
					
					// Delete Font Files
					if ( $font_to_delete ) {
						global $wp_filesystem;
						
						// Init WP Filesystem
						TypoLab::initFilesystem();
						
						$uploads = wp_upload_dir();
						$deleted = $wp_filesystem->rmdir( "{$uploads['basedir']}/{$font_to_delete['path']}", true );
						
						if ( is_wp_error( $deleted ) ) {
							Kalium_Helpers::addAdminNotice( $deleted->get_error_messages(), 'error' );
						} else {
							Kalium_Helpers::addAdminNotice( 'Font has been deleted successfully.' );
						}
					}
				}
				
				// Save Font Settings
				if ( kalium()->post( 'save_font_settings' ) && check_admin_referer( 'typolab-save-font-settings' ) ) {
					$typolab_enabled   = kalium()->post( 'typolab_enabled' );
					
					$font_preview_text = kalium()->post( 'font_preview_text' );
					$font_preview_size = kalium()->post( 'font_preview_size' );
					$font_placement    = kalium()->post( 'font_placement' );
					$font_combining    = kalium()->post( 'font_combining' );
					
					$import_font_settings = kalium()->post( 'typolab_import_font_settings' );
					
					// Font Settings
					$font_settings = self::getFontSettings();
					
					// Font Preview Text
					$font_settings['typolab_enabled'] = 'yes' == $typolab_enabled;
					
					// Font Preview Text
					if ( $font_preview_text ) {
						$font_settings['font_preview_str'] = $font_preview_text;
					}
					
					// Font Preview Size
					if ( is_numeric( $font_preview_size ) && $font_preview_size > 0 ) {
						$font_settings['font_preview_size'] = $font_preview_size;
					}
					
					// Font Placement
					if ( ! empty( $font_placement ) ) {
						$font_settings['font_placement'] = $font_placement;
					}
					
					// Font Files Combining
					$font_settings['font_combining'] = $font_combining == 'yes';
					
					// Save font settings
					self::setSetting( 'font_settings', $font_settings );
					
					Kalium_Helpers::addAdminNotice( 'Font settings have been saved.' );
					
					// Import Font Settings
					if ( ! empty( $import_font_settings ) ) {
						$import_font_settings = maybe_unserialize( base64_decode( $import_font_settings ) );
						
						
						if ( is_array( $import_font_settings ) ) {
							include_once( dirname( __FILE__ ) . '/inc/classes/typolab-font-export-import.php' );
							
							$export_import_manager = new TypoLab_Font_Export_Import();
							
							// Import Settings
							if ( $export_import_manager->import( $import_font_settings ) ) {
								Kalium_Helpers::addAdminNotice( 'Font import was successful.' );
							}
						}
					}
					
				}
			}
		}
		
		// Font Settings
		$font_settings = self::getFontSettings();
		
		// TypoLab Plugin Status
		if ( isset( $font_settings['typolab_enabled'] ) ) {
			self::$typolab_enabled = $font_settings['typolab_enabled'];
		}
		
		// Font Preview Text
		if ( false == empty( $font_settings['font_preview_str'] ) ) {
			self::$font_preview_str = $font_settings['font_preview_str'];
		}
		
		// Font Preview Size
		if ( false == empty( $font_settings['font_preview_size'] ) ) {
			self::$font_preview_size = $font_settings['font_preview_size'];
		}
		
		// Font Placement
		if ( false == empty( $font_settings['font_placement'] ) ) {
			self::$font_placement = $font_settings['font_placement'];
		}
		
		// Font Files Combining
		if ( isset( $font_settings['font_combining'] ) ) {
			self::$font_combining = $font_settings['font_combining'];
		}
		
		// Missing Fonts
		$fonts = self::getFonts( true, true );
		
		$downloaded_fonts = array(
			'font-squirrel'  => TypoLab_Font_Squirrel::getDownloadedFonts(),
			'premium'        => TypoLab_Premium_Fonts::getDownloadedFonts()
		);
		
		foreach ( $fonts as $font ) {
			$font_id = $font['id'];
			
			// Check if font squirrel is installed
			if ( 'font-squirrel' == $font['source'] ) {
				$font_data = $font['options']['data'];
				
				if ( is_array( $font_data ) ) {
					$font_data = reset( $font_data );
				}
				
				$family_urlname = $font_data->family_urlname;
				
				if ( ! isset( $downloaded_fonts['font-squirrel'][ $family_urlname ] ) ) {
					self::$missing_fonts[ $font_id ] = $font;
				}
			}
			// Check if premium font is installed
			else if ( 'premium' == $font['source'] ) {
				$family_urlname = $font['options']['data']->family_urlname;
				
				if ( ! isset( $downloaded_fonts['premium'][ $family_urlname ] ) ) {
					self::$missing_fonts[ $font_id ] = $font;
				}
			}
		}
		
		// Add Sitewide Font Install Warning
		if ( self::$missing_fonts && 'typolab' !== kalium()->url->get( 'page' ) ) {
			$missing_fonts_count = count( self::$missing_fonts );
			$font_warning = sprintf( '<strong>TypoLab:</strong> %s to be installed. <a href="%s">Click here</a> to install them &raquo;', $missing_fonts_count > 1 ? "There are <strong>{$missing_fonts_count}</strong> fonts that need" : "There is a font that needs", admin_url( 'admin.php?page=typolab' )  );
			
			Kalium_Helpers::addAdminNotice( $font_warning, 'warning', false );
		}
	}
	
	/**
	 *	Save Font Changes from Form Method
	 */
	private function saveFontChanges() {
		$font_id = kalium()->url->get( 'font-id' );
		$font = self::getFont( $font_id );
		
		$font_family    = kalium()->post( 'font_family' );
		$font_variants	= kalium()->post( 'font_variants' );
		$font_subsets	= kalium()->post( 'font_subsets' );
		$font_data      = kalium()->post( 'font_data' );
		$font_selectors = kalium()->post( 'font_selectors' );
		
		// Set Font Family
		if ( ! empty( $font_family ) ) {
			$font['valid'] = true;
			$font['family'] = $font_family;
		}
		
		// Font Variants
		$font['variants'] = $font_variants;
		
		// Font Subsets
		if ( ! empty( $font_subsets ) ) {
			$font['subsets'] = $font_subsets;
		}
		
		// Font Data
		if ( ! empty( $font_data ) ) {
			$font['options']['data'] = @json_decode( wp_unslash( $font_data ) );
		}
		
		// Font Selectors
		$font['options']['selectors'] = array_map( 'stripslashes_deep', self::preserveSelectorsOrder( $font_selectors ) );
		
		// Creation Date
		if ( empty( $font['options']['created_time'] ) ) {
			$font['options']['created_time'] = time();
		}
		
		// Font Squirrel Generate Variants File
		if ( 'font-squirrel' == $font['source'] ) {
			$selected_variants = array();
			$downloaded_fonts = TypoLab_Font_Squirrel::getDownloadedFonts();
			
			if ( ! empty( $font['options']['data'] ) && ! empty( $font_variants ) ) {
				$font_squirrel_font_variant = $font['options']['data'];
				$family_urlname = $font_squirrel_font_variant[0]->family_urlname;
				
				foreach ( $font_variants as $variant ) {
					foreach ( $font_squirrel_font_variant as $font_squirrel_font ) {
						if ( $variant == $font_squirrel_font->fontface_name ) {
							$selected_variants[] = $font_squirrel_font;
						}
					}
				}
				
				// Generate Font Load File with Selected Variants
				if ( isset( $downloaded_fonts[ $family_urlname ] ) ) {
					$uploads = wp_upload_dir();
					$font_path = $uploads['basedir'] . "/{$downloaded_fonts[ $family_urlname ]['path']}";
					
					TypoLab_Font_Squirrel::createFontIncludeFile( $selected_variants, $font_path, 'load.css' );
				}
			}
		}
		
		// Premium Font Generate Variants & Subsets File
		else if ( 'premium' == $font['source'] ) {
			$downloaded_fonts = TypoLab_Premium_Fonts::getDownloadedFonts();
			
			if ( $font['options']['data'] ) {
				$premium_font_data = $font['options']['data'];
				
				if ( isset( $downloaded_fonts[ $premium_font_data->family_urlname ] ) ) {
					$uploads = wp_upload_dir();
					$font_dir = $uploads['basedir'] . '/' . $downloaded_fonts[ $premium_font_data->family_urlname ]['path'];
					
					TypoLab_Premium_Fonts::createFontIncludeFile( $premium_font_data, $font_subsets, $font_variants, $font_dir, 'load.css' );
				}
			}
		}
		
		// Custom Font Process Options
		else if ( 'custom-font' == $font['source'] ) {
			$font_url = wp_extract_urls( kalium()->post( 'font_url' ) );
			$font_variants = TypoLab_Custom_Font::wrapFontFamilyName( kalium()->post( 'font_variants' ) );
			
			// Get only the first url
			$font_url = $font_url ? rtrim( reset( $font_url ), '\\' ) : '';
			
			if ( is_array( $font_variants ) ) {
				$font_variants = array_map( 'wp_unslash', $font_variants );
			}
			
			$font['valid'] = true;
			$font['family'] = TypoLab_Custom_Font::clearFontFamilyName( $font_variants[0] );
			
			$font['options']['font_url'] = $font_url;
			$font['options']['font_variants'] = $font_variants;
		}
		
		// TypeKit Font Process
		else if ( 'typekit' == $font['source'] ) {
			$kit_id = kalium()->post( 'kit_id' );
			
			// Save Kit ID
			$font['kit_id'] = $kit_id;
			
			// Get Kit Options
			$typekit = new Typekit();
			$kit_info = $typekit->get( $kit_id );
			
			// Kit does exists
			if ( $kit_info !== null ) {
				$font_family = $kit_info['kit']['families'];
				
				if ( is_array( $font_family ) ) {
					$font_family = $kit_info['kit']['families'][0]['name'];
					
					$font['valid'] = true;
					$font['family'] = $font_family;
				}
				
				$font['options']['data'] = $kit_info;
			}
			// Kit does not exits
			else {
				Kalium_Helpers::addAdminNotice( 'Kit ID <strong>' . esc_html( $kit_id ) . '</strong> does not exists, font will not be loaded in frontend.', 'error' );
			}
		}
		
		
		// Conditional Font Loading
		$statements = kalium()->post( 'statements' );
		$operators  = kalium()->post( 'operators' );
		$criterions = kalium()->post( 'criterions' );
		
		$conditional_statements = array();
		
		if ( is_array( $statements ) && count( $statements ) ) {
			
			foreach ( $statements as $i => $statement ) {
				$operator = $operators[ $i ];
				$criteria = $criterions[ $i ];
				
				$conditional_statements[] = array(
					'statement' => $statement,
					'operator' => $operator,
					'criteria' => $criteria
				);
			}
			
		}
		
		$font['options']['conditional_loading'] = $conditional_statements;
		
		// Font Status
		$font_status = kalium()->post( 'font_status' );
		$font['font_status'] = $font_status;
		
		// Font Enqueue
		$font_placement = kalium()->post( 'font_placement' );
		$font['font_placement'] = $font_placement;
		
		// Delete Combined Files
		self::deleteCombinedFiles();
		
		// Delete Font Loading Cache
		TypoLab_Font_Loader::deleteFontSelectorsCache();
		
		// Save Font
		self::saveFont( $font_id, $font );
		
		// Show Font Updated Message
		Kalium_Helpers::addAdminNotice( 'Font changes have been saved.' );
	}
	
	/**
	 *	Save Font Size Changes	
	 */
	private function saveFontSizeChanges() {
		$new_font_sizes = kalium()->post( 'font_sizes' );
		
		// Get values from defined grup sizes
		$font_sizes = TypoLab_Font_Sizes::getFontSizes();
		
		foreach ( $font_sizes as $i => & $size_group ) {
			$size_group = array_merge( $size_group, $new_font_sizes[ $i ] );
		}
		
		// Save Settings
		self::setSetting( 'font_sizes', $font_sizes );
		Kalium_Helpers::addAdminNotice( 'Font sizes have been saved.', 'success' );
		
		// Create Group Info
		$new_group_title       = kalium()->post( 'new_group_title' );
		$new_group_description = kalium()->post( 'new_group_description' );
		$new_group_size_alias  = kalium()->post( 'new_group_size_alias' );
		$new_group_size_path   = kalium()->post( 'new_group_size_path' );
		
		// Delete Font Loading Cache
		TypoLab_Font_Loader::deleteFontSelectorsCache();
		
		// Add Custom Selectors Group
		if ( $new_group_title && is_array( $new_group_size_path ) && is_array( $new_group_size_path ) ) {
			$new_selectors = array();
			
			foreach ( $new_group_size_alias as $i => $selector_id ) {
				if ( ! empty( $selector_id ) && ! empty( $new_group_size_path[ $i ] ) ) {
					$new_selectors[ $selector_id ] = $new_group_size_path[ $i ];
				}
			}
			
			$custom_font_size_group = array(
				'title'       => $new_group_title,
				'description' => $new_group_description,
				'selectors'   => stripslashes_deep( $new_selectors ),
				'builtin'     => false,
				'sizes'       => array()
			);
			
			TypoLab_Font_Sizes::addCustomFontSizeGroup( $custom_font_size_group );
			
			Kalium_Helpers::addAdminNotice( 'Font size group has been created.', 'success' );
		}
	}
	
	/**
	 *	Export/Import Manager
	 */
	public function fontExportImportManager() {
		include_once( dirname( __FILE__ ) . '/inc/classes/typolab-font-export-import.php' );
		
		$resp = array();
		$export_import_manager = new TypoLab_Font_Export_Import();
		
		// Export Settings
		if ( kalium()->post( 'doExport' ) ) {
			$font_faces    = kalium()->post( 'fontFaces' );
			$font_sizes    = kalium()->post( 'fontSizes' );
			$font_settings = kalium()->post( 'fontSettings' );
			
			$resp['exported'] = base64_encode( maybe_serialize( $export_import_manager->export( $font_faces, $font_sizes, $font_settings ) ) );
		}
		
		echo json_encode( $resp );
		die();
	}
	
	/**
	 *	Get Post Entries for Post Type (Conditional Loading Module)
	 */
	public function ajaxGetPostsForPostType() {
		$post_type = kalium()->post( 'post_type' );
		
		$resp = array(
			'success' => false
		);
		
		if ( $post_type ) {
			$entries = new WP_Query( "post_type={$post_type}&posts_per_page=-1" );
			$entries_select = array();
			$entries_select[] = $this->anyItem();
			
			while ( $entries->have_posts() ) {
				$entries->the_post();
				
				$entries_select[] = array(
					'value' => get_the_ID(),
					'text' => get_the_title()
				);
			}
			
			wp_reset_postdata();
			
			$resp['entries'] = $entries_select;
			$resp['success'] = true;
		}
		
		echo json_encode( $resp );
		die();
	}
	
	/**
	 *	Get Taxonomy Entries (Conditional Loading Module)
	 */
	public function ajaxGetTaxonomies() {
		$taxonomy = kalium()->post( 'taxonomy' );
		
		$resp = array(
			'success' => false
		);
		
		if ( $taxonomy ) {
			$entries = get_terms( array(
				'taxonomy' => $taxonomy,
				'hide_empty' => false
			) );
			
			$entries_select = array();
			$entries_select[] = $this->anyItem();
			
			foreach ( $entries as $entry ) {
				
				$entries_select[] = array(
					'value' => $entry->term_id,
					'text' => $entry->name
				);
			}
			
			wp_reset_postdata();
			
			$resp['entries'] = $entries_select;
			$resp['success'] = true;
		}
		
		echo json_encode( $resp );
		die();
	}
	
	/**
	 *	Typography Menu Item
	 */
	public function typographyMenuItem() {
		// Add New Font
		if ( ! is_null( kalium()->post( 'typolab_add_font' ) ) && check_admin_referer( 'typolab-add-font' ) ) {
			$font_source = kalium()->post( 'font_source' );
			$font_sources_ids = array_keys( self::$font_sources );
			
			if ( in_array( $font_source, $font_sources_ids ) ) {
				$font_id = self::addFont( $font_source );
				$url = admin_url( 'admin.php?page=' . kalium()->url->get( 'page' ) . '&typolab-action=edit-font&font-id=' . $font_id );
				wp_redirect( $url );
				exit();
			}
		}
		
		add_submenu_page( 'laborator_options', 'TypoLab', 'Typography', 'edit_theme_options', 'typolab', array( & $this, 'typoLabPage' ) );
	}
	
	/**
	 *	Fonts List
	 */
	public function getFonts( $valid_fonts_only = false, $published_fonts_only = false ) {
		$fonts = self::getSetting( 'registered_fonts', array() );
		uasort( $fonts, array( 'self', 'sortFontsBySource' ) );
		
		if ( $valid_fonts_only || $published_fonts_only ) {
			foreach ( $fonts as $i => $font ) {
				// Valid Fonts Only
				if ( $valid_fonts_only && ! isset( $font['valid'] ) ) {
					unset( $fonts[ $i ] );
				} 
				// Published Fonts Only
				else if ( $published_fonts_only && ( ! isset( $font['font_status'] ) || 'published' != $font['font_status'] ) ) {
					unset( $fonts[ $i ] );
				}
			}
		}
		return apply_filters( 'typolab_get_fonts', $fonts, $valid_fonts_only, $published_fonts_only );
	}
	
	public static function sortFontsBySource( $a, $b ) {
		return strcmp( $a['source'], $b['source'] );
	}
	
	/**
	 *	Get single font
	 */
	public function getFont( $id ) {
		$fonts = $this->getFonts();
		
		foreach ( $fonts as $font ) {
			if ( isset( $font['id'] ) && $id == $font['id'] ) {
				return $font;
			}
		}
		
		return null;
	}
	
	/**
	 *	Delete a font entry
	 */
	public function deleteFont( $id ) {
		$fonts_list = $this->getFonts();
		$font_deleted = false;
		
		foreach ( $fonts_list as $i => $font ) {
			if ( $id == $font['id'] ) {
				unset( $fonts_list[ $i ] );
				$font_deleted = true;
			}
		}
		
		if ( $font_deleted ) {
			self::setSetting( 'registered_fonts', $fonts_list );
		}
		
		return $font_deleted;
	}
	
	/**
	 *	Update font data
	 */
	public function saveFont( $font_id, $font_updated ) {
		$fonts_list = $this->getFonts();
		
		foreach ( $fonts_list as & $font ) {
			if ( $font_updated['id'] == $font['id'] ) {
				$font = array_merge( $font, $font_updated );
			}
		}
		
		self::setSetting( 'registered_fonts', $fonts_list );
	}
	
	/**
	 *	Add New Font
	 */
	public function addFont( $font_source ) {
		$new_id = $this->newId();
		$fonts_list = $this->getFonts();
		
		$fonts_list[] = array(
			'id' => $new_id,
			'source' => $font_source,
			'options' => array()
		);
		
		self::setSetting( 'registered_fonts', $fonts_list );
		
		return $new_id;
	}
	
	/**
	 *	Preserve Selectors Order
	 */
	public function preserveSelectorsOrder( $selectors ) {
		$new_array = array();
		
		if ( ! is_array( $selectors ) ) {
			return $new_array;
		}
		
		foreach ( $selectors as $selector_row ) {
			$new_array[] = $selector_row;
		}
		
		return $new_array;
	}
	
	/**
	 *	TypoLab Admin Screen
	 */
	public function typoLabPage() {
		$admin_tpls   = dirname( __FILE__ ) . '/admin-tpls';
		$page         = kalium()->url->get( 'typolab-page' );
		$action       = kalium()->url->get( 'typolab-action' );
		
		switch ( $page ) {
			case 'settings':
				$title = 'Font Settings';
				require $admin_tpls . '/typolab-settings.php';
				break;
				
			case 'font-sizes':
				$title = 'Font Sizes';
				require $admin_tpls . '/typolab-font-sizes.php';
				break;
				
			default:
				// Default Title
				$title = 'Fonts {add-font-link}';
				
				// Add Font
				if ( 'add-font' == $action ) {
					$title = 'Add New Font';
					require $admin_tpls . '/typolab-add-font.php';
					return;
				}
				// Edit Font
				else if ( 'edit-font' == $action && ( $font_id = kalium()->url->get( 'font-id' ) ) ) {
					$font = self::getFont( $font_id );
					if ( $font ) {
						$title = 'Edit Font';
						
						if ( ! empty( $font['family'] ) ) {
							$title .= ': "' . $font['family'] . '"';
						} else {
							$title .= " (Select Font)";
						}
						
						// Font Source
						$sub_title = 'Source: ' . self::$font_sources[ $font['source'] ]['name'];
						
						require $admin_tpls . '/typolab-edit-font.php';
						return;
					}
				}
				
			require $admin_tpls . '/typolab-main.php';
		}
	}
}

$typolab = new TypoLab();