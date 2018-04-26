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

/**
 * End of main wrapper actions
 */
do_action( 'kalium_wrapper_end' );

?>

	</div><?php // Wrapper end ?>
	
	<?php
	
	/**
	 * Footer area template
	 */
	if ( apply_filters( 'kalium_show_footer', true ) ) { 
		
		get_template_part( 'tpls/footer-main' );
		
	}
	
	/**
	 * WP Footer
	 */
	wp_footer();
	?>
	
	<!-- <?php echo 'ET: ', microtime( true ) - kalium()->getStartTime(), 's ', kalium()->getVersion(), ( is_child_theme() ? 'ch' : '' ); ?> -->
</body>
</html>