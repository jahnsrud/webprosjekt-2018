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

// Footer texts
$footer_text = get_data( 'footer_text' );
$footer_text_right = get_data( 'footer_text_right' );

?>
<footer id="footer" role="contentinfo" <?php kalium_footer_class(); ?>>
	
	<?php
		// Display footer widgets, if enabled
		if ( get_data( 'footer_widgets' ) ) {
			get_template_part( 'tpls/footer-widgets' );
		}
	?>

	<?php if ( get_data( 'footer_bottom_visible' ) ) : ?>
	
	<div class="footer-bottom">
		
		<div class="container">

			<div class="footer-bottom-content">
				
				<?php if ( $footer_text_right ) : ?>
				
					<div class="footer-content-right">
							<?php echo do_shortcode( laborator_esc_script( $footer_text_right ) ); ?>
							
					</div>
					
				<?php endif; ?>

				<?php if ( $footer_text ) : ?>
				
					<div class="footer-content-left">
						
						<div class="copyrights site-info">
							
							<p><?php echo do_shortcode( laborator_esc_script( $footer_text ) ); ?></p>
							
						</div>
						
					</div>
				
				<?php endif; ?>
			</div>

		</div>
		
	</div>
	
	<?php endif; ?>

</footer>