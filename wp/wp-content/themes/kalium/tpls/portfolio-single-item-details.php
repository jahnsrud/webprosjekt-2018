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

global $portfolio_args;

// Get Portfolio Options
$portfolio_args = kalium_get_portfolio_query( array( 'no_query' => true ) );

// Global Data
$portfolio_share_item           = get_data( 'portfolio_share_item' );
$portfolio_share_item_networks  = get_data( 'portfolio_share_item_networks' );
$portfolio_likes                = get_data( 'portfolio_likes' );
$portfolio_prev_next            = get_data( 'portfolio_prev_next' );
$portfolio_caption_position		= get_data( 'portfolio_gallery_caption_position' );

// Thumbnail
$has_thumbnail                  = has_post_thumbnail();
$post_id						= get_the_id();
$post_thumbnail_id              = get_post_thumbnail_id();

// Portfolio Details
$sub_title                      = kalium()->acf->get_field( 'sub_title' );

$checklists                     = kalium()->acf->get_field( 'checklists' );

$launch_link_title              = kalium()->acf->get_field( 'launch_link_title' );
$launch_link_href               = kalium()->acf->get_field( 'launch_link_href' );
$new_window                     = kalium()->acf->get_field( 'new_window' );

$gallery_items                  = kalium()->acf->get_field( 'gallery' );
$gallery_type                   = kalium()->acf->get_field( 'gallery_type' );
$gallery_stick_to_top           = kalium()->acf->get_field( 'gallery_stick_to_top' );

$images_reveal_effect           = kalium()->acf->get_field( 'images_reveal_effect' );
$image_spacing                  = kalium()->acf->get_field( 'image_spacing' );

$description_alignment          = kalium()->acf->get_field( 'item_description_alignment' );
$description_width              = kalium()->acf->get_field( 'item_description_width' );
$sticky_description             = kalium()->acf->get_field( 'sticky_description' );

// Custom Archive URL
$portfolio_custom_archive_url	= kalium()->acf->get_field( 'custom_archive_url' );

// Captions Position
$image_captions_position		= kalium()->acf->get_field( 'image_captions_position' );

if ( empty( $image_captions_position ) ||  'inherit' == $image_captions_position ) {
	$image_captions_position = $portfolio_caption_position;
}

// Portfolio Type 2 Information
$layout_type                    = kalium()->acf->get_field( 'layout_type' );
$share_and_like_position        = kalium()->acf->get_field( 'share_and_like_position' );
$gallery_columns_gap			= kalium()->acf->get_field( 'gallery_columns_gap' );
$full_width_gallery             = kalium()->acf->get_field( 'full_width_gallery' );
$show_featured_image            = kalium()->acf->get_field( 'show_featured_image' );
$fullwidth_featured_image       = kalium()->acf->get_field( 'fullwidth_featured_image' );
$title_position                 = kalium()->acf->get_field( 'title_position' );


if ( ! is_array( $gallery_items ) ) {
	$gallery_items = array();
}

if ( ! is_array( $checklists ) ) {
	$checklists = array();
}