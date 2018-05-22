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

$nav = kalium_nav_menu();

// Menu In Use
$menu_type	 = get_data( 'main_menu_type' );
$sticky_header = get_data( 'sticky_header' );

// Header Options
$header_vpadding_top    = get_data( 'header_vpadding_top' );
$header_vpadding_bottom = get_data( 'header_vpadding_bottom' );
$header_fullwidth       = get_data( 'header_fullwidth' );

// Header Classes
$header_classes = array( 'site-header', 'main-header' );
$header_classes[] = 'menu-type-' . esc_attr( $menu_type );

// Fullwidth Header
if ( $header_fullwidth ) {
	$header_classes[] = 'fullwidth-header';
}

// Header Options
$header_options = array(
	'stickyHeader' => false
);

// Current Menu Skin
switch ( $menu_type ) {
	// Fullscreen Menu
	case 'full-bg-menu':
		$current_menu_skin = get_data( 'menu_full_bg_skin' );
		break;
		
	// Standard Menu
	case 'standard-menu':
		$current_menu_skin = get_data( 'menu_standard_skin' );
		break;
	
	// Top Menu
	case 'top-menu':
		$current_menu_skin = get_data( 'menu_top_skin' );
		break;
	
	// Sidebar Menu
	case 'sidebar-menu':
		$current_menu_skin = get_data( 'menu_sidebar_skin' );
		break;
}

// Header Vertical Padding
if ( is_numeric( $header_vpadding_top ) && $header_vpadding_top >= 0 ) {
	generate_custom_style( 'header.site-header', "padding-top: {$header_vpadding_top}px;" );
	
	// Responsive
	if ( $header_vpadding_top >= 40 ) {
		generate_custom_style( 'header.site-header', 'padding-top: ' . ( $header_vpadding_top / 2 ) . 'px;', 'screen and (max-width: 992px)' );
	}
	
	if ( $header_vpadding_top >= 40 ) {
		generate_custom_style( 'header.site-header', 'padding-top: ' . ( $header_vpadding_top / 3 ) . 'px;', 'screen and (max-width: 768px)' );
	}
}

if ( is_numeric( $header_vpadding_bottom ) && $header_vpadding_bottom >= 0 ) {
	generate_custom_style( 'header.site-header', "padding-bottom: {$header_vpadding_bottom}px;" );
	
	// Responsive
	if ( $header_vpadding_top >= 40 ) {
		generate_custom_style( 'header.site-header', 'padding-bottom: ' . ( $header_vpadding_bottom / 2 ) . 'px;', 'screen and (max-width: 992px)' );
	}
	
	if ( $header_vpadding_top >= 40 ) {
		generate_custom_style( 'header.site-header', 'padding-bottom: ' . ( $header_vpadding_bottom / 3 ) . 'px;', 'screen and (max-width: 768px)' );
	}
}

// Sticky Header
if ( $sticky_header ) {
	$header_classes[] = 'is-sticky';
	
	/// Sticky Header Options
	$header_options['stickyHeader'] = kalium_get_sticky_header_options();
	
	// Logo Switch Sections
	$header_options['sectionLogoSwitch'] = kalium_get_logo_switch_sections();
}

