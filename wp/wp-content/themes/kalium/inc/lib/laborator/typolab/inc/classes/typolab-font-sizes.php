<?php
/**
 *	Font Sizes Generator
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class TypoLab_Font_Sizes {
	
	private static $font_sizes = array();
	
	/**
	 *	Initalize Font Sizes
	 */
	public function __construct() {
		// Initialize CSS font groups for admin
		add_action( 'admin_init', array( 'TypoLab_Font_Sizes', 'initializeFontSizeGroups' ) );
	}
	
	/**
	 *	Create Font Size Groups
	 */
	public static function initializeFontSizeGroups() {
		self::$font_sizes = array();
		
		// Theme or plugins can add their font size selector groups
		do_action( 'typolab_add_font_size_groups' );
		
		// Custom Defined Font Sizes
		$custom_font_sizes = TypoLab::getSetting( 'custom_font_sizes', array() );
		
		foreach ( $custom_font_sizes as $font_size_group ) {
			self::addFontSizeGroup( $font_size_group['title'], $font_size_group['description'], $font_size_group['selectors'], $font_size_group['builtin'], $font_size_group['id'] );
			
		}
	}
	
	/**
	 *	Add Font Size Group
	 */
	public static function addFontSizeGroup( $group_name, $group_description, $selectors = array(), $builtin = true, $id = null ) {
		$font_size_group = array(
			'id'		  => $id ? $id : null,
			'title'       => $group_name,
			'description' => $group_description,
			'selectors'   => $selectors,
			'builtin'	  => $builtin,
			'sizes' 	  => array()
		);
		
		// Assign given ID
		if ( ! $id ) {
			$font_size_group['id'] = sanitize_title( $group_name );
		}
		
		self::$font_sizes[] = $font_size_group;
	}
	
	/**
	 *	Get Defined Font Size Groups
	 */
	public static function getFontSizes( $reinitialize = false ) {
		
		if ( $reinitialize ) {
			self::initializeFontSizeGroups();
		}
		
		return self::$font_sizes;
	}
	
	/**
	 *	Get Sizes Only
	 */
	public static function getOnlySizes() {
		return TypoLab::getSetting( 'font_sizes', array() );
	}
	
	/**
	 *	Add Custom Font Size Group
	 */
	public static function addCustomFontSizeGroup( $font_size_group ) {
		$id = "custom-" . mt_rand( 1000, 9999 ) . time();
		
		$custom_font_size_group = array_merge( array( 'id' => $id ), $font_size_group );
		
		$custom_font_sizes = TypoLab::getSetting( 'custom_font_sizes', array() );
		$custom_font_sizes[] = $custom_font_size_group;
		
		TypoLab::setSetting( 'custom_font_sizes', $custom_font_sizes );
		
		// Reset Array of Defined Font Sizes
		self::initializeFontSizeGroups();
		
	}
	
	/**
	 *	Delete Custom Font Group
	 */
	public static function deleteCustomFontGroup( $group_id ) {
		$all_font_sizes = self::getFontSizes();
		$custom_font_sizes = array();
		$deleted = false;
		
		foreach ( $all_font_sizes as $i => $font_size_group ) {
			if ( false == $font_size_group['builtin'] ) {
				if ( $group_id != $font_size_group['id'] ) {
					$custom_font_sizes[] = $font_size_group;
				} else {
					$deleted = true;
					unset( self::$font_sizes[ $i ] );
				}
			}
		}
		
		// Font sizes group has been deleted
		if ( $deleted ) {
			TypoLab::setSetting( 'custom_font_sizes', $custom_font_sizes );
		
			// Delete Any Defined Custom Font Size
			$sizes = self::getOnlySizes();
			
			foreach ( $sizes as $i => $size_group ) {
				if ( $group_id == $size_group['id'] ) {
					unset( $sizes[ $i ] );
				}
			}
			
			TypoLab::setSetting( 'font_sizes', $sizes );
		}
		
		return $deleted;
	}
}

new TypoLab_Font_Sizes();