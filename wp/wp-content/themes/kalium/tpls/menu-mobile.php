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

$nav_id = 'main-menu';

if ( has_nav_menu( 'mobile-menu' ) ) {
	$nav_id = 'mobile-menu';
}

$mobile_menu_class = array( 'mobile-menu-wrapper' );

// Menu Type
$is_fullscreen_type = '0' === get_data( 'menu_mobile_type' );

if ( $is_fullscreen_type ) {
	$mobile_menu_class[] = 'mobile-menu-fullscreen';
} else {
	$mobile_menu_class[] = 'mobile-menu-slide';
}
?>
<div class="<?php echo esc_attr( implode( ' ', $mobile_menu_class ) ); ?>">
	
	<div class="mobile-menu-container">
		
		<?php
			// Mobile menu
			wp_nav_menu( array(
				'theme_location' => $nav_id,
				'container'      => '',
				'menu_class'     => 'menu',
			) );
			
			// Mobile cart icon
			if ( is_shop_supported() && get_data( 'shop_cart_icon_menu' ) ) {
				kalium_woocommerce_cart_menu_icon_mobile();
			}
			
			// WPML Switcher
			kalium_wpml_language_switcher();
		?>
		
		<?php if ( get_data( 'menu_mobile_search_field', true ) ) : ?>
		<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ) ; ?>">
			<input type="search" class="search-field" placeholder="<?php echo __( 'Search site...', 'kalium' ) ; ?>" value="<?php echo get_search_query() ; ?>" name="s" id="search_mobile_inp" />
			
			<label for="search_mobile_inp">
				<i class="fa fa-search"></i>
			</label>
			
			<input type="submit" class="search-submit" value="<?php echo __( 'Go', 'kalium' ); ?>" />
		</form>
		<?php endif; ?>
		
		<?php if ( $is_fullscreen_type ) : ?>
		<a href="#" class="mobile-menu-close-link menu-bar exit menu-skin-light">
			<?php kalium_menu_icon_or_label(); ?>
		</a>
		<?php endif; ?>
		
	</div>
	
</div>

<div class="mobile-menu-overlay"></div>