// Sub menu indicator
$submenu_dropdown_indicator = get_data( 'submenu_dropdown_indicator' );
?>
<header class="<?php echo implode( ' ', $header_classes ); ?>">
	
	<div class="container">

		<div class="logo-and-menu-container">
			
			<?php do_action( 'kalium_header_main_before_logo' ); ?>
			
			<div itemscope itemtype="http://schema.org/Organization" class="logo-column">
				
				<?php get_template_part( 'tpls/logo' ); ?>
				
			</div>
			
			<?php do_action( 'kalium_header_main_before_menu' ); ?>
				
			<div class="menu-column">
			<?php
				
			// Show Menu (by type)
			switch ( $menu_type ) :
			
				case 'full-bg-menu':
				
					$menu_full_bg_search_field      = get_data( 'menu_full_bg_search_field' );
					$menu_full_bg_alignment         = get_data( 'menu_full_bg_alignment' );
					$menu_full_bg_footer_block		= get_data( 'menu_full_bg_footer_block' );
					$menu_full_bg_skin				= get_data( 'menu_full_bg_skin' );
					$menu_full_bg_opacity			= get_data( 'menu_full_bg_opacity' );
					
					$menu_bar_skin_active = $menu_full_bg_skin;
					
					switch ( $menu_full_bg_skin ) {
						case "menu-skin-light":
							$menu_bar_skin_active = 'menu-skin-dark';
							break;
							
						default:
							$menu_bar_skin_active = 'menu-skin-light';
					}
					
					?>
					<div class="full-bg-menu-items menu-items-blocks">
						
						<?php
							
						// Show Language Switcher
						kalium_wpml_language_switcher( $current_menu_skin );
						
						// Show Search Field
						kalium_header_search_field( $current_menu_skin );
						
						// Show Mini Cart
						if ( is_shop_supported() ) {
							kalium_woocommerce_header_mini_cart( $current_menu_skin );
						}
						
						?>
						<a class="<?php kalium()->helpers->showClasses( array( 'menu-bar', $current_menu_skin ), true ); ?>" data-menu-skin-default="<?php echo esc_attr( $current_menu_skin ); ?>" data-menu-skin-active="<?php echo esc_attr( $menu_bar_skin_active ); ?>" href="#">
							<?php kalium_menu_icon_or_label(); ?>
						</a>
						
					</div>
					<?php
					
					break;
				
				case 'standard-menu':
					
					$menu_standard_menu_bar_visible    = get_data( 'menu_standard_menu_bar_visible' );
					$menu_standard_skin                = get_data( 'menu_standard_skin' );
					$menu_standard_menu_bar_effect     = get_data( 'menu_standard_menu_bar_effect' );
					
					// Standard menu classes
					$standard_menu_classes = array( 'menu-items-blocks', 'standard-menu-container' );
					
					$standard_menu_classes[] = $menu_standard_skin;
					$standard_menu_classes[] = $menu_standard_menu_bar_effect;
					
					if ( $menu_standard_menu_bar_visible ) {
						$standard_menu_classes[] = 'menu-bar-root-items-hidden';
					}
					
					if ( $submenu_dropdown_indicator ) {
						$standard_menu_classes[] = 'dropdown-caret';
					}
					
					// Standard menu bar classes
					$standard_menu_bar_classes = array( 'menu-bar' );
					$standard_menu_bar_classes[] = $menu_standard_skin;
					
					if ( ! $menu_standard_menu_bar_visible ) {
						$standard_menu_bar_classes[] = 'menu-bar-hidden-desktop';
					}
					?>
					<div class="<?php kalium()->helpers->showClasses( $standard_menu_classes, true ); ?>">
						
						<nav><?php echo $nav; ?></nav>

						<?php
						// Show Language Switcher
						kalium_wpml_language_switcher( $current_menu_skin );
							
						// Show Search Field
						kalium_header_search_field( $current_menu_skin );
						
						// Show Mini Cart
						if ( is_shop_supported() ) {
							kalium_woocommerce_header_mini_cart( $current_menu_skin );
						}
						?>
						
						<a class="<?php kalium()->helpers->showClasses( $standard_menu_bar_classes, true ); ?>" href="#">
							<?php kalium_menu_icon_or_label(); ?>
						</a>
					</div>
					<?php
					break;
			
			case 'top-menu':
			
				$menu_top_skin = get_data( 'menu_top_skin' );
				
				?>
				<div class="menu-items-blocks top-menu-items">
					
					<?php
					// Show Language Switcher
					kalium_wpml_language_switcher( $current_menu_skin );
						
					// Show Search Field
					kalium_header_search_field( $current_menu_skin );
						
					// Show Mini Cart
					if ( is_shop_supported() ) {
						kalium_woocommerce_header_mini_cart( $current_menu_skin );
					}
					?>
					
					<a class="<?php kalium()->helpers->showClasses( array( 'menu-bar', $current_menu_skin ), true ); ?>" href="#">
						<?php kalium_menu_icon_or_label(); ?>
					</a>
					
				</div>
				<?php
					break;
			
			case 'sidebar-menu':
				
				$menu_sidebar_skin = get_data( 'menu_sidebar_skin' );
				
				?>
				
				<div class="menu-items-blocks sidebar-menu-items">
					
					<?php	
					// Show Language Switcher
					kalium_wpml_language_switcher( $current_menu_skin );
						
					// Show Search Field
					kalium_header_search_field( $current_menu_skin );
						
					// Show Mini Cart
					if ( is_shop_supported() ) {
						kalium_woocommerce_header_mini_cart( $current_menu_skin );
					}
					
					// Menu bar
					?>
					<a class="<?php kalium()->helpers->showClasses( array( 'menu-bar', $current_menu_skin ), true ); ?>" href="#">
						<?php kalium_menu_icon_or_label(); ?>
					</a>
					
				</div>
				<?php	
				
				endswitch;
				?>
			</div>
		</div>
		
		<?php
		// Full Screen Menu Container
		if ( $menu_type == 'full-bg-menu' ) :
			
			// Full bg menu classes
			$full_bg_menu_classes = array( 'full-screen-menu', 'menu-open-effect-fade' );
			$full_bg_menu_classes[] = $menu_full_bg_skin;
			
			if ( $submenu_dropdown_indicator ) {
				$full_bg_menu_classes[] = 'submenu-indicator';
			}
			
			if ( 'centered-horizontal' == $menu_full_bg_alignment ) {
				$full_bg_menu_classes[] = 'menu-horizontally-center';
			}
			
			if ( in_array( $menu_full_bg_alignment, array( 'centered', 'centered-horizontal' ) ) ) {
				$full_bg_menu_classes[] = 'menu-aligned-center';
			}
			
			if ( $menu_full_bg_footer_block ) {
				$full_bg_menu_classes[] = 'has-fullmenu-footer';
			}
			
			if ( $menu_full_bg_opacity ) {
				$full_bg_menu_classes[] = 'translucent-background';
			}
		?>
		<div class="<?php kalium()->helpers->showClasses( $full_bg_menu_classes, true ); ?>">
			
			<div class="container">
				
				<nav>
				<?php 
					// Navigation
					echo $nav;
					
					// Search field
					if ( $menu_full_bg_search_field ) :
					
						?>
						<form class="search-form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" enctype="application/x-www-form-urlencoded">
							
							<input id="full-bg-search-inp" type="search" class="search-field" value="<?php echo get_search_query(); ?>" name="s" autocomplete="off" />
							
							<label for="full-bg-search-inp">
							
								<?php
									// Search placeholder
									printf( '%s %s', __( 'Search', 'kalium' ), '<span><i></i><i></i><i></i></span>' );
								?>
								
							</label>
							
						</form>
						<?php
						
					endif; 
				?>
				</nav>
					
			</div>
				
				<?php 
				
				if ( $menu_full_bg_footer_block ) : 
				
				?>
					<div class="full-menu-footer">
						
						<div class="container">
							
							<div class="right-part">
								
								<?php echo do_shortcode( '[lab_social_networks rounded]' ); ?>
								
							</div>
							
							<div class="left-part">
								
								<?php echo do_shortcode( get_data( 'footer_text' ) ); ?>
								
							</div>
							
						</div>
						
					</div>
				<?php 
					
				endif; 
				
				?>
				
			</div>
			
		<?php
		endif;
		// End of: Full Screen Menu Container
		?>

	</div>
	
</header>

<script type="text/javascript">
	var headerOptions = headerOptions || {};
	jQuery.extend( headerOptions, <?php echo kalium()->helpers->safeEncodeJSON( $header_options ); ?> );
</script>
<?php
	
do_action( 'kalium_header_main_heading_title_before' );

// Page heading title
get_template_part( "tpls/page-heading-title" );
