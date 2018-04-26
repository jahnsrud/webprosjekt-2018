<?php
/**
 *	Theme Options
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

add_action( 'init','of_options' );

/*
	Advanced Folding Instructions
	
	'afolds' => 1 (element container)
	
	
	'afold' => "option_name:value1,value2,value3" (match any)
	'afold' => 'option_name:checked'
	'afold' => 'option_name:notChecked'
	'afold' => 'option_name:hasMedia'
	'afold' => 'option_name:hasNotMedia'
*/

if ( ! function_exists( 'of_options' ) ) {
	
function of_options( $force = false ) {
	global $pagenow;
	
	// Do not execute on frontend, save some execution time
	if ( ! is_admin() && ! $force ) {
		return;
	}

/*-----------------------------------------------------------------------------------*/
/* The Options Array */
/*-----------------------------------------------------------------------------------*/

// Set the Options Array
global $of_options;

$of_options = array();
$note_str = '<strong>NOTE</strong>:';

$show_sidebar_options = array(
	'hide'     => 'Hide sidebar',
	'right'    => 'Show sidebar on right',
	'left'     => 'Show sidebar on left',
);

$endless_pagination_style = array(
	'_1' => 'Spinning loader',
	'_2' => 'Pulsating loader',
);

$menu_type_skins = array(
	'menu-skin-main'   => 'Default (Primary Theme Color)',
	'menu-skin-dark'   => 'Black (Dark)',
	'menu-skin-light'  => 'White (Light)',
);

$thumbnail_sizes_info = 'Default thumbnail sizes: full, large, medium, thumbnail.';

$lab_social_networks_shortcode = "
<code style='font-size: 10px; display: inline-block; margin-top: 10px;'>[lab_social_networks]</code> – Standard <br>
<code style='font-size: 10px; display: inline-block; margin-top: 10px;'>[lab_social_networks rounded]</code> – Rounded icons <br>
<code style='font-size: 10px; display: inline-block; margin-top: 10px;'>[lab_social_networks colored]</code> – Colored text <br>
<code style='font-size: 10px; display: inline-block; margin-top: 10px;'>[lab_social_networks colored=\"hover\"]</code> – Colored text on hover <br>
<code style='font-size: 10px; display: inline-block; margin-top: 10px;'>[lab_social_networks rounded colored=\"hover\"]</code> – Rounded icons and colored on hover<br>
<code style='font-size: 10px; display: inline-block; margin-top: 10px;'>[lab_social_networks colored-bg]</code> – Colored background <br>
<code style='font-size: 10px; display: inline-block; margin-top: 10px;'>[lab_social_networks colored-bg=\"hover\"]</code> – Colored background on hover<br>
<code style='font-size: 10px; display: inline-block; margin-top: 10px;'>[lab_social_networks colored-bg=\"hover\" rounded]</code> – Rounded &amp; colored background on hover";



/***** LOGO ****/
$of_options[] = array( 	'name' 		=> 'Branding',
						'type' 		=> 'heading',
						'icon'		=> 'fa fa-cube'
				);

$of_options[] = array( 	'type' 		=> 'tabs',
						'id'		=> 'branding-tabs',
						'tabs'		=> array(
							'branding-main'  => 'Branding Settings',
							'branding-other' => 'Other Settings',
						)
				);

$of_options[] = array(  'name'   	=> 'Site Brand',
						'desc'   	=> 'Enter the text that will appear as logo',
						'id'   		=> 'logo_text',
						'std'   	=> get_bloginfo('title'),
						'type'   	=> 'text',
						
						'tab_id'	=> 'branding-main'
					);

$of_options[] = array(
						'desc'   	=> 'Brand logo<br><small>' . $note_str . ' Use your brand logo.</small>',
						'id'   		=> 'use_uploaded_logo',
						'std'   	=> 0,
						'on'  		=> 'Yes',
						'off'  		=> 'No',
						'type'   	=> 'switch',
						'folds'  	=> 1,
						
						'tab_id'	=> 'branding-main',
					);

$of_options[] = array(	'name' 		=> 'Brand Logo',
						'desc' 		=> "Upload your brand logo<br><small>{$note_str} To upload SVG logo, install <a href=\"https://wordpress.org/plugins/svg-support/\" target=\"_blank\">SVG Support plugin</a>. <br>If you don't see this logo in any of your pages, please <a href=\"http://documentation.laborator.co/kb/kalium/page-parameters-options/\" target=\"_blank\">read this article</a> to learn more.</small>",
						'id' 		=> 'custom_logo_image',
						'std' 		=> "",
						'type' 		=> 'media',
						'mod' 		=> 'min',
						'fold' 		=> 'use_uploaded_logo',
						'afolds'	=> true,
						
						'tab_id'	=> 'branding-main'
					);

$of_options[] = array( 	'desc' 		=> 'Set maximum width for uploaded logo (Optional)<br><small>' . $note_str . ' If set empty, original logo width will be applied.</small>',
						'id' 		=> 'custom_logo_max_width',
						'std' 		=> "",
						'plc'		=> 'Logo Width',
						'type' 		=> 'text',
						'numeric'	=> true,
						'fold' 		=> 'use_uploaded_logo',
						'afold'		=> 'custom_logo_image:hasMedia',
						'postfix'	=> 'px',
						
						'tab_id'	=> 'branding-main'
				);

$of_options[] = array( 	'desc' 		=> 'Set maximum width for uploaded logo in mobile devices (Optional)<br><small>' . $note_str . ' If set empty, it will inherit maximum logo height from the above field.</small>',
						'id' 		=> 'custom_logo_mobile_max_width',
						'std' 		=> "",
						'plc'		=> 'Mobile Logo Width',
						'type' 		=> 'text',
						'numeric'	=> true,
						'fold' 		=> 'use_uploaded_logo',
						'afold'		=> 'custom_logo_image:hasMedia',
						'postfix'	=> 'px',
						
						'tab_id'	=> 'branding-main'
				);

if ( has_site_icon() ) {
$icon_32 = get_site_icon_url( 32 );

$of_options[] = array( 	'name' 		=> 'Favicon Set',
						'desc' 		=> "",
						'id' 		=> 'favicon_set',
						'std' 		=> "
						<p>
							Site icon is already set on <strong>Theme Customizer</strong> &gt; <strong>Site Identity</strong> &gt; <strong>Site Icon</strong> section. Site icon:
						</p>
						
						<a href=" . admin_url( "customize.php" ). " class=\"tc-site-icon\"><img src='{$icon_32}'></a>
						<a href="  . admin_url( 'customize.php' ) . " class=\"tc-link\"><span class='dashicons dashicons-admin-customizer'></span> Theme Customizer</a>
						",
						'icon' 		=> true,
						'type' 		=> 'info',
						
						'tab_id'	=> 'branding-other'
					);
}


$of_options[] = array(	'name' 		=> 'Favicon',
						'desc' 		=> 'Select 64x64 favicon of the PNG format',
						'id' 		=> 'favicon_image',
						'std' 		=> "",
						'type' 		=> 'media',
						'mod' 		=> 'min',
						
						'tab_id'	=> 'branding-other'
					);


$of_options[] = array(	'name' 		=> 'Apple Touch Icon',
						'desc' 		=> 'Required image size 114x114 (PNG format)',
						'id' 		=> 'apple_touch_icon',
						'std' 		=> "",
						'type' 		=> 'media',
						'mod' 		=> 'min',
						
						'tab_id'	=> 'branding-other'
					);

$of_options[] = array(	'name'		=> 'Google Theme Color',
						'desc'   	=> "Applied only on mobile devices, <a href=\"http://updates.html5rocks.com/2014/11/Support-for-theme-color-in-Chrome-39-for-Android\" target=\"_blank\">click here</a> to learn more about this",
						'id'   		=> 'google_theme_color',
						'std'   	=> '',
						'type'   	=> 'color',
						
						'tab_id'	=> 'branding-other'
					);
/***** END OF: LOGO ****/

$of_options[] = array( 	'name' 		=> 'Header and Menu',
						'type' 		=> 'heading',
						'icon'		=> 'fa fa-header'
				);

$of_options[] = array( 	'type' 		=> 'tabs',
						'id'		=> 'header-and-menu-tabs',
						'tabs'		=> array(
							'header-settings-menu'           => 'Menu Settings',
							'header-settings-sticky-header'  => 'Sticky Header',
							'header-settings-position'       => 'Header Settings',
							'header-settings-mobile'       	 => 'Mobile Menu',
							'header-settings-other'          => 'Other Settings',
						)
				);

$of_options[] = array( 	'name'		=> 'Header Position',
						'desc' 		=> 'Header Position (logo and menu container)',
						'id' 		=> 'header_position',
						'std' 		=> 'static',
						'options'	=> array(
							'static' => 'Content Below (Static)',
							'absolute' => 'Over the Content (Absolute)',
						),
						'type' 		=> 'select',
						
						'afolds'	=> true,
						
						'tab_id'	=> 'header-settings-position'
				);

$of_options[] = array( 	'desc' 		=> "Header Spacing<br><small>{$note_str} If Header Position is set to absolute this setting will take effect.</small>",
						'id' 		=> 'header_spacing',
						'std' 		=> "",
						'plc'		=> 'Default is: 0',
						'type' 		=> 'text',
						'numeric'	=> true,
						'postfix'	=> 'px',
						
						'afold'		=> 'header_position:absolute',
						
						'tab_id'	=> 'header-settings-position'
				);

$of_options[] = array( 	'name'		=> 'Header Vertical Padding',
						'desc' 		=> "Set custom top padding for the header (Optional)",
						'id' 		=> 'header_vpadding_top',
						'std' 		=> "",
						'plc'		=> 'Default is: 50',
						'type' 		=> 'text',
						'numeric'	=> true,
						'postfix'	=> 'px',
						
						'tab_id'	=> 'header-settings-position'
				);

$of_options[] = array( 	'desc' 		=> "Set custom bottom padding for the header (Optional)",
						'id' 		=> 'header_vpadding_bottom',
						'std' 		=> "",
						'plc'		=> 'Default is: 50',
						'type' 		=> 'text',
						'numeric'	=> true,
						'postfix'	=> 'px',
						
						'tab_id'	=> 'header-settings-position'
				);

$of_options[] = array( 	'name' 		=> 'Full-width Header',
						'desc' 		=> "Extend header container to the browser edge",
						'id' 		=> 'header_fullwidth',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						'afold'		=> '',
						
						'tab_id'	=> 'header-settings-position',
				);
				
				

$of_options[] = array( 	'name' 		=> 'Custom Hamburger Menu Label',
						'desc' 		=> "Instead of three horizontal bars you can replace icon with text or both.",
						'id' 		=> 'menu_hamburger_custom_label',
						'std' 		=> 0,
						'on' 		=> 'Enable',
						'off' 		=> 'Disable',
						'type' 		=> 'switch',
						'folds'		=> 1,
						
						'tab_id'	=> 'header-settings-other',
				);

$of_options[] = array(	'desc' 		=> "Display text<br><small>{$note_str} This text is used as &quot;show menu&quot; indicator.</small>",
						'id' 		=> 'menu_hamburger_custom_label_text',
						'std'		=> 'MENU',
						'type' 		=> 'text',
						'fold'		=> 'menu_hamburger_custom_label',
						
						'tab_id'	=> 'header-settings-other'
				);

$of_options[] = array(	'desc' 		=> "Close text<br><small>{$note_str} This text is used as &quot;hide menu&quot; indicator.</small>",
						'id' 		=> 'menu_hamburger_custom_label_close_text',
						'std'		=> 'CLOSE',
						'type' 		=> 'text',
						'fold'		=> 'menu_hamburger_custom_label',
						
						'tab_id'	=> 'header-settings-other'
				);
				
$of_options[] = array( 	'desc' 		=> 'Hamburger menu icon visibility',
						'id' 		=> 'menu_hamburger_custom_icon_position',
						'std' 		=> 'hide',
						'type' 		=> 'select',
						'options'	=> array(
							'hide'   => 'Hide Icon',
							'left'   => 'Left',
							'right'  => 'Right',
						),
						'fold'		=> 'menu_hamburger_custom_label',
						
						'tab_id'	=> 'header-settings-other'
				);

$of_options[] = array( 	'name' 		=> 'Search Field',
						'desc' 		=> 'Show or hide search field main header menu',
						'id' 		=> 'header_search_field',
						'std' 		=> 0,
						'on' 		=> 'Show',
						'off' 		=> 'Hide',
						'type' 		=> 'switch',
						
						'folds'		=> true,
						
						'tab_id'	=> 'header-settings-other'
				);

$of_options[] = array( 	'desc' 		=> 'Search field icon animation type',
						'id' 		=> 'header_search_field_icon_animation',
						'std'		=> 'scale',
						'type' 		=> 'select',
						
						'options'	=> array(
							'shift'  => 'Shift search loop on the left',
							'scale'  => 'Scale to a smaller search loop',
							'none'   => 'None',
							
						),
						
						'fold'		=> 'header_search_field',
						
						'tab_id'	=> 'header-settings-other'
				);

if ( function_exists( 'icl_object_id' ) ) {
	$of_options[] = array( 	'name' 		=> 'WPML Language Switcher',
							'desc' 		=> 'Show or hide language switcher next to menu bars button',
							'id' 		=> 'header_wpml_language_switcher',
							'std' 		=> 0,
							'on' 		=> 'Show',
							'off' 		=> 'Hide',
							'type' 		=> 'switch',
							
							'folds'		=> true,
							
							'tab_id'	=> 'header-settings-other'
					);

$of_options[] = array( 	'desc' 		=> 'Language switcher trigger',
						'id' 		=> 'header_wpml_language_trigger',
						'std'		=> 'hover',
						'type' 		=> 'select',
						
						'options'	=> array(
							'hover' => 'Show on hover',
							'click'	=> 'Show on click',
							
						),
						
						'fold'		=> 'header_wpml_language_switcher',
						
						'tab_id'	=> 'header-settings-other'
				);

$of_options[] = array( 	'desc' 		=> 'Flag position in language switcher',
						'id' 		=> 'header_wpml_language_flag_position',
						'std'		=> 'left',
						'type' 		=> 'select',
						
						'options'	=> array(
							'left'   => 'Left',
							'right'  => 'Right',
							'hide'   => 'Hide',
							
						),
						
						'fold'		=> 'header_wpml_language_switcher',
						
						'tab_id'	=> 'header-settings-other'
				);

$of_options[] = array( 	'desc' 		=> 'Text display type',
						'id' 		=> 'header_wpml_language_switcher_text_display_type',
						'std'		=> 'flag-name',
						'type' 		=> 'select',
						
						'options'	=> array(
							'name'            => 'Native name',
							'translated'      => 'Translated name',
							'initials'        => 'Initials',
							'name-translated' => 'Native name + (translated name)',
							'translated-name' => 'Translated name + (native name)',
							'hide'			  => 'Hide text'
							
						),
						
						'fold'		=> 'header_wpml_language_switcher',
						
						'tab_id'	=> 'header-settings-other'
				);
}

$of_options[] = array( 	'name' 		=> 'Mobile Menu Type',
						'desc' 		=> 'Select mobile menu type for mobile devices only',
						'id' 		=> 'menu_mobile_type',
						'std' 		=> 1,
						'on' 		=> 'Slide from left',
						'off' 		=> 'Fullscreen',
						'type' 		=> 'switch',
						
						'tab_id'	=> 'header-settings-mobile'
				);

$of_options[] = array( 	'name' 		=> 'Search Field on Mobile Menu',
						'desc' 		=> 'Show or hide search field on mobile menu',
						'id' 		=> 'menu_mobile_search_field',
						'std' 		=> 1,
						'on' 		=> 'Show',
						'off' 		=> 'Hide',
						'type' 		=> 'switch',
						
						'tab_id'	=> 'header-settings-mobile'
				);

/*
$of_options[] = array( 	'name' 		=> 'WPML Language Switcher',
						'desc' 		=> 'Show or hide WPML language switcher on mobile menu<br><small>' . $note_str . ' Language switcher will be shown only if WPML plugin is installed and active.</small>',
						'id' 		=> 'menu_mobile_wpml_switcher',
						'std' 		=> 1,
						'on' 		=> 'Show',
						'off' 		=> 'Hide',
						'type' 		=> 'switch',
						
						'tab_id'	=> 'header-settings-mobile'
				);
*/

$of_options[] = array(	'name'		=> 'Mobile Menu Breakpoint',
						'desc' 		=> "Maximum width when mobile menu is activated",
						'id' 		=> 'menu_mobile_breakpoint',
						'plc' 		=> 'Leave empty to use default breakpoint',
						'std'		=> '',
						'type' 		=> 'text',
						'numeric'	=> true,
						'postfix'	=> 'px',
						
						'tab_id'	=> 'header-settings-mobile',
				);

$of_options[] = array( 	'name'		=> 'Main Menu Type',
						'desc' 		=> 'Set default menu style as general site navigation.',
						'id' 		=> 'main_menu_type',
						'std' 		=> 'full-bg-menu',
						'options'	=> array(							
							'full-bg-menu'   => kalium()->assetsUrl( 'images/admin/menu-full-bg.png' ),
							'standard-menu'  => kalium()->assetsUrl( 'images/admin/menu-standard.png' ),
							'top-menu'       => kalium()->assetsUrl( 'images/admin/menu-top.png' ),
							'sidebar-menu'   => kalium()->assetsUrl( 'images/admin/menu-sidebar.png' ),
						),
						'type' 		=> 'images',
						'descrs'	=> array(
							'full-bg-menu'   => 'Full Background Menu',
							'standard-menu'  => 'Standard Menu',
							'top-menu'       => 'Top Menu',
							'sidebar-menu'   => 'Sidebar Menu',
						),
						'afolds'	=> true,
						
						'tab_id'	=> 'header-settings-menu'
				);

$of_options[] = array( 	'name' 		=> 'Full Background Menu Settings',
						'desc' 		=> 'Search field after the last menu item',
						'id' 		=> 'menu_full_bg_search_field',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'afold'		=> 'main_menu_type:full-bg-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);

$of_options[] = array( 	'desc' 		=> 'Show copyrights and social networks (bottom)',
						'id' 		=> 'menu_full_bg_footer_block',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'afold'		=> 'main_menu_type:full-bg-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);

$of_options[] = array( 	'desc' 		=> 'Translucent menu background (apply opacity)',
						'id' 		=> 'menu_full_bg_opacity',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'afold'		=> 'main_menu_type:full-bg-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);

$of_options[] = array( 	'desc' 		=> 'Menu alignment (when toggled)',
						'id' 		=> 'menu_full_bg_alignment',
						'std' 		=> '',
						'type' 		=> 'select',
						'options'	=> array(
							'left'                   => 'Left',
							'centered'               => 'Centered',
							'centered-horizontal'    => 'Centered (Horizontal)',
						),
						
						'afold'		=> 'main_menu_type:full-bg-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);

$of_options[] = array( 	'desc' 		=> 'Select color palette for this menu type',
						'id' 		=> 'menu_full_bg_skin',
						'std' 		=> '',
						'type' 		=> 'select',
						'options'	=> $menu_type_skins,
						'afold'		=> 'main_menu_type:full-bg-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);


$of_options[] = array( 	'name' 		=> 'Standard Menu Settings',
						'desc' 		=> "Show menu links only when clicking <strong>menu bar</strong>",
						'id' 		=> 'menu_standard_menu_bar_visible',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'folds'		=> true,
						'afold'		=> 'main_menu_type:standard-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);

$of_options[] = array( 	'desc' 		=> "Reveal effect on <strong>menu bar</strong> click",
						'id' 		=> 'menu_standard_menu_bar_effect',
						'std' 		=> '',
						'type' 		=> 'select',
						'options'	=> array(
							'reveal-from-top'    => 'Slide from Top',
							'reveal-from-right'  => 'Slide from Right',
							'reveal-from-left'   => 'Slide from Left',
							'reveal-from-bottom' => 'Slide from Bottom',
							'reveal-fade'        => 'Fade Only',
						),
						'fold'		=> 'menu_standard_menu_bar_visible',
						'afold'		=> 'main_menu_type:standard-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);

$of_options[] = array( 	'desc' 		=> 'Select color palette for this menu type',
						'id' 		=> 'menu_standard_skin',
						'std' 		=> '',
						'type' 		=> 'select',
						'options'	=> $menu_type_skins,
						'afold'		=> 'main_menu_type:standard-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);


$of_options[] = array( 	'name' 		=> 'Top Menu Settings',
						'desc' 		=> "Show top menu widgets (<a href=\"".admin_url('widgets.php')."\">manage widgets here</a>)",
						'id' 		=> 'menu_top_show_widgets',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'folds'		=> true,
						'afold'		=> 'main_menu_type:top-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);

$of_options[] = array( 	'desc' 		=> 'Center menu items links (first level only)',
						'id' 		=> 'menu_top_nav_links_center',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						'folds'		=> true,
						'afold'		=> 'main_menu_type:top-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);

$menus_list = array(
	'default'  => '- Main Menu (Default) -'
);

if(is_admin())
{
	$nav_menus = wp_get_nav_menus();
	
	foreach($nav_menus as $item)
	{
		$menus_list["menu-{$item->term_id}"] = $item->name;
	}
}

$of_options[] = array( 	'desc' 		=> 'Select menu to use for top menu',
						'id' 		=> 'menu_top_menu_id',
						'std' 		=> 'default',
						'type' 		=> 'select',
						'options'	=> array_merge($menus_list, array('-' => '(Show no menu)')),
						'afold'		=> 'main_menu_type:top-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);

$of_options[] = array( 	'desc' 		=> 'Menu items per row (applied to root level only)',
						'id' 		=> 'menu_top_items_per_row',
						'std' 		=> 'items-3',
						'type' 		=> 'select',
						'options'	=> array(
							'items-1'  => '1 Menu Item per Row',
							'items-2'  => '2 Menu Items per Row',
							'items-3'  => '3 Menu Items per Row',
							'items-4'  => '4 Menu Items per Row',
							'items-5'  => '5 Menu Items per Row',
							'items-6'  => '6 Menu Items per Row',
							'items-7'  => '7 Menu Items per Row',
							'items-8'  => '8 Menu Items per Row',
						),
						'afold'		=> 'main_menu_type:top-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);

$of_options[] = array( 	'desc' 		=> 'Widgets container width',
						'id' 		=> 'menu_top_widgets_container_width',
						'std' 		=> 'col-6',
						'type' 		=> 'select',
						'options'	=> array(
							'col-3' => '25% of row width',
							'col-4' => '33% of row width',
							'col-5' => '40% of row width',
							'col-6' => '50% of row width',
							'col-7' => '60% of row width',
							'col-8' => '65% of row width',
						),
						'fold'		=> 'menu_top_show_widgets',
						'afold'		=> 'main_menu_type:top-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);

$of_options[] = array( 	'desc' 		=> 'Set number of widgets per row for top menu',
						'id' 		=> 'menu_top_widgets_per_row',
						'std' 		=> '',
						'type' 		=> 'select',
						'options'	=> array(
							'six'    => '2 Widgets per Row',
							'four'   => '3 Widgets per Row',
							'three'  => '4 Widgets per Row',
						),
						'fold'		=> 'menu_top_show_widgets',
						'afold'		=> 'main_menu_type:top-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);

$of_options[] = array( 	'desc' 		=> 'Select color palette for this menu type',
						'id' 		=> 'menu_top_skin',
						'std' 		=> '',
						'type' 		=> 'select',
						'options'	=> $menu_type_skins,
						'afold'		=> 'main_menu_type:top-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);

$of_options[] = array( 	'desc' 		=> "Force top menu include in header
										<br>
										<small class=\"nowrap\">
											When you are not using Top Menu as main menu you can alternatively include it by enabling this option
											<br>
											Enable this option when you separately want to show this menu type by clicking an element with <strong>.top-menu-toggle</strong> class.
										</small>",
						'id' 		=> 'menu_top_force_include',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						'folds'		=> true,
						'afold'		=> 'main_menu_type:top-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);


$of_options[] = array( 	'name' 		=> 'Sidebar Menu Settings',
						'desc' 		=> "Show sidebar menu widgets (<a href=\"" . admin_url('widgets.php') . "\">manage widgets here</a>)",
						'id' 		=> 'menu_sidebar_show_widgets',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'afold'		=> 'main_menu_type:sidebar-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);


$of_options[] = array( 	'desc' 		=> 'Select primary menu to use for sidebar',
						'id' 		=> 'menu_sidebar_menu_id',
						'std' 		=> 'default',
						'type' 		=> 'select',
						'options'	=> array_merge($menus_list, array('-' => '(Show no menu)')),
						'afold'		=> 'main_menu_type:sidebar-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);

$of_options[] = array( 	'desc' 		=> 'Sidebar alignment in browser viewport',
						'id' 		=> 'menu_sidebar_alignment',
						'std' 		=> 'right',
						'type' 		=> 'select',
						'options'	=> array(
							'left'   => 'Left',
							'right'  => 'Right',
						),
						'fold'		=> 'menu_top_show_widgets',
						'afold'		=> 'main_menu_type:sidebar-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);

$of_options[] = array( 	'desc' 		=> 'Select color palette for this menu type',
						'id' 		=> 'menu_sidebar_skin',
						'std' 		=> '',
						'type' 		=> 'select',
						'options'	=> $menu_type_skins,
						'afold'		=> 'main_menu_type:sidebar-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);

$of_options[] = array( 	'desc' 		=> "Force sidebar menu include in header
										<br>
										<small class=\"nowrap\">
											When you are not using Sidebar Menu as main menu you can alternatively include it by enabling this option
											<br>
											Enable this option when you separately want to show this menu type by clicking an element with <strong>.sidebar-menu-toggle</strong> class.
										</small>",
						'id' 		=> 'menu_sidebar_force_include',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						'folds'		=> true,
						'afold'		=> 'main_menu_type:sidebar-menu',
						
						'tab_id'	=> 'header-settings-menu',
				);


$of_options[] = array( 	'name'		=> 'Miscellaneous',
						'desc' 		=> "Show dropdown caret for items that have submenu items",
						'id' 		=> 'submenu_dropdown_indicator',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'header-settings-menu',
				);

$of_options[] = array( 	'name' 		=> 'Sticky Header',
						'desc' 		=> 'Enable or disable sticky header entirely',
						'id' 		=> 'sticky_header',
						'std' 		=> 0,
						'on' 		=> 'Enable',
						'off' 		=> 'Disable',
						'type' 		=> 'switch',
						'afolds'	=> 1,
						
						'tab_id'	=> 'header-settings-sticky-header',
				);
				
// ! Sticky Header : Styling Options
$of_options[] = array( 	'name'		=> 'Styling Options',
						'desc' 		=> 'Apply background color when sticky header is active',
						'id' 		=> 'sticky_header_background_color',
						'std' 		=> '',
						'type' 		=> 'color',
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array(	'desc' 		=> "Vertical padding when sticky header is active<br /><small><span class=\"note\">NOTE:</span> Padding above and below the header logo/menu.</small>",
						'id' 		=> 'sticky_header_vertical_padding',
						'plc' 		=> 'Leave empty if you don\'t want to change the size',
						'std'		=> '10',
						'type' 		=> 'text',
						'numeric'	=> true,
						'postfix'	=> 'px',
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array( 	'desc' 		=> 'Select menu skin to use when sticky menu is active',
						'id' 		=> 'sticky_header_skin',
						'std' 		=> '',
						'type' 		=> 'select',
						'options'	=> array_merge( array( '' => 'Do not change menu skin' ), $menu_type_skins ),
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array(	'desc'   	=> "Bottom Border and Shadow<br><small>{$note_str} Apply bottom border and/or shadow for sticky menu.</small>",
						'id'   		=> 'sticky_header_border',
						'std'   	=> 0,
						'on' 		=> 'Yes',
						'off' 		=> 'No',
						'type'   	=> 'switch',
						'folds'		=> true,
						
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
					);

$width_in_pixels = array();
$blur_in_pixels = array();

for ( $i = 0; $i <= 30; $i++ ) {
	$width_in_pixels[] = $i . 'px';
}

for ( $i = 0; $i <= 60; $i++ ) {
	$blur_in_pixels[] = $i . 'px';
}

$border_apply_when_options = array(
	'always'           => 'Always',
	'sticky-active'    => 'Only when sticky menu is active',
	'sticky-inactive'  => 'Only when sticky menu is not active',
);

$of_options[] = array( 	'name'		=> 'Border',
						'desc' 		=> 'Border color (optional)',
						'id' 		=> 'sticky_header_border_color',
						'std' 		=> '',
						'type' 		=> 'color',
						'fold' 		=> 'sticky_header_border',
						
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array( 	'desc' 		=> 'Border width',
						'id' 		=> 'sticky_header_border_width',
						'std' 		=> '1px',
						'type' 		=> 'select',
						'options'	=> $width_in_pixels,
						'fold' 		=> 'sticky_header_border',
						
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array( 	'name'		=> 'Shadow',
						'desc' 		=> 'Shadow color (optional)',
						'id' 		=> 'sticky_header_shadow_color',
						'std' 		=> '',
						'type' 		=> 'color',
						'fold' 		=> 'sticky_header_border',
						
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array( 	'desc' 		=> 'Shadow width',
						'id' 		=> 'sticky_header_shadow_width',
						'std' 		=> '',
						'type' 		=> 'select',
						'options'	=> $width_in_pixels,
						'fold' 		=> 'sticky_header_border',
						
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array( 	'desc' 		=> 'Blur radius',
						'id' 		=> 'sticky_header_shadow_blur',
						'type' 		=> 'select',
						'options'	=> $blur_in_pixels,
						'fold' 		=> 'sticky_header_border',
						
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

// ! Sticky Header : Autohide Settings
$of_options[] = array(	'name'		=> 'Autohide Sticky',
						'desc' 		=> "Enable or disable auto hide sticky header.<br><small>{$note_str} Only show sticky header when users scrolls upside.</small>",
						'id'   		=> 'sticky_header_autohide',
						'std'   	=> 0,
						'on' 		=> 'Yes',
						'off' 		=> 'No',
						'type'   	=> 'switch',
						'folds'  	=> 1,
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array(	'desc' 		=> "Show/hide animate duration in seconds",
						'id' 		=> 'sticky_header_autohide_duration',
						'plc' 		=> '0.3',
						'std'		=> '',
						'type' 		=> 'text',
						'numeric'	=> true,
						'postfix'	=> 's',
						
						'fold'		=> 'sticky_header_autohide',
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);
				
$of_options[] = array(	'desc' 		=> "Animation type",
						'id' 		=> 'sticky_header_autohide_animation_type',
						'std'		=> 'Sine',
						'type' 		=> 'select',
						
						'options'	=> array(
							'fade'               => 'Fade only',
							'fade-slide-top'     => 'Fade and slide from top',
							'fade-slide-bottom'  => 'Fade and slide from bottom',
						),
						
						'fold'		=> 'sticky_header_autohide',
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$greensock_easings = array( 'Back', 'Bounce', 'Circ', 'Cubic', 'Elastic', 'Expo', 'Linear', 'Power0', 'Power1', 'Power2', 'Power3', 'Power4', 'Quad', 'Quart', 'Quint', 'Sine', 'Strong', 'SlowMo' );

$greensock_ease_types = array(
	'easeInOut'  => 'Ease-in-out',
	'easeIn'     => 'Ease-in',
	'easeOut'    => 'Ease-out',
);

$of_options[] = array(	'desc' 		=> "Animate easing",
						'id' 		=> 'sticky_header_autohide_easing',
						'std'		=> 'Sine',
						'type' 		=> 'select',
						
						'options'	=> $greensock_easings,
						
						'fold'		=> 'sticky_header_autohide',
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);
				
$of_options[] = array(	'desc' 		=> "Ease type",
						'id' 		=> 'sticky_header_autohide_easing_type',
						'std'		=> 'easeInOut',
						'type' 		=> 'select',
						
						'options'	=> $greensock_ease_types,
						
						'fold'		=> 'sticky_header_autohide',
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

// ! Sticky Header : Logo Settings
$of_options[] = array(	'name'		=> 'Logo Settings',
						'desc' 		=> "Logo width in sticky mode.<br><small>{$note_str} Custom logo width when sticky header is active.</small>",
						'id' 		=> 'sticky_header_logo_width',
						'plc' 		=> 'Default logo width',
						'std'		=> '',
						'type' 		=> 'text',
						'numeric'	=> true,
						'postfix'	=> 'px',
						
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array(	#'name'		=> 'Upload Logo',
						'desc' 		=> "Use custom logo when sticky header is active",
						'id' 		=> 'sticky_header_logo',
						'std' 		=> "",
						'type' 		=> 'media',
						'mod' 		=> 'min',
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
					);

// ! Sticky Header : Other Settings
$of_options[] = array(	'name'		=> 'Other Settings',
						'desc' 		=> "Animate sticky header with scroll duration<br><small>{$note_str} If enabled, sticky header styles will be animated with scroll wheel.</small>",
						'id'   		=> 'sticky_header_animate_duration',
						'std'   	=> 0,
						'on' 		=> 'No',
						'off' 		=> 'Yes',
						'type'   	=> 'switch',
						'folds'  	=> 1,
						'afold' 	=> 'sticky_header:checked',
						
						'reverse'	=> true, // Reverse switch
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array(	'desc' 		=> "Animate duration in seconds<br><small><small>NOTE</small>: Applied for header style when sticky switches back and forth.</small>",
						'id' 		=> 'header_sticky_duration',
						'plc' 		=> '0.3',
						'std'		=> '',
						'type' 		=> 'text',
						'numeric'	=> true,
						'postfix'	=> 's',
						
						'fold'		=> 'sticky_header_animate_duration',
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array(	'desc' 		=> "Initial offset<br><small>{$note_str} Scroll position when sticky header styles begin animation.</small>",
						'id' 		=> 'header_sticky_initial_offset',
						'plc' 		=> '10',
						'std'		=> '10',
						'type' 		=> 'text',
						'numeric'	=> true,
						'postfix'	=> 'px',
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);
				
				
$of_options[] = array(	'desc' 		=> "Animation chaining<br><small>{$note_str} Set order of animations for header style when sticky switches back and forth.</small>",
						'id' 		=> 'sticky_header_animation_chaining',
						'std'		=> 'all',
						'type' 		=> 'select',
						
						'options'	=> array(
							'all' => 'All at once',
							'padding-bg_logo' => 'Padding -> Background, Logo',
							'bg_logo-padding' => 'Background, Logo -> Padding',
							
							'logo_padding-bg' => 'Logo, Padding -> Background',
							'bg-logo_padding' => 'Background -> Logo, Padding',
							
							'padding-bg-logo' => 'Padding -> Background -> Logo',
							'bg-logo-padding' => 'Background -> Logo -> Padding',
							'logo-bg-padding' => 'Logo -> Background -> Padding',
							
						),
						
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);
				
// ! Sticky Header : Responsive Settings
$of_options[] = array(	'name'		=> 'Responsive Settings',
						'desc'   	=> "Sticky header in desktop screens",
						'id'   		=> 'sticky_header_support_desktop',
						'std'   	=> 1,
						'on' 		=> 'Enable',
						'off' 		=> 'Disable',
						'type'   	=> 'switch',
						'folds'  	=> 1,
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
					);
					
$of_options[] = array(	'desc'   	=> "Sticky header in tablet screens",
						'id'   		=> 'sticky_header_support_tablet',
						'std'   	=> 1,
						'on' 		=> 'Enable',
						'off' 		=> 'Disable',
						'type'   	=> 'switch',
						'folds'  	=> 1,
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
					);
					
$of_options[] = array(	'desc'   	=> "Sticky header in mobile screens",
						'id'   		=> 'sticky_header_support_mobile',
						'std'   	=> 1,
						'on' 		=> 'Enable',
						'off' 		=> 'Disable',
						'type'   	=> 'switch',
						'folds'  	=> 1,
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
					);

/*$of_options[] = array(	'desc'   	=> "Sticky menu on Mobile Mode<br><small>{$note_str} Enable or disable sticky menu on mobile.</small>",
						'id'   		=> 'header_sticky_mobile',
						'std'   	=> 1,
						'on' 		=> 'Yes',
						'off' 		=> 'No',
						'type'   	=> 'switch',
						'folds'  	=> 1,
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
					);

$of_options[] = array(	'desc'   	=> "Auto-Hide Mode<br><small>{$note_str} Only show sticky menu when users scrolls upside.</small>",
						'id'   		=> 'header_sticky_autohide',
						'std'   	=> 0,
						'on' 		=> 'Yes',
						'off' 		=> 'No',
						'type'   	=> 'switch',
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
					);

$of_options[] = array( 	'name'		=> 'Styling Options',
						'desc' 		=> 'You can apply background color when sticky menu is active',
						'id' 		=> 'header_sticky_bg',
						'std' 		=> '',
						'type' 		=> 'color',
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array(	'desc' 		=> "Vertical padding when sticky is active<br /><small>{$note_str} Padding above and below the header logo/menu.</small>",
						'id' 		=> 'header_sticky_vpadding',
						'plc' 		=> 'Leave empty if you don\'t want to change the size',
						'std'		=> '10',
						'type' 		=> 'text',
						'numeric'	=> true,
						'postfix'	=> 'px',
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array( 	'desc' 		=> 'Select menu skin to use when sticky menu is active',
						'id' 		=> 'sticky_header_skin',
						'std' 		=> '',
						'type' 		=> 'select',
						'options'	=> array_merge(array('' => 'Use Default'), $menu_type_skins),
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array(	'desc'   	=> "Bottom Border and Shadow<br><small>{$note_str} Apply bottom border and/or shadow for sticky menu.</small>",
						'id'   		=> 'sticky_header_border',
						'std'   	=> 0,
						'on' 		=> 'Yes',
						'off' 		=> 'No',
						'type'   	=> 'switch',
						'folds'		=> true,
						
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
					);

$width_in_pixels = array();
$blur_in_pixels = array();

for ( $i = 0; $i <= 30; $i++ ) {
	$width_in_pixels[] = $i . 'px';
}

for ( $i = 0; $i <= 60; $i++ ) {
	$blur_in_pixels[] = $i . 'px';
}

$border_apply_when_options = array(
	'always'           => 'Always',
	'sticky-active'    => 'Only when sticky menu is active',
	'sticky-inactive'  => 'Only when sticky menu is not active',
);

$of_options[] = array( 	'name'		=> 'Border',
						'desc' 		=> 'Border color (optional)',
						'id' 		=> 'sticky_header_border_color',
						'std' 		=> '',
						'type' 		=> 'color',
						'fold' 		=> 'sticky_header_border',
						
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array( 	'desc' 		=> 'Border width',
						'id' 		=> 'sticky_header_border_width',
						'std' 		=> '1px',
						'type' 		=> 'select',
						'options'	=> $width_in_pixels,
						'fold' 		=> 'sticky_header_border',
						
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array( 	'desc' 		=> 'When to apply the border',
						'id' 		=> 'sticky_header_border_apply_when',
						'std' 		=> 'sticky-active',
						'type' 		=> 'select',
						'options'	=> $border_apply_when_options,
						'fold' 		=> 'sticky_header_border',
						
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array( 	'name'		=> 'Shadow',
						'desc' 		=> 'Shadow color (optional)',
						'id' 		=> 'sticky_header_shadow_color',
						'std' 		=> '',
						'type' 		=> 'color',
						'fold' 		=> 'sticky_header_border',
						
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array( 	'desc' 		=> 'Shadow width',
						'id' 		=> 'sticky_header_shadow_width',
						'std' 		=> '',
						'type' 		=> 'select',
						'options'	=> $width_in_pixels,
						'fold' 		=> 'sticky_header_border',
						
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array( 	'desc' 		=> 'Blur radius',
						'id' 		=> 'sticky_header_shadow_blur',
						'type' 		=> 'select',
						'options'	=> $blur_in_pixels,
						'fold' 		=> 'sticky_header_border',
						
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array( 	'desc' 		=> 'When to apply the border',
						'id' 		=> 'sticky_header_shadow_apply_when',
						'std' 		=> 'sticky-active',
						'type' 		=> 'select',
						'options'	=> $border_apply_when_options,
						'fold' 		=> 'sticky_header_border',
						
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);

$of_options[] = array(	'name'		=> 'Custom Logo',
						'desc'   	=> 'Switch to custom logo when sticky menu is active (optional)',
						'id'   		=> 'header_sticky_custom_logo',
						'std'   	=> 0,
						'on'  		=> 'Yes',
						'off'  		=> 'No',
						'type'   	=> 'switch',
						'folds'  	=> 1,
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
					);

$of_options[] = array(	#'name'		=> 'Upload Logo',
						'desc' 		=> "Upload/choose your custom logo image for sticky menu",
						'id' 		=> 'header_sticky_logo_image_id',
						'std' 		=> "",
						'type' 		=> 'media',
						'mod' 		=> 'min',
						'fold' 		=> 'header_sticky_custom_logo',
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
					);

$of_options[] = array( 	'desc' 		=> "Set maximum width for the uploaded logo, mostly used when you use retina (@2x) logo<br /><small>{$note_str} Even if you don't upload custom logo, you may set custom width for the current logo in sticky menu.</small>",
						'id' 		=> 'header_sticky_logo_width',
						'std' 		=> "",
						'plc'		=> 'Custom Logo Width for Sticky Menu',
						'type' 		=> 'text',
						'postfix'	=> 'px',
						'numeric'	=> true,
						//'fold' 		=> 'header_sticky_custom_logo',
						'afold' 	=> 'sticky_header:checked',
						
						'tab_id'	=> 'header-settings-sticky-header',
				);
	*/

$of_options[] = array( 	'name' 		=> 'Footer',
						'type' 		=> 'heading',
						'icon'		=> 'fa fa-sort-amount-asc'
				);

$of_options[] = array( 	'type' 		=> 'tabs',
						'id'		=> 'footer-tabs',
						'tabs'		=> array(
							'footer-general'     => 'General Settings',
							'footer-widgets'     => 'Footer Widgets',
							'footer-custom-js'   => 'JavaScript &amp; Tracking Code',
						)
				);

$of_options[] = array( 	'name' 		=> 'Footer Visibility',
						'desc' 		=> 'Show or hide footer globally',
						'id' 		=> 'footer_visibility',
						'std' 		=> 1,
						'on' 		=> 'Show',
						'off' 		=> 'Hide',
						'type' 		=> 'switch',
						
						'afolds'	=> true,
						
						'tab_id'	=> 'footer-general',
				);

$of_options[] = array( 	'desc' 		=> 'Set the background color. Leave empty for transparent background',
						'id' 		=> 'footer_bg',
						'std' 		=> '#eeeeee',
						'type' 		=> 'color',
						'fold'		=> 'footer_bg_transparent',
						
						'afold'		=> 'footer_visibility:checked',
						
						'tab_id'	=> 'footer-general',
				);

$of_options[] = array( 	'name' 		=> 'Footer Options',
						'desc' 		=> 'Footer type<br><small>' . $note_str . ' Setting this setting to fixed will stick footer to bottom edge of window behind the wrapper.</small>',
						'id' 		=> 'footer_fixed',
						'std' 		=> '',
						'type' 		=> 'select',
						'options'	=> array(
							''               => 'Normal',
							'fixed'          => 'Fixed to Bottom (No animations)',
							'fixed-fade'     => 'Fixed to Bottom with Fade Animation',
							'fixed-slide'    => 'Fixed to Bottom with Slide Animation',
						),
						
						'afold'		=> 'footer_visibility:checked',
						
						'tab_id'	=> 'footer-general',
				);
				
$of_options[] = array( 	'desc' 		=> 'Text color<br><small>' . $note_str . ' Select footer text color, default color is theme skin.</small>',
						'id' 		=> 'footer_style',
						'std' 		=> '',
						'type' 		=> 'select',
						'options'	=> array(
							''           => 'Default (Based on Current Skin)',
							'inverted'   => 'White',
						),
						
						'afold'		=> 'footer_visibility:checked',
						
						'tab_id'	=> 'footer-general',
				);

$of_options[] = array( 	'desc' 		=> "Full-width footer<br><small>Extend footer container to the browser edge.</small>",
						'id' 		=> 'footer_fullwidth',
						'std' 		=> 0,
						'on' 		=> 'Yes',
						'off' 		=> 'No',
						'type' 		=> 'switch',
						'afold'		=> 'footer_visibility:checked',
						
						'tab_id'	=> 'footer-general',
				);

$of_options[] = array( 	'name' 		=> 'Footer Bottom Section',
						'desc' 		=> 'Enable or remove the bottom footer with copyrights text',
						'id' 		=> 'footer_bottom_visible',
						'std' 		=> 1,
						'on' 		=> 'Show',
						'off' 		=> 'Hide',
						'type' 		=> 'switch',
						'folds'		=> 1,
						
						'afold'		=> 'footer_visibility:checked',
						
						'tab_id'	=> 'footer-general',
				);

$of_options[] = array( 	'name' 		=> 'Footer Style',
						'desc' 		=> 'Select which type of bottom footer you want to use',
						'id' 		=> 'footer_bottom_style',
						'std' 		=> 'horizontal',
						'type' 		=> 'images',
						'options' 	=> array(
							'horizontal' => kalium()->assetsUrl( 'images/admin/footer-style-horizontal.png' ),
							'vertical'   => kalium()->assetsUrl( 'images/admin/footer-style-vertical.png' ),
						),
						'fold'		=> 'footer_bottom_visible',
						'descrs'	=> array(
							'horizontal' => 'Columned',
							'vertical'   => 'Centered',
						),
						
						'afold'		=> 'footer_visibility:checked',
						
						'tab_id'	=> 'footer-general',
				);

$of_options[] = array( 	'name' 		=> 'Footer Text',
						'desc' 		=> 'Footer Left - Copyrights text in the footer',
						'id' 		=> 'footer_text',
						'std' 		=> "&copy; Copyright ".date('Y').'. All Rights Reserved',
						'type' 		=> 'textarea',
						'fold'		=> 'footer_bottom_visible',
						
						'afold'		=> 'footer_visibility:checked',
						
						'tab_id'	=> 'footer-general',
				);

$of_options[] = array( 	'desc' 		=> 'Footer Right - Content for the right block in the footer bottom<br><small>' . $note_str . ' You can also add social networks in footer text, example:<br> ' . $lab_social_networks_shortcode . '</small>',
						'id' 		=> 'footer_text_right',
						'std' 		=> "[lab_social_networks]",
						'type' 		=> 'textarea',
						'fold'		=> 'footer_bottom_visible',
						
						'afold'		=> 'footer_visibility:checked',
						
						'tab_id'	=> 'footer-general',
				);

$of_options[] = array( 	'name' 		=> 'Footer Widgets',
						'desc' 		=> 'Show or hide footer widgets',
						'id' 		=> 'footer_widgets',
						'std' 		=> 1,
						'on' 		=> 'Show',
						'off' 		=> 'Hide',
						'type' 		=> 'switch',
						'folds'		=> 1,
						
						'tab_id'	=> 'footer-widgets',
				);

$of_options[] = array( 	'desc' 		=> 'Set number of columns to split widgets',
						'id' 		=> 'footer_widgets_columns',
						'std' 		=> 'three',
						'type' 		=> 'select',
						'options' 	=> array(
							'one'    => '1 widget per row',// (1/1)",
							'two'    => '2 widgets per row',// (1/2)",
							'three'  => '3 widgets per row',// (1/3)",
							'four'   => '4 widgets per row',// (1/4)",
							'five'   => '5 widgets per row',// (1/4)",
							'six'    => '6 widgets per row',// (1/6)"
						),
						'fold'		=> 'footer_widgets',
						
						'tab_id'	=> 'footer-widgets',
				);

$of_options[] = array( 	'desc' 		=> "Collapse or expand footer widgets in mobile devices<br><small>{$note_str} Users still can see footer widgets (if collapsed) when they click <strong>three dots (...)</strong> link</small>",
						'id' 		=> 'footer_collapse_mobile',
						'std' 		=> 0,
						'on' 		=> 'Collapsed',
						'off' 		=> 'Expanded',
						'type' 		=> 'switch',
						'fold'		=> 'footer_widgets',
						
						'tab_id'	=> 'footer-widgets',
				);
				
$of_options[] = array( 	'name' 		=> 'Custom JavaScript Information',
						'desc' 		=> "",
						'id' 		=> 'portfolio_lb',
						'std' 		=> "
						<h3 style=\"margin: 0 0 10px;\">Custom JavaScript</h3>
						<p>
							&lt;script&gt;&lt;/script&gt; tags are optional.<br>
							It is recommended to add your custom JavaScript in footer unless you are required to add it in the header.
						</p>",
						'icon' 		=> true,
						'type' 		=> 'info',
						
						'tab_id'	=> 'footer-custom-js'
					);

$of_options[] = array( 	'name' 		=> 'Header JavaScript',
						'desc' 		=> "Add your JavaScript code here. The code entered here will be added in &lt;head&gt; tag.<br /><small>{$note_str} Unless it is required to put JavaScript in header, otherwise it is recommended to put your JavaScript in Footer section. <a href=\"https://developer.yahoo.com/performance/rules.html#js_bottom=\" target=\"_blank\">Learn more</a></small>",
						'id' 		=> 'user_custom_js_head',
						'std' 		=> "",
						'type' 		=> 'textarea',
						'plc'		=> "// Example\nvar a = 1;\nvar b = 2;\n\nfunction fx( c ) {\n\treturn Math.pow( a + b, c );\n}",
						
						'tab_id'	=> 'footer-custom-js',
				);

$of_options[] = array( 	'name' 		=> 'Footer JavaScript',
						'desc' 		=> "Add your JavaScript code here. The code entered here will be added in page footer.<br /><small><span class=\"note\">Example:</span> Google Analytics tracking code can be added here.</small>",
						'id' 		=> 'user_custom_js',
						'std' 		=> "",
						'type' 		=> 'textarea',
						'plc'		=> "",
						
						'tab_id'	=> 'footer-custom-js',
				);


// BLOG SETTINGS
$of_options[] = array( 	'name' 		=> 'Blog Settings',
						'type' 		=> 'heading',
						'icon'		=> 'fa fa-newspaper-o'
				);

$of_options[] = array( 	'type' 		=> 'tabs',
						'id'		=> 'blog-settings-tabs',
						'tabs'		=> array(
							'blog-settings-loop'     => 'Blog Page',
							'blog-settings-single'   => 'Single Page',
							'blog-settings-sharing'  => 'Share Settings',
							'blog-settings-other'    => 'Other Settings',
						)
				);
				

$of_options[] = array( 	'name' 		=> "Blog Title & Description",
						'desc' 		=> 'Show header title and description in blog page',
						'id' 		=> 'blog_show_header_title',
						'std' 		=> 1,
						'on' 		=> 'Show',
						'off' 		=> 'Hide',
						'type' 		=> 'switch',
						'folds'		=> 1,
						
						'tab_id' 	=> 'blog-settings-other',
				);

$of_options[] = array( 	'desc' 		=> 'Blog header title (optional)',
						'id' 		=> 'blog_title',
						'std' 		=> 'Blog',
						'plc'		=> "",
						'type' 		=> 'text',
						'fold'		=> 'blog_show_header_title',
						
						'tab_id' 	=> 'blog-settings-other',
				);

$of_options[] = array( 	'desc' 		=> 'Blog description in header (optional)',
						'id' 		=> 'blog_description',
						'std'		=> 'Our everyday thoughts are presented here'.PHP_EOL."Music, video presentations, photo-shootings and more",
						'plc' 		=> "",
						'type' 		=> 'textarea',
						'type' 		=> 'textarea',
						'fold'		=> 'blog_show_header_title',
						
						'tab_id' 	=> 'blog-settings-other',
 				);

$of_options[] = array( 	'name'		=> 'Default Blog Template',
						'desc' 		=> 'Select your preferred blog template to show blog posts',
						'id' 		=> 'blog_template',
						'std' 		=> 'blog-squared',
						'options'	=> array(
							
							'blog-squared' => kalium()->assetsUrl( 'images/admin/blog-template-squared.png' ),
							'blog-rounded'   => kalium()->assetsUrl( 'images/admin/blog-template-rounded.png' ),
							'blog-masonry'   => kalium()->assetsUrl( 'images/admin/blog-template-masonry.png' ),
						),
						'descrs'	=> array(
							'blog-squared'   => 'Classic',
							'blog-rounded'   => 'Rounded',
							'blog-masonry'   => 'Masonry',
						),
						'type' 		=> 'images',
						'afolds'	=> true,
						
						'tab_id' 	=> 'blog-settings-loop',
				);

$of_options[] = array( 	'name' 		=> 'Sidebar',
					 	'desc' 		=> "Set blog sidebar position or hide it",
						'id' 		=> 'blog_sidebar_position',
						'std' 		=> 'right',
						'type' 		=> 'select',
						'options' 	=> $show_sidebar_options,
						
						'tab_id' 	=> 'blog-settings-loop',
				);
				
$of_options[] = array( 	'name' 		=> 'Blog Options',
						'desc' 		=> 'Show post thumbnails',
						'id' 		=> 'blog_thumbnails',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'folds'		=> true,
						
						'tab_id' 	=> 'blog-settings-loop',
				);
				
$of_options[] = array( 	'desc' 		=> 'Show thumbnail placeholders<br><small>If there is no featured image, gray image will be shown.</small>',
						'id' 		=> 'blog_thumbnails_placeholder',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'fold'		=> 'blog_thumbnails',
						
						'tab_id' 	=> 'blog-settings-loop',
				);

$of_options[] = array( 	'desc' 		=> 'Show post format icon',
						'id' 		=> 'blog_post_type_icon',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id' 	=> 'blog-settings-loop',
				);

$of_options[] = array( 	'desc' 		=> 'Show post format content',
						'id' 		=> 'blog_post_formats',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id' 	=> 'blog-settings-loop',
				);

$of_options[] = array( 	'desc' 		=> 'Show post title',
						'id' 		=> 'blog_post_title',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id' 	=> 'blog-settings-loop',
				);

$of_options[] = array( 	'desc' 		=> 'Show post excerpt',
						'id' 		=> 'blog_post_excerpt',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id' 	=> 'blog-settings-loop',
				);

$of_options[] = array( 	'desc' 		=> 'Show post date',
						'id' 		=> 'blog_post_date',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id' 	=> 'blog-settings-loop',
				);

$of_options[] = array( 	'desc' 		=> 'Show post category',
						'id' 		=> 'blog_category',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id' 	=> 'blog-settings-loop',
				);
				
$of_options[] = array( 	'desc' 		=> 'Use proportional thumbnail height',
						'id' 		=> 'blog_loop_proportional_thumbnails',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						
						'tab_id' 	=> 'blog-settings-loop',
				);

$of_options[] = array( 	'desc' 		=> 'Posts per row<br><small>' . $note_str . ' Applied for masonry blog type only.</small>',
						'id' 		=> 'blog_columns',
						'std' 		=> '_3',
						'options'	=> array(
							'_1' => '1 post per row',
							'_2' => '2 posts per row',
							'_3' => '3 posts per row',
							'_4' => '4 posts per row'
						),
						'type' 		=> 'select',
						
						'afold'		=> 'blog_template:blog-masonry',
						'tab_id' 	=> 'blog-settings-loop',
				);

$of_options[] = array( 	'desc' 		=> 'Vertical alignment<br><small>' . $note_str . ' Set alignment of posts between rows.</small>',
						'id' 		=> 'blog_masonry_layout_mode',
						'std' 		=> 'packery',
						'options'	=> array(
							'packery' => 'Masonry',
							'fit-rows' => 'Fit Rows',
						),
						'type' 		=> 'select',
						
						'afold'		=> 'blog_template:blog-masonry',
						'tab_id' 	=> 'blog-settings-loop',
				);

$of_options[] = array( 	'desc' 		=> "Columns gap<br><small>{$note_str} Set custom spacing for post columns.</small>",
						'id' 		=> 'blog_masonry_columns_gap',
						'std' 		=> "",
						'plc'		=> 'Default gap: 30 pixels',
						'type' 		=> 'text',
						'numeric'	=> true,
						'postfix'	=> 'px',
						
						'afold'		=> 'blog_template:blog-masonry',
						'tab_id' 	=> 'blog-settings-loop',
				);
				
				//sssssssss

$of_options[] = array( 	'desc' 		=> 'Thumbnail hover effect',
						'id' 		=> 'blog_thumbnail_hover_effect',
						'std' 		=> 'full-cover',
						'options'	=> array(
							''						 => 'No hover effect',
							
							'distanced'              => 'Distanced cover (semi-transparent)',
							'distanced-no-opacity'   => 'Distanced cover',
							
							'full-cover'             => 'Full cover (semi-transparent)',
							'full-cover-no-opacity'  => 'Full cover',
						),
						'type' 		=> 'select',
						
						'tab_id' 	=> 'blog-settings-loop',
				);
				
$of_options[] = array( 	'desc' 		=> 'Thumbnail hover layer icon',
						'id' 		=> 'blog_post_hover_layer_icon',
						'std' 		=> get_data( 'blog_post_hover_animatd_eye' ) ? 'animated-eye' : 'static-eye',
						'type' 		=> 'select',
						'options' 	=> array(
							'static-eye'     => 'Static Eye Icon',
							'animated-eye'   => 'Animated Eye Icon',
							'custom'         => 'Custom Icon'
						),
						'afolds'	=> true,
						
						'tab_id' 	=> 'blog-settings-loop'
				);
				

$of_options[] = array(	'name'		=> 'Custom Hover Layer Icon',
						'desc' 		=> 'Select custom hover layer icon',
						'id' 		=> 'blog_post_hover_layer_icon_custom',
						'std' 		=> "",
						'type' 		=> 'media',
						'mod' 		=> 'min',
						
						'afold'		=> 'blog_post_hover_layer_icon:custom',
						
						'tab_id'	=> 'blog-settings-loop'
					);

$of_options[] = array( 	'desc' 		=> "Custom hover icon width",
						'id' 		=> 'blog_post_hover_layer_icon_custom_width',
						'std' 		=> "",
						'plc'		=> '',
						'type' 		=> 'text',
						'postfix'	=> 'px',
						
						'afold'		=> 'blog_post_hover_layer_icon:custom',
						
						'tab_id'	=> 'blog-settings-loop'
					);



$of_options[] = array( 	'name' 		=> 'Pagination',
						'desc' 		=> 'Select pagination type',
						'id' 		=> 'blog_pagination_type',
						'std' 		=> 'center',
						'type' 		=> 'select',
						'options' 	=> array(
							'normal'         => 'Normal Pagination',
							'endless'        => 'Infinite Scroll',
							'endless-reveal' => "Infinite Scroll + Auto Reveal"
						),
						
						'afolds'	=> true,
						
						'tab_id' 	=> 'blog-settings-loop',
				);

$of_options[] = array( 	'desc' 		=> 'Pagination style for endless scroll while loading',
						'id' 		=> 'blog_endless_pagination_style',
						'std' 		=> '_1',
						'type' 		=> 'select',
						'options' 	=> $endless_pagination_style,
						
						'afold'		=> 'blog_pagination_type:endless,endless-reveal',
						
						'tab_id' 	=> 'blog-settings-loop',
				);

$of_options[] = array( 	'desc' 		=> 'Set pagination position',
						'id' 		=> 'blog_pagination_position',
						'std' 		=> 'center',
						'type' 		=> 'select',
						'options' 	=> array('left' => 'Left', 'center' => 'Center', 'right' => 'Right', 'stretched' => 'Stretched' ),
						
						'tab_id' 	=> 'blog-settings-loop',
				);

$of_options[] = array( 	'name' 		=> 'Post Options',
						'desc' 		=> 'Show post featured image',
						'id' 		=> 'blog_single_thumbnails',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id' 	=> 'blog-settings-single',
				);

$of_options[] = array( 	'desc' 		=> 'Show post title',
						'id' 		=> 'blog_single_title',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id' 	=> 'blog-settings-single',
				);

$of_options[] = array( 	'desc' 		=> 'Show author info',
						'id' 		=> 'blog_author_info',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						'folds'		=> true,
						
						'tab_id' 	=> 'blog-settings-single',
				);

$of_options[] = array( 	'desc' 		=> 'Show post date',
						'id' 		=> 'blog_post_date_single',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id' 	=> 'blog-settings-single',
				);

$of_options[] = array( 	'desc' 		=> 'Show post category',
						'id' 		=> 'blog_category_single',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id' 	=> 'blog-settings-single',
				);

$of_options[] = array( 	'desc' 		=> 'Show post tags',
						'id' 		=> 'blog_tags',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id' 	=> 'blog-settings-single',
				);

$of_options[] = array( 	'desc' 		=> 'Show post next-previous links',
						'id' 		=> 'blog_post_prev_next',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id' 	=> 'blog-settings-single',
				);

$of_options[] = array( 	'desc' 		=> 'Featured image placement',
						'id' 		=> 'blog_featured_image_placement',
						'std' 		=> 'container',
						'options'	=> array(
							'container'  => 'Boxed (within container)',
							'full-width' => 'Full-width',
						),
						'type' 		=> 'select',
						
						'tab_id' 	=> 'blog-settings-single',
				);

$of_options[] = array( 	'desc' 		=> 'Featured image size',
						'id' 		=> 'blog_featured_image_size_type',
						'std' 		=> 'default',
						'options'	=> array(
							'default' => 'Default thumbnail size',
							'full' 	  => 'Original image size',
						),
						'type' 		=> 'select',
						
						'tab_id' 	=> 'blog-settings-single',
				);

$of_options[] = array( 	'desc' 		=> 'Author info placement',
						'id' 		=> 'blog_author_info_placement',
						'std' 		=> 'left',
						'options'	=> array(
							'left'   => 'Left',
							'right'  => 'Right',
							'bottom' => 'Below the article',
						),
						'type' 		=> 'select',
						'fold'		=> 'blog_author_info',
						
						'tab_id' 	=> 'blog-settings-single',
				);

$of_options[] = array( 	'desc' 		=> 'Blog post comments visibility',
						'id' 		=> 'blog_comments',
						'std' 		=> 'show',
						'options'	=> array(
							'show'   => 'Show comments',
							'hide'  => 'Hide comments',
						),
						'type' 		=> 'select',
						
						'tab_id' 	=> 'blog-settings-single',
				);

$of_options[] = array( 	'desc' 		=> "Featured image thumbnail height (applied on single post only). If you change this value, you need to <a href=\"admin.php?page=laborator-docs#regenerate-thumbnails\" target=\"_blank\">regenerate thumbnails</a> again",
						'id' 		=> 'blog_thumbnail_height',
						'std' 		=> "",
						'plc'		=> 'Default value is applied if set to empty: 490',
						'type' 		=> 'text',
						'numeric'	=> true,
						
						'tab_id' 	=> 'blog-settings-single',
				);

$of_options[] = array( 	'desc' 		=> 'Auto-switch interval for gallery images<br><small>' . $note_str . ' Leave empty to disable auto-switch</small>',
						'id' 		=> 'blog_gallery_autoswitch',
						'std' 		=> "",
						'plc'		=> 'No auto-switch',
						'type' 		=> 'text',
						'numeric'	=> true,
						'postfix'	=> 's',
						
						'tab_id' 	=> 'blog-settings-single',
				);

$of_options[] = array( 	'name' 		=> 'Sidebar',
					 	'desc' 		=> "Set blog sidebar position in single post or hide it<br><small>{$note_str} Author info will be placed below the article (if its set to show)</small>",
						'id' 		=> 'blog_single_sidebar_position',
						'std' 		=> 'hide',
						'type' 		=> 'select',
						'options' 	=> $show_sidebar_options,
						
						'tab_id' 	=> 'blog-settings-single',
				);

$of_options[] = array( 	'name' 		=> 'Share Story',
						'desc' 		=> 'Enable or disable sharing blog post on social networks',
						'id' 		=> 'blog_share_story',
						'std' 		=> 0,
						'on' 		=> 'Allow Share',
						'off' 		=> 'No',
						'type' 		=> 'switch',
						'folds'		=> 1,
						
						'tab_id'	=> 'blog-settings-sharing'
				);

$share_story_networks = array(
			'visible' => array (
				'placebo'	=> 'placebo',
				'fb'   	 	=> 'Facebook',
				'tw'   	 	=> 'Twitter',
				'lin'       => 'LinkedIn',
				'tlr'       => 'Tumblr',
				'gp'       	=> 'Google Plus',
			),

			'hidden' => array (
				'placebo'   => 'placebo',
				'pi'       	=> 'Pinterest',
				'em'       	=> 'Email',
				'vk'       	=> 'VKontakte',
			),
);

$of_options[] = array( 	'name' 		=> 'Share on:',
						'desc' 		=> 'Choose social networks that visitors can share your blog post',
						'id' 		=> 'blog_share_story_networks',
						'std' 		=> $share_story_networks,
						'type' 		=> 'sorter',
						'fold'		=> 'blog_share_story',
						
						'tab_id'	=> 'blog-settings-sharing'
				);

$of_options[] = array( 	'name'		=> 'Share links options',
						'desc' 		=> 'Rounded social networks icons',
						'id' 		=> 'blog_share_story_rounded_icons',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						'fold'		=> 'blog_share_story',
						
						'tab_id'	=> 'blog-settings-sharing'
				);
// END OF BLOG SETTINGS


// PORTFOLIO SETTINGS
$of_options[] = array( 	'name' 		=> 'Portfolio Settings',
						'type' 		=> 'heading',
						'icon'		=> 'fa fa-th'
				);

$of_options[] = array( 	'type' 		=> 'tabs',
						'id'		=> 'portfolio-settings-tabs',
						'tabs'		=> array(
							'portfolio-settings-layout'      => 'Portfolio Layout',
							'portfolio-settings-loop'        => 'Portfolio Page',
							'portfolio-settings-single'      => 'Single Page',
							'portfolio-settings-lightbox'    => 'Lightbox Options',
							'portfolio-settings-sharing'     => 'Share Settings',
							'portfolio-settings-other'       => 'Other Settings',
						)
				);


$of_options[] = array( 	'name' 		=> 'Portfolio Layout Type',
						'desc' 		=> "Select default type to show portfolio items.<br /><small>{$note_str} You can override this setting for individual portfolio pages.</small><br><br>",
						'id' 		=> 'portfolio_type',
						'std' 		=> 'type-1',
						'type' 		=> 'images',
						'options' 	=> array(
							'type-1' => kalium()->assetsUrl( 'images/admin/portfolio-grid/portfolio-type-1.png' ),
							'type-2' => kalium()->assetsUrl( 'images/admin/portfolio-grid/portfolio-type-2.png' ),
							'type-3' => kalium()->assetsUrl( 'images/admin/portfolio-grid/portfolio-type-3.png' ),
							//'type-4' => kalium()->assetsUrl( 'images/admin/portfolio-grid/portfolio-type-4.png' ),
						),
						'descrs'	=> array(
							'type-1' => 'Visible Titles',
							'type-2' => 'Titles Inside',
							'type-3' => 'Titles Inside + Masonry Layout',
						),
						'afolds'	=> 1,
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);

$of_options[] = array( 	'name' 		=> 'Visible Titles Settings',
						'desc' 		=> 'Use proportional image size (not cropped)',
						'id' 		=> 'portfolio_type_1_dynamic_height',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						'afold'		=> 'portfolio_type:type-1',
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);

$of_options[] = array( 	'desc' 		=> 'Columns count for the current view type',
						'id' 		=> 'portfolio_type_1_columns_count',
						'std' 		=> '4',
						'type' 		=> 'select',
						'options' 	=> array(
							'1'  => '1 Item per Row',
							'2'  => '2 Items per Row',
							'3'  => '3 Items per Row',
							'4'  => '4 Items per Row',
							'5'  => '5 Items per Row',
							'6'  => '6 Items per Row',
						),
						'afold'		=> 'portfolio_type:type-1',
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);

$of_options[] = array( 	'desc' 		=> "Portfolio items per page for this type<br><small>{$note_str} To display all portfolio items in single page enter <strong>-1</strong> value.</small>",
						'id' 		=> 'portfolio_type_1_items_per_page',
						'std' 		=> "",
						'plc'		=> '(leave empty to use WordPress default)',
						'type' 		=> 'text',
						'numeric'	=> true,
						'afold'		=> 'portfolio_type:type-1',
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);

$of_options[] = array( 	'desc' 		=> 'Thumbnail hover layer icon',
						'id' 		=> 'portfolio_type_1_hover_layer_icon',
						'std' 		=> get_data( 'portfolio_type_1_hover_animatd_eye' ) ? 'animated-eye' : 'static-eye',
						'type' 		=> 'select',
						'options' 	=> array(
							'static-eye'     => 'Static Eye Icon',
							'animated-eye'   => 'Animated Eye Icon',
							'custom'         => 'Custom Icon'
						),
						'afolds'	=> true,
						'afold'		=> 'portfolio_type:type-1',
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);
				

$of_options[] = array(	'name'		=> 'Custom Hover Layer Icon',
						'desc' 		=> 'Select custom hover layer icon',
						'id' 		=> 'portfolio_type_1_hover_layer_icon_custom',
						'std' 		=> "",
						'type' 		=> 'media',
						'mod' 		=> 'min',
						
						'afold'		=> 'portfolio_type_1_hover_layer_icon:custom',
						
						'tab_id'	=> 'portfolio-settings-layout'
					);

$of_options[] = array( 	'desc' 		=> "Custom hover icon width",
						'id' 		=> 'portfolio_type_1_hover_layer_icon_custom_width',
						'std' 		=> "",
						'plc'		=> '',
						'type' 		=> 'text',
						'postfix'	=> 'px',
						
						'afold'		=> 'portfolio_type_1_hover_layer_icon:custom',
						
						'tab_id'	=> 'portfolio-settings-layout'
					);


$of_options[] = array( 	'name'		=> 'Thumbnail Options',
						'desc' 		=> 'Hover effect',
						'id' 		=> 'portfolio_type_1_hover_effect',
						'std' 		=> 'full',
						'type' 		=> 'select',
						'options' 	=> array(
							'none'       => 'No hover effect',
							'full'       => 'Full background hover',
							'distanced'  => 'Distanced background hover'
						),
						'afold'		=> 'portfolio_type:type-1',
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);

$of_options[] = array( 	'desc' 		=> 'Hover transparency',
						'id' 		=> 'portfolio_type_1_hover_transparency',
						'std' 		=> 'opacity',
						'type' 		=> 'select',
						'options' 	=> array(
							'opacity'    => 'Apply Transparency',
							'no-opacity' => 'No Transparency',
						),
						'afold'		=> 'portfolio_type:type-1',
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);

$of_options[] = array( 	'desc' 		=> 'Hover color background for this type',
						'id' 		=> 'portfolio_type_1_hover_color',
						'std' 		=> "",
						'type' 		=> 'color',
						'afold'		=> 'portfolio_type:type-1',
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);

$of_options[] = array( 	'desc' 		=> "Thumbnail size for this type<br><small>If you change dimensions you must <a href=\"admin.php?page=laborator-docs#regenerate-thumbnails\" target=\"_blank\">regenerate thumbnails</a>. <br>{$thumbnail_sizes_info}</small>",
						'id' 		=> 'portfolio_thumbnail_size_1',
						'std' 		=> "",
						'plc'		=> 'Leave empty to use default: 505x420',
						'type' 		=> 'text',
						'afold'		=> 'portfolio_type:type-1',
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);


$of_options[] = array( 	'name' 		=> "Titles Inside &amp; Masonry Layout Settings",
						'desc' 		=> 'Show like button for portfolio items',
						'id' 		=> 'portfolio_type_2_likes_show',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'afold'		=> "portfolio_type:type-2,type-3",
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);

$of_options[] = array( 	'desc' 		=> 'Columns count for the current view type',
						'id' 		=> 'portfolio_type_2_columns_count',
						'std' 		=> '4',
						'type' 		=> 'select',
						'options' 	=> array(
							'1'  => '1 Item per Row',
							'2'  => '2 Items per Row',
							'3'  => '3 Items per Row',
							'4'  => '4 Items per Row',
							'5'  => '5 Items per Row',
							'6'  => '6 Items per Row',
						),
						'afold'		=> "portfolio_type:type-2,type-3",
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);

$of_options[] = array( 	'desc' 		=> 'Spacing between portfolio items',
						'id' 		=> 'portfolio_type_2_grid_spacing',
						'std' 		=> 'four',
						'type' 		=> 'select',
						'options' 	=> array(
							'normal' => 'Default spacing',
							'merged' => 'Merged (no spacing)'
						),
						'afold'		=> "portfolio_type:type-2,type-3",
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);

$of_options[] = array( 	'desc' 		=> "Default item spacing<br><small>{$note_str} Not applied when &quot;Merged&quot; spacing is selected.</small>",
						'id' 		=> 'portfolio_type_2_default_spacing',
						'std' 		=> "",
						'plc'		=> 'Default spacing: 30',
						'type' 		=> 'text',
						'numeric'	=> true,
						'postfix'	=> 'px',
						'afold'		=> "portfolio_type:type-2,type-3",
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);

$of_options[] = array( 	'desc' 		=> "Portfolio items per page for this type<br><small>{$note_str} To display all portfolio items in single page enter <strong>-1</strong> value.</small>",
						'id' 		=> 'portfolio_type_2_items_per_page',
						'std' 		=> "",
						'plc'		=> '(leave empty to use WordPress default)',
						'type' 		=> 'text',
						'numeric'	=> true,
						'afold'		=> "portfolio_type:type-2,type-3",
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);

$of_options[] = array( 	'name'		=> 'Thumbnail Options',
						'desc' 		=> 'Hover effect',
						'id' 		=> 'portfolio_type_2_hover_effect',
						'std' 		=> 'full',
						'type' 		=> 'select',
						'options' 	=> array(
							'none'       => 'No hover effect',
							'full'       => 'Full background hover',
							'distanced'  => 'Distanced background hover'
						),
						'afold'		=> "portfolio_type:type-2,type-3",
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);

$of_options[] = array( 	'desc' 		=> 'Hover text position',
						'id' 		=> 'portfolio_type_2_hover_text_position',
						'std' 		=> 'bottom-left',
						'type' 		=> 'select',
						'options' 	=> array(
							'top-left'       => 'Top Left',
							'top-right'      => 'Top Right',
							'center'         => 'Center',
							'bottom-left'    => 'Bottom Left',
							'bottom-right'   => 'Bottom Right',
						),
						'afold'		=> "portfolio_type:type-2,type-3",
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);

$of_options[] = array( 	'desc' 		=> 'Hover transparency',
						'id' 		=> 'portfolio_type_2_hover_transparency',
						'std' 		=> 'opacity',
						'type' 		=> 'select',
						'options' 	=> array(
							'opacity'    => 'Apply Transparency',
							'no-opacity' => 'No Transparency',
						),
						'afold'		=> "portfolio_type:type-2,type-3",
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);

$of_options[] = array( 	'desc' 		=> 'Hover background style',
						'id' 		=> 'portfolio_type_2_hover_style',
						'std' 		=> 'primary',
						'type' 		=> 'select',
						'options' 	=> array(
							'primary'=> 'Primary theme color',
							'black'  => 'Black background',
							'white'  => 'White background'
						),
						'afold'		=> "portfolio_type:type-2,type-3",
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);

$of_options[] = array( 	'desc' 		=> 'Hover color background for this type',
						'id' 		=> 'portfolio_type_2_hover_color',
						'std' 		=> "",
						'type' 		=> 'color',
						'afold'		=> "portfolio_type:type-2,type-3",
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);
				

$of_options[] = array( 	'desc' 		=> "Thumbnail size for this type<br><small>If you change dimensions you must <a href=\"admin.php?page=laborator-docs#regenerate-thumbnails\" target=\"_blank\">regenerate thumbnails</a>.<br>{$thumbnail_sizes_info}</small>",
						'id' 		=> 'portfolio_thumbnail_size_2',
						'std' 		=> "",
						'plc'		=> 'Leave empty to use default: 505x420',
						'type' 		=> 'text',
						'afold'		=> "portfolio_type:type-2,type-3",
						
						'tab_id' 	=> 'portfolio-settings-layout'
				);

$of_options[] = array( 	'name' 		=> 'Full-width Portfolio',
						'desc' 		=> 'Extend portfolio container to the browser edge',
						'id' 		=> 'portfolio_full_width',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						
						'folds'		=> true,
						
						'tab_id'	=> 'portfolio-settings-layout'
				);

$of_options[] = array( 	'desc' 		=> 'Include title and filter within container',
						'id' 		=> 'portfolio_full_width_title_filter_container',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'fold'		=> 'portfolio_full_width',
						
						'tab_id'	=> 'portfolio-settings-layout'
				);

// Portfolio Loop
$of_options[] = array( 	'name' 		=> 'Portfolio Page Options',
						'desc' 		=> 'Like feature for portfolio items',
						'id' 		=> 'portfolio_likes',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'folds'		=> true,
						
						'tab_id'	=> 'portfolio-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> 'Category filter for portfolio items',
						'id' 		=> 'portfolio_category_filter',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'folds'		=> true,
						
						'tab_id'	=> 'portfolio-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> 'Enable subcategory filtering',
						'id' 		=> 'portfolio_filter_enable_subcategories',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						'fold'		=> 'portfolio_category_filter',
						
						'tab_id'	=> 'portfolio-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> "Like icon type<br><small>{$note_str} Select \"like\" icon shape to show.</small>",
						'id' 		=> 'portfolio_likes_icon',
						'std' 		=> 'categories',
						'type' 		=> 'select',
						'options' 	=> array(
							'heart'  => 'Heart',
							'thumb'  => 'Thumbs Up',
							'star'   => 'Star',
						),
						
						'fold'		=> 'portfolio_likes',
						
						'tab_id'	=> 'portfolio-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> "Information under portfolio item title<br><small>{$note_str} Select type of content you want to show under portfolio titles in the loop.</small>",
						'id' 		=> 'portfolio_loop_subtitles',
						'std' 		=> 'categories',
						'type' 		=> 'select',
						'options' 	=> array(
							'categories'         => 'Show Item Categories',
							'categories-parent'  => 'Show Item Parent Categories Only',
							'subtitle'           => 'Show Item Subtitle',
							'hide'               => 'Hide'
						),
						
						'tab_id'	=> 'portfolio-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> 'Reveal effect for portfolio items',
						'id' 		=> 'portfolio_reveal_effect',
						'std' 		=> 'slidenfade',
						'type' 		=> 'select',
						'options' 	=> array(
							'none'			 => 'None',
							'fade'           => 'Fade',
							'slidenfade'     => 'Slide and Fade',
							'zoom'           => 'Zoom In',
							'fade-one'       => 'Fade (one by one)',
							'slidenfade-one' => 'Slide and Fade (one by one)',
							'zoom-one'       => 'Zoom In (one by one)'
						),
						
						'tab_id'	=> 'portfolio-settings-loop'
				);

$portfolio_post_type_obj = get_post_type_object( 'portfolio' );
$portfolio_prefix_url_slug_placeholder = '';

if ( $portfolio_post_type_obj != null ) {
	$portfolio_prefix_url_slug_placeholder = 'Current portfolio slug: ' . $portfolio_post_type_obj->rewrite['slug'];
}

if ( $portfolio_prefix_url_slug_placeholder )  {


	$of_options[] = array( 	'name' 		=> 'URL Rewrite Options',
							'desc' 		=> 'Prefix for portfolio items URL<br><small>' . $note_str . ' Adds prefix to portfolio items: <em>domain.com/<strong>' . $portfolio_post_type_obj->rewrite['slug'] . '/</strong>portfolio-item-slug</em></small>',
							'id' 		=> 'portfolio_url_add_prefix',
							'std' 		=> 1,
							'on' 		=> 'Yes',
							'off' 		=> 'No',
							'type' 		=> 'switch',
							'folds'		=> 1,
							
							'tab_id' 	=> 'portfolio-settings-loop'
					);
	
	$of_options[] = array( 	#'name'		=> 'URL Rewrite Options',
							'desc'		=> "Custom portfolio item URL prefix<br><small>{$note_str} When you change this setting you need to <a href=\"" . admin_url( 'admin.php?page=laborator-docs#flush-rewrite-rules' ) . "\" target=\"_blank\">flush rewrite rules</a>.</small>",
							'id' 		=> 'portfolio_prefix_url_slug',
							'std' 		=> "",
							'plc'		=> $portfolio_prefix_url_slug_placeholder,
							'type' 		=> 'text',
							
							'fold'		=> 'portfolio_url_add_prefix',
						
							'tab_id'	=> 'portfolio-settings-loop',
					);
					
	// Portfolio Category URL prefix
	$portfolio_category_args = apply_filters( 'portfolioposttype_category_args', array() );

	$portfolio_category_prefix_url_slug_placeholder = '';
	
	if( ! empty ( $portfolio_category_args['rewrite']['slug'] ) ) {
		$portfolio_category_prefix_url_slug_placeholder = $portfolio_category_args['rewrite']['slug'];
	}
	
	$of_options[] = array( 	'desc'		=> "Custom portfolio category URL prefix<br><small>{$note_str} When you change this setting you need to <a href=\"" . admin_url( 'admin.php?page=laborator-docs#flush-rewrite-rules' ) . "\" target=\"_blank\">flush rewrite rules</a>.</small>",
							'id' 		=> 'portfolio_category_prefix_url_slug',
							'std' 		=> "",
							'plc'		=> 'Current category slug: ' . $portfolio_category_prefix_url_slug_placeholder,
							'type' 		=> 'text',
						
						'tab_id'	=> 'portfolio-settings-loop',
					);
}

$of_options[] = array( 	'desc' 		=> "Filter link type<br><small>{$note_str} Set portfolio filter links to absolute or appended hash links. <a href=\"http://drops.laborator.co/FKg2\" target=\"_blank\">Click to learn more</a>.</small>",
						'id' 		=> 'portfolio_filter_link_type',
						'std' 		=> 'hash',
						'type' 		=> 'select',
						'options' 	=> array(
							'hash'       => 'Hash (appended in the end of URL)',
							'pushState'  => 'Absolute Category Link (pushState)',
						),
						
						'fold'		=> 'portfolio_category_filter',
						
						'tab_id'	=> 'portfolio-settings-loop'
				);


$of_options[] = array( 	'name' 		=> 'Pagination',
						'desc' 		=> 'Select pagination type',
						'id' 		=> 'portfolio_pagination_type',
						'std' 		=> 'center',
						'type' 		=> 'select',
						'options' 	=> array(
							'normal'         => 'Normal Pagination',
							'endless'        => 'Infinite Scroll',
							'endless-reveal' => "Infinite Scroll + Auto Reveal"
						),
						
						'afolds'	=> true,
						
						'tab_id' 	=> 'portfolio-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> 'Pagination style for endless scroll while loading',
						'id' 		=> 'portfolio_endless_pagination_style',
						'std' 		=> '_1',
						'type' 		=> 'select',
						'options' 	=> $endless_pagination_style,
						
						'afold'		=> 'portfolio_pagination_type:endless,endless-reveal',
						
						'tab_id' 	=> 'portfolio-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> "Number of items to fetch<br><small>{$note_str} Specify custom number of items to fetch when &quot;Show More&quot; is clicked. (Optional)</small>",
						'id' 		=> 'portfolio_endless_pagination_fetch_count',
						'plc' 		=> 'Leave empty to inherit the value',
						'type' 		=> 'text',
						
						'afold'		=> 'portfolio_pagination_type:endless,endless-reveal',
						
						'tab_id' 	=> 'portfolio-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> 'Set pagination position',
						'id' 		=> 'portfolio_pagination_position',
						'std' 		=> 'center',
						'type' 		=> 'select',
						'options' 	=> array('left' => 'Left', 'center' => 'Center', 'right' => 'Right'),
						
						'tab_id' 	=> 'portfolio-settings-loop'
				);
				

// Portfolio: Single
$of_options[] = array( 	'name' 		=> 'Item Options',
						'desc' 		=> "<strong>Next-Prev</strong> navigation in single item",
						'id' 		=> 'portfolio_prev_next',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'folds'		=> 1,
						
						'tab_id'	=> 'portfolio-settings-single'
				);

$of_options[] = array( 	'desc' 		=> 'Next-Prev links to the current category items',
						'id' 		=> 'portfolio_prev_next_category',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'fold'		=> 'portfolio_prev_next',
						
						'tab_id'	=> 'portfolio-settings-single'
				);

$of_options[] = array( 	'desc' 		=> "Show item titles as <strong>next/previous</strong> links",
						'id' 		=> 'portfolio_prev_next_show_titles',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'portfolio-settings-single'
				);

$of_options[] = array( 	'desc' 		=> 'Disable lightbox for images',
						'id' 		=> 'portfolio_disable_lightbox',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'portfolio-settings-single',
				);

$of_options[] = array( 	'desc' 		=> 'Portfolio archive url links to current item category',
						'id' 		=> 'portfolio_archive_url_category',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						
						'afolds'	=> true,
						
						'tab_id'	=> 'portfolio-settings-single',
				);

$of_options[] = array( 	'desc' 		=> "Portfolio archive url (if empty default portfolio archive url will be used)<br><small>{$note_str} This URL will be used in Next-Prev navigation</small>",
						'id' 		=> 'portfolio_archive_url',
						'std' 		=> "",
						'plc'		=> get_post_type_archive_link('portfolio'),
						'type' 		=> 'text',
						'fold'		=> 'portfolio_prev_next',
						
						'afold'		=> 'portfolio_archive_url_category:notChecked',
						
						'tab_id'	=> 'portfolio-settings-single'
				);

$of_options[] = array( 	'desc' 		=> 'Select default Next-Prev design layout',
						'id' 		=> 'portfolio_prev_next_type',
						'std' 		=> 'simple',
						'type' 		=> 'select',
						'options' 	=> array(
							'simple' => 'Simple Next-Prev (in the end of page)',
							'fixed'  => 'Fixed Position Next-Prev',
						),
						'fold'		=> 'portfolio_prev_next',
						
						'afolds'	=> true,
						
						'tab_id'	=> 'portfolio-settings-single'
				);

$of_options[] = array( 	'desc' 		=> "Next-Prev alignment in the browser.<br /><small>{$note_str} This setting is supported for Fixed Position Next-Prev Type only.</small>",
						'id' 		=> 'portfolio_prev_next_position',
						'std' 		=> 'right-side',
						'type' 		=> 'select',
						'options' 	=> array(
							'left-side'  => 'Next-Prev - Left',
							'centered'   => 'Next-Prev - Center',
							'right-side' => 'Next-Prev - Right',
						),
						'fold'		=> 'portfolio_prev_next',
						
						'afold'		=> 'portfolio_prev_next_type:fixed',
						
						'tab_id'	=> 'portfolio-settings-single'
				);

$of_options[] = array( 	'desc' 		=> "Like &amp; share design layout",
						'id' 		=> 'portfolio_like_share_layout',
						'std' 		=> 'default',
						'type' 		=> 'select',
						'options' 	=> array(
							'default'    => 'Plain links',
							'rounded'    => 'Rounded links (circles)',
						),
						
						'tab_id'	=> 'portfolio-settings-single'
				);

$of_options[] = array( 	'desc' 		=> "Gallery image caption position",
						'id' 		=> 'portfolio_gallery_caption_position',
						'std' 		=> 'hover',
						'type' 		=> 'select',
						'options' 	=> array(
							'hover'    => 'Show on hover',
							'below'    => 'Show below image',
						),
						
						'tab_id'	=> 'portfolio-settings-single'
				);


// Lightbox: General
$of_options[] = array( 	'name' 		=> 'Lightbox Portfolio Item Type Settings',
						'desc' 		=> "",
						'id' 		=> 'portfolio_lb',
						'std' 		=> "<h3 style=\"margin: 0 0 10px;\">Information</h3>
						<p>The settings below will be applied to &quot;Lightbox&quot; portfolio items only.</p>",
						'icon' 		=> true,
						'type' 		=> 'info',
						
						'tab_id'	=> 'portfolio-settings-lightbox'
					);

$of_options[] = array( 	'name' 		=> 'Lightbox Navigation',
						'desc' 		=> "Browse mode<br><small>Note: If set to \"Linked\" mode, lightbox previous and next arrows will continue through all portfolio items shown in the list.</small>",
						'id' 		=> 'portfolio_lb_navigation_mode',
						'std' 		=> 'single',
						'type' 		=> 'select',
						'options' 	=> array(
							'single' => 'Single Item',
							'linked' => 'Linked',
						),
						
						'tab_id'	=> 'portfolio-settings-lightbox',
				);
					

$of_options[] = array(	'name' 		=> 'General Settings',
						'desc' 		=> 'Enable fullscreen mode',
						'id' 		=> 'portfolio_lb_fullscreen',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'portfolio-settings-lightbox',
				);
				
$of_options[] = array( 	'desc' 		=> 'Enable captions',
						'id' 		=> 'portfolio_lb_captions',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'portfolio-settings-lightbox',
				);
				
$of_options[] = array( 	'desc' 		=> 'Enable download',
						'id' 		=> 'portfolio_lb_download',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'portfolio-settings-lightbox',
				);
				
$of_options[] = array( 	'desc' 		=> 'Show counter',
						'id' 		=> 'portfolio_lb_counter',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'portfolio-settings-lightbox',
				);
				
$of_options[] = array( 	'desc' 		=> 'Slides are draggable',
						'id' 		=> 'portfolio_lb_draggable',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'portfolio-settings-lightbox',
				);
				
$of_options[] = array( 	'desc' 		=> 'Enable URL hash for items',
						'id' 		=> 'portfolio_lb_hash',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'portfolio-settings-lightbox',
				);
				
$of_options[] = array( 	'desc' 		=> "Loop infinitely (next/prev)",
						'id' 		=> 'portfolio_lb_loop',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'portfolio-settings-lightbox',
				);
				
$of_options[] = array( 	'desc' 		=> 'Enable pager',
						'id' 		=> 'portfolio_lb_pager',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'portfolio-settings-lightbox',
				);

$of_options[] = array( 	'desc' 		=> 'Select lightbox skin',
						'id' 		=> 'portfolio_lb_skin',
						'std' 		=> 'lg-skin-kalium-default',
						'type' 		=> 'select',
						'options' 	=> array(
							'default'        => 'Default',
							'kalium-dark'    => 'Dark',
							'kalium-light'   => 'Light'
						),
						
						'tab_id'	=> 'portfolio-settings-lightbox',
				);
				
$of_options[] = array( 	'desc' 		=> 'Type of transition between images',
						'id' 		=> 'portfolio_lb_mode',
						'std' 		=> 'lg-fade',
						'type' 		=> 'select',
						'options' 	=> array(
							'lg-slide'                       => 'Slide',
							'lg-fade'                        => 'Fade',
							'lg-zoom-in'                     => 'Zoom in',
							'lg-zoom-in-big'                 => 'Zoom in big', 
							'lg-zoom-out'                    => 'Zoom out',
							'lg-zoom-out-big'                => 'Zoom out big', 
							'lg-zoom-out-in'                 => 'Zoom out in',
							'lg-zoom-in-out'                 => 'Zoom in out',
							'lg-soft-zoom'                   => 'Soft zoom', 
							'lg-scale-up'                    => 'Scale up', 
							'lg-slide-circular'              => 'Slide circular', 
							'lg-slide-circular-vertical'     => 'Slide circular vertical', 
							'lg-slide-vertical'              => 'Slide vertical', 
							'lg-slide-vertical-growth'       => 'Slide vertical growth', 
							'lg-slide-skew-only'             => 'Slide skew only', 
							'lg-slide-skew-only-rev'         => 'Slide skew only reverse',
							'lg-slide-skew-only-y'           => 'Slide skew only y', 
							'lg-slide-skew-only-y-rev'       => 'Slide skew only y reverse',
							'lg-slide-skew'                  => 'Slide skew', 
							'lg-slide-skew-rev'              => 'Slide skew reverse',
							'lg-slide-skew-cross'            => 'Slide skew cross', 
							'lg-slide-skew-cross-rev'        => 'Slide skew cross reverse',
							'lg-slide-skew-ver'              => 'Slide skew vertically', 
							'lg-slide-skew-ver-rev'          => 'Slide skew vertically reverse',
							'lg-slide-skew-ver-cross'        => 'Slide skew vertically cross', 
							'lg-slide-skew-ver-cross-rev'    => 'Slide skew vertically cross reverse',
							'lg-lollipop'                    => 'Lollipop', 
							'lg-lollipop-rev'                => 'Lollipop reverse',
							'lg-rotate'                      => 'Rotate', 
							'lg-rotate-rev'                  => 'Rotate reverse',
							'lg-tube'                        => 'Tube',
						),
						
						'tab_id'	=> 'portfolio-settings-lightbox',
				);

$of_options[] = array( 	'desc' 		=> "Transition duration (in seconds)<br><small>{$note_str} Enter numeric value only.</small>",
						'id' 		=> 'portfolio_lb_speed',
						'std' 		=> '',
						'plc'		=> 'Default: 0.6',
						'postfix'	=> 's',
						'type' 		=> 'text',
						'numeric'	=> true,
						
						'tab_id'	=> 'portfolio-settings-lightbox',
				);

$of_options[] = array( 	'desc' 		=> "Delay for hiding gallery controls in seconds<br><small>{$note_str} Enter numeric value only.</small>",
						'id' 		=> 'portfolio_lb_hide_bars_delay',
						'std' 		=> '',
						'plc'		=> 'Default: 3',
						'postfix'	=> 's',
						'type' 		=> 'text',
						'numeric'	=> true,
						
						'tab_id'	=> 'portfolio-settings-lightbox',
				);

$of_options[] = array( 	'desc' 		=> "Image size for gallery items<br><small>{$note_str} Enter defined image size. Click <a href=\"https://codex.wordpress.org/Post_Thumbnails\" target=\"_blank\">here</a> to learn more.</small>",
						'id' 		=> 'portfolio_lb_image_size_large',
						'std' 		=> '',
						'plc'		=> 'Default: original',
						'type' 		=> 'text',
						
						'tab_id'	=> 'portfolio-settings-lightbox'
				);

$of_options[] = array( 	'desc' 		=> "Image size for thumbnails<br><small>{$note_str} Enter defined image size. Click <a href=\"https://codex.wordpress.org/Post_Thumbnails\" target=\"_blank\">here</a> to learn more.</small>",
						'id' 		=> 'portfolio_lb_image_size_thumbnail',
						'std' 		=> '',
						'plc'		=> 'Default: thumbnail',
						'type' 		=> 'text',
						
						'tab_id'	=> 'portfolio-settings-lightbox'
				);

// Lightbox: Thumbnails				
$of_options[] = array( 	'name' 		=> 'Thumbnails',
						'desc' 		=> 'Enable lightbox thumbnails',
						'id' 		=> 'portfolio_lb_thumbnails',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'folds'		=> true,
						
						'tab_id'	=> 'portfolio-settings-lightbox'
				);

$of_options[] = array( 	'desc' 		=> 'One-line thumbnails (swipe nav)',
						'id' 		=> 'portfolio_lb_thumbnails_animated',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'fold'		=> 'portfolio_lb_thumbnails',
						
						'tab_id'	=> 'portfolio-settings-lightbox'
				);

$of_options[] = array( 	'desc' 		=> 'Pull captions above thumbnails',
						'id' 		=> 'portfolio_lb_thumbnails_pullcaptions_up',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'fold'		=> 'portfolio_lb_thumbnails',
						
						'tab_id'	=> 'portfolio-settings-lightbox'
				);

$of_options[] = array( 	'desc' 		=> 'Show thumbnails by default',
						'id' 		=> 'portfolio_lb_thumbnails_show',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						'fold'		=> 'portfolio_lb_thumbnails',
						
						'tab_id'	=> 'portfolio-settings-lightbox'
				);

$of_options[] = array( 	'desc' 		=> "Thumbnail size<br><small>{$note_str} Applied as width value.</small>",
						'id' 		=> 'portfolio_lb_thumbnails_width',
						'std' 		=> '',
						'plc'		=> '100',
						'type' 		=> 'text',
						'postfix' 	=> 'px',
						'numeric'	=> true,
						'fold'		=> 'portfolio_lb_thumbnails',
						
						'tab_id'	=> 'portfolio-settings-lightbox'
				);

$of_options[] = array( 	'desc' 		=> "Thumbnail container height",
						'id' 		=> 'portfolio_lb_thumbnails_container_height',
						'std' 		=> '',
						'plc'		=> '100',
						'type' 		=> 'text',
						'postfix' 	=> 'px',
						'numeric'	=> true,
						'fold'		=> 'portfolio_lb_thumbnails',
						
						'tab_id'	=> 'portfolio-settings-lightbox'
				);

/*TMP$of_options[] = array( 	'desc' 		=> 'Thumbnails pager position',
						'id' 		=> 'portfolio_lb_thumbnails_pager_position',
						'std' 		=> 'middle',
						'type' 		=> 'select',
						'options' 	=> array(
							'left'   => 'Left',
							'middle' => 'Middle',
							'right'  => 'Right'
						),
						'fold'		=> 'portfolio_lb_thumbnails'
				);*/


// Lightbox: AutoPlay
$of_options[] = array( 	'name' 		=> 'Autoplay',
						'desc' 		=> 'Enable gallery autoplay',
						'id' 		=> 'portfolio_lb_autoplay',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'folds'		=> true,
						
						'tab_id'	=> 'portfolio-settings-lightbox'
				);

$of_options[] = array( 	'desc' 		=> "Show/hide autoplay controls",
						'id' 		=> 'portfolio_lb_autoplay_controls',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'fold'		=> 'portfolio_lb_autoplay',
						
						'tab_id'	=> 'portfolio-settings-lightbox'
				);

$of_options[] = array( 	'desc' 		=> 'Enable autoplay progress bar',
						'id' 		=> 'portfolio_lb_autoplay_progressbar',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'fold'		=> 'portfolio_lb_autoplay',
						
						'tab_id'	=> 'portfolio-settings-lightbox'
				);

$of_options[] = array( 	'desc' 		=> 'Force autoplay',
						'id' 		=> 'portfolio_lb_autoplay_force_autoplay',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						'fold'		=> 'portfolio_lb_autoplay',
						
						'tab_id'	=> 'portfolio-settings-lightbox'
				);

$of_options[] = array( 	'desc' 		=> "The time (in seconds) between each auto transition<br><small>{$note_str} Enter numeric value only.</small>",
						'id' 		=> 'portfolio_lb_autoplay_pause',
						'std' 		=> '',
						'plc'		=> 'Default: 5',
						'type' 		=> 'text',
						'numeric'	=> true,
						'fold'		=> 'portfolio_lb_autoplay',
						
						'tab_id'	=> 'portfolio-settings-lightbox'
				);


// Lightbox: Zoom
$of_options[] = array( 	'name' 		=> 'Zoom',
						'desc' 		=> 'Enable zoom option',
						'id' 		=> 'portfolio_lb_zoom',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'folds'		=> true,
						
						'tab_id'	=> 'portfolio-settings-lightbox'
				);

$of_options[] = array( 	'desc' 		=> "Zoom scale<br><small>{$note_str} Value of zoom should be incremented/decremented.</small>",
						'id' 		=> 'portfolio_lb_zoom_scale',
						'std' 		=> '',
						'plc'		=> 'Default: 1',
						'type' 		=> 'text',
						'numeric'	=> true,
						'fold'		=> 'portfolio_lb_zoom',
						
						'tab_id'	=> 'portfolio-settings-lightbox'
				);


$of_options[] = array( 	'name' 		=> 'Share Item',
						'desc' 		=> 'Enable or disable sharing portfolio item on social networks',
						'id' 		=> 'portfolio_share_item',
						'std' 		=> 0,
						'on' 		=> 'Allow Share',
						'off' 		=> 'No',
						'type' 		=> 'switch',
						'folds'		=> 1,
						
						'tab_id' 	=> 'portfolio-settings-sharing'
				);


$share_portfolio_networks = array(
			'visible' => array (
				'placebo'	=> 'placebo',
				'fb'   	 	=> 'Facebook',
				'tw'   	 	=> 'Twitter',
				'pr'   	 	=> 'Print Page',
			),

			'hidden' => array (
				'placebo'   => 'placebo',
				'pi'       	=> 'Pinterest',
				'em'       	=> 'Email',
				'tlr'       => 'Tumblr',
				'lin'       => 'LinkedIn',
				'gp'       	=> 'Google Plus',
				'vk'       	=> 'VKontakte',
			),
);

$of_options[] = array( 	'name' 		=> 'Share on:',
						'desc' 		=> 'Choose social networks that visitors can share your portfolio item',
						'id' 		=> 'portfolio_share_item_networks',
						'std' 		=> $share_portfolio_networks,
						'type' 		=> 'sorter',
						'fold'		=> 'portfolio_share_item',
						
						'tab_id' 	=> 'portfolio-settings-sharing'
				);


$of_options[] = array( 	'name' 		=> "Portfolio Title & Description",
						'desc' 		=> "Show header title and description in portfolio page. <br /><small>{$note_str} This setting is applied only for <a href=\"" . get_post_type_archive_link( 'portfolio' ) . "\" target=\"_blank\">portfolio archive page</a>.</small>",
						'id' 		=> 'portfolio_show_header_title',
						'std' 		=> 1,
						'on' 		=> 'Show',
						'off' 		=> 'Hide',
						'type' 		=> 'switch',
						'folds'		=> 1,
						
						'tab_id' 	=> 'portfolio-settings-other'
				);

$of_options[] = array( 	'desc' 		=> 'Portfolio header title (optional)',
						'id' 		=> 'portfolio_title',
						'std' 		=> 'Portfolio',
						'plc'		=> "",
						'type' 		=> 'text',
						'fold'		=> 'portfolio_show_header_title',
						
						'tab_id' 	=> 'portfolio-settings-other'
				);

$of_options[] = array( 	'desc' 		=> 'Portfolio description in header (optional)',
						'id' 		=> 'portfolio_description',
						'std'		=> "Our everyday work is presented here, we do what we love,".PHP_EOL."Case studies, video presentations and photo-shootings below",
						'plc' 		=> "",
						'type' 		=> 'textarea',
						'fold'		=> 'portfolio_show_header_title',
						
						'tab_id' 	=> 'portfolio-settings-other'
				);

$of_options[] = array( 	'name' 		=> 'Portfolio Tags',
						'desc' 		=> "Enable portfolio tags<br><small>Note: Portfolio tags are used for filtering/grouping of portfolio items available only in admin area.</small>",
						'id' 		=> 'portfolio_enable_tags',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'portfolio-settings-other'
				);
				
// END OF PORTFOLIO SETTINGS


// SHOP SETTINGS
$of_options[] = array( 	'name' 		=> 'Shop Settings',
						'type' 		=> 'heading',
						'icon'		=> 'fa fa-shopping-cart'
				);
				
if ( false == is_shop_supported() ) {
	$of_options[] = array( 	'name' 		=> 'WooCommere Not Activated',
						'desc' 		=> '',
						'id' 		=> 'wc_not_activated',
						'std' 		=> "
						<h3 style=\"margin: 0 0 10px;\">WooCommerce is not installed or activated!</h3>
						To configure <strong>Shop Settings</strong> you need to install <a href=\"".admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' )."\">WooCommerce</a> plugin first.",
						'icon' 		=> true,
						'type' 		=> 'info',
				);
} else {
	


$woocommerce_image_notice = '';
$woocommerce_image_customizer_link = '';

if ( function_exists( 'wc_get_page_permalink' ) ) {
	
	$woocommerce_image_customizer_link = add_query_arg( array(
		'autofocus' => array(
			'panel' => 'woocommerce',
		),
		'url' => wc_get_page_permalink( 'shop' ),
	 ), admin_url( 'customize.php' ) );
	
	$woocommerce_image_notice = wp_kses( sprintf( 'Looking for the product display options? They can now be found in the Customizer. <a href="%s">Go see them in action here.</a>', esc_url( $woocommerce_image_customizer_link ) ), array(
		'a' => array(
			'href'  => array(),
			'title' => array(),
		),
	) );
}	

$of_options[] = array( 	'type' 		=> 'tabs',
						'id'		=> 'shop-settings-tabs',
						'tabs'		=> array(
							'shop-settings-loop'             => 'Catalog Page',
							'shop-settings-single'           => 'Single Page',
							'shop-settings-sharing'          => 'Share Settings',
							'shop-settings-other'            => 'Other Settings',
							'shop-settings-img-dimensions'   => 'Image Dimensions',
						)
				);

$of_options[] = array( 	'name'		=> 'Shop Catalog Layout',
						'desc' 		=> "",
						'id' 		=> 'shop_catalog_layout',
						'std' 		=> 'default',
						'options'	=> array(
							'default'            => kalium()->assetsUrl( 'images/admin/shop-loop-layout-1.png' ),
							'full-bg'            => kalium()->assetsUrl( 'images/admin/shop-loop-layout-2.png' ),
							'distanced-centered' => kalium()->assetsUrl( 'images/admin/shop-loop-layout-3.png' ),
							'transparent-bg'     => kalium()->assetsUrl( 'images/admin/shop-loop-layout-4.png' ),
						),
						'descrs'	=> array(
							'default'            => 'Default',
							'full-bg'            => 'Full Background',
							'distanced-centered' => 'Distanced Bg – Centered',
							'transparent-bg'     => 'Minimal',
						),
						'type' 		=> 'images',
						
						'afolds'	=> true,
						
						'tab_id'	=> 'shop-settings-loop'
				);

$of_options[] = array( 	'name' 		=> 'Catalog Options',
						'desc' 		=> 'Shop head title and results count',
						'id' 		=> 'shop_title_show',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'shop-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> 'Product sorting in catalog page',
						'id' 		=> 'shop_sorting_show',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'shop-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> "Show <strong>sale</strong> badge",
						'id' 		=> 'shop_sale_ribbon_show',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'shop-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> "Show <strong>out of stock</strong> badge",
						'id' 		=> 'shop_oos_ribbon_show',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'shop-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> "Show <strong>featured</strong> badge",
						'id' 		=> 'shop_featured_ribbon_show',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'shop-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> 'Show product category',
						'id' 		=> 'shop_product_category_listing',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'shop-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> 'Show product price',
						'id' 		=> 'shop_product_price_listing',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'shop-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> 'Add to cart product',
						'id' 		=> 'shop_add_to_cart_listing',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						
						'tab_id'	=> 'shop-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> "Products grid layout",
						'id' 		=> 'shop_loop_masonry_layout_mode',
						'std' 		=> 'fitRows',
						'type' 		=> 'select',
						'options' 	=> array(
							'masonry'    => 'Fit gaps (Masonry)',
							'fitRows'    => 'Fit rows'
						),
						
						'tab_id'	=> 'shop-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> 'Product thumbnail preview type',
						'id' 		=> 'shop_item_preview_type',
						'std' 		=> 'fade',
						'type' 		=> 'select',
						'options' 	=> array(
							'fade'       => 'Second Image on Hover',
							'gallery'    => 'Product Gallery Slider',
							'none'       => 'None'
						),
						
						'afold'		=> 'shop_catalog_layout:default',
						
						'tab_id'	=> 'shop-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> "Products to show in mobile devices",
						'id' 		=> 'shop_product_columns_mobile',
						'std' 		=> 'one',
						'type' 		=> 'select',
						'options' 	=> array(
							'one' => '1 product per row',
							'two' => '2 products per row',
						),
						
						'tab_id'	=> 'shop-settings-loop'
				);

$shop_columns_count = array(
	'one'    => '1 product per row',
	'two'    => '2 products per row',
	'three'  => '3 products per row',
	'four'   => '4 products per row',
	'five'   => '5 products per row',
	'six'    => '6 products per row',
);

$of_options[] = array( 	'name' 		=> 'WooCommerce images',
						'desc' 		=> '',
						'id' 		=> 'wc_images_notice',
						'std' 		=> $woocommerce_image_notice,
						'icon' 		=> true,
						'type' 		=> 'info',
						
						'tab_id'	=> 'shop-settings-loop'
				);

$of_options[] = array( 	'name' 		=> 'Sidebar',
						'desc' 		=> "Shop sidebar visibility",
						'id' 		=> 'shop_sidebar',
						'std' 		=> 'hide',
						'type' 		=> 'select',
						'options' 	=> $show_sidebar_options,
						
						'afolds'	=> true,
						
						'tab_id'	=> 'shop-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> 'Show sidebar before products on mobile viewport',
						'id' 		=> 'shop_sidebar_before_products_mobile',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						'afold'		=> 'shop_sidebar:left,right',
						
						'tab_id'	=> 'shop-settings-loop'
				);

function kalium_woocommerce_product_categories_name_replace( $item ) {
	return str_replace( array( 'product per row', 'products per row' ), array( 'category per row', 'categories per row' ), $item );
}


$of_options[] = array( 	'name' 		=> 'Product Categories',
						'desc' 		=> 'Set number of columns for product categories',
						'id' 		=> 'shop_category_columns',
						'std' 		=> 'decide',
						'options'	=> array_map( 'kalium_woocommerce_product_categories_name_replace', $shop_columns_count ),
						'type' 		=> 'select',
						
						'tab_id'	=> 'shop-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> "Category Image Size <br /><small>If you change dimensions you must <a href=\"admin.php?page=laborator-docs#regenerate-thumbnails\" target=\"_blank\">regenerate thumbnails</a>.<br>{$thumbnail_sizes_info}</small>",
						'id' 		=> 'shop_category_image_size',
						'std' 		=> "",
						'plc'		=> 'Default: 500x290',
						'type' 		=> 'text',
						
						'tab_id'	=> 'shop-settings-loop'
				);
				

$of_options[] = array( 	'name' 		=> 'Pagination',
					 	'desc' 		=> 'Select pagination type',
						'id' 		=> 'shop_pagination_type',
						'std' 		=> 'normal',
						'type' 		=> 'select',
						'options' 	=> array(
							'normal'         => 'Normal Pagination',
							'endless'        => 'Infinite Scroll',
							'endless-reveal' => "Infinite Scroll + Auto Reveal"
						),
						
						'afolds'	=> true,
						
						'tab_id'	=> 'shop-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> 'Pagination style for endless scroll while loading',
						'id' 		=> 'shop_endless_pagination_style',
						'std' 		=> '_1',
						'type' 		=> 'select',
						'options' 	=> $endless_pagination_style,
						
						'afold'		=> 'shop_pagination_type:endless,endless-reveal',
						
						'tab_id'	=> 'shop-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> 'Set pagination position',
						'id' 		=> 'shop_pagination_position',
						'std' 		=> 'center',
						'type' 		=> 'select',
						'options' 	=> array('left' => 'Left', 'center' => 'Center', 'right' => 'Right'),
						
						'tab_id'	=> 'shop-settings-loop'
				);

$of_options[] = array( 	'name'		=> 'Catalog mode',
						'desc' 		=> "Enable catalog mode<br /><small>{$note_str} Will disable <strong>add to cart</strong> functionality. <a href='https://documentation.laborator.co/kb/kalium/shop-layout/#catalog-options' target='_blank'>Read more</a></small>",
						'id' 		=> 'shop_catalog_mode',
						'std' 		=> 0,
						'type' 		=> 'switch',
						'folds'		=> true,
						'on'		=> 'Enable',
						'off'		=> 'Disable',
						
						'tab_id'	=> 'shop-settings-loop'
				);

$of_options[] = array( 	'desc' 		=> "Hide prices<br /><small>{$note_str} Show/hide product prices if there is a price set.</small>",
						'id' 		=> 'shop_catalog_mode_hide_prices',
						'std' 		=> 0,
						'type' 		=> 'switch',
						'fold'		=> 'shop_catalog_mode',
						'on'		=> 'Yes',
						'off'		=> 'No',
						
						'tab_id'	=> 'shop-settings-loop'
				);

// ! Shop: Single
$of_options[] = array( 	'name' 		=> 'Product Details',
						'desc' 		=> "",
						'id' 		=> 'shop_single_product_images_layout',
						'std' 		=> 'default',
						'options'	=> array(
							'default'        => kalium()->assetsUrl( 'images/admin/shop-single-product-image-layout-default.png' ),
							'plain'          => kalium()->assetsUrl( 'images/admin/shop-single-product-image-layout-plain.png' ),
							'plain-sticky'   => kalium()->assetsUrl( 'images/admin/shop-single-product-image-layout-plain-sticky.png' ),
						),
						'descrs'	=> array(
							'default'        => 'Main Image with Thumbnails Below',
							'plain'          => 'Plain Images List',
							'plain-sticky'   => 'Sticky Description'
						),
						'type' 		=> 'images',
						
						'afolds'	=> true,
						
						'tab_id'	=> 'shop-settings-single'
				);

$of_options[] = array( 	'desc' 		=> "Product Images Column Size<br /><small>{$note_str} Set the size for product images container.</small>",
						'id' 		=> 'shop_single_image_column_size',
						'std' 		=> 'medium',
						'type' 		=> 'select',
						'options' 	=> array(
							'small'   => "Small (4/12)",
							'medium'  => "Medium (5/12)",
							'large'  => "Large (6/12)",
							'xlarge'  => "Extra Large (8/12)",
						),
						
						'tab_id'	=> 'shop-settings-single'
				);

$of_options[] = array( 	'desc' 		=> "Product Images Alignment<br /><small>{$note_str} Set product images container alignment – left or right.</small>",
						'id' 		=> 'shop_single_image_alignment',
						'std' 		=> 'left',
						'type' 		=> 'select',
						'options' 	=> array(
							'left'   => 'Left',
							'right'  => 'Right'
						),
						
						'tab_id'	=> 'shop-settings-single'
				);

$of_options[] = array( 	'desc' 		=> "Stretch Images to Browser Edge<br /><small>{$note_str} Setting this option to Yes, will stretch the width of images container to the browser edge.</small>",
						'id' 		=> 'shop_single_plain_image_stretch',
						'std' 		=> 'no',
						'type' 		=> 'select',
						'options' 	=> array(
							'yes'    => 'Yes',
							'no'     => 'No'
						),
						
						'afold'		=> 'shop_single_product_images_layout:plain,plain-sticky',
						
						'tab_id'	=> 'shop-settings-single'
				);

$of_options[] = array( 	'desc' 		=> "Image Carousel Transition<br /><small>{$note_str} Select image transition type to apply to main product images.</small>",
						'id' 		=> 'shop_single_image_carousel_transition',
						'std' 		=> 'fade',
						'type' 		=> 'select',
						'options' 	=> array(
							'fade'   => 'Fade',
							'slide'  => 'Slide',
						),
						
						'afold'		=> 'shop_single_product_images_layout:default',
						
						'tab_id'	=> 'shop-settings-single'
				);

$of_options[] = array( 	'desc' 		=> "Auto-Rotate Product Images<br><small>{$note_str} Unit is seconds, default value is <strong>5</strong> seconds, enter <strong>0</strong> to disable auto-rotation.</small>",
						'id' 		=> 'shop_single_auto_rotate_image',
						'std' 		=> "",
						'plc'		=> 'Default value: 5',
						'postfix'	=> 's',
						'type' 		=> 'text',
						'numeric'	=> true,
						
						'afold'		=> 'shop_single_product_images_layout:default',
						
						'tab_id'	=> 'shop-settings-single',
				);

$of_options[] = array( 	'desc' 		=> "Rating Style<br /><small>{$note_str} Select rating style to show for products.</small>",
						'id' 		=> 'shop_single_rating_style',
						'std' 		=> 'circles',
						'type' 		=> 'select',
						'options' 	=> array(
							'stars'      => 'Stars',
							'circles'    => 'Circles',
							'rectangles' => 'Rectangles'
						),
						
						'tab_id'	=> 'shop-settings-single'
				);

$of_options[] = array( 	'desc' 		=> "Related/Up-sells Product Columns<br><small>{$note_str} Set number of columns for related and upsells products only.</small>",
						'id' 		=> 'shop_related_products_columns',
						'std' 		=> 4,
						'type' 		=> 'select',
						'options' 	=> range(2, 6),
						
						'tab_id'	=> 'shop-settings-single'
				);

$of_options[] = array( 	'desc' 		=> "Related Products Count<br><small>{$note_str} Number of related products shown on single product page.</small>",
						'id' 		=> 'shop_related_products_per_page',
						'std' 		=> 4,
						'type' 		=> 'select',
						'options' 	=> range(12, 0),
						
						'tab_id'	=> 'shop-settings-single'
				);

$of_options[] = array( 	'name' 		=> 'Sidebar',
					 	'desc' 		=> "Set sidebar position in single product page or hide it",
						'id' 		=> 'shop_single_sidebar_position',
						'std' 		=> 'hide',
						'type' 		=> 'select',
						'options' 	=> $show_sidebar_options,
						
						'afolds'	=> true,
						
						'tab_id' 	=> 'shop-settings-single',
				);

$of_options[] = array( 	'desc' 		=> 'Show sidebar before products on mobile viewport',
						'id' 		=> 'shop_single_sidebar_before_products_mobile',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						'afold'		=> 'shop_single_sidebar_position:left,right',
						
						'tab_id'	=> 'shop-settings-single'
				);
				
				

$of_options[] = array( 	'name' 		=> 'Product Images Gallery',
					 	'desc' 		=> "Image zoom<br><small class=\"note\">{$note_str} Hovering on product image(s) will magnify the image, also supported on mobile devices.</small>",
						'id' 		=> 'shop_single_product_image_zoom',
						'std' 		=> 1,
						'type' 		=> 'switch',
						
						'tab_id' 	=> 'shop-settings-single',
				);
				

$of_options[] = array( 	'desc' 		=> "Lightbox gallery<br><small class=\"note\">{$note_str} Each product image will contain a + icon to view all product images on lightbox.</small>",
						'id' 		=> 'shop_single_product_image_lightbox',
						'std' 		=> 1,
						'type' 		=> 'switch',
						
						'tab_id' 	=> 'shop-settings-single',
				);
				

$of_options[] = array( 	'name'		=> 'Custom image size',
						'desc' 		=> "Custom image size<br><small class=\"note\">{$note_str} Enabling this option will affect only <strong>main product images</strong>.</small>",
						'id' 		=> 'shop_single_product_custom_image_size',
						'std' 		=> 0,
						'type' 		=> 'switch',
						'folds'		=> true,
						'on'		=> 'Enable',
						'off'		=> 'Disable',
						
						'tab_id' 	=> 'shop-settings-single',
				);


$of_options[] = array( 	'desc' 		=> 'Image width',
						'id' 		=> 'shop_single_product_custom_image_size_width',
						'std' 		=> '',
						'plc'		=> '820',
						'postfix'	=> 'px',
						'type' 		=> 'text',
						'numeric'	=> true,
						
						'fold'		=> 'shop_single_product_custom_image_size',
						
						'tab_id' 	=> 'shop-settings-single'
				);


$of_options[] = array( 	'desc' 		=> 'Image height',
						'id' 		=> 'shop_single_product_custom_image_size_height',
						'std' 		=> '',
						'plc'		=> '990',
						'postfix'	=> 'px',
						'type' 		=> 'text',
						'numeric'	=> true,
						
						'fold'		=> 'shop_single_product_custom_image_size',
						
						'tab_id' 	=> 'shop-settings-single'
				);

$of_options[] = array( 	'name'		=> 'Share Product',
						'desc' 		=> 'Enable product sharing on social networks',
						'id' 		=> 'shop_single_share_product',
						'std' 		=> 1,
						'type' 		=> 'switch',
						'folds'		=> 1,
						
						'on'		=> 'Allow Share',
						'off'		=> 'No',
						
						'tab_id'	=> 'shop-settings-sharing'
				);

$share_product_networks = array(
			'visible' => array (
				'placebo'	=> 'placebo',
				'fb'   	 	=> 'Facebook',
				'tw'   	 	=> 'Twitter',
				'gp'       	=> 'Google Plus',
				'pi'        => 'Pinterest',
				'em'       	=> 'Email',
			),

			'hidden' => array (
				'placebo'   => 'placebo',
				'lin'       => 'LinkedIn',
				'tlr'       => 'Tumblr',
				'vk'        => 'VKontakte',
			),
);

$of_options[] = array( 	'name'		=> 'Share on:',
						'desc' 		=> "Share Product Networks<br><small>{$note_str} Select social networks that you allow users to share the products of your shop</small>",
						'id' 		=> 'shop_share_product_networks',
						'std' 		=> $share_product_networks,
						'type' 		=> 'sorter',
						'options' 	=> array(
							'rows-1' => '1 row',
							'rows-2' => '2 rows',
						),
						'fold'		=> 'shop_single_share_product',
						
						'tab_id'	=> 'shop-settings-sharing'
				);

$of_options[] = array( 	'name' 		=> 'Mini Cart',
						'desc' 		=> 'Show cart icon in menu',
						'id' 		=> 'shop_cart_icon_menu',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						'folds'		=> 1,
						
						'tab_id'	=> 'shop-settings-other'
				);

$of_options[] = array( 	'desc' 		=> 'Products count indicator',
						'id' 		=> 'shop_cart_icon_menu_count',
						'std' 		=> 1,
						'type' 		=> 'checkbox',
						'fold'		=> 'shop_cart_icon_menu',
						
						'tab_id'	=> 'shop-settings-other'
				);

$of_options[] = array( 	'desc' 		=> 'Hide cart icon when its empty',
						'id' 		=> 'shop_cart_icon_menu_hide_empty',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						'fold'		=> 'shop_cart_icon_menu',
						
						'tab_id'	=> 'shop-settings-other'
				);

$of_options[] = array( 	'desc' 		=> "Mini cart contents popup<br><small>{$note_str} Cart popup contains items current items in the cart, Checkout and Cart url.</small>",
						'id' 		=> 'shop_cart_contents',
						'std' 		=> 'show-on-click',
						'type' 		=> 'select',
						'options' 	=> array(
							'hide' => 'Do not show cart contents popup',
							'show-on-click' => 'Show cart contents on click',
							'show-on-hover' => 'Show cart contents on hover',
						),
						'fold'		=> 'shop_cart_icon_menu',
						
						'tab_id'	=> 'shop-settings-other'
				);

$of_options[] = array( 	'desc' 		=> "Cart Icon <br /><small>Select cart icon you want to display in the menu</small>",
						'id' 		=> 'shop_cart_icon',
						'std' 		=> 'ecommerce-cart-content',
						'options'	=> array(							
							'ecommerce-cart-content' => kalium()->assetsUrl( 'images/admin/cart-menu-icon-1.png' ),
							'ecommerce-bag'          => kalium()->assetsUrl( 'images/admin/cart-menu-icon-2.png' ),
							'ecommerce-basket'       => kalium()->assetsUrl( 'images/admin/cart-menu-icon-3.png' ),
						),
						'type' 		=> 'images',
						'fold'		=> 'shop_cart_icon_menu',
						
						'tab_id'	=> 'shop-settings-other'
				);

if ( is_shop_supported() ) {
	
	$of_options[] = array( 	'name' 		=> 'Image Dimensions Info',
							'desc' 		=> "",
							'id' 		=> 'shop_image_dimensions_info',
							'std' 		=> "<h3 style=\"margin: 0 0 10px;\">Shop Image Dimensions</h3>
							<p>As of WooCommerce 3.3 the image settings have been moved in customizer. <a href=\"$woocommerce_image_customizer_link\">Click here</a> to manage WooCommerce image sizes.
							</p>",
							'icon' 		=> true,
							'type' 		=> 'info',
							
							'tab_id'	=> 'shop-settings-img-dimensions'
					);
}
/*

$of_options[] = array( 	'desc' 		=> 'Sidebar visibility (single page)',
						'id' 		=> 'shop_single_sidebar',
						'std' 		=> 'hide',
						'type' 		=> 'select',
						'options' 	=> $show_sidebar_options
				);

$of_options[] = array( 	//'name' 		=> 'Footer Sidebar Columns',
					 	'desc' 		=> "Set the number of columns to show in <strong>footer</strong> sidebar",
						'id' 		=> 'shop_sidebar_footer_columns',
						'std' 		=> '4',
						'type' 		=> 'select',
						'options' 	=> array('2', '3', '4'),
						'fold'		=> 'shop_sidebar_footer'
				);

$of_options[] = array( 	'desc' 		=> "Show <strong>footer</strong> sidebar",
						'id' 		=> 'shop_sidebar_footer',
						'std' 		=> 0,
						'type' 		=> 'checkbox',
						'folds'		=> 1
				);


$of_options[] = array( 	'name' 		=> 'Single Item Settings',
						'desc' 		=> "Show <strong>sale</strong> badge (single page)",
						'id' 		=> 'shop_single_sale_ribbon_show',
						'std' 		=> 1,
						'type' 		=> 'checkbox'
				);

$of_options[] = array( 	'desc' 		=> "Show <strong>out of stock</strong> badge (single page)",
						'id' 		=> 'shop_single_oos_ribbon_show',
						'std' 		=> 1,
						'type' 		=> 'checkbox'
				);

$of_options[] = array( 	'desc' 		=> "Show <strong>featured</strong> badge (single page)",
						'id' 		=> 'shop_single_featured_ribbon_show',
						'std' 		=> 1,
						'type' 		=> 'checkbox'
				);

$of_options[] = array( 	'desc' 		=> "Product <strong>Next-Prev</strong> navigation",
						'id' 		=> 'shop_single_next_prev',
						'std' 		=> 1,
						'type' 		=> 'checkbox'
				);

$of_options[] = array( 	'desc' 		=> "Show product <strong>rating</strong> (below title)",
						'id' 		=> 'shop_single_rating',
						'std' 		=> 1,
						'type' 		=> 'checkbox'
				);

$of_options[] = array( 	'desc' 		=> 'Show product category (below title)',
						'id' 		=> 'shop_single_product_category',
						'std' 		=> 1,
						'type' 		=> 'checkbox'
				);

$of_options[] = array( 	'desc' 		=> "Product meta (id, sku, category and tags)",
						'id' 		=> 'shop_single_meta_show',
						'std' 		=> 1,
						'type' 		=> 'checkbox'
				);

$of_options[] = array( 	'desc' 		=> "Product image size. <br /><small>If you change dimensions you must <a href=\"admin.php?page=laborator-docs#regenerate-thumbnails\">regenerate thumbnails</a>.</small>",
						'id' 		=> 'shop_single_image_size',
						'std' 		=> "",
						'plc'		=> 'Default: 555x710',
						'type' 		=> 'text'
				);

$of_options[] = array( 	'desc' 		=> 'Auto rotate product images',
						'id' 		=> 'shop_single_auto_rotate_image',
						'std' 		=> "",
						'plc'		=> 'Default: 5 (seconds) - 0 to disable',
						'type' 		=> 'text'
				);

$of_options[] = array( 	'desc' 		=> 'Product aside thumbnails to show (they will be splitted)',
						'id' 		=> 'shop_single_aside_thumbnails_count',
						'std' 		=> 5,
						'type' 		=> 'select',
						'options' 	=> range(1, 10)
				);


$of_options[] = array( 	'name' 		=> 'Share Product Networks',
						'desc' 		=> 'Select social networks that you allow users to share the products of your shop',
						'id' 		=> 'shop_share_product_networks',
						'std' 		=> $share_product_networks,
						'type' 		=> 'sorter',
						'fold'		=> 'shop_share_product'
				);

$of_options[] = array( 	'name'		=> 'Category Settings',
						'desc' 		=> 'Category columns per row',
						'id' 		=> 'shop_category_columns',
						'std' 		=> 4,
						'type' 		=> 'select',
						'options' 	=> range(2, 4)
				);

$of_options[] = array( 	'desc' 		=> 'Show items count for category (category page)',
						'id' 		=> 'shop_category_count',
						'std' 		=> 1,
						'type' 		=> 'checkbox'
				);
*/
}
// END OF SHOP SETTINGS


// OTHER SETTINGS
$of_options[] = array( 	'name' 		=> 'Other Settings',
						'type' 		=> 'heading',
						'icon'		=> 'fa fa-gears'
				);

$of_options[] = array( 	'type' 		=> 'tabs',
						'id'		=> 'other-settings-tabs',
						'tabs'		=> array(
							'other-settings-misc'                        => 'Miscellaneous',
							'other-settings-search'                      => 'Search Settings',
							'other-settings-video-audio-settings'        => 'Video &amp; Audio Settings',
							'other-settings-image-loading-placeholder'   => 'Image Loading Placeholder',
						)
				);
				
$of_options[] = array(  'name'		=> 'Theme Style File (style.css)',
						'desc'   	=> 'Disable enqueue of style.css of the theme',
						'id'   		=> 'do_not_enqueue_style_css',
						'std'   	=> 0,
						'type'   	=> 'checkbox',
						
						'tab_id'	=> 'other-settings-misc',
					);
				
$of_options[] = array( 	'name' 		=> 'Go to Top',
						'desc' 		=> "Show &quot;Go to Top&quot; button when users scroll down to the page",
						'id' 		=> 'footer_go_to_top',
						'std' 		=> 0,
						'on' 		=> 'Show',
						'off' 		=> 'Hide',
						'type' 		=> 'switch',
						'folds'		=> 1,
						
						'tab_id'	=> 'other-settings-misc'
				);

$of_options[] = array( 	'desc' 		=> "Set number of pixels or percentage of window user needs to scroll when &quot;Go to Top&quot; link will be shown<br><small>{$note_str} If you set value to <strong>footer</strong>, link will appear only when user sees footer.</small>",
						'id' 		=> 'footer_go_to_top_activate',
						'std' 		=> 'footer',
						'plc'		=> "",
						'type' 		=> 'text',
						'fold'		=> 'footer_go_to_top',
						
						'tab_id'	=> 'other-settings-misc'
				);

$of_options[] = array( 	'desc' 		=> 'Box type for "Go to top" button',
						'id' 		=> 'footer_go_to_top_type',
						'std' 		=> 'circle',
						'type' 		=> 'select',
						'options' 	=> array(
							'square' => 'Square',
							'circle' => 'Circle',
						),
						'fold'		=> 'footer_go_to_top',
						
						'tab_id'	=> 'other-settings-misc'
				);

$of_options[] = array( 	'desc' 		=> 'Link position',
						'id' 		=> 'footer_go_to_top_position',
						'std' 		=> 'bottom-right',
						'type' 		=> 'select',
						'options' 	=> array(
							'bottom-right'   => 'Bottom Right',
							'bottom-left'    => 'Bottom Left',
							'bottom-center'  => 'Bottom Center',
							
							'top-right'      => 'Top Right',
							'top-left'       => 'Top Left',
							'top-center'     => 'Top Center',
						),
						'fold'		=> 'footer_go_to_top',
						
						'tab_id'	=> 'other-settings-misc'
				);

$of_options[] = array( 	'name' 		=> 'Sidebar Skin',
						'desc' 		=> 'Select the style for site widgets<br><small>' . $note_str . ' Sidebar skin will not applied for footer or top menu widgets</small>',
						'id' 		=> 'sidebar_skin',
						'std' 		=> 'plain',
						'type' 		=> 'select',
						'options' 	=> array(
							'plain'           => 'Plain',
							'bordered'        => 'Bordered',
							'background-fill' => 'Background fill'
							
						),
						
						'tab_id'	=> 'other-settings-misc'
				);


$of_options[] = array( 	'name'		=> 'Google Maps API Key',
						'desc' 		=> 'Google maps requires unique API key for each site, click here to learn more about generating <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">Google API Key</a>',
						'id' 		=> 'google_maps_api',
						'std' 		=> '',
						'plc'		=> '',
						'type' 		=> 'text',
						
						'tab_id'	=> 'other-settings-misc'
				);



				
$of_options[] = array( 	'name' 		=> 'Thumbnails for Results',
						'desc' 		=> "Show post featured image next to search results",
						'id' 		=> 'search_thumbnails',
						'std' 		=> 1,
						'on' 		=> 'Show',
						'off' 		=> 'Hide',
						'type' 		=> 'switch',
						
						'tab_id'	=> 'other-settings-search'
				);

$post_types_obj = get_post_types(array('_builtin' => false, 'publicly_queryable' => true, 'exclude_from_search' => false), 'objects');

$post_types = array();

$post_types['post'] = 'Posts';
$post_types['page'] = 'Pages';

foreach($post_types_obj as $pt => $obj)
{
	$post_types[$pt] = $obj->labels->name;
}


$of_options[] = array( 	'name'		=> 'Exclude Post Types',
						'desc' 		=> 'Select post types to exclude from search results',
						'id' 		=> 'exclude_search_post_types',
						'std' 		=> array(),
						'type' 		=> 'multicheck',
						'options' 	=> $post_types,
						
						'tab_id'	=> 'other-settings-search'
				);

$of_options[] = array( 	'name'		=> "Video &amp; Audio Player",
						'desc' 		=> "Select default video &amp; audio player skin to use<br><small>{$note_str} This replaces default WordPress player for audio and video embeds.</small>",
						'id' 		=> 'videojs_player_skin',
						'std' 		=> 'minimal',
						'options'	=> array(
							'standard'   => 'Standard Skin',
							'minimal'    => 'Minimal Skin',
						),
						'type' 		=> 'select',
						
						'tab_id'	=> 'other-settings-video-audio-settings'
				);

$of_options[] = array( 	'desc' 		=> "Preload Video Embeds<br><small>{$note_str} To learn more about video pre-loading <a href=\"http://www.stevesouders.com/blog/2013/04/12/html5-video-preload/\" target=\"_blank\">click here</a>.</small>",
						'id' 		=> 'videojs_player_preload',
						'std' 		=> 'auto',
						'options'	=> array(
							'auto'       => 'Auto',
							'none'       => 'None',
							'metadata'   => 'Preload only meta data',
						),
						'type' 		=> 'select',
						
						'tab_id'	=> 'other-settings-video-audio-settings'
				);
				
$of_options[] = array(  'desc'   	=> "Auto Play Videos<br><small>{$note_str} Enabling this option will auto-play all videos you post.</small>",
						'id'   		=> 'videojs_player_autoplay',
						'std'   	=> 'no',
						'options'	=> array(
							'no'             => 'Disable',
							'yes'            => 'Enable',
							'on-viewport'    => 'Auto-play on viewport'
						),
						'type'   	=> 'select',
						
						'tab_id'	=> 'other-settings-video-audio-settings',
					);
				
$of_options[] = array(  'desc'   	=> "Loop Videos<br><small>{$note_str} Videos will restart after they end (infinite looping).</small>",
						'id'   		=> 'videojs_player_loop',
						'std'   	=> 'no',
						'options'	=> array(
							'no'     => 'Disable',
							'yes'    => 'Enable',
						),
						'type'   	=> 'select',
						
						'tab_id'	=> 'other-settings-video-audio-settings',
					);
				
$of_options[] = array(  'desc'   	=> "Share video<br><small>{$note_str} Enable/disable video sharing to social networks. Not applied for native YouTube player.</small>",
						'id'   		=> 'videojs_share',
						'std'   	=> 'no',
						'options'	=> array(
							'yes' => 'Enable',
							'no'  => 'Disable',
						),
						'type'   	=> 'select',
						
						'tab_id'	=> 'other-settings-video-audio-settings',
					);
					
				
$of_options[] = array(  'desc'   	=> "YouTube player<br><small>{$note_str} Select YouTube player to use.</small>",
						'id'   		=> 'youtube_player',
						'std'   	=> 'videojs',
						'options'	=> array(
							'videojs' => 'VideoJS YouTube Player',
							'native'  => 'Native YouTube Player',
						),
						'type'   	=> 'select',
						
						'tab_id'	=> 'other-settings-video-audio-settings',
					);
					

$of_options[] = array( 	'name'		=> 'Image Loading Placeholder Type',
						'desc' 		=> "",
						'id' 		=> 'image_loading_placeholder_type',
						'std' 		=> 'static-color',
						'options'	=> array(
							'static-color'   => kalium()->assetsUrl( 'images/admin/image-placeholder-color.png' ) ,
							'preselected'    => kalium()->assetsUrl( 'images/admin/image-placeholder-preloaders.png' ),
							'custom'         => kalium()->assetsUrl( 'images/admin/image-placeholder-custom.png' ),
						),
						'descrs'	=> array(
							'static-color'   => 'Static Color',
							'preselected'    => 'Preselected Loaders',
							'custom'         => 'Custom Preloader',
						),
						'type' 		=> 'images',
						
						'afolds'	=> true,
						
						'tab_id'	=> 'other-settings-image-loading-placeholder'
				);

$of_options[] = array(	'name'		=> 'Placeholder Background Color',
						'desc'   	=> 'Placeholder color',
						'id'   		=> 'image_loading_placeholder_bg',
						'std'   	=> '#eeeeee',
						'type'   	=> 'color',
						
						'afold'		=> '',
						
						'tab_id'	=> 'other-settings-image-loading-placeholder',
					);

$of_options[] = array( 	'desc' 		=> 'Use gradient',
						'id' 		=> 'image_loading_placeholder_use_gradient',
						'std' 		=> 0,
						'type' 		=> 'switch',
						'on'		=> 'Yes',
						'off'		=> 'No',
						
						'folds'		=> true,
						
						'afold'		=> '',
						
						'tab_id'	=> 'other-settings-image-loading-placeholder',
				);

$of_options[] = array( 	'desc' 		=> 'Dominant image color<br><small>Note: Enabling this option, will ignore gradient and background color.</small>',
						'id' 		=> 'image_loading_placeholder_dominant_color',
						'std' 		=> 0,
						'type' 		=> 'switch',
						'on'		=> 'Yes',
						'off'		=> 'No',
						
						'folds'		=> true,
						
						'afold'		=> '',
						
						'tab_id'	=> 'other-settings-image-loading-placeholder',
				);
				
$of_options[] = array(  'name'		=> 'Gradient Color',
						'desc'   	=> 'Gradient type',
						'id'   		=> 'image_loading_placeholder_gradient_type',
						'std'   	=> 'fade',
						'options'	=> array(
							'linear' => 'Linear',
							'radial' => 'Radial',
						),
						'type'   	=> 'select',
						
						'fold'		=> 'image_loading_placeholder_use_gradient',
						
						'afold'		=> '',
						
						'tab_id'	=> 'other-settings-image-loading-placeholder',
					);

$of_options[] = array(	'desc'   	=> 'Gradient color',
						'id'   		=> 'image_loading_placeholder_gradient_bg',
						'std'   	=> '#8d8d8d',
						'type'   	=> 'color',
						
						'fold'		=> 'image_loading_placeholder_use_gradient',
						
						'afold'		=> '',
						
						'tab_id'	=> 'other-settings-image-loading-placeholder',
					);

if ( is_admin() && 'admin.php' == $pagenow ) {
	$loaders_html       = '';
	$loading_spinners = Kalium_Image_Loading_Spinner::getSpinners();
	$loading_spinners_values = array();
	
	$current_spinner    = get_data( 'image_loading_placeholder_preselected_loader' );
	
	foreach ( $loading_spinners as $spinner_id => $spinner ) {
		$loading_spinners_values[ $spinner_id ] = $spinner['name'];
	}
	
	if ( ! $current_spinner ) {
		$current_spinner = reset( array_keys( $loading_spinners_values ) );
	}
	
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		foreach ( $loading_spinners as $spinner_id => $spinner_name ) {
			$spinner = Kalium_Image_Loading_Spinner::getSpinnerById( $spinner_id, array(
				'holder' => 'div',
			) );
			
			if ( $current_spinner == $spinner_id ) {
				$spinner = str_replace( '"loader"', '"loader current"', $spinner );
			}
			
			$loaders_html .= $spinner;
		}
	}
	
	$loaders_html = '<div class="loaders clearfix">' . $loaders_html . '</div>';
} else {
	$current_spinner = $loaders_html = '';
	$loading_spinners_values = array();
}


$loaders_position = array(
	'top-left'       => 'Top Left',
	'top-center'     => 'Top Center',
	'top-right'      => 'Top Right',
	'center-left'    => 'Center Left',
	'center'         => 'Centered',
	'center-right'   => 'Center Right',
	'bottom-left'    => 'Bottom Left',
	'bottom-center'  => 'Bottom Center',
	'bottom-right'   => 'Bottom Right',
);

$of_options[] = array( 	'name'		=> 'Preselected Loaders',
						'desc' 		=> 'See the below list of loaders and select default image loading spinner.',
						'id' 		=> 'image_loading_placeholder_preselected_loader',
						'std' 		=> $current_spinner,
						'type' 		=> 'select',
						'options' 	=> $loading_spinners_values,
						
						'afold'		=> 'image_loading_placeholder_type:preselected',
						
						'tab_id' 	=> 'other-settings-image-loading-placeholder'
				);

$of_options[] = array( 	'desc' 		=> 'Position of loader/spinner',
						'id' 		=> 'image_loading_placeholder_preselected_loader_position',
						'std' 		=> 'center',
						'type' 		=> 'select',
						'options' 	=> $loaders_position,
						
						'afold'		=> 'image_loading_placeholder_type:preselected',
						
						'tab_id' 	=> 'other-settings-image-loading-placeholder'
				);


$of_options[] = array( 	'desc' 		=> 'Loader size (in percentage scale)<br><small>' . $note_str . ' The actual size of selected loader is shown as 100% of its size.</small>',
						'id' 		=> 'image_loading_placeholder_preselected_size',
						'std' 		=> '',
						'plc'		=> '100',
						'postfix'	=> '%',
						'type' 		=> 'text',
						'numeric'	=> true,
						
						'afold'		=> 'image_loading_placeholder_type:preselected',
						
						'tab_id' 	=> 'other-settings-image-loading-placeholder'
				);


$of_options[] = array( 	'desc' 		=> 'Loader spacing (padding)<br><small>' . $note_str . ' Depending on loader size, this option will help you to place correctly the loader inside the thumbnail.</small>',
						'id' 		=> 'image_loading_placeholder_preselected_spacing',
						'std' 		=> '',
						'plc'		=> '20',
						'postfix'	=> 'px',
						'type' 		=> 'text',
						'numeric'	=> true,
						
						'afold'		=> 'image_loading_placeholder_type:preselected',
						
						'tab_id' 	=> 'other-settings-image-loading-placeholder'
				);
				

$of_options[] = array(	'desc'   	=> 'Spinner color<br><small>' . $note_str . ' This option will change the color of animated spinners.</small>',
						'id'   		=> 'image_loading_placeholder_preselected_loader_color',
						'std'   	=> '#ffffff',
						'type'   	=> 'color',
						
						'afold'		=> 'image_loading_placeholder_type:preselected',
						
						'tab_id'	=> 'other-settings-image-loading-placeholder',
					);
				


$of_options[] = array( 	'name' 		=> 'Preselected Loaders',
						'desc' 		=> '',
						'id' 		=> 'image_loading_placeholder_preselected',
						'std' 		=> "<h3 style=\"margin: 0 0 10px;\">Preselected Loaders List</h3>
						Here is the list of predefined loaders/spinners you can use for Image Loading Placeholder elements:" . $loaders_html,
						'icon' 		=> true,
						'type' 		=> 'info',
						
						'afold'		=> 'image_loading_placeholder_type:preselected',
						
						'tab_id' 	=> 'other-settings-image-loading-placeholder'
				);
				

$of_options[] = array( 	'name'		=> 'Custom Preloader',
						'desc' 		=> 'Loading image<br><small>' . $note_str . ' Select animated loading spinner, GIF format is supported.</small>',
						'id' 		=> 'image_loading_placeholder_custom_image',
						'std' 		=> '',
						'type' 		=> 'media',
						'mod' 		=> 'min',
						'afold'		=> 'image_loading_placeholder_type:custom',
						
						'tab_id' 	=> 'other-settings-image-loading-placeholder'
				);

$of_options[] = array( 	'desc' 		=> 'Set the width for loader image<br><small>' . $note_str . ' If set empty, actual image size will be shown.</small>',
						'id' 		=> 'image_loading_placeholder_custom_image_width',
						'plc' 		=> 'Actual size',
						'type' 		=> 'text',
						'numeric'	=> true,
						'postfix'	=> 'px',
						'afold'		=> 'image_loading_placeholder_type:custom',
						
						'tab_id' 	=> 'other-settings-image-loading-placeholder'
				);

$of_options[] = array( 	'desc' 		=> 'Position of loader/spinner',
						'id' 		=> 'image_loading_placeholder_custom_loader_position',
						'std' 		=> 'center',
						'type' 		=> 'select',
						'options' 	=> $loaders_position,
						
						'afold'		=> 'image_loading_placeholder_type:custom',
						
						'tab_id' 	=> 'other-settings-image-loading-placeholder'
				);

$of_options[] = array( 	'desc' 		=> 'Loader spacing<br><small>' . $note_str . ' Depending on loader size, this option will help you to place correctly the loader inside the thumbnail.</small>',
						'id' 		=> 'image_loading_placeholder_custom_spacing',
						'std' 		=> '',
						'plc'		=> '20',
						'postfix'	=> 'px',
						'type' 		=> 'text',
						'numeric'	=> true,
						
						'afold'		=> 'image_loading_placeholder_type:custom',
						
						'tab_id' 	=> 'other-settings-image-loading-placeholder'
				);
// END OF OTHER SETTINGS


$fonts_list = array(
	'ABeeZee'  => 'ABeeZee',
	'Abel'  => 'Abel',
	'Abril Fatface'  => 'Abril Fatface',
	'Aclonica'  => 'Aclonica',
	'Acme'  => 'Acme',
	'Actor'  => 'Actor',
	'Adamina'  => 'Adamina',
	'Advent Pro'  => 'Advent Pro',
	'Aguafina Script'  => 'Aguafina Script',
	'Akronim'  => 'Akronim',
	'Aladin'  => 'Aladin',
	'Aldrich'  => 'Aldrich',
	'Alef'  => 'Alef',
	'Alegreya'  => 'Alegreya',
	'Alegreya SC'  => 'Alegreya SC',
	'Alegreya Sans'  => 'Alegreya Sans',
	'Alegreya Sans SC'  => 'Alegreya Sans SC',
	'Alex Brush'  => 'Alex Brush',
	'Alfa Slab One'  => 'Alfa Slab One',
	'Alice'  => 'Alice',
	'Alike'  => 'Alike',
	'Alike Angular'  => 'Alike Angular',
	'Allan'  => 'Allan',
	'Allerta'  => 'Allerta',
	'Allerta Stencil'  => 'Allerta Stencil',
	'Allura'  => 'Allura',
	'Almendra'  => 'Almendra',
	'Almendra Display'  => 'Almendra Display',
	'Almendra SC'  => 'Almendra SC',
	'Amarante'  => 'Amarante',
	'Amaranth'  => 'Amaranth',
	'Amatic SC'  => 'Amatic SC',
	'Amatica SC'  => 'Amatica SC',
	'Amethysta'  => 'Amethysta',
	'Amiko'  => 'Amiko',
	'Amiri'  => 'Amiri',
	'Amita'  => 'Amita',
	'Anaheim'  => 'Anaheim',
	'Andada'  => 'Andada',
	'Andika'  => 'Andika',
	'Angkor'  => 'Angkor',
	'Annie Use Your Telescope'  => 'Annie Use Your Telescope',
	'Anonymous Pro'  => 'Anonymous Pro',
	'Antic'  => 'Antic',
	'Antic Didone'  => 'Antic Didone',
	'Antic Slab'  => 'Antic Slab',
	'Anton'  => 'Anton',
	'Arapey'  => 'Arapey',
	'Arbutus'  => 'Arbutus',
	'Arbutus Slab'  => 'Arbutus Slab',
	'Architects Daughter'  => 'Architects Daughter',
	'Archivo Black'  => 'Archivo Black',
	'Archivo Narrow'  => 'Archivo Narrow',
	'Aref Ruqaa'  => 'Aref Ruqaa',
	'Arima Madurai'  => 'Arima Madurai',
	'Arimo'  => 'Arimo',
	'Arizonia'  => 'Arizonia',
	'Armata'  => 'Armata',
	'Artifika'  => 'Artifika',
	'Arvo'  => 'Arvo',
	'Arya'  => 'Arya',
	'Asap'  => 'Asap',
	'Asar'  => 'Asar',
	'Asset'  => 'Asset',
	'Assistant'  => 'Assistant',
	'Astloch'  => 'Astloch',
	'Asul'  => 'Asul',
	'Athiti'  => 'Athiti',
	'Atma'  => 'Atma',
	'Atomic Age'  => 'Atomic Age',
	'Aubrey'  => 'Aubrey',
	'Audiowide'  => 'Audiowide',
	'Autour One'  => 'Autour One',
	'Average'  => 'Average',
	'Average Sans'  => 'Average Sans',
	'Averia Gruesa Libre'  => 'Averia Gruesa Libre',
	'Averia Libre'  => 'Averia Libre',
	'Averia Sans Libre'  => 'Averia Sans Libre',
	'Averia Serif Libre'  => 'Averia Serif Libre',
	'Bad Script'  => 'Bad Script',
	'Baloo'  => 'Baloo',
	'Baloo Bhai'  => 'Baloo Bhai',
	'Baloo Bhaina'  => 'Baloo Bhaina',
	'Baloo Chettan'  => 'Baloo Chettan',
	'Baloo Da'  => 'Baloo Da',
	'Baloo Paaji'  => 'Baloo Paaji',
	'Baloo Tamma'  => 'Baloo Tamma',
	'Baloo Thambi'  => 'Baloo Thambi',
	'Balthazar'  => 'Balthazar',
	'Bangers'  => 'Bangers',
	'Basic'  => 'Basic',
	'Battambang'  => 'Battambang',
	'Baumans'  => 'Baumans',
	'Bayon'  => 'Bayon',
	'Belgrano'  => 'Belgrano',
	'Belleza'  => 'Belleza',
	'BenchNine'  => 'BenchNine',
	'Bentham'  => 'Bentham',
	'Berkshire Swash'  => 'Berkshire Swash',
	'Bevan'  => 'Bevan',
	'Bigelow Rules'  => 'Bigelow Rules',
	'Bigshot One'  => 'Bigshot One',
	'Bilbo'  => 'Bilbo',
	'Bilbo Swash Caps'  => 'Bilbo Swash Caps',
	'BioRhyme'  => 'BioRhyme',
	'BioRhyme Expanded'  => 'BioRhyme Expanded',
	'Biryani'  => 'Biryani',
	'Bitter'  => 'Bitter',
	'Black Ops One'  => 'Black Ops One',
	'Bokor'  => 'Bokor',
	'Bonbon'  => 'Bonbon',
	'Boogaloo'  => 'Boogaloo',
	'Bowlby One'  => 'Bowlby One',
	'Bowlby One SC'  => 'Bowlby One SC',
	'Brawler'  => 'Brawler',
	'Bree Serif'  => 'Bree Serif',
	'Bubblegum Sans'  => 'Bubblegum Sans',
	'Bubbler One'  => 'Bubbler One',
	'Buda'  => 'Buda',
	'Buenard'  => 'Buenard',
	'Bungee'  => 'Bungee',
	'Bungee Hairline'  => 'Bungee Hairline',
	'Bungee Inline'  => 'Bungee Inline',
	'Bungee Outline'  => 'Bungee Outline',
	'Bungee Shade'  => 'Bungee Shade',
	'Butcherman'  => 'Butcherman',
	'Butterfly Kids'  => 'Butterfly Kids',
	'Cabin'  => 'Cabin',
	'Cabin Condensed'  => 'Cabin Condensed',
	'Cabin Sketch'  => 'Cabin Sketch',
	'Caesar Dressing'  => 'Caesar Dressing',
	'Cagliostro'  => 'Cagliostro',
	'Cairo'  => 'Cairo',
	'Calligraffitti'  => 'Calligraffitti',
	'Cambay'  => 'Cambay',
	'Cambo'  => 'Cambo',
	'Candal'  => 'Candal',
	'Cantarell'  => 'Cantarell',
	'Cantata One'  => 'Cantata One',
	'Cantora One'  => 'Cantora One',
	'Capriola'  => 'Capriola',
	'Cardo'  => 'Cardo',
	'Carme'  => 'Carme',
	'Carrois Gothic'  => 'Carrois Gothic',
	'Carrois Gothic SC'  => 'Carrois Gothic SC',
	'Carter One'  => 'Carter One',
	'Catamaran'  => 'Catamaran',
	'Caudex'  => 'Caudex',
	'Caveat'  => 'Caveat',
	'Caveat Brush'  => 'Caveat Brush',
	'Cedarville Cursive'  => 'Cedarville Cursive',
	'Ceviche One'  => 'Ceviche One',
	'Changa'  => 'Changa',
	'Changa One'  => 'Changa One',
	'Chango'  => 'Chango',
	'Chathura'  => 'Chathura',
	'Chau Philomene One'  => 'Chau Philomene One',
	'Chela One'  => 'Chela One',
	'Chelsea Market'  => 'Chelsea Market',
	'Chenla'  => 'Chenla',
	'Cherry Cream Soda'  => 'Cherry Cream Soda',
	'Cherry Swash'  => 'Cherry Swash',
	'Chewy'  => 'Chewy',
	'Chicle'  => 'Chicle',
	'Chivo'  => 'Chivo',
	'Chonburi'  => 'Chonburi',
	'Cinzel'  => 'Cinzel',
	'Cinzel Decorative'  => 'Cinzel Decorative',
	'Clicker Script'  => 'Clicker Script',
	'Coda'  => 'Coda',
	'Coda Caption'  => 'Coda Caption',
	'Codystar'  => 'Codystar',
	'Coiny'  => 'Coiny',
	'Combo'  => 'Combo',
	'Comfortaa'  => 'Comfortaa',
	'Coming Soon'  => 'Coming Soon',
	'Concert One'  => 'Concert One',
	'Condiment'  => 'Condiment',
	'Content'  => 'Content',
	'Contrail One'  => 'Contrail One',
	'Convergence'  => 'Convergence',
	'Cookie'  => 'Cookie',
	'Copse'  => 'Copse',
	'Corben'  => 'Corben',
	'Cormorant'  => 'Cormorant',
	'Cormorant Garamond'  => 'Cormorant Garamond',
	'Cormorant Infant'  => 'Cormorant Infant',
	'Cormorant SC'  => 'Cormorant SC',
	'Cormorant Unicase'  => 'Cormorant Unicase',
	'Cormorant Upright'  => 'Cormorant Upright',
	'Courgette'  => 'Courgette',
	'Cousine'  => 'Cousine',
	'Coustard'  => 'Coustard',
	'Covered By Your Grace'  => 'Covered By Your Grace',
	'Crafty Girls'  => 'Crafty Girls',
	'Creepster'  => 'Creepster',
	'Crete Round'  => 'Crete Round',
	'Crimson Text'  => 'Crimson Text',
	'Croissant One'  => 'Croissant One',
	'Crushed'  => 'Crushed',
	'Cuprum'  => 'Cuprum',
	'Cutive'  => 'Cutive',
	'Cutive Mono'  => 'Cutive Mono',
	'Damion'  => 'Damion',
	'Dancing Script'  => 'Dancing Script',
	'Dangrek'  => 'Dangrek',
	'David Libre'  => 'David Libre',
	'Dawning of a New Day'  => 'Dawning of a New Day',
	'Days One'  => 'Days One',
	'Dekko'  => 'Dekko',
	'Delius'  => 'Delius',
	'Delius Swash Caps'  => 'Delius Swash Caps',
	'Delius Unicase'  => 'Delius Unicase',
	'Della Respira'  => 'Della Respira',
	'Denk One'  => 'Denk One',
	'Devonshire'  => 'Devonshire',
	'Dhurjati'  => 'Dhurjati',
	'Didact Gothic'  => 'Didact Gothic',
	'Diplomata'  => 'Diplomata',
	'Diplomata SC'  => 'Diplomata SC',
	'Domine'  => 'Domine',
	'Donegal One'  => 'Donegal One',
	'Doppio One'  => 'Doppio One',
	'Dorsa'  => 'Dorsa',
	'Dosis'  => 'Dosis',
	'Dr Sugiyama'  => 'Dr Sugiyama',
	'Droid Sans'  => 'Droid Sans',
	'Droid Sans Mono'  => 'Droid Sans Mono',
	'Droid Serif'  => 'Droid Serif',
	'Duru Sans'  => 'Duru Sans',
	'Dynalight'  => 'Dynalight',
	'EB Garamond'  => 'EB Garamond',
	'Eagle Lake'  => 'Eagle Lake',
	'Eater'  => 'Eater',
	'Economica'  => 'Economica',
	'Eczar'  => 'Eczar',
	'Ek Mukta'  => 'Ek Mukta',
	'El Messiri'  => 'El Messiri',
	'Electrolize'  => 'Electrolize',
	'Elsie'  => 'Elsie',
	'Elsie Swash Caps'  => 'Elsie Swash Caps',
	'Emblema One'  => 'Emblema One',
	'Emilys Candy'  => 'Emilys Candy',
	'Engagement'  => 'Engagement',
	'Englebert'  => 'Englebert',
	'Enriqueta'  => 'Enriqueta',
	'Erica One'  => 'Erica One',
	'Esteban'  => 'Esteban',
	'Euphoria Script'  => 'Euphoria Script',
	'Ewert'  => 'Ewert',
	'Exo'  => 'Exo',
	'Exo 2'  => 'Exo 2',
	'Expletus Sans'  => 'Expletus Sans',
	'Fanwood Text'  => 'Fanwood Text',
	'Farsan'  => 'Farsan',
	'Fascinate'  => 'Fascinate',
	'Fascinate Inline'  => 'Fascinate Inline',
	'Faster One'  => 'Faster One',
	'Fasthand'  => 'Fasthand',
	'Fauna One'  => 'Fauna One',
	'Federant'  => 'Federant',
	'Federo'  => 'Federo',
	'Felipa'  => 'Felipa',
	'Fenix'  => 'Fenix',
	'Finger Paint'  => 'Finger Paint',
	'Fira Mono'  => 'Fira Mono',
	'Fira Sans'  => 'Fira Sans',
	'Fjalla One'  => 'Fjalla One',
	'Fjord One'  => 'Fjord One',
	'Flamenco'  => 'Flamenco',
	'Flavors'  => 'Flavors',
	'Fondamento'  => 'Fondamento',
	'Fontdiner Swanky'  => 'Fontdiner Swanky',
	'Forum'  => 'Forum',
	'Francois One'  => 'Francois One',
	'Frank Ruhl Libre'  => 'Frank Ruhl Libre',
	'Freckle Face'  => 'Freckle Face',
	'Fredericka the Great'  => 'Fredericka the Great',
	'Fredoka One'  => 'Fredoka One',
	'Freehand'  => 'Freehand',
	'Fresca'  => 'Fresca',
	'Frijole'  => 'Frijole',
	'Fruktur'  => 'Fruktur',
	'Fugaz One'  => 'Fugaz One',
	'GFS Didot'  => 'GFS Didot',
	'GFS Neohellenic'  => 'GFS Neohellenic',
	'Gabriela'  => 'Gabriela',
	'Gafata'  => 'Gafata',
	'Galada'  => 'Galada',
	'Galdeano'  => 'Galdeano',
	'Galindo'  => 'Galindo',
	'Gentium Basic'  => 'Gentium Basic',
	'Gentium Book Basic'  => 'Gentium Book Basic',
	'Geo'  => 'Geo',
	'Geostar'  => 'Geostar',
	'Geostar Fill'  => 'Geostar Fill',
	'Germania One'  => 'Germania One',
	'Gidugu'  => 'Gidugu',
	'Gilda Display'  => 'Gilda Display',
	'Give You Glory'  => 'Give You Glory',
	'Glass Antiqua'  => 'Glass Antiqua',
	'Glegoo'  => 'Glegoo',
	'Gloria Hallelujah'  => 'Gloria Hallelujah',
	'Goblin One'  => 'Goblin One',
	'Gochi Hand'  => 'Gochi Hand',
	'Gorditas'  => 'Gorditas',
	'Goudy Bookletter 1911'  => 'Goudy Bookletter 1911',
	'Graduate'  => 'Graduate',
	'Grand Hotel'  => 'Grand Hotel',
	'Gravitas One'  => 'Gravitas One',
	'Great Vibes'  => 'Great Vibes',
	'Griffy'  => 'Griffy',
	'Gruppo'  => 'Gruppo',
	'Gudea'  => 'Gudea',
	'Gurajada'  => 'Gurajada',
	'Habibi'  => 'Habibi',
	'Halant'  => 'Halant',
	'Hammersmith One'  => 'Hammersmith One',
	'Hanalei'  => 'Hanalei',
	'Hanalei Fill'  => 'Hanalei Fill',
	'Handlee'  => 'Handlee',
	'Hanuman'  => 'Hanuman',
	'Happy Monkey'  => 'Happy Monkey',
	'Harmattan'  => 'Harmattan',
	'Headland One'  => 'Headland One',
	'Heebo'  => 'Heebo',
	'Henny Penny'  => 'Henny Penny',
	'Herr Von Muellerhoff'  => 'Herr Von Muellerhoff',
	'Hind'  => 'Hind',
	'Hind Guntur'  => 'Hind Guntur',
	'Hind Madurai'  => 'Hind Madurai',
	'Hind Siliguri'  => 'Hind Siliguri',
	'Hind Vadodara'  => 'Hind Vadodara',
	'Holtwood One SC'  => 'Holtwood One SC',
	'Homemade Apple'  => 'Homemade Apple',
	'Homenaje'  => 'Homenaje',
	'IM Fell DW Pica'  => 'IM Fell DW Pica',
	'IM Fell DW Pica SC'  => 'IM Fell DW Pica SC',
	'IM Fell Double Pica'  => 'IM Fell Double Pica',
	'IM Fell Double Pica SC'  => 'IM Fell Double Pica SC',
	'IM Fell English'  => 'IM Fell English',
	'IM Fell English SC'  => 'IM Fell English SC',
	'IM Fell French Canon'  => 'IM Fell French Canon',
	'IM Fell French Canon SC'  => 'IM Fell French Canon SC',
	'IM Fell Great Primer'  => 'IM Fell Great Primer',
	'IM Fell Great Primer SC'  => 'IM Fell Great Primer SC',
	'Iceberg'  => 'Iceberg',
	'Iceland'  => 'Iceland',
	'Imprima'  => 'Imprima',
	'Inconsolata'  => 'Inconsolata',
	'Inder'  => 'Inder',
	'Indie Flower'  => 'Indie Flower',
	'Inika'  => 'Inika',
	'Inknut Antiqua'  => 'Inknut Antiqua',
	'Irish Grover'  => 'Irish Grover',
	'Istok Web'  => 'Istok Web',
	'Italiana'  => 'Italiana',
	'Italianno'  => 'Italianno',
	'Itim'  => 'Itim',
	'Jacques Francois'  => 'Jacques Francois',
	'Jacques Francois Shadow'  => 'Jacques Francois Shadow',
	'Jaldi'  => 'Jaldi',
	'Jim Nightshade'  => 'Jim Nightshade',
	'Jockey One'  => 'Jockey One',
	'Jolly Lodger'  => 'Jolly Lodger',
	'Jomhuria'  => 'Jomhuria',
	'Josefin Sans'  => 'Josefin Sans',
	'Josefin Slab'  => 'Josefin Slab',
	'Joti One'  => 'Joti One',
	'Judson'  => 'Judson',
	'Julee'  => 'Julee',
	'Julius Sans One'  => 'Julius Sans One',
	'Junge'  => 'Junge',
	'Jura'  => 'Jura',
	'Just Another Hand'  => 'Just Another Hand',
	'Just Me Again Down Here'  => 'Just Me Again Down Here',
	'Kadwa'  => 'Kadwa',
	'Kalam'  => 'Kalam',
	'Kameron'  => 'Kameron',
	'Kanit'  => 'Kanit',
	'Kantumruy'  => 'Kantumruy',
	'Karla'  => 'Karla',
	'Karma'  => 'Karma',
	'Katibeh'  => 'Katibeh',
	'Kaushan Script'  => 'Kaushan Script',
	'Kavivanar'  => 'Kavivanar',
	'Kavoon'  => 'Kavoon',
	'Kdam Thmor'  => 'Kdam Thmor',
	'Keania One'  => 'Keania One',
	'Kelly Slab'  => 'Kelly Slab',
	'Kenia'  => 'Kenia',
	'Khand'  => 'Khand',
	'Khmer'  => 'Khmer',
	'Khula'  => 'Khula',
	'Kite One'  => 'Kite One',
	'Knewave'  => 'Knewave',
	'Kotta One'  => 'Kotta One',
	'Koulen'  => 'Koulen',
	'Kranky'  => 'Kranky',
	'Kreon'  => 'Kreon',
	'Kristi'  => 'Kristi',
	'Krona One'  => 'Krona One',
	'Kumar One'  => 'Kumar One',
	'Kumar One Outline'  => 'Kumar One Outline',
	'Kurale'  => 'Kurale',
	'La Belle Aurore'  => 'La Belle Aurore',
	'Laila'  => 'Laila',
	'Lakki Reddy'  => 'Lakki Reddy',
	'Lalezar'  => 'Lalezar',
	'Lancelot'  => 'Lancelot',
	'Lateef'  => 'Lateef',
	'Lato'  => 'Lato',
	'League Script'  => 'League Script',
	'Leckerli One'  => 'Leckerli One',
	'Ledger'  => 'Ledger',
	'Lekton'  => 'Lekton',
	'Lemon'  => 'Lemon',
	'Lemonada'  => 'Lemonada',
	'Libre Baskerville'  => 'Libre Baskerville',
	'Libre Franklin'  => 'Libre Franklin',
	'Life Savers'  => 'Life Savers',
	'Lilita One'  => 'Lilita One',
	'Lily Script One'  => 'Lily Script One',
	'Limelight'  => 'Limelight',
	'Linden Hill'  => 'Linden Hill',
	'Lobster'  => 'Lobster',
	'Lobster Two'  => 'Lobster Two',
	'Londrina Outline'  => 'Londrina Outline',
	'Londrina Shadow'  => 'Londrina Shadow',
	'Londrina Sketch'  => 'Londrina Sketch',
	'Londrina Solid'  => 'Londrina Solid',
	'Lora'  => 'Lora',
	'Love Ya Like A Sister'  => 'Love Ya Like A Sister',
	'Loved by the King'  => 'Loved by the King',
	'Lovers Quarrel'  => 'Lovers Quarrel',
	'Luckiest Guy'  => 'Luckiest Guy',
	'Lusitana'  => 'Lusitana',
	'Lustria'  => 'Lustria',
	'Macondo'  => 'Macondo',
	'Macondo Swash Caps'  => 'Macondo Swash Caps',
	'Mada'  => 'Mada',
	'Magra'  => 'Magra',
	'Maiden Orange'  => 'Maiden Orange',
	'Maitree'  => 'Maitree',
	'Mako'  => 'Mako',
	'Mallanna'  => 'Mallanna',
	'Mandali'  => 'Mandali',
	'Marcellus'  => 'Marcellus',
	'Marcellus SC'  => 'Marcellus SC',
	'Marck Script'  => 'Marck Script',
	'Margarine'  => 'Margarine',
	'Marko One'  => 'Marko One',
	'Marmelad'  => 'Marmelad',
	'Martel'  => 'Martel',
	'Martel Sans'  => 'Martel Sans',
	'Marvel'  => 'Marvel',
	'Mate'  => 'Mate',
	'Mate SC'  => 'Mate SC',
	'Maven Pro'  => 'Maven Pro',
	'McLaren'  => 'McLaren',
	'Meddon'  => 'Meddon',
	'MedievalSharp'  => 'MedievalSharp',
	'Medula One'  => 'Medula One',
	'Meera Inimai'  => 'Meera Inimai',
	'Megrim'  => 'Megrim',
	'Meie Script'  => 'Meie Script',
	'Merienda'  => 'Merienda',
	'Merienda One'  => 'Merienda One',
	'Merriweather'  => 'Merriweather',
	'Merriweather Sans'  => 'Merriweather Sans',
	'Metal'  => 'Metal',
	'Metal Mania'  => 'Metal Mania',
	'Metamorphous'  => 'Metamorphous',
	'Metrophobic'  => 'Metrophobic',
	'Michroma'  => 'Michroma',
	'Milonga'  => 'Milonga',
	'Miltonian'  => 'Miltonian',
	'Miltonian Tattoo'  => 'Miltonian Tattoo',
	'Miniver'  => 'Miniver',
	'Miriam Libre'  => 'Miriam Libre',
	'Mirza'  => 'Mirza',
	'Miss Fajardose'  => 'Miss Fajardose',
	'Mitr'  => 'Mitr',
	'Modak'  => 'Modak',
	'Modern Antiqua'  => 'Modern Antiqua',
	'Mogra'  => 'Mogra',
	'Molengo'  => 'Molengo',
	'Molle'  => 'Molle',
	'Monda'  => 'Monda',
	'Monofett'  => 'Monofett',
	'Monoton'  => 'Monoton',
	'Monsieur La Doulaise'  => 'Monsieur La Doulaise',
	'Montaga'  => 'Montaga',
	'Montez'  => 'Montez',
	'Montserrat'  => 'Montserrat',
	'Montserrat Alternates'  => 'Montserrat Alternates',
	'Montserrat Subrayada'  => 'Montserrat Subrayada',
	'Moul'  => 'Moul',
	'Moulpali'  => 'Moulpali',
	'Mountains of Christmas'  => 'Mountains of Christmas',
	'Mouse Memoirs'  => 'Mouse Memoirs',
	'Mr Bedfort'  => 'Mr Bedfort',
	'Mr Dafoe'  => 'Mr Dafoe',
	'Mr De Haviland'  => 'Mr De Haviland',
	'Mrs Saint Delafield'  => 'Mrs Saint Delafield',
	'Mrs Sheppards'  => 'Mrs Sheppards',
	'Mukta Vaani'  => 'Mukta Vaani',
	'Muli'  => 'Muli',
	'Mystery Quest'  => 'Mystery Quest',
	'NTR'  => 'NTR',
	'Neucha'  => 'Neucha',
	'Neuton'  => 'Neuton',
	'New Rocker'  => 'New Rocker',
	'News Cycle'  => 'News Cycle',
	'Niconne'  => 'Niconne',
	'Nixie One'  => 'Nixie One',
	'Nobile'  => 'Nobile',
	'Nokora'  => 'Nokora',
	'Norican'  => 'Norican',
	'Nosifer'  => 'Nosifer',
	'Nothing You Could Do'  => 'Nothing You Could Do',
	'Noticia Text'  => 'Noticia Text',
	'Noto Sans'  => 'Noto Sans',
	'Noto Serif'  => 'Noto Serif',
	'Nova Cut'  => 'Nova Cut',
	'Nova Flat'  => 'Nova Flat',
	'Nova Mono'  => 'Nova Mono',
	'Nova Oval'  => 'Nova Oval',
	'Nova Round'  => 'Nova Round',
	'Nova Script'  => 'Nova Script',
	'Nova Slim'  => 'Nova Slim',
	'Nova Square'  => 'Nova Square',
	'Numans'  => 'Numans',
	'Nunito'  => 'Nunito',
	'Odor Mean Chey'  => 'Odor Mean Chey',
	'Offside'  => 'Offside',
	'Old Standard TT'  => 'Old Standard TT',
	'Oldenburg'  => 'Oldenburg',
	'Oleo Script'  => 'Oleo Script',
	'Oleo Script Swash Caps'  => 'Oleo Script Swash Caps',
	'Open Sans'  => 'Open Sans',
	'Open Sans Condensed'  => 'Open Sans Condensed',
	'Oranienbaum'  => 'Oranienbaum',
	'Orbitron'  => 'Orbitron',
	'Oregano'  => 'Oregano',
	'Orienta'  => 'Orienta',
	'Original Surfer'  => 'Original Surfer',
	'Oswald'  => 'Oswald',
	'Over the Rainbow'  => 'Over the Rainbow',
	'Overlock'  => 'Overlock',
	'Overlock SC'  => 'Overlock SC',
	'Ovo'  => 'Ovo',
	'Oxygen'  => 'Oxygen',
	'Oxygen Mono'  => 'Oxygen Mono',
	'PT Mono'  => 'PT Mono',
	'PT Sans'  => 'PT Sans',
	'PT Sans Caption'  => 'PT Sans Caption',
	'PT Sans Narrow'  => 'PT Sans Narrow',
	'PT Serif'  => 'PT Serif',
	'PT Serif Caption'  => 'PT Serif Caption',
	'Pacifico'  => 'Pacifico',
	'Palanquin'  => 'Palanquin',
	'Palanquin Dark'  => 'Palanquin Dark',
	'Paprika'  => 'Paprika',
	'Parisienne'  => 'Parisienne',
	'Passero One'  => 'Passero One',
	'Passion One'  => 'Passion One',
	'Pathway Gothic One'  => 'Pathway Gothic One',
	'Patrick Hand'  => 'Patrick Hand',
	'Patrick Hand SC'  => 'Patrick Hand SC',
	'Pattaya'  => 'Pattaya',
	'Patua One'  => 'Patua One',
	'Pavanam'  => 'Pavanam',
	'Paytone One'  => 'Paytone One',
	'Peddana'  => 'Peddana',
	'Peralta'  => 'Peralta',
	'Permanent Marker'  => 'Permanent Marker',
	'Petit Formal Script'  => 'Petit Formal Script',
	'Petrona'  => 'Petrona',
	'Philosopher'  => 'Philosopher',
	'Piedra'  => 'Piedra',
	'Pinyon Script'  => 'Pinyon Script',
	'Pirata One'  => 'Pirata One',
	'Plaster'  => 'Plaster',
	'Play'  => 'Play',
	'Playball'  => 'Playball',
	'Playfair Display'  => 'Playfair Display',
	'Playfair Display SC'  => 'Playfair Display SC',
	'Podkova'  => 'Podkova',
	'Poiret One'  => 'Poiret One',
	'Poller One'  => 'Poller One',
	'Poly'  => 'Poly',
	'Pompiere'  => 'Pompiere',
	'Pontano Sans'  => 'Pontano Sans',
	'Poppins'  => 'Poppins',
	'Port Lligat Sans'  => 'Port Lligat Sans',
	'Port Lligat Slab'  => 'Port Lligat Slab',
	'Pragati Narrow'  => 'Pragati Narrow',
	'Prata'  => 'Prata',
	'Preahvihear'  => 'Preahvihear',
	'Press Start 2P'  => 'Press Start 2P',
	'Pridi'  => 'Pridi',
	'Princess Sofia'  => 'Princess Sofia',
	'Prociono'  => 'Prociono',
	'Prompt'  => 'Prompt',
	'Prosto One'  => 'Prosto One',
	'Proza Libre'  => 'Proza Libre',
	'Puritan'  => 'Puritan',
	'Purple Purse'  => 'Purple Purse',
	'Quando'  => 'Quando',
	'Quantico'  => 'Quantico',
	'Quattrocento'  => 'Quattrocento',
	'Quattrocento Sans'  => 'Quattrocento Sans',
	'Questrial'  => 'Questrial',
	'Quicksand'  => 'Quicksand',
	'Quintessential'  => 'Quintessential',
	'Qwigley'  => 'Qwigley',
	'Racing Sans One'  => 'Racing Sans One',
	'Radley'  => 'Radley',
	'Rajdhani'  => 'Rajdhani',
	'Rakkas'  => 'Rakkas',
	'Raleway'  => 'Raleway',
	'Raleway Dots'  => 'Raleway Dots',
	'Ramabhadra'  => 'Ramabhadra',
	'Ramaraja'  => 'Ramaraja',
	'Rambla'  => 'Rambla',
	'Rammetto One'  => 'Rammetto One',
	'Ranchers'  => 'Ranchers',
	'Rancho'  => 'Rancho',
	'Ranga'  => 'Ranga',
	'Rasa'  => 'Rasa',
	'Rationale'  => 'Rationale',
	'Ravi Prakash'  => 'Ravi Prakash',
	'Redressed'  => 'Redressed',
	'Reem Kufi'  => 'Reem Kufi',
	'Reenie Beanie'  => 'Reenie Beanie',
	'Revalia'  => 'Revalia',
	'Rhodium Libre'  => 'Rhodium Libre',
	'Ribeye'  => 'Ribeye',
	'Ribeye Marrow'  => 'Ribeye Marrow',
	'Righteous'  => 'Righteous',
	'Risque'  => 'Risque',
	'Roboto'  => 'Roboto',
	'Roboto Condensed'  => 'Roboto Condensed',
	'Roboto Mono'  => 'Roboto Mono',
	'Roboto Slab'  => 'Roboto Slab',
	'Rochester'  => 'Rochester',
	'Rock Salt'  => 'Rock Salt',
	'Rokkitt'  => 'Rokkitt',
	'Romanesco'  => 'Romanesco',
	'Ropa Sans'  => 'Ropa Sans',
	'Rosario'  => 'Rosario',
	'Rosarivo'  => 'Rosarivo',
	'Rouge Script'  => 'Rouge Script',
	'Rozha One'  => 'Rozha One',
	'Rubik'  => 'Rubik',
	'Rubik Mono One'  => 'Rubik Mono One',
	'Rubik One'  => 'Rubik One',
	'Ruda'  => 'Ruda',
	'Rufina'  => 'Rufina',
	'Ruge Boogie'  => 'Ruge Boogie',
	'Ruluko'  => 'Ruluko',
	'Rum Raisin'  => 'Rum Raisin',
	'Ruslan Display'  => 'Ruslan Display',
	'Russo One'  => 'Russo One',
	'Ruthie'  => 'Ruthie',
	'Rye'  => 'Rye',
	'Sacramento'  => 'Sacramento',
	'Sahitya'  => 'Sahitya',
	'Sail'  => 'Sail',
	'Salsa'  => 'Salsa',
	'Sanchez'  => 'Sanchez',
	'Sancreek'  => 'Sancreek',
	'Sansita One'  => 'Sansita One',
	'Sarala'  => 'Sarala',
	'Sarina'  => 'Sarina',
	'Sarpanch'  => 'Sarpanch',
	'Satisfy'  => 'Satisfy',
	'Scada'  => 'Scada',
	'Scheherazade'  => 'Scheherazade',
	'Schoolbell'  => 'Schoolbell',
	'Scope One'  => 'Scope One',
	'Seaweed Script'  => 'Seaweed Script',
	'Secular One'  => 'Secular One',
	'Sevillana'  => 'Sevillana',
	'Seymour One'  => 'Seymour One',
	'Shadows Into Light'  => 'Shadows Into Light',
	'Shadows Into Light Two'  => 'Shadows Into Light Two',
	'Shanti'  => 'Shanti',
	'Share'  => 'Share',
	'Share Tech'  => 'Share Tech',
	'Share Tech Mono'  => 'Share Tech Mono',
	'Shojumaru'  => 'Shojumaru',
	'Short Stack'  => 'Short Stack',
	'Shrikhand'  => 'Shrikhand',
	'Siemreap'  => 'Siemreap',
	'Sigmar One'  => 'Sigmar One',
	'Signika'  => 'Signika',
	'Signika Negative'  => 'Signika Negative',
	'Simonetta'  => 'Simonetta',
	'Sintony'  => 'Sintony',
	'Sirin Stencil'  => 'Sirin Stencil',
	'Six Caps'  => 'Six Caps',
	'Skranji'  => 'Skranji',
	'Slabo 13px'  => 'Slabo 13px',
	'Slabo 27px'  => 'Slabo 27px',
	'Slackey'  => 'Slackey',
	'Smokum'  => 'Smokum',
	'Smythe'  => 'Smythe',
	'Sniglet'  => 'Sniglet',
	'Snippet'  => 'Snippet',
	'Snowburst One'  => 'Snowburst One',
	'Sofadi One'  => 'Sofadi One',
	'Sofia'  => 'Sofia',
	'Sonsie One'  => 'Sonsie One',
	'Sorts Mill Goudy'  => 'Sorts Mill Goudy',
	'Source Code Pro'  => 'Source Code Pro',
	'Source Sans Pro'  => 'Source Sans Pro',
	'Source Serif Pro'  => 'Source Serif Pro',
	'Space Mono'  => 'Space Mono',
	'Special Elite'  => 'Special Elite',
	'Spicy Rice'  => 'Spicy Rice',
	'Spinnaker'  => 'Spinnaker',
	'Spirax'  => 'Spirax',
	'Squada One'  => 'Squada One',
	'Sree Krushnadevaraya'  => 'Sree Krushnadevaraya',
	'Sriracha'  => 'Sriracha',
	'Stalemate'  => 'Stalemate',
	'Stalinist One'  => 'Stalinist One',
	'Stardos Stencil'  => 'Stardos Stencil',
	'Stint Ultra Condensed'  => 'Stint Ultra Condensed',
	'Stint Ultra Expanded'  => 'Stint Ultra Expanded',
	'Stoke'  => 'Stoke',
	'Strait'  => 'Strait',
	'Sue Ellen Francisco'  => 'Sue Ellen Francisco',
	'Suez One'  => 'Suez One',
	'Sumana'  => 'Sumana',
	'Sunshiney'  => 'Sunshiney',
	'Supermercado One'  => 'Supermercado One',
	'Sura'  => 'Sura',
	'Suranna'  => 'Suranna',
	'Suravaram'  => 'Suravaram',
	'Suwannaphum'  => 'Suwannaphum',
	'Swanky and Moo Moo'  => 'Swanky and Moo Moo',
	'Syncopate'  => 'Syncopate',
	'Tangerine'  => 'Tangerine',
	'Taprom'  => 'Taprom',
	'Tauri'  => 'Tauri',
	'Taviraj'  => 'Taviraj',
	'Teko'  => 'Teko',
	'Telex'  => 'Telex',
	'Tenali Ramakrishna'  => 'Tenali Ramakrishna',
	'Tenor Sans'  => 'Tenor Sans',
	'Text Me One'  => 'Text Me One',
	'The Girl Next Door'  => 'The Girl Next Door',
	'Tienne'  => 'Tienne',
	'Tillana'  => 'Tillana',
	'Timmana'  => 'Timmana',
	'Tinos'  => 'Tinos',
	'Titan One'  => 'Titan One',
	'Titillium Web'  => 'Titillium Web',
	'Trade Winds'  => 'Trade Winds',
	'Trirong'  => 'Trirong',
	'Trocchi'  => 'Trocchi',
	'Trochut'  => 'Trochut',
	'Trykker'  => 'Trykker',
	'Tulpen One'  => 'Tulpen One',
	'Ubuntu'  => 'Ubuntu',
	'Ubuntu Condensed'  => 'Ubuntu Condensed',
	'Ubuntu Mono'  => 'Ubuntu Mono',
	'Ultra'  => 'Ultra',
	'Uncial Antiqua'  => 'Uncial Antiqua',
	'Underdog'  => 'Underdog',
	'Unica One'  => 'Unica One',
	'UnifrakturCook'  => 'UnifrakturCook',
	'UnifrakturMaguntia'  => 'UnifrakturMaguntia',
	'Unkempt'  => 'Unkempt',
	'Unlock'  => 'Unlock',
	'Unna'  => 'Unna',
	'VT323'  => 'VT323',
	'Vampiro One'  => 'Vampiro One',
	'Varela'  => 'Varela',
	'Varela Round'  => 'Varela Round',
	'Vast Shadow'  => 'Vast Shadow',
	'Vesper Libre'  => 'Vesper Libre',
	'Vibur'  => 'Vibur',
	'Vidaloka'  => 'Vidaloka',
	'Viga'  => 'Viga',
	'Voces'  => 'Voces',
	'Volkhov'  => 'Volkhov',
	'Vollkorn'  => 'Vollkorn',
	'Voltaire'  => 'Voltaire',
	'Waiting for the Sunrise'  => 'Waiting for the Sunrise',
	'Wallpoet'  => 'Wallpoet',
	'Walter Turncoat'  => 'Walter Turncoat',
	'Warnes'  => 'Warnes',
	'Wellfleet'  => 'Wellfleet',
	'Wendy One'  => 'Wendy One',
	'Wire One'  => 'Wire One',
	'Work Sans'  => 'Work Sans',
	'Yanone Kaffeesatz'  => 'Yanone Kaffeesatz',
	'Yantramanav'  => 'Yantramanav',
	'Yatra One'  => 'Yatra One',
	'Yellowtail'  => 'Yellowtail',
	'Yeseva One'  => 'Yeseva One',
	'Yesteryear'  => 'Yesteryear',
	'Yrsa'  => 'Yrsa',
	'Zeyada'  => 'Zeyada',
);

$font_preview = array(
	'text' => "<span class=\"nums\">1234567890</span><span class=\"uppers\">ABCDEFGHIKLMNOPQRSTVXYZ</span><span class=\"lowers\">abcdefghiklmnopqrstvxyz</span>",
	'size' => '25px'
);

$font_primary_list      = array_merge(array('none' => 'Use default'), $fonts_list);
$font_heading_list    = array_merge(array('none' => 'Use default'), $fonts_list);

$font_weights = array(
	'' => 'Use Default',
	300, 
	400, 
	500, 
	600, 
	700, 
	'bold' => 'Bold'
);

$text_transforms = array(
	''             => 'Use Default',
	'none'         => 'None', 
	'uppercase'    => 'Upper Case', 
	'lowercase'    => 'Lower Case', 
	'capitalize'   => 'Capitalize', 
);


$of_options[] = array( 	'name' 		=> 'Typography',
						'type' 		=> 'heading',
						'icon'		=> 'fa fa-font'
				);

$of_options[] = array( 	'type' 		=> 'tabs',
						'id'		=> 'typography-settings-tabs',
						'tabs' 		=> array()
				);



$of_options[] = array( 	'name' 		=> 'Typography Warning',
						'desc' 		=> "",
						'id' 		=> 'typography_warning',
						'std' 		=> "<h3 style=\"margin: 0 0 10px;\">Typography is moved to <strong>Laborator &gt; Typography</strong></h3>
						<a href='admin.php?page=typolab'>Click here to go to TypoLab section &raquo;</a>",
						'icon' 		=> true,
						'type' 		=> 'info',
				);


$of_options[] = array( 	'name' 		=> 'Theme Styling',
						'type' 		=> 'heading',
						'icon'		=> 'fa fa-tint'
				);

$of_options[] = array( 	'type' 		=> 'tabs',
						'id'		=> 'styling-settings-tabs',
						'tabs'		=> array(
							'styling-settings-skin'          => 'Custom Skin',
							'styling-settings-borders'       => 'Theme Borders'
						)
				);
				
$of_options[] = array(  'name'		=> 'Custom Skin Builder',
						'desc'   	=> 'Create your own skin for this theme',
						'id'   		=> 'use_custom_skin',
						'std'   	=> 0,
						'folds'  	=> 1,
						'on'  		=> 'Yes',
						'off'  		=> 'No',
						'type'   	=> 'switch',
						
						'tab_id'	=> 'styling-settings-skin'
					);
				
$of_options[] = array(  'desc'   	=> 'If skin is showing 404 error or not being applied then check this option.',
						'id'   		=> 'theme_skin_include_alternate',
						'std'   	=> 0,
						'type'   	=> 'checkbox',
						'fold'  	=> 'use_custom_skin',
						
						'tab_id'	=> 'styling-settings-skin',
					);

$of_options[] = array(	'name'		=> 'Skin Colors',
						'desc'   	=> 'Background color',
						'id'   		=> 'custom_skin_bg_color',
						'std'   	=> '#FFF',
						'type'   	=> 'color',
						'fold'  	=> 'use_custom_skin',
						
						'tab_id'	=> 'styling-settings-skin',
					);

$of_options[] = array(	'desc'   	=> 'Link color',
						'id'   		=> 'custom_skin_link_color',
						'std'   	=> '#F6364D',
						'type'   	=> 'color',
						'fold'  	=> 'use_custom_skin',
						
						'tab_id'	=> 'styling-settings-skin',
					);

$of_options[] = array(	'desc'   	=> 'Headings color',
						'id'   		=> 'custom_skin_headings_color',
						'std'   	=> '#F6364D',
						'type'   	=> 'color',
						'fold'  	=> 'use_custom_skin',
						
						'tab_id'	=> 'styling-settings-skin',
					);

$of_options[] = array(	'desc'   	=> 'Paragraph color',
						'id'   		=> 'custom_skin_paragraph_color',
						'std'   	=> '#777777',
						'type'   	=> 'color',
						'fold'  	=> 'use_custom_skin',
						
						'tab_id'	=> 'styling-settings-skin',
					);

$of_options[] = array(	'desc'   	=> 'Footer background color',
						'id'   		=> 'custom_skin_footer_bg_color',
						'std'   	=> '#FAFAFA',
						'type'   	=> 'color',
						'fold'  	=> 'use_custom_skin',
						
						'tab_id'	=> 'styling-settings-skin',
					);

$of_options[] = array(	'desc'   	=> 'Borders color',
						'id'   		=> 'custom_skin_borders_color',
						'std'   	=> '#EEEEEE',
						'type'   	=> 'color',
						'fold'  	=> 'use_custom_skin',
						
						'tab_id'	=> 'styling-settings-skin',
					);
					

$of_options[] = array( 	'name' 		=> 'Custom CSS',
						'desc' 		=> "",
						'id' 		=> 'skin_palettes_list',
						'std' 		=> "
						<h3 style=\"margin: 0 0 10px;\">Our selection of predefined skin palettes</h3>".
						'
						<a href="#" class=\'skin-palette\'>
							<span style="background-color: #FFF;"></span>
							<span style="background-color: #F6364D;"></span>
							<span style="background-color: #F6364D;"></span>
							<span style="background-color: #777;"></span>
							<span style="background-color: #FAFAFA;"></span>
							<span style="background-color: #EEE;"></span>
							
							<em>Pink</em>
						</a>
						
						<a href="#" class=\'skin-palette\'>
							<span style="background-color: #f2f0ec;"></span>
							<span style="background-color: #e09a0e;"></span>
							<span style="background-color: #242321;"></span>
							<span style="background-color: #242321;"></span>
							<span style="background-color: #ece9e4;"></span>
							<span style="background-color: #FFF;"></span>
							
							<em>Gold</em>
						</a>
						
						<a href="#" class=\'skin-palette\'>
							<span style="background-color: #FFF;"></span>
							<span style="background-color: #a58f60;"></span>
							<span style="background-color: #222;"></span>
							<span style="background-color: #555;"></span>
							<span style="background-color: #EAEAEA;"></span>
							<span style="background-color: #EEE;"></span>
							
							<em>Creme</em>
						</a>
						
						<a href="#" class=\'skin-palette\'>
							<span style="background-color: #333333;"></span>
							<span style="background-color: #FBC441;"></span>
							<span style="background-color: #FFF;"></span>
							<span style="background-color: #CCC;"></span>
							<span style="background-color: #222;"></span>
							<span style="background-color: #333;"></span>
							
							<em>Dark Skin</em>
						</a>
						'
						,
						'icon' 		=> true,
						'type' 		=> 'info',
						'fold'  	=> 'use_custom_skin',
						
						'tab_id'	=> 'styling-settings-skin',
				);


				

// BORDERS
/*
$of_options[] = array( 	'name' 		=> 'Borders',
						'type' 		=> 'heading',
						'icon'		=> 'fa fa-square-o'
				);
*/
				
$of_options[] = array(  'name'		=> 'Theme Borders',
						'desc'   	=> 'Show or hide theme borders',
						'id'   		=> 'theme_borders',
						'std'   	=> 0,
						'folds'  	=> 1,
						'on'  		=> 'Show',
						'off'  		=> 'Hide',
						'type'   	=> 'switch',
						
						'tab_id'	=> 'styling-settings-borders'
					);
				
$of_options[] = array(  'name'		=> 'Border Settings',
						'desc'   	=> 'Show borders with animation',
						'id'   		=> 'theme_borders_animation',
						'std'   	=> 'fade',
						'options'	=> array(
							'none'   => 'No Animations',
							'fade'   => 'Fade In',
							'slide'  => 'Slide In',
						),
						'type'   	=> 'select',
						'fold'  	=> 'theme_borders',
						
						'tab_id'	=> 'styling-settings-borders',
						
						'afolds'	=> 1
					);
				
$of_options[] = array(  'desc'   	=> 'Borders animate duration in seconds <em>(if animations are enabled)</em>',
						'id'   		=> 'theme_borders_animation_duration',
						'std'   	=> '1',
						'type'   	=> 'text',
						'numeric'	=> true,
						'postfix'	=> 's',
						'fold'  	=> 'theme_borders',
						
						'afold'		=> 'theme_borders_animation:fade,slide',
						
						'tab_id'	=> 'styling-settings-borders',
					);
				
$of_options[] = array(  'desc'   	=> 'Borders animation delay in seconds <em>(if animations are enabled)</em>',
						'id'   		=> 'theme_borders_animation_delay',
						'std'   	=> '0.2',
						'type'   	=> 'text',
						'numeric'	=> true,
						'fold'  	=> 'theme_borders',
						
						'afold'		=> 'theme_borders_animation:fade,slide',
						
						'tab_id'	=> 'styling-settings-borders',
					);
				
$of_options[] = array(  'desc'   	=> 'Border thickness',
						'id'   		=> 'theme_borders_thickness',
						'std'   	=> '',
						'plc'		=> 'If not set, default is used: 22',
						'type'   	=> 'text',
						'postfix'	=> 'px',
						'numeric'	=> true,
						'fold'  	=> 'theme_borders',
						
						'tab_id'	=> 'styling-settings-borders',
					);

$of_options[] = array(	'desc'   	=> 'Set borders color',
						'id'   		=> 'theme_borders_color',
						'std'   	=> '#f3f3ef',
						'type'   	=> 'color',
						'fold'  	=> 'theme_borders',
						
						'tab_id'	=> 'styling-settings-borders',
					);
// END OF BORDERS


$of_options[] = array( 	'name' 		=> 'Social Networks',
						'type' 		=> 'heading',
						'icon'		=> 'fa fa-share-alt'
				);

$social_networks_ordering = array(
			'visible' => array (
				'placebo'	=> 'placebo',
				'fb'   	 	=> 'Facebook',
				'tw'   	 	=> 'Twitter',
				'ig'        => 'Instagram',
				'vm'        => 'Vimeo',
				'be'        => 'Behance',
				'fs'        => 'Foursquare',
				'custom'    => 'Custom Link',
			),

			'hidden' => array (
				'placebo'   => 'placebo',
				'gp'        => "Google+",
				'lin'       => 'LinkedIn',
				'yt'        => 'YouTube',
				'drb'       => 'Dribbble',
				'pi'        => 'Pinterest',
				'vk'        => 'VKontakte',
				'da'        => 'DeviantArt',
				'fl'        => 'Flickr',
				'vi'        => 'Vine',
				'tu'        => 'Tumblr',
				'sk'        => 'Skype',
				'gh'        => 'GitHub',
				'sc'        => 'SoundCloud',
				'hz'        => 'Houzz',
				'px'        => '500px',
				'xi'        => 'Xing',
				'sp'        => 'Spotify',
				'sn'        => 'Snapchat',
				'em'        => 'Email',
				'yp'        => 'Yelp',
				'ta'        => 'TripAdvisor',
			),
);

$of_options[] = array( 	'name' 		=> 'Social Networks Ordering',
						'desc' 		=> "Set the appearing order of social networks in the footer. To use social networks links list copy this shortcode:<br> " . $lab_social_networks_shortcode,
						'id' 		=> 'social_order',
						'std' 		=> $social_networks_ordering,
						'type' 		=> 'sorter'
				);
				

$of_options[] = array( 	'name'		=> 'Link Target',
						'desc' 		=> 'Open social links in new window or current window',
						'id' 		=> 'social_networks_target_attr',
						'std' 		=> '_blank',
						'type' 		=> 'select',
						'options' 	=> array(
							'_self'  => 'Same Window',
							'_blank' => 'New Window',
						)
				);

$of_options[] = array( 	'name' 		=> 'Social Networks',
						'desc' 		=> 'Facebook',
						'id' 		=> 'social_network_link_fb',
						'std' 		=> "",
						'plc'		=> "https://facebook.com/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'Twitter',
						'id' 		=> 'social_network_link_tw',
						'std' 		=> "",
						'plc'		=> "https://twitter.com/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'LinkedIn',
						'id' 		=> 'social_network_link_lin',
						'std' 		=> "",
						'plc'		=> "https://linkedin.com/in/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'YouTube',
						'id' 		=> 'social_network_link_yt',
						'std' 		=> "",
						'plc'		=> "https://youtube.com/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'Vimeo',
						'id' 		=> 'social_network_link_vm',
						'std' 		=> "",
						'plc'		=> "https://vimeo.com/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'Dribbble',
						'id' 		=> 'social_network_link_drb',
						'std' 		=> "",
						'plc'		=> "https://dribbble.com/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'Instagram',
						'id' 		=> 'social_network_link_ig',
						'std' 		=> "",
						'plc'		=> "https://instagram.com/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'Pinterest',
						'id' 		=> 'social_network_link_pi',
						'std' 		=> "",
						'plc'		=> "https://pinterest.com/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'Google Plus',
						'id' 		=> 'social_network_link_gp',
						'std' 		=> "",
						'plc'		=> "https://plus.google.com/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'VKontakte',
						'id' 		=> 'social_network_link_vk',
						'std' 		=> "",
						'plc'		=> "https://vk.com/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'DeviantArt',
						'id' 		=> 'social_network_link_da',
						'std' 		=> "",
						'plc'		=> "https://username.deviantart.com",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'Tumblr',
						'id' 		=> 'social_network_link_tu',
						'std' 		=> "",
						'plc'		=> "https://username.tumblr.com",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'Vine',
						'id' 		=> 'social_network_link_vi',
						'std' 		=> "",
						'plc'		=> "https://vine.co/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'Behance',
						'id' 		=> 'social_network_link_be',
						'std' 		=> "",
						'plc'		=> "https://www.behance.net/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'Flickr',
						'id' 		=> 'social_network_link_fl',
						'std' 		=> "",
						'plc'		=> "https://www.flickr.com/photos/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'Foursquare',
						'id' 		=> 'social_network_link_fs',
						'std' 		=> "",
						'plc'		=> "https://foursquare.com/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'Skype',
						'id' 		=> 'social_network_link_sk',
						'std' 		=> "",
						'plc'		=> 'skype:username',
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'GitHub',
						'id' 		=> 'social_network_link_gh',
						'std' 		=> "",
						'plc'		=> "https://github.com/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'SoundCloud',
						'id' 		=> 'social_network_link_sc',
						'std' 		=> "",
						'plc'		=> "https://soundcloud.com/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'Houzz',
						'id' 		=> 'social_network_link_hz',
						'std' 		=> "",
						'plc'		=> "https://www.houzz.com/user/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> '500px',
						'id' 		=> 'social_network_link_px',
						'std' 		=> "",
						'plc'		=> "https://500px.com/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'Xing',
						'id' 		=> 'social_network_link_xi',
						'std' 		=> "",
						'plc'		=> "https://www.xing.com/profile/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'Spotify',
						'id' 		=> 'social_network_link_sp',
						'std' 		=> "",
						'plc'		=> "https://open.spotify.com/user/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'Snapchat',
						'id' 		=> 'social_network_link_sn',
						'std' 		=> "",
						'plc'		=> "https://www.snapchat.com/add/username",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'Yelp',
						'id' 		=> 'social_network_link_yp',
						'std' 		=> "",
						'plc'		=> "https://www.yelp.com/biz/alias",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "",
						'desc' 		=> 'Trip Advisor',
						'id' 		=> 'social_network_link_ta',
						'std' 		=> "",
						'plc'		=> "https://www.tripadvisor.com/",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> "Email",
						'desc' 		=> 'Contact mail',
						'id' 		=> 'social_network_link_em',
						'std' 		=> "",
						'plc'		=> "john.doe@email.com",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'desc' 		=> 'Default subject',
						'id' 		=> 'social_network_link_em_subject',
						'std' 		=> "Hello!",
						'plc'		=> "",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'name' 		=> 'Custom Link',
						'desc' 		=> 'Link Title',
						'id' 		=> 'social_network_custom_link_title',
						'std' 		=> "",
						'plc'		=> 'My Custom Link',
						'type' 		=> 'text'
				);

$of_options[] = array( 	'desc' 		=> 'Link',
						'id' 		=> 'social_network_custom_link_link',
						'std' 		=> "",
						'plc'		=> "https://www.mywebsite.com/",
						'type' 		=> 'text'
				);

$of_options[] = array( 	'desc' 		=> 'Icon (optional)<br><small>Note: If you want to set custom icon, enter icon alias from <a href="https://fontawesome.com/v4.7.0/icons/" target="_blank">Font Awesome</a> icon collection.</small>',
						'id' 		=> 'social_network_custom_link_icon',
						'std' 		=> "",
						'plc'		=> "Example: bookmark",
						'type' 		=> 'text'
				);



$of_options[] = array( 	'name' 		=> 'Coming Soon Mode',
						'type' 		=> 'heading',
						'icon'		=> 'fa fa-clock-o',
				);

$of_options[] = array( 	'type' 		=> 'tabs',
						'id'		=> 'coming-soon-settings-tabs',
						'tabs'		=> array(
							'coming-soon-settings-main'          => 'General Settings',
							'coming-soon-settings-countdown'     => 'Countdown Timer',
							'coming-soon-settings-custom-bg'     => 'Custom Background',
							'coming-soon-settings-custom-logo'   => 'Custom Logo',
						)
				);


$of_options[] = array( 	'name' 		=> 'Coming Soon Warning',
						'desc' 		=> "",
						'id' 		=> 'custom_coming_soon_warning',
						'std' 		=> "<h3 style=\"margin: 0 0 10px;\">Warning</h3>
						To view settings on this tab you must enable <strong>Coming Soon Mode</strong> in <strong>General Settings</strong> tab.",
						'icon' 		=> true,
						'type' 		=> 'info',
						
						'afold'		=> 'coming_soon_mode:notChecked',
						
						'tab_id' 	=> 'coming-soon-settings-countdown'
				);

$last = end( $of_options );

$last['tab_id'] = 'coming-soon-settings-custom-bg'; 
$of_options[] = $last;

$last['tab_id'] = 'coming-soon-settings-custom-logo'; 
$of_options[] = $last;

$of_options[] = array(	'name'		=> 'Coming Soon Mode',
						'desc'   	=> "Activate coming soon mode page with countdown timer. <br /><small>Note that as an administrator you will not see the coming soon page unless you <a href=\"" . home_url( '?view-coming-soon=true' ) . "\" target=\"_blank\">click here</a>.</small>",
						'id'   		=> 'coming_soon_mode',
						'std'   	=> 0,
						'on'  		=> 'Enable',
						'off'  		=> 'Disable',
						'type'   	=> 'switch',
						'afolds'	=> 1,
						
						'tab_id'	=> 'coming-soon-settings-main'
					);

$of_options[] = array( 	'name' 		=> 'Title and Description',
						'desc' 		=> 'Set page title to show in this page (leave empty to use site slogan)',
						'id' 		=> 'coming_soon_mode_title',
						'std' 		=> "",
						'type' 		=> 'text',
						'afold'		=> 'coming_soon_mode:checked',
						
						'tab_id'	=> 'coming-soon-settings-main'
				);

$of_options[] = array( 	'desc' 		=> 'Description text that explains your visitors why or when the site is back',
						'id' 		=> 'coming_soon_mode_description',
						'std' 		=> "We are currently working on the back-end,
our team is working hard and we’ll be back within the time",
						'type' 		=> 'textarea',
						'afold'		=> 'coming_soon_mode:checked',
						
						'tab_id'	=> 'coming-soon-settings-main'
				);

$of_options[] = array(	'name'   	=> 'Social networks',
						'desc'   	=> 'Show or hide social networks in the footer of this page',
						'id'   		=> 'coming_soon_mode_social_networks',
						'std'   	=> 0,
						'on'  		=> 'Show',
						'off'  		=> 'Hide',
						'type'   	=> 'switch',
						'afold'		=> 'coming_soon_mode:checked',
						
						'tab_id'	=> 'coming-soon-settings-main'
					);

$of_options[] = array(	'name'   	=> 'Countdown Timer',
						'desc'   	=> 'Show or hide countdown timer',
						'id'   		=> 'coming_soon_mode_countdown',
						'std'   	=> 0,
						'on'  		=> 'Show',
						'off'  		=> 'Hide',
						'type'   	=> 'switch',
						'folds'  	=> 1,
						'afold'		=> 'coming_soon_mode:checked',
						
						'tab_id'	=> 'coming-soon-settings-countdown'
					);

$of_options[] = array( 	'name'		=> 'Release Date',
						'desc' 		=> 'Enter the date when site will be online (format YYYY-MM-DD HH:MM:SS)',
						'id' 		=> 'coming_soon_mode_date',
						'std' 		=> date('Y-m-d', strtotime("+3 months")) . ' 18:00:00',
						'plc'		=> "",
						'type' 		=> 'text',
						'fold'		=> 'coming_soon_mode_countdown',
						'afold'		=> 'coming_soon_mode:checked',
						
						'tab_id'	=> 'coming-soon-settings-countdown'
				);

$of_options[] = array(	'name'   	=> 'Custom Background',
						'desc'   	=> 'Include custom background image for this page',
						'id'   		=> 'coming_soon_mode_custom_bg',
						'std'   	=> 0,
						'on'  		=> 'Yes',
						'off'  		=> 'No',
						'type'   	=> 'switch',
						'folds'  	=> 1,
						'afold'		=> 'coming_soon_mode:checked',
						
						'tab_id'	=> 'coming-soon-settings-custom-bg'
					);

$of_options[] = array(	'name' 		=> 'Upload Background Image',
						'desc' 		=> "Upload/choose your custom background image from gallery",
						'id' 		=> 'coming_soon_mode_custom_bg_id',
						'std' 		=> "",
						'type' 		=> 'media',
						'mod' 		=> 'min',
						'fold' 		=> 'coming_soon_mode_custom_bg',
						'afold'		=> 'coming_soon_mode:checked',
						
						'tab_id'	=> 'coming-soon-settings-custom-bg'
					);

$of_options[] = array( 	'desc' 		=> 'Background fill options',
						'id' 		=> 'coming_soon_mode_custom_bg_size',
						'std' 		=> 'cover',
						'type' 		=> 'select',
						'options' 	=> array(
							'cover'      => 'Cover',
							'contain'    => 'Contain',
						),
						'fold'		=> 'coming_soon_mode_custom_bg',
						'afold'		=> 'coming_soon_mode:checked',
						
						'tab_id'	=> 'coming-soon-settings-custom-bg'
				);

$of_options[] = array( 	'desc' 		=> 'Background color (optional)',
						'id' 		=> 'coming_soon_mode_custom_bg_color',
						'std' 		=> '',
						'type' 		=> 'color',
						'fold'		=> 'coming_soon_mode_custom_bg',
						'afold'		=> 'coming_soon_mode:checked',
						
						'tab_id'	=> 'coming-soon-settings-custom-bg'
				);

$of_options[] = array( 	'desc' 		=> 'Text color (optional)',
						'id' 		=> 'coming_soon_mode_custom_txt_color',
						'std' 		=> '',
						'type' 		=> 'color',
						'fold'		=> 'coming_soon_mode_custom_bg',
						'afold'		=> 'coming_soon_mode:checked',
						
						'tab_id'	=> 'coming-soon-settings-custom-bg'
				);

$of_options[] = array(	'name'   	=> 'Custom Logo',
						'desc'   	=> 'Use Custom Logo',
						'id'   		=> 'coming_soon_mode_use_uploaded_logo',
						'std'   	=> 0,
						'on'  		=> 'Yes',
						'off'  		=> 'No',
						'type'   	=> 'switch',
						'folds'  	=> 1,
						'afold'		=> 'coming_soon_mode:checked',
						
						'tab_id'	=> 'coming-soon-settings-custom-logo'
					);

$of_options[] = array(	'name' 		=> 'Upload Logo',
						'desc' 		=> "Upload/choose your custom logo image from gallery if you want to use it instead of the default logo uploaded in <strong>Branding</strong> section",
						'id' 		=> 'coming_soon_mode_custom_logo_image',
						'std' 		=> "",
						'type' 		=> 'media',
						'mod' 		=> 'min',
						'fold' 		=> 'coming_soon_mode_use_uploaded_logo',
						'afold'		=> 'coming_soon_mode:checked',
						
						'tab_id'	=> 'coming-soon-settings-custom-logo'
					);

$of_options[] = array( 	'desc' 		=> 'Set maximum width for uploaded logo',
						'id' 		=> 'coming_soon_mode_custom_logo_max_width',
						'std' 		=> "",
						'plc'		=> 'Logo Width',
						'type' 		=> 'text',
						'numeric'	=> true,
						'postfix'	=> 'px',
						'fold' 		=> 'coming_soon_mode_use_uploaded_logo',
						'afold'		=> 'coming_soon_mode:checked',
						
						'tab_id'	=> 'coming-soon-settings-custom-logo'
				);



$of_options[] = array( 	'name' 		=> 'Maintenance Mode',
						'type' 		=> 'heading',
						'icon'		=> 'fa fa-wrench',
				);

$of_options[] = array(	'name'   	=> 'Maintenance Mode',
						'desc'   	=> "Enable or disable maintenance mode. Note that if coming soon mode is enabled this page will not be visible. <br /><small>Note that as an administrator you will not see the coming soon page unless you <a href=\"" . home_url( '?view-maintenance=true' ) . "\" target=\"_blank\">click here</a>.</small>",
						'id'   		=> 'maintenance_mode',
						'std'   	=> 0,
						'on'  		=> 'Enable',
						'off'  		=> 'Disable',
						'type'   	=> 'switch',
						'folds'		=> 1
					);

$of_options[] = array( 	'name' 		=> 'Title and description',
						'desc' 		=> 'Set page title to show in this page (leave empty to use site slogan)',
						'id' 		=> 'maintenance_mode_title',
						'std' 		=> "",
						'type' 		=> 'text',
						'fold'		=> 'maintenance_mode'
				);

$of_options[] = array( 	'desc' 		=> 'Description text that explains your visitors why this site is under maintenance',
						'id' 		=> 'maintenance_mode_description',
						'std' 		=> "We are currently working on the back-end,
our team is working hard and we’ll be back within the time",
						'type' 		=> 'textarea',
						'fold'		=> 'maintenance_mode'
				);

$of_options[] = array(	'name'   	=> 'Custom Background',
						'desc'   	=> 'Include custom background image for this page',
						'id'   		=> 'maintenance_mode_custom_bg',
						'std'   	=> 0,
						'on'  		=> 'Yes',
						'off'  		=> 'No',
						'type'   	=> 'switch',
						'folds'  	=> 1,
						'fold'		=> 'maintenance_mode'
					);

$of_options[] = array(	'name' 		=> 'Upload Background Image',
						'desc' 		=> "Upload/choose your custom background image from gallery",
						'id' 		=> 'maintenance_mode_custom_bg_id',
						'std' 		=> "",
						'type' 		=> 'media',
						'mod' 		=> 'min',
						'fold' 		=> 'maintenance_mode_custom_bg'
					);

$of_options[] = array( 	'desc' 		=> 'Background fill options',
						'id' 		=> 'maintenance_mode_custom_bg_size',
						'std' 		=> 'cover',
						'type' 		=> 'select',
						'options' 	=> array(
							'cover'      => 'Cover',
							'contain'    => 'Contain',
						),
						'fold'		=> 'maintenance_mode_custom_bg'
				);

$of_options[] = array( 	'desc' 		=> 'Background color (optional)',
						'id' 		=> 'maintenance_mode_custom_bg_color',
						'std' 		=> '',
						'type' 		=> 'color',
						'fold'		=> 'maintenance_mode_custom_bg'
				);

$of_options[] = array( 	'desc' 		=> 'Text color (optional)',
						'id' 		=> 'maintenance_mode_custom_txt_color',
						'std' 		=> '',
						'type' 		=> 'color',
						'fold'		=> 'maintenance_mode_custom_bg'
				);


// Backup Options
$of_options[] = array( 	'name' 		=> 'Backup Options',
						'type' 		=> 'heading',
						'icon'		=> 'fa fa-download'
				);

$of_options[] = array( 	'name' 		=> 'Backup and Restore Options',
						'id' 		=> 'of_backup',
						'std' 		=> "",
						'type' 		=> 'backup',
						'desc' 		=> 'You can use the two buttons below to backup your current options, and then restore it back at a later time. This is useful if you want to experiment on the options but would like to keep the old settings in case you need it back',
				);


$of_options[] = array( 	'name' 		=> 'Transfer Theme Options Data',
						'id' 		=> 'of_transfer',
						'std' 		=> "",
						'type' 		=> 'transfer',
						'desc' 		=> 'You can tranfer the saved options data between different installs by copying the text inside the text box. To import data from another install, replace the data in the text box with the one from another install and click \'Import Options\'',
				);



$of_options[] = array( 	'name' 		=> 'Documentation',
						'type' 		=> 'heading',
						'icon'		=> 'fa fa-life-ring',
						'redirect'	=> admin_url('admin.php?page=laborator-docs')
				);

$of_options[] = array( 	'name' 		=> 'Theme Documentation',
						'desc' 		=> "",
						'id' 		=> 'theme_documentation',
						'std' 		=> "<h3 style=\"margin: 0 0 10px;\">Theme Documentation</h3>
						<a href=\"" . admin_url( 'admin.php?page=laborator-docs' ) . "\">Click here to access theme documentation &raquo;</a>",
						'icon' 		=> true,
						'type' 		=> 'info'
				);

$of_options[] = array( 	'name' 		=> 'Changelog',
						'type' 		=> 'heading',
						'icon'		=> 'fa fa-code-fork',
				);
	}//End function: of_options()
	
}//End check if function exists: of_options()
