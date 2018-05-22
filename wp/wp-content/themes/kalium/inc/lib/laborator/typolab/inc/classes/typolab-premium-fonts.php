<?php
/**
 *	TypoLab Premium Fonts Class
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class TypoLab_Premium_Fonts {
	
	/**
	 *	Provider ID
	 */
	public static $providerID = 'premium-fonts';
	
	/**
	 *	Loaded Premium Fonts
	 */
	public static $fonts_list = array();
	
	/**
	 *	Unicode Ranges
	 */
	public static $unicodeRanges = array(
		'macroman'        => 'U+20-126, U+161-255, U+338-339, U+376, U+710, U+732, U+2019, U+201C-201E, U+8192-8202, U+8208-8212, U+8216-8218, U+8220-8222, U+8226, U+8230, U+8239, U+8249-8250, U+8287, U+8364, U+8482, U+9724, U+64257-64258, U+FFEB',
		'afrikaans'       => 'U+20-126, U+162-163, U+165, U+168-169, U+171, U+174, U+180, U+184, U+187, U+200-203, U+206-207, U+212, U+219, U+232-235, U+238-239, U+244, U+251, U+710, U+730, U+732, U+2019, U+201C-201E, U+8211-8212, U+8216-8218, U+8220-8222, U+8230, U+8249-8250, U+8364, U+8482, U+FFEB',
		'english'         => 'U+20-126, U+162-163, U+165, U+169, U+174, U+180, U+2019, U+201C-201E, U+8211-8212, U+8216-8217, U+8220-8221, U+8226, U+8230, U+8364, U+8482, U+FFEB',
		'french'          => 'U+20-126, U+162-163, U+165, U+168-169, U+171, U+174, U+180, U+184, U+187, U+192, U+194, U+198-203, U+206-207, U+212, U+217, U+219-220, U+224, U+226, U+230-235, U+238-239, U+244, U+249, U+251-252, U+255, U+338-339, U+376, U+710, U+730, U+732, U+2019, U+201C-201E, U+8211-8212, U+8216-8218, U+8220-8222, U+8230, U+8249-8250, U+8364, U+8482, U+FFEB',
		'german'          => 'U+20-126, U+162-163, U+165, U+168-169, U+171, U+174, U+180, U+184, U+187, U+196, U+214, U+220, U+223, U+228, U+246, U+252, U+710, U+730, U+732, U+2019, U+201C-201E, U+8211-8212, U+8216-8218, U+8220-8222, U+8230, U+8249-8250, U+8364, U+8482, U+FFEB',
		'latin'           => 'U+20-126, U+162-163, U+165, U+168-169, U+171, U+174, U+180, U+184, U+187, U+710, U+730, U+732, U+2019, U+201C-201E, U+8211-8212, U+8216-8218, U+8220-8222, U+8230, U+8249-8250, U+8364, U+8482, U+FFEB',
		'latin-extreme'   => 'U+20-126, U+160-263, U+268-275, U+278-283, U+286-287, U+290-291, U+298-299, U+302-305, U+310-311, U+313-318, U+321-328, U+332-333, U+336-347, U+350-357, U+362-363, U+366-371, U+376-382, U+536-539, U+710, U+730, U+732, U+2019, U+201C-201E, U+8211-8212, U+8216-8218, U+8220-8222, U+8226, U+8230, U+8249-8250, U+8364, U+8482, U+64257-64258, U+FFEB',
		'spanish'         => 'U+20-126, U+161-163, U+165, U+168-169, U+171, U+174, U+180, U+184, U+187, U+191, U+193, U+201, U+205, U+209, U+211, U+218, U+220, U+225, U+233, U+237, U+241, U+243, U+250, U+252, U+710, U+730, U+732, U+2019, U+201C-201E, U+8211-8212, U+8216-8218, U+8220-8222, U+8230, U+8249-8250, U+8364, U+8482, U+FFEB',
		'swedish'         => 'U+20-126, U+162-163, U+165, U+168-169, U+171, U+174, U+180, U+184, U+187, U+192-193, U+196-197, U+201, U+203, U+214, U+220, U+224-225, U+228-229, U+233, U+235, U+246, U+252, U+710, U+730, U+732, U+2019, U+201C-201E, U+8211-8212, U+8216-8218, U+8220-8222, U+8230, U+8249-8250, U+8364, U+8482, U+FFEB',
		'turkish'         => 'U+20-126, U+162-163, U+165, U+168-169, U+171, U+174, U+180, U+184, U+187, U+194, U+199, U+206, U+214, U+219-220, U+226, U+231, U+238, U+246, U+251-252, U+286-287, U+304-305, U+350-351, U+710, U+730, U+732, U+2019, U+201C-201E, U+8211-8212, U+8216-8218, U+8220-8222, U+8230, U+8249-8250, U+8364, U+8482, U+FFEB'
	);
	
	/**
	 *	Initalize Premium Fonts Adapter
	 */
	public function __construct() {
		add_action( 'wp_ajax_typolab_premium_fonts_download', array( & $this, 'installFont' ) );
		add_action( 'wp_ajax_typolab-preview-premium-fonts', array( & $this, 'preview' ) );
	}
	
	/**
	 *	Preview Font
	 */
	public function preview() {
		$font_family = kalium()->url->get( 'font-family' );
		
		if ( ! $font_family ) {
			return;
		}
		
		// Font Info
		$fonts_list = self::getFontsList();
		$preview_font = null;
		
		// Get the font
		foreach ( $fonts_list as $font ) {
			if ( $font_family == $font->family_urlname ) {
				$preview_font = $font;
			}
		}
		
		// Font doesn't exits
		if ( ! $preview_font ) {
			return;
		}
		
		// Font URL
		$uploads = wp_upload_dir();
		$font_url = $uploads['baseurl'] . '/';
		$downloaded_fonts = self::getDownloadedFonts();
		
		if ( isset( $downloaded_fonts[ $font_family ] ) ) {
			$font_url .= $downloaded_fonts[ $font_family ]['path'];
			$font_url .= '/preview.css';
		}
		
		// Font Variants
		$variants = (array) $preview_font->variants;
		
		// Check if its single line
		$single_line = isset( $_GET['single-line'] );
		
		if ( $single_line ) {
			$variants = array_slice( $variants, 0, 1 );
		}
		
		?>
		<html>
			<head>
				<link rel="stylesheet" href="<?php echo TypoLab::$typolab_assets_url . '/css/typolab.min.css'; ?>">
				<link rel="stylesheet" href="<?php echo $font_url; ?>">
				<style>
					.font-entry p {
						font-size: <?php echo intval( TypoLab::$font_preview_size ); ?>px;
					}
				</style>
			</head>
			<body id="preview-mode">
				<div class="font-preview">
				<?php
				foreach ( $variants as $font_face => $variant ) :
					?>
					<div class="font-entry<?php when_match( $single_line, 'single-entry' ); ?>">
						<p style="font-family: '<?php echo esc_attr( $font_face ); ?>';"><?php echo esc_html( TypoLab::$font_preview_str ); ?></p>
						<?php if ( ! $single_line ) : ?>
						<span><?php echo $variant->name; ?></span>
						<?php endif; ?>
					</div>
					<?php
				endforeach;
				?>
				</div>
			</body>
		</html>
		<?php
		die();
	}
	
	/**
	 *	Single Line Font Preview Link
	 */
	public static function singleLinePreview( $font ) {
		
		if ( ! isset( $font['options']['data']->family_urlname ) ) {
			return '';
		}
		
		$font_data = $font['options']['data'];
		$url = admin_url( "admin-ajax.php?action=typolab-preview-premium-fonts&single-line=true&font-family=" . rawurlencode( $font_data->family_urlname ) );
		return $url;
	}
	
	/**
	 *	Download and Install Premium Font
	 */
	public function installFont() {
		global $wp_filesystem;
		
		// File.php is required
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		
		$resp = array(
			'errors' => true,
			'error_msg' => ''
		);
		
		$fonts_list = self::getFontsList();
		$font_to_download = null;
		
		$font_family = kalium()->post( 'font_family' );
		
		// Get the font
		foreach ( $fonts_list as $font ) {
			if ( $font_family == $font->family ) {
				$font_to_download = $font;
			}
		}
		
		// Check if font family is provided
		if ( $font_to_download ) {
			$family_urlname = $font_to_download->family_urlname;
			
			// Check for license
			if ( Kalium_Theme_License::isValid() ) {
				global $wp_version;
				
				$license = Kalium_Theme_License::license();
				$uploads = wp_upload_dir();
				
				// Make request
				$download_link = str_replace( '{license-key}', $license->license_key, $font_to_download->package );
				
				// When OpenSSL version is not supported, remove https protocol
				if ( function_exists( 'get_openssl_version_number' ) && version_compare( get_openssl_version_number(), '1.0', '<' ) ) {
					$download_link = str_replace( 'https://', 'http://', $download_link );
				}
				
				// Make test request
				$request = wp_remote_post( $download_link, array(
					'body' => array(
						'test' => true
					)
				) );
				
				// Font download can start
				if ( 200 == wp_remote_retrieve_response_code( $request ) ) {
					$font_package_file = TypoLab::downloadFont( $download_link, self::$providerID );
					
					// Font is successfully downloaded
					if ( ! is_wp_error( $font_package_file ) ) {
						$font_dir = dirname( $font_package_file ) . "/{$family_urlname}";
						$extracted_font = TypoLab::unzipFont( $font_package_file, $font_dir, true );
						
						// Extract font
						if ( ! is_wp_error( $extracted_font ) ) {
							// Init WP Filesystem
							TypoLab::initFilesystem();
							
							// Move up one directory extracted files
							$extracted_dir = glob( "{$extracted_font}/*", GLOB_ONLYDIR );
							$extracted_dir = array_combine( $extracted_dir, array_map( 'filectime', $extracted_dir ) );
							arsort( $extracted_dir );
							
							$last_extracted_dir = key( $extracted_dir );
							
							foreach ( glob( "{$last_extracted_dir}/*", GLOB_ONLYDIR ) as $i => $move_dir ) {
								$working_directory = dirname( $move_dir );
								$dir_name = basename( $move_dir );
								rename( $move_dir, $font_dir . "/{$dir_name}" );
							}
					
							// Silence is golden folder
							TypoLab::silenceIsGolden( $font_dir );
							
							// Delete Temporary Directory
							$wp_filesystem->rmdir( $working_directory, true );
					
							// Create Preview File
							self::createFontIncludeFile( $font_to_download, $font_to_download->subsets, $font_to_download->variants,  $font_dir, 'preview.css' );
							
							// Save as downloaded font
							self::addDownloadedFont( $family_urlname, ltrim( str_replace( $uploads['basedir'] , '', $font_dir ), '/' ) );
							
							// Request was successful
							$resp['errors'] = false;
							$resp['downloaded'] = true;
						} 
						else {
							$resp['error_msg'] = $extracted_font->get_error_messages();
						}
					} 
					// Font cannot be downloaded
					else {
						$resp['error_msg'] = $font_package_file->get_error_messages();
					}
				}
				// Report error
				else if ( is_wp_error( $request ) ) {
					$resp['error_msg'] = $request->get_error_messages();
				}
				// Cannot download font, something is wrong with license or font file
				else {
					$request_response = json_decode( wp_remote_retrieve_body( $request ) );
					
					$error = new WP_Error( 'TypoLab-FontDownloadFailed', "[{$request_response->error_code}] {$request_response->error_msg}" );
					$resp['error_msg'] = $error->get_error_messages();
				}
			}
			// Site is not activated
			else {
				$error = new WP_Error( 'TypoLab-SiteNotActivated', 'Theme is not activated, you cannot download this font!' );
				$resp['error_msg'] = $error->get_error_messages();
			}
		}
		// Font doesn't exists
		else {
			$error = new WP_Error( 'TypoLab-FontDoesntExists', "The requested font doesn't exists!" );
			$resp['error_msg'] = $error->get_error_messages();
		}
		
		echo json_encode( $resp );
		die();
	}
	
	/**
	 *	Get Downloaded Fonts
	 */
	public static function getDownloadedFonts() {
		return TypoLab::getSetting( 'premium_fonts_downloads', array() );
	}
	
	/**
	 *	Get Downloaded Fonts
	 */
	public static function addDownloadedFont( $family, $font_path ) {
		$downloaded_fonts = self::getDownloadedFonts();
		
		$downloaded_fonts[ $family ] = array(
			'date' => time(),
			'path' => $font_path
		);
		
		TypoLab::setSetting( 'premium_fonts_downloads', $downloaded_fonts );
	}
	
	/**
	 *	Remove Downloaded Font
	 */
	public static function removeDownloadedFont( $font_id ) {
		$downloaded_fonts = self::getDownloadedFonts();
		
		if ( isset( $downloaded_fonts[ $font_id ] ) ) {
			unset( $downloaded_fonts[ $font_id ] );
		}
		
		TypoLab::setSetting( 'premium_fonts_downloads', $downloaded_fonts );
	}
	
	/**
	 *	Create CSS File that Includes Specified Font Variants
	 */
	public static function createFontIncludeFile( $font, $font_subsets, $font_variants, $font_dir, $font_file_name = 'font.css' ) {
		$font_file_path = "{$font_dir}/{$font_file_name}";
		$stylesheets = array();
		
		// Convert From Object to Array
		if ( is_object( $font_subsets ) ) {
			$font_subsets = array_keys( (array) $font_subsets );
		}
		
		if ( is_object( $font_variants ) ) {
			$font_variants = array_keys( (array) $font_variants );
		}

		if ( $font_subsets ) {
			foreach ( $font_subsets as $subset ) {
				if ( $font_variants ) {
					foreach ( $font_variants as $variant ) {
						$font_files = glob( "{$font_dir}/{$subset}/{$variant}_*/stylesheet.css" );
						$stylesheets = array_merge( $stylesheets, $font_files );
					}
				}
			}
		}

		
		// Append Font Faces
		$contents = array();
		
		foreach ( $stylesheets as $stylesheet ) {
			$font_variant_relative_path  = dirname( $stylesheet );
			$font_variant_path           = basename( $font_variant_relative_path );
			$font_subset_path            = basename( dirname( $font_variant_relative_path ) );
			
			$font_facename 				 = preg_replace( '/_[a-z0-9-]+$/i', '', $font_variant_path );
			
			$stylesheet_contents = file_get_contents( $stylesheet );
			$stylesheet_contents = str_replace( "url('", "url('{$font_subset_path}/{$font_variant_path}/", $stylesheet_contents );
			
			// Set Font Name
			$stylesheet_contents = preg_replace( "/font-family: '.*?';/", "font-family: '{$font_facename}';", $stylesheet_contents );
			
			// Set Unicode Range for the given subset
			if ( isset( self::$unicodeRanges[ $font_subset_path ] ) ) {
				$unicode_range = self::$unicodeRanges[ $font_subset_path ];
				$stylesheet_contents = preg_replace( "/(\@font-face\s+\{)(.*?)(\})/si", "/* Subset: {$font_subset_path} */\n\\1\\2\tunicode-range: {$unicode_range};\n\\3", $stylesheet_contents );
			}
			
			$contents[] = $stylesheet_contents;
		}
		
		return file_put_contents( $font_file_path, implode( '', $contents ) );
	}
	
	/**
	 *	Load Premium Fonts from JSON File
	 */
	public static function getFontsList() {
		// Once initialized, no need to load fonts list again
		if ( self::$fonts_list ) {
			return self::$fonts_list;
		}
		
		$fonts_json = file_get_contents( TypoLab::$typolab_path . '/assets/json/premium-fonts.json' );
		$fonts_json = @json_decode( $fonts_json );
		
		return self::$fonts_list = $fonts_json;
	}
	
	/**
	 *	Get Alphabetic Order 
	 */
	public static function groupFontsByFirstLetter() {
		$alphabetic_order = array();
		
		foreach ( self::$fonts_list as $font ) {
			$first = strtoupper( substr( $font->family, 0, 1 ) );
			if ( ! isset( $alphabetic_order[ $first ] ) ) {
				$alphabetic_order[ $first ] = array( 'letter' => $first, 'count' => 1 );
			} else {
				$alphabetic_order[ $first ]['count']++;
			}
		}
		
		uasort( $alphabetic_order, 'self::sortArrayAlpabetically' );
		
		return $alphabetic_order;
	}
	
	public static function sortArrayAlpabetically( $a, $b ) {
		return strcmp( $a['letter'], $b['letter'] );
	}
	
	/**
	 *	Get All Font Categories
	 */
	public static function groupFontsByCategory() {
		$font_categories = array();
		
		foreach ( self::$fonts_list as $font ) {
			$category = $font->category;
			
			if ( ! isset( $font_categories[ $category ] ) ) {
				$font_categories[ $category ] = array(
					'name' => $category,
					'count' => 1
				);
			} else {
				$font_categories[ $category ]['count']++;
			}
		}
		
		uasort( $font_categories, 'self::sortArrayByName' );
		
		return $font_categories;
	}
	
	public static function sortArrayByName( $a, $b ) {
		return strcmp( $a['name'], $b['name'] );
	}
}

new TypoLab_Premium_Fonts();
