<?php
/**
 *	TypoLab Google Font Class
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class TypoLab_Font_Squirrel {
	
	/**
	 *	Provider ID
	 */
	public static $providerID = 'font-squirrel';
	
	/**
	 *	Loaded Font Squirrel Fonts
	 */
	public static $fonts_list = array();
	
	/**
	 *	Initialize TypoLab Google Fonts Adapter
	 */
	public function __construct() {
		add_action( 'wp_ajax_typolab_font_squirrel_download', array( & $this, 'installFont' ) );
		add_action( 'wp_ajax_typolab-preview-font-squirrel', array( & $this, 'preview' ) );
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
		$font_info = self::getFontFamilyInfo( $font_family );
		
		// Font URL
		$uploads = wp_upload_dir();
		$font_url = $uploads['baseurl'] . '/';
		$downloaded_fonts = self::getDownloadedFonts();
		
		if ( isset( $downloaded_fonts[ $font_family ] ) ) {
			$font_url .= $downloaded_fonts[ $font_family ]['path'];
			$font_url .= '/preview.css';
		}
		
		// Check if its single line
		$single_line = isset( $_GET['single-line'] );
		
		if ( $single_line ) {
			$font_info = array_splice( $font_info, 0, 1 );
		}
		
		?>
		<html>
			<head>
				<link rel="stylesheet" href="<?php echo TypoLab::$typolab_assets_url . '/css/typolab.min.css'; ?>">
				<link rel="stylesheet" href="<?php echo $font_url; ?>">
				<style>
					.font-entry p {
						font-family: '<?php echo esc_attr( $font_family ); ?>', sans-serif;
						font-size: <?php echo intval( TypoLab::$font_preview_size ); ?>px;
					}
				</style>
			</head>
			<body id="preview-mode">
				<div class="font-preview">
				<?php
				foreach ( $font_info as $font_variant ) :
					$fontface_name = $font_variant->fontface_name;
					$style_name = $font_variant->style_name;
					?>
					<div class="font-entry<?php when_match( $single_line, 'single-entry' ); ?>">
						<p style="font-family: '<?php echo esc_attr( $fontface_name ); ?>';"><?php echo esc_html( TypoLab::$font_preview_str ); ?></p>
						<?php if ( ! $single_line ) : ?>
						<span><?php echo $style_name; ?></span>
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
		$font_data = $font['options']['data'];
		
		if ( is_array( $font_data ) ) {
			$font_data = reset( $font_data );
		}
		
		$url = admin_url( "admin-ajax.php?action=typolab-preview-font-squirrel&single-line=true&font-family=" . rawurlencode( $font_data->family_urlname ) );
		return $url;
	}
	
	/**
	 *	Download Font and Install It
	 */
	public function installFont() {
		global $wp_filesystem;
		
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		
		$resp = array(
			'errors' => true,
			'error_msg' => 'Unknown error happened!'
		);
		
		/**
		 *	Font Family URL Name (Required)
		 */
		$font_family = kalium()->post( 'font_family' );
		
		// Check if font is provided
		if ( $font_family ) {
			$download_url = 'https://www.fontsquirrel.com/fontfacekit/' . $font_family;
			$font_package = TypoLab::downloadFont( $download_url, self::$providerID );
			$uploads      = wp_upload_dir();
			
			// Font is downloaded
			if ( ! is_wp_error( $font_package ) ) {
				$font_files_extract_dir = dirname( $font_package ) . "-tmp";
				$tmp_font_dir = TypoLab::unzipFont( $font_package, $font_files_extract_dir, true );
				
				// Font is extracted
				if ( ! is_wp_error( $tmp_font_dir ) ) {
					// Init WP Filesystem
					TypoLab::initFilesystem();
					
					$font_dir = dirname( $font_package ) . "/{$font_family}";
					
					// Create Font Directory
					wp_mkdir_p( $font_dir );
					
					// Move valid font files to font directory
					$extracted_files = glob( $tmp_font_dir . "/web fonts/*" );
					
					foreach ( $extracted_files as $file ) {
						rename( $file, $font_dir . '/' . basename( $file ) );
					}
					
					// Silence is golden folder
					TypoLab::silenceIsGolden( $font_dir );
									
					// Delete TMP directory
					$wp_filesystem->rmdir( $tmp_font_dir, true );
				
					// Font Info
					$font_info = self::getFontFamilyInfo( $font_family );
					
					// Create Preview File
					self::createFontIncludeFile( $font_info, $font_dir, 'preview.css' );
				
					// Save as downloaded font
					self::addDownloadedFont( $font_family, ltrim( str_replace( $uploads['basedir'] , '', $font_dir ), '/' ) );
					
					// Request was successful
					$resp['errors'] = false;
					$resp['downloaded'] = true;
					
						
				} else {
					$resp['error_msg'] = $tmp_font_dir->get_error_messages();
				}
			} 
			// Font coudn't be downloaded, show errors
			else {
				$resp['error_msg'] = $font_package->get_error_messages();
			}
			
		} else {
			$resp['error_msg'] = array( 'No font family name is provided!' );
		}
		
		echo json_encode( $resp );
		die();
	}
	
	/**
	 *	Get Downloaded Fonts
	 */
	public static function getDownloadedFonts() {
		return TypoLab::getSetting( 'font_squirrel_downloads', array() );
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
		
		TypoLab::setSetting( 'font_squirrel_downloads', $downloaded_fonts );
	}
	
	/**
	 *	Remove Downloaded Font
	 */
	public static function removeDownloadedFont( $font_id ) {
		$downloaded_fonts = self::getDownloadedFonts();
		
		if ( isset( $downloaded_fonts[ $font_id ] ) ) {
			unset( $downloaded_fonts[ $font_id ] );
		}
		
		TypoLab::setSetting( 'font_squirrel_downloads', $downloaded_fonts );
	}
	
	/**
	 *	Sanitize Font Variants from Font Family Info Object
	 */
	public static function sanitizeFontVariants( $variants ) {
		$sanitized_variants = array();
		
		if ( is_array( $variants ) ) {
			foreach ( $variants as $variant ) {
				$sanitized_variants[] = strtolower( str_replace( ' ', '', $variant->style_name ) );
			}
		}
		
		return $sanitized_variants;
	}
	
	/**
	 *	Get Font Family Info
	 */
	public static function getFontFamilyInfo( $family_urlname ) {
		// Font Info
		$font_info = json_decode( wp_remote_retrieve_body( wp_remote_get( 'http://www.fontsquirrel.com/api/familyinfo/' . $family_urlname ) ) );
		
		return $font_info;
	}
	
	/**
	 *	Create CSS File that Includes Specified Font Variants
	 */
	public static function createFontIncludeFile( $variants, $font_dir, $font_file_name = 'font.css' ) {
		$font_file_path = "{$font_dir}/{$font_file_name}";
		
		if ( false === file_exists( $font_dir ) ) {
			return false;
		}
		
		$contents = array();
		
		foreach ( $variants as $variant ) {
			$fontface_name = $variant->fontface_name;
			$style_name    = strtolower( str_replace( ' ', '', $variant->style_name ) );
			$stylesheets   = glob( "{$font_dir}/*_{$style_name}_*/stylesheet.css" );
			
			$assigned_variants = array();
			
			if ( $stylesheets ) {
				$stylesheets = array_slice( $stylesheets, 0, 1 );
				
				foreach ( $stylesheets as $stylesheet ) {
					$font_variant_relative_path = basename( dirname( $stylesheet ) );
					
					$stylesheet_contents = file_get_contents( $stylesheet );
					$stylesheet_contents = str_replace( "url('", "url('{$font_variant_relative_path}/", $stylesheet_contents );
					$stylesheet_contents = preg_replace( "/font-family: '(.*?)';/", "font-family: '{$fontface_name}';", $stylesheet_contents );
					
					$contents[] = $stylesheet_contents;
				}
			}
		}
		
		return file_put_contents( $font_file_path, implode( '', $contents ) );
	}
	
	/**
	 *	Load Font Squirrel Fonts from JSON File
	 */
	public static function getFontsList() {
		// Once initialized, no need to load fonts list again
		if ( self::$fonts_list ) {
			return self::$fonts_list;
		}
		
		$fonts_json = file_get_contents( TypoLab::$typolab_path . '/assets/json/font-squirrel.json' );
		$fonts_json = @json_decode( $fonts_json );
		
		return self::$fonts_list = $fonts_json;
	}
	
	/**
	 *	Get Alphabetic Order 
	 */
	public static function groupFontsByFirstLetter() {
		$alphabetic_order = array();
		
		foreach ( self::$fonts_list as $font ) {
			$first = strtoupper( substr( $font->family_name, 0, 1 ) );
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
			$category = $font->classification;
			
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

new TypoLab_Font_Squirrel();