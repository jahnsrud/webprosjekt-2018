<?php
/**
 * Checkout login form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
	return;
}

$info_message  = apply_filters( 'woocommerce_checkout_login_message', __( 'Returning customer?', 'woocommerce' ) );
$info_message .= ' <a href="#" class="showlogin">' . __( 'Click here to login', 'woocommerce' ) . '</a>';

?>
<?php // start: modified by Arlind ?>
<div class="checkout-form-option<?php when_match( ! empty( WC()->cart->applied_coupons ), 'checkout-form-option--only' ); ?>">

	<div class="checkout-form-option--header">
		<?php 
			wc_print_notice( $info_message, 'notice' );
		?>
	</div>
	
	<div class="checkout-form-option--content" id="checkout-login-form-container">
	<?php // end: modified by Arlind ?>

		<?php
		
		woocommerce_login_form(
			array(
				'message'  => __( 'If you have shopped with us before, please enter your details in the boxes below. If you are a new customer, please proceed to the Billing &amp; Shipping section.', 'woocommerce' ),
				'redirect' => wc_get_page_permalink( 'checkout' ),
				'hidden'   => false, // Modified by Arlind Nushi
			)
		);
		
	?>
	
	<?php // start: modified by Arlind ?>
	</div>
	
</div>
<?php // end: modified by Arlind