<?php
/**
 *	Kalium WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

$license = Kalium_Theme_License::license();

wp_enqueue_script( 'laborator-product-activation' );
?>
<div class="product-activated-window">
	<h1>Product Activated!</h1>
	
	<div class="about-description">
		Congratulations! <strong><?php echo wp_get_theme(); ?></strong> has been successfully activated and now you can get latest updates of the theme.
	</div>
	
	
	<form id="theme-backups-form" method="post" enctype="application/x-www-form-urlencoded">
		<p>Below you can enable or disable theme auto backups when updating the theme to newest version:</p>
		
		<select name="theme_backups" id="theme_backups">
			<option value="1" <?php echo selected( 1, $license->save_backups ); ?>>Enable Backups</option>
			<option value="0" <?php echo selected( 0, $license->save_backups ); ?>>Disable Backups</option>
		</select>
		<button type="submit" class="button button-primary">Save</button>
	</form>
	
	<a href="#" class="start-using-kalium close-this-window">
		Start using Kalium!
	</a>
	
	<br>
	<p>You can <a href="#" class="close-this-window">close this window</a> now.</p>
</div>

<script type="text/javascript">
	jQuery( 'body' ).addClass( 'laborator-product-activated-window' );
	
	// Resize popup window
	jQuery( document ).ready( function( $ ) {
		var $lastEl = jQuery( '.start-using-kalium' );
		window.resizeTo( jQuery( window ).width(), $lastEl.offset().top + $lastEl.outerHeight() + 50 );
	} );
</script>
<style>
	html.wp-toolbar, #wpbody {
		padding-top: 0;
	}
	
	.notice-warning {
		display: none !important;
	}
</style>

<?php do_action( 'kalium_product_registration_page' ); ?>