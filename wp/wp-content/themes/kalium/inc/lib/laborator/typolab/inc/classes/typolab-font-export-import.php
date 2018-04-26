<?php
/**
 *	Font Export/Import Manager
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class TypoLab_Font_Export_Import {
	
	/**
	 *	Export Settings
	 */
	public function export( $font_faces = true, $font_sizes = true, $font_settings = false ) {
		$export_object = array();
		
		// Export Font Faces
		if ( $font_faces ) {
			$font_faces_list = TypoLab::getFonts( true );
			$export_object['fontFaces'] = $font_faces_list;
		}
		
		// Export Font Sizes
		if ( $font_sizes ) {
			$font_sizes = TypoLab::getSetting( 'font_sizes' );
			$custom_font_sizes = TypoLab::getSetting( 'custom_font_sizes' );
			
			$export_object['fontSizes'] = array();
			$export_object['fontSizes']['sizes'] = $font_sizes;
			$export_object['fontSizes']['customFontSizeGroups'] = $custom_font_sizes;
		}
		
		// Export Font Settings
		if ( $font_settings ) {
			$font_settings = TypoLab::getFontSettings();
			
			$export_settings_vars = array(
				'font_placement', 'font_combining'
			);
			
			$export_object['fontSettings'] = array();
			
			foreach ( $export_settings_vars as $settings_var ) {
				if ( isset( $font_settings[ $settings_var ] ) ) {
					$export_object['fontSettings'][ $settings_var ] = $font_settings[ $settings_var ];
				}
			}
		}
		
		return $export_object;
	}
	
	/**
	 *	Import Settings
	 */
	public function import( $font_settings ) {
		$results = array();

		// Import Font Faces
		if ( isset( $font_settings['fontFaces'] ) ) {
			$results[] = $this->importFontFaces( $font_settings['fontFaces'] );
		}
		
		// Import Font Sizes
		if ( isset( $font_settings['fontSizes'] ) ) {
			$results[] = $this->importFontSizes( $font_settings['fontSizes'] );
		}
		
		// Import Font Sizes
		if ( isset( $font_settings['fontSettings'] ) ) {
			$results[] = $this->importFontSettings( $font_settings['fontSettings'] );
		}
		
		return count( array_filter( $results ) ) > 0;
	}
	
	/**
	 *	Import Font Faces
	 */
	public function importFontFaces( $font_faces ) {
		$new_font_faces = array();
		$current_font_faces = TypoLab::getSetting( 'registered_fonts', array() );
		
		foreach ( $font_faces as $i => & $font_face ) {
			$new_id = TypoLab::newId();
			$old_id = $font_face['id'];
			
			// First selector
			if ( ! empty( $font_face['options']['selectors'] ) ) {
				
				foreach ( $font_face['options']['selectors'] as & $selector ) {
					if ( $selector['selector'] == '.' . $old_id ) {
						$selector['selector'] = '.font-' . $new_id;
					}
				}
			}
			
			$font_face['id']  = 'font-' . $new_id;
			$new_font_faces[] = $font_face;
		}
		
		// Save Font Faces
		$font_faces_concatenated = array_merge( $current_font_faces, $new_font_faces );
		TypoLab::setSetting( 'registered_fonts', $font_faces_concatenated );
		
		return true;
	}
	
	/**
	 *	Import Font Sizes
	 */
	public function importFontSizes( $font_sizes ) {
		$results = array();
		
		$current_sizes = TypoLab_Font_Sizes::getOnlySizes();
		$current_custom_size_groups = TypoLab::getSetting( 'custom_font_sizes', array() );
		
		// Import Font Sizes
		if ( ! empty( $font_sizes['sizes'] ) ) {
			$new_font_sizes = array();
			
			foreach ( $font_sizes['sizes'] as $i => $size ) {
				$exists = false;
				
				// Check existing font size if already exists
				foreach ( $current_sizes as $j => $current_size ) {
					// Replace existing font size
					if ( $size['id'] == $current_size['id'] ) {
						$exists = true;
						$current_sizes[ $j ] = $size;
					}
				}
				
				// Create new font size parameters
				if ( ! $exists ) {
					$new_font_sizes[] = $size;
				}
			}
			
			// Save new Font Sizes
			$font_sizes_concatenated = array_merge( $current_sizes, $new_font_sizes );
			TypoLab::setSetting( 'font_sizes', $font_sizes_concatenated );
			
			// This step was successful
			$results[] = true;
		}
		
		// Import Custom Font Size Groups
		if ( ! empty( $font_sizes['customFontSizeGroups'] ) ) {
			$new_font_size_groups = array();
			
			foreach ( $font_sizes['customFontSizeGroups'] as $i => $font_size_group ) {
				$exists = false;
				
				// Check current custom font size group if already exists
				foreach ( $current_custom_size_groups as $j => $current_font_size_group ) {
					
					// Replace existing custom font size group
					if ( $font_size_group['id'] == $current_font_size_group['id'] ) {
						$exists = true;
						$current_custom_size_groups[ $j ] = $font_size_group;
						break;
					}
				}
				
				// Create new font size group
				if ( ! $exists ) {
					$new_font_size_groups[] = $font_size_group;
				}
			}
			
				
			// Save new custom Font Size Groups
			$custom_font_sizes_concatenated = array_merge( $current_custom_size_groups, $new_font_size_groups );
			TypoLab::setSetting( 'custom_font_sizes', $custom_font_sizes_concatenated );
			
			// This step was successful
			$results[] = true;
		}
	}
	
	/**
	 *	Import Font Settings
	 */
	public function importFontSettings( $import_font_settings ) {
		
		if ( is_array( $import_font_settings ) ) {			
			$font_settings_concatenated = array_merge( TypoLab::getFontSettings(), $import_font_settings );
			TypoLab::setSetting( 'font_settings', $font_settings_concatenated );
			
			return true;
		}
		
		return false;
	}
}