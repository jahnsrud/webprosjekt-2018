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

// Fetch the post
the_post();

// When using "mPress Custom Front Page" template switching is necesarry
if ( 'portfolio' == get_post_type() ) {
	get_template_part( 'single-portfolio' );
	return;
}

// Show header
get_header();

// Check if is default container
$is_vc_content = preg_match( "/\[vc_row.*?\]/i", $post->post_content );

// Password protected page doesn't use vc container
if ( post_password_required() ) {
	$is_vc_content = false;
}

// Page title (show or hide)
$show_title = false == $is_vc_content && is_singular() && kalium()->acf->get_field( 'heading_title' );

// Container start
$container = array();

if ( $is_vc_content ) {
	$container[] = 'vc-container';
} else {
	$container[] = 'container';
	$container[] = 'default-margin';
	
	if ( ! is_shop_supported() || ! ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
		$container[] = 'post-formatting';
	}
}
?>
<div class="<?php echo esc_attr( implode( ' ', $container ) ); ?>">
<?php


// Show page title
if ( false == defined( 'HEADING_TITLE_DISPLAYED' ) && apply_filters( 'kalium_page_title', $show_title ) ) {
	?>
	<h1 class="wp-page-title"><?php the_title(); ?></h1>
	<?php
} 

// Page content		
the_content();
		

// Container end
?>
</div>
<?php

// Show footer
get_footer();