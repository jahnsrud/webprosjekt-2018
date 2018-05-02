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

$footer_bottom_visible  = get_data( 'footer_bottom_visible' );
$footer_collapse_mobile = get_data( 'footer_collapse_mobile' );
$footer_widgets_columns = get_data( 'footer_widgets_columns' );

$footer_widgets_classes = array( 'footer-widgets' );

if ( $footer_collapse_mobile ) {
	$footer_widgets_classes[] = 'footer-collapsed-mobile';
}

?>
<div class="container">
	
	<div class="<?php echo implode(' ', $footer_widgets_classes ); ?>">
		
		<?php if ( $footer_collapse_mobile ) : ?>
		<a href="#" class="footer-collapse-link">
			<span>.</span>
			<span>.</span>
			<span>.</span>
		</a>
		<?php endif; ?>
	
		<div class="footer--widgets widget-area widgets--columned-layout widgets--columns-<?php echo kalium_get_number_from_word( $footer_widgets_columns ); ?>" role="complementary">
			
			<?php dynamic_sidebar( 'footer_sidebar' ); ?>
			
		</div>
	
	</div>
	
	<hr>
	
</div>