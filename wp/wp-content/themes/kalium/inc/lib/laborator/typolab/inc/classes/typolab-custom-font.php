<?php
/**
 *	Custom Font
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class TypoLab_Custom_Font {
	
	/**
	 *	Provider ID
	 */
	public static $providerID = 'custom-font';
	
	/**
	 *	Initialize TypoLab Custom Font Adapter
	 */
	public function __construct() {
		add_action( 'wp_ajax_typolab-preview-custom-font', array( & $this, 'preview' ) );
	}
	
	/**
	 *	Preview Font
	 */
	public function preview() {
		$font_url = kalium()->url->get( 'font-url' );
		$font_family = wp_unslash( kalium()->url->get( 'font-family' ) );
		
		if ( ! $font_url || ! $font_family ) {
			return;
		}
		
		// Font URL
		$font_url = wp_extract_urls( $font_url );
		$font_url = $font_url ? rtrim( reset( $font_url ), '\\' ) : '';
		
		// Font Family Entries
		$font_family_entries =  self::wrapFontFamilyName( $font_family );
		
		if ( ! is_array( $font_family_entries ) ) {
			$font_family_entries = array( $font_family_entries );
		}
		
		// Check if its single line
		$single_line = isset( $_GET['single-line'] );
		
		if ( $single_line ) {
			$font_family_entries = array_splice( $font_family_entries, 0, 1 );
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
				foreach ( $font_family_entries as $font_family ) :
					?>
					<div class="font-entry<?php when_match( $single_line, 'single-entry' ); ?>">
						<p style="font-family: <?php echo esc_attr( self::wrapFontFamilyName( $font_family ) ); ?>;"><?php echo esc_html( TypoLab::$font_preview_str ); ?></p>
						<?php if ( ! $single_line ) : ?>
						<span><?php echo self::clearFontFamilyName( $font_family ); ?></span>
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
		
		$font_url = $font['options']['font_url'];
		$font_family = implode( ';', $font['options']['font_variants'] );
		
		$url = admin_url( "admin-ajax.php?action=typolab-preview-custom-font&single-line=true&font-url=" . rawurlencode( $font_url ) . '&font-family=' . rawurlencode( $font_family ) );
		return $url;
	}
	
	/**
	 *	Clear Font Family Name
	 */
	public static function clearFontFamilyName( $font_family ) {
		$font_family = str_replace( array( "'", '"' ), '', $font_family );
		$font_family = trim( $font_family );
		$font_family = explode( ',', $font_family );
		
		return esc_html( reset( $font_family ) );
	}
	
	/**
	 *	Wrap font family names with sinqle quote
	 */
	public static function wrapFontFamilyName( $font_family ) {
		
		// Generic Font Names
		$generic_font_names = array ( '', 'Arial', 'Courier', 'Garamond', 'Geneva', 'Georgia', 'Helvetica', 'Monaco', 'Palatino', 'Symbol', 'Tahoma', 'Verdana', 'Times', 'monospace', 'sans-serif', 'serif', 'cursive', 'fantasy' );
		
		// Wrapp array of font family names
		if ( is_array( $font_family ) ) {
			$font_family_names = array();
			
			foreach ( $font_family as $font_family_name ) {
				$font_family_wrapped = self::wrapFontFamilyName( $font_family_name );
				
				if ( is_array( $font_family_wrapped ) ) {
					$font_family_names = array_merge( $font_family_names, $font_family_wrapped );
				} else {
					$font_family_names[] = $font_family_wrapped;
				}
			}
			
			return array_filter( $font_family_names );
		}
		
		// Split font family names
		if ( strpos( $font_family, ';' ) !== false ) {
			$font_families = explode( ';', $font_family );
			return self::wrapFontFamilyName( $font_families );
		}
		
		// Wrap font family name
		$font_family = str_replace( array( 'font-family:' ), '', $font_family );
		$font_family = array_map( 'trim', explode( ',', str_replace( array( '"', "'" ), '', $font_family ) ) );
		
		foreach ( $font_family as $i => $font_family_name ) {
			$font_family[ $i ] = strpos( $font_family_name, ' ' ) !== false || ! in_array( $font_family_name, $generic_font_names ) ? "'{$font_family_name}'" : $font_family_name;
		}
		
		return implode( ', ', array_filter( $font_family ) );
	}
}

new TypoLab_Custom_Font();