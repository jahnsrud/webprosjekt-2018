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

class TypoLab_Google_Fonts {
	
	/**
	 *	Provider ID
	 */
	public static $providerID = 'google-fonts';
	
	/**
	 *	Loaded Google Fonts
	 */
	public static $fonts_list = array();
	
	/**
	 *	Initialize TypoLab Google Fonts Adapter
	 */
	public function __construct() {
		add_action( 'wp_ajax_typolab-preview-google-fonts', array( & $this, 'preview' ) );
	}
	
	/**
	 *	Preview Font
	 */
	public function preview() {
		$font_family = kalium()->url->get( 'font-family' );
		$font_to_load = null;
		
		$fonts_list = self::getFontsList();
		
		foreach ( $fonts_list as $font ) {
			if ( $font_family == $font->family ) {
				$font_to_load = $font;
				break;
			}
		}
		
		if ( ! $font_family || ! $font_to_load ) {
			return;
		}
		
		// Font Details
		$family   = $font_to_load->family;
		$variants = $font_to_load->variants;
		$subsets  = $font_to_load->subsets;
		
		// Font URL
		$font_url = self::fontURL( $family, $variants, $subsets );
		
		// Check if its single line
		$single_line = isset( $_GET['single-line'] );
		
		if ( $single_line ) {
			$variants = array_splice( $variants, 0, 1 );
		}
		
		?>
		<html>
			<head>
				<link rel="stylesheet" href="<?php echo TypoLab::$typolab_assets_url . '/css/typolab.min.css'; ?>">
				<link rel="stylesheet" href="<?php echo $font_url; ?>">
				<style>
					.font-entry p {
						font-family: '<?php echo esc_attr( $family ); ?>', sans-serif;
						font-size: <?php echo intval( TypoLab::$font_preview_size ); ?>px;
					}
				</style>
			</head>
			<body id="preview-mode">
				<div class="font-preview">
				<?php
				foreach ( $variants as $variant ) :
					$italic = strpos( $variant, 'italic' ) !== false;
					$variant_id = str_replace( 'italic', '', $variant );
					?>
					<div class="font-entry<?php when_match( $single_line, 'single-entry' ); ?>">
						<p style="font-weight: <?php echo 'regular' == $variant_id ? 'normal' : $variant_id; ?>; font-style: <?php echo $italic ? 'italic' : 'normal'; ?>"><?php echo esc_html( TypoLab::$font_preview_str ); ?></p>
						<?php if ( ! $single_line ) : ?>
						<span><?php echo trim( str_replace( 'italic', ',italic', $variant ), ',' ); ?></span>
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
		$url = admin_url( "admin-ajax.php?action=typolab-preview-google-fonts&single-line=true&font-family=" . rawurlencode( $font_data->family ) );
		return $url;
	}
	
	/**
	 *	Generate Font URL
	 */
	public static function fontURL( $family, $variants = array( 'regular' ), $subsets = array( 'latin' ) ) {
		// Googe Fonts API URL
		$url = 'https://fonts.googleapis.com/css?';
		
		// Font Family
		$url .= 'family=';
		$url .= urlencode( $family );
		
		// Variants
		$url .= ':';
		$url .= implode( ',', array_map( 'urlencode', $variants ) );
		
		// Subset
		$url .= '&amp;';
		$url .= implode( ',', array_map( 'urlencode', $subsets ) );
		
		return $url;
	}
	
	/**
	 *	Load Google Fonts from JSON File
	 */
	public static function getFontsList() {
		// Once initialized, no need to load fonts list again
		if ( self::$fonts_list ) {
			return self::$fonts_list;
		}
		
		$fonts_json = file_get_contents( TypoLab::$typolab_path . '/assets/json/google-fonts.json' );
		$fonts_json = @json_decode( $fonts_json );
		
		if ( isset( $fonts_json->items ) ) {
			return self::$fonts_list = $fonts_json->items;
		}
		
		return array();
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
			
			switch ( $category ) {
				case 'display':
					$category = 'Display';
					break;
					
				case 'handwriting':
					$category = 'Handwriting';
					break;
					
				case 'monospace':
					$category = 'Monospace';
					break;
					
				case 'sans-serif':
					$category = 'Sans Serif';
					break;
					
				case 'serif':
					$category = 'Serif';
					break;
			}
			
			if ( ! isset( $font_categories[ $font->category ] ) ) {
				$font_categories[ $font->category ] = array(
					'name' => $category,
					'count' => 1
				);
			} else {
				$font_categories[ $font->category ]['count']++;
			}
		}
		
		uasort( $font_categories, 'self::sortArrayByName' );
		
		return $font_categories;
	}
	
	public static function sortArrayByName( $a, $b ) {
		return strcmp( $a['name'], $b['name'] );
	}
}

new TypoLab_Google_Fonts();