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

global $header_logo_class, $force_use_uploaded_logo, $force_custom_logo_image, $force_custom_logo_max_width;

$logo_text                      = get_data( 'logo_text' );
$use_uploaded_logo              = get_data( 'use_uploaded_logo' );
$custom_logo_image              = get_data( 'custom_logo_image' );
$custom_logo_max_width          = get_data( 'custom_logo_max_width' );
$custom_logo_mobile_max_width   = get_data( 'custom_logo_mobile_max_width' );

// Logo Sizes (array to be parsed in JavaScript)
$logo_sizes = array();

if ( ! $header_logo_class ) {
	$header_logo_class = 'header-logo';
}

if ( $force_use_uploaded_logo ) {
	$use_uploaded_logo     = $force_use_uploaded_logo;
	$custom_logo_image     = $force_custom_logo_image;
	$custom_logo_max_width = $force_custom_logo_max_width;
}

if ( $use_uploaded_logo ) {
	$logo_image = wp_get_attachment_image_src( $custom_logo_image, 'original' );

	if ( is_array( $logo_image ) ) {
		$logo_image_url = $logo_image[0];
		
		if ( ! $custom_logo_max_width ) {
			$custom_logo_max_width = $logo_image[1];
		}
	} else {
		$custom_logo_image = false;
	}

	// Custom Logo Width
	if ( $custom_logo_image && $custom_logo_max_width ) {
		$custom_logo_max_height = $logo_image[1] > 1 ? ( $custom_logo_max_width / $logo_image[1] ) * $logo_image[2] : false;
		generate_custom_style( '.header-logo.logo-image', "width: {$custom_logo_max_width}px;" . when_match( $custom_logo_max_height, "height: {$custom_logo_max_height}px;", '', false ) );
	}

	// Custom Logo Mobile Width
	if ( $custom_logo_image && $custom_logo_mobile_max_width ) {
		$mobile_menu_breakpoint = kalium_get_mobile_menu_breakpoint();
		$custom_logo_mobile_max_height = $logo_image[1] > 1 ? ( $custom_logo_mobile_max_width / $logo_image[1] ) * $logo_image[2] : false;
		$custom_logo_mobile_important = apply_filters( 'kalium_logo_mobile_width_force_important', false ) ? '!important' : ''; // Temporary fix until a newer version of sticky menu is released
		generate_custom_style( '.header-logo.logo-image', "width: {$custom_logo_mobile_max_width}px {$custom_logo_mobile_important};" . when_match( $custom_logo_mobile_max_height, "height: {$custom_logo_mobile_max_height}px {$custom_logo_mobile_important};", '', false ), "screen and (max-width: {$mobile_menu_breakpoint}px)" );
	}
}

do_action( 'kalium_before_logo' );

?>
<a itemprop="url" href="<?php echo apply_filters( 'kalium_logo_url', home_url() ); ?>" class="<?php 
	echo esc_attr( $header_logo_class );
	when_match( $use_uploaded_logo, 'logo-image', 'logo-text' );
?>">
	<?php if ( $use_uploaded_logo && isset( $logo_image_url ) ) : ?>
	<img itemprop="logo" src="<?php echo esc_url( str_replace( array( 'http:', 'https:' ), '', $logo_image_url ) ); ?>" width="<?php echo $logo_image[1]; ?>" height="<?php echo $logo_image[2]; ?>" class="main-logo" alt="<?php echo sanitize_title( get_bloginfo( 'name' ) ); ?>" />
<?php
	else:
		echo esc_html( $logo_text );
	endif; ?>
</a>

<?php
if ( count( $logo_sizes ) ) {
	?>
	<script type="text/javascript">
		var headerOptions = headerOptions || {};
		headerOptions.logoSizes = <?php echo json_encode( $logo_sizes ); ?>;
	</script>
	<?php
}

do_action( 'kalium_after_logo' );