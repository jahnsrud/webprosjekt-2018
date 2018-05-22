<?php
/**
 *	Typolab Tabs
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

$current_page = isset( $_GET['typolab-page'] ) ? $_GET['typolab-page'] : '';
$typolab_active = in_array( $current_page, array( 'settings', 'font-sizes' ) ) ? $current_page : 'main';

$typolab_main_link = admin_url( "admin.php?page={$_GET['page']}" );
$typolab_settings_link = admin_url( "admin.php?page={$_GET['page']}&typolab-page=settings" );
$typolab_font_sizes_link = admin_url( "admin.php?page={$_GET['page']}&typolab-page=font-sizes" );

?>
<h2 class="nav-tab-wrapper">
	<a href="<?php echo $typolab_main_link; ?>" class="nav-tab<?php when_match( 'main' == $typolab_active, 'nav-tab-active' ); ?>">Fonts</a>
	<a href="<?php echo $typolab_font_sizes_link; ?>" class="nav-tab<?php when_match( 'font-sizes' == $typolab_active, 'nav-tab-active' ); ?>">Font Sizes</a>
	<a href="<?php echo $typolab_settings_link; ?>" class="nav-tab<?php when_match( 'settings' == $typolab_active, 'nav-tab-active' ); ?>">Settings</a>
</h2>