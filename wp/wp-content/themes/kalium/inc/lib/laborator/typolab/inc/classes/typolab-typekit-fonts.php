<?php
/**
 *	TypeKit Fonts
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class TypoLab_TypeKit_Fonts {
	
	/**
	 *	Provider ID
	 */
	public static $providerID = 'typekit-fonts';
	
	/**
	 *	Loaded Typekit Fonts
	 */
	public static $fonts_list = array();
	
	/**
	 *	Initalize Premium Fonts Adapter
	 */
	public function __construct() {
		// TypeKit Library
		require_once TypoLab::$typolab_path . '/inc/typekit-client.php';
		
		// Preview font
		add_action( 'wp_ajax_typolab-preview-typekit-font', array( & $this, 'preview' ) );
	}
	
	/**
	 *	Preview Font
	 */
	public function preview() {
		$kit_id = kalium()->url->get( 'kit-id' );
		
		if ( ! $kit_id ) {
			return;
		}
		
		$typekit = new Typekit();
		$kit_info = $typekit->get( $kit_id );
		
		// Check if its single line
		$single_line = isset( $_GET['single-line'] );
		
		if ( $single_line ) {
			$kit_info['kit']['families'] = array_splice( $kit_info['kit']['families'], 0, 1 );
		}
		?>
		<html>
			<head>
				<link rel="stylesheet" href="<?php echo TypoLab::$typolab_assets_url . '/css/typolab.min.css'; ?>">
				<?php self::embedKitJS( $kit_id ); ?>
			</head>
			<body id="preview-mode" style="visibility: hidden;">
				<?php if ( $kit_info ) : ?>
				<div class="font-preview">
				<?php
				foreach ( $kit_info['kit']['families'] as $font ) :
					$css_stack = $font['css_stack'];
					?>
					<div class="font-entry<?php when_match( $single_line, 'single-entry' ); ?>">
						<p style="font-family: <?php echo esc_attr( $css_stack ); ?>;"><?php echo esc_html( TypoLab::$font_preview_str ); ?></p>
						<?php if ( ! $single_line ) : ?>
						<span><?php echo $font['name']; ?></span>
						<?php endif; ?>
					</div>
					<?php
				endforeach;
				?>
				</div>
				<?php else : ?>
				<p style="padding: 20px 15px;font-family: Helvetica, sans-serif;">Kit ID <strong><?php echo $kit_id; ?></strong> doesn't exists!</p>
				<?php endif; ?>
				
				<script>
					window.onload = function() {
						document.body.style.visibility = 'visible';
						window.kitInfo = <?php echo json_encode( $kit_info ); ?>
					}
				</script>
			</body>
		</html>
		<?php
		
		die();
	}
	
	/**
	 *	Single Line Font Preview Link
	 */
	public static function singleLinePreview( $font ) {
		if ( ! isset( $font['kit_id'] ) ) {
			return '';
		}
		
		$kit_id = $font['kit_id'];
		$url = admin_url( "admin-ajax.php?action=typolab-preview-typekit-font&single-line=true&kit-id=" . rawurlencode( $kit_id ) );
		return $url;
	}
	
	/**
	 *	Get Fonts List
	 */
	public static function getFontsList() {
		
		// Once initialized, no need to load fonts list again
		if ( self::$fonts_list ) {
			return self::$fonts_list;
		}
		
		return self::$fonts_list;
	}
	
	/**
	 *	Embedd Kit in JavaScript
	 */
	public static function embedKitJS( $kit_id ) {
		$opts = array(
			'async' => true
		);
		?>
		<script src="https://use.typekit.net/<?php echo esc_attr( $kit_id ); ?>.js"></script>
		<script>try{Typekit.load(<?php echo json_encode( apply_filters( 'typolab_typekit_embed_load_args', $opts ) ); ?>);}catch(e){}</script>
		<?php
	}
	
	/**
	 *	Synchronized loading for font embedding
	 */
	public static function syncLoadingFilter( $opts ) {
		$opts['async'] = false;
		return $opts;
	}
}

new TypoLab_TypeKit_Fonts();