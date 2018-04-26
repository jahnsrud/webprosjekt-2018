<?php
/**
 *	Scroll Box
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

// Element Information
$lab_vc_element_icon = kalium()->locateFileUrl( 'inc/lib/vc/lab_scroll_box/scrollbox.svg' );

vc_map( array(
	'base'             => 'lab_scroll_box',
	'name'             => 'Scroll Box',
	"description"      => "Content with scrollbar",
	'category'         => 'Laborator',
	'icon'             => $lab_vc_element_icon,
	'params' => array(
		array(
			'type'           => 'textfield',
			'heading'        => 'Height',
			'param_name'     => 'scroll_height',
			'description'	 => 'Set the maximum height of content box, scrollbar will be visible when there is more text.',
			'admin_label'    => true,
			'value'          => '450'
		),
		array(
			'type'       => 'textarea_html',
			'heading'    => 'Content',
			'param_name' => 'content',
			'value'      => ''
		),
		array(
			'type'           => 'textfield',
			'heading'        => 'Extra class name',
			'param_name'     => 'el_class',
			'description'    => 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'
		),
		array(
			'type'       => 'css_editor',
			'heading'    => 'Css',
			'param_name' => 'css',
			'group'      => 'Design options'
		)
	)
) );

class WPBakeryShortCode_Lab_Scroll_Box extends WPBakeryShortCode {}