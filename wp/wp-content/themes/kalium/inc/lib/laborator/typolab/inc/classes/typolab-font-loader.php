<?php
/**
 *	TypoLab Front-end Font Loader
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class TypoLab_Font_Loader extends TypoLab {
	
	/**
	 *	Font Combine URL
	 */
	public $font_combine_script;
	
	/**
	 *	Fonts List
	 */
	private $fonts_list;
	
	/**
	 *	Font Sizes
	 */
	private $font_sizes;
	
	/**
	 *	Font Settings
	 */
	private $font_settings;
	
	/**
	 *	Last Modified Checksum
	 */
	private $checksum;
	
	/**
	 *	Defined Post Types
	 */
	private $post_types = array();
	
	/**
	 *	Defined Taxonomies
	 */
	private $taxonomies = array();
	
	/**
	 *	Hosted Fonts to Enqueue
	 */
	private $enqueue_hosted_fonts = array();
	
	/**
	 *	Font Variants CSS Selectors
	 */
	private $font_variants_selectors = array();
	
	/**
	 *	Cache Outputs
	 */
	private $cache_output = true;
	
	/**
	 *	Initialize Font Loader
	 */
	public function __construct() {

		// Font Settings
		$font_settings = self::getFontSettings();
		
		// Disable font loader in admin pages
		if ( is_admin() || ( isset( $font_settings['typolab_enabled'] ) && false == $font_settings['typolab_enabled'] ) || ( isset( $GLOBALS['pagenow'] ) && in_array( $GLOBALS['pagenow'], array( 'wp-login.php' ) ) ) ) {
			return;
		}
		
		// Get Fonts List
		$this->fonts_list = $this->getFonts( true, true );
		
		// Get Font Sizes
		$this->font_sizes = TypoLab_Font_Sizes::getOnlySizes();
		
		// Checksum
		$this->checksum = md5( serialize( array( $this->font_sizes, $this->fonts_list ) ) );
		
		// Do not run if there are not fonts at all
		if ( ! ( $this->fonts_list || $this->font_sizes ) ) {
			return;
		}
		
		// Check Conditional Statements and Group Fonts by Font Placement and Source
		add_action( 'wp', array( $this, 'checkForConditionaFontlLoading' ), 10 );
		add_action( 'wp', array( $this, 'groupFontsByPlacementAndSource' ), 11 );
		
		// Enqueue Fonts in Frontend
		add_filter( 'wp_enqueue_scripts', array( $this, 'enqueueQualifiedFonts' ), 10 );
		
		// Print Font Variant Selectors
		add_filter( 'wp_print_scripts', array( $this, 'printFontVariants' ), 1000 );
	}
	
	/**
	 *	Filter fonts by their conditional rules
	 */
	public function checkForConditionaFontlLoading() {

		foreach ( $this->fonts_list as $i => $font ) {
			// Check for conditional statements
			if ( isset( $font['options']['conditional_loading'] ) && ! $this->canLoadThisFont( $font['options']['conditional_loading'] ) ) {
				unset( $this->fonts_list[ $i ] );
			}
		}
	}
	
	/**
	 *	Group fonts by placement
	 */
	public function groupFontsByPlacementAndSource() {
		$grouped = array();

		$this->font_settings = self::getFontSettings();
		$default_font_placement = ! empty( $this->font_settings['font_placement'] ) ? $this->font_settings['font_placement'] : 'head';
		
		foreach ( $this->fonts_list as $font ) {
			$font_source = $font['source'];
			$font_placement = ! empty( $font['font_placement'] ) ? $font['font_placement'] : $default_font_placement;
			
			// Create placement group if not exists
			if ( ! isset( $grouped[ $font_placement ] ) ) {
				$grouped[ $font_placement ] = array();
			}
			
			// Create source group if not exists
			if ( ! isset( $grouped[ $font_placement ][ $font_source ] ) ) {
				$grouped[ $font_placement ][ $font_source ] = array();
			}
			
			$font['font_placement'] = $font_placement;
			$grouped[ $font_placement ][ $font_source ][] = $font;
		}
		
		$this->fonts_list = $grouped;
	}
	
	/**
	 *	Enqueue Qualified Fonts
	 */
	public function enqueueQualifiedFonts() {
		// Enqueue Defined Fonts
		$font_selectors = array();
		
		foreach ( $this->fonts_list as $font_placement => $fonts_by_source ) {
			foreach ( $fonts_by_source as $font_source => $fonts ) {
				// Enqueue Fonts
				$this->enqueueFonts( $font_source, $font_placement, $fonts );
				
				// Get CSS Selectors
				if ( is_array( $fonts ) ) {
					foreach ( $fonts as $font ) {
						if ( isset( $font['options']['selectors'] ) ) {
							$css_selectors = $font['options']['selectors'];
							$font_data = isset( $font['options']['data'] ) ? $font['options']['data'] : null;
							
							foreach ( $css_selectors as & $css_selector ) {
								$css_selector['source'] = $font_source;
								$css_selector['data'] = $font_data;
							}
							
							$font_selectors = array_merge( $font_selectors, $css_selectors );
						}
					}
				}
			}
		}
		
		// Enable or disable cache output
		$this->cache_output = apply_filters( 'typolab_font_selectors_cache_output', $this->cache_output );
		
		// Create Font Variants and Font Sizes
		$font_variants_output = get_theme_mod( "typolab_font_variants_and_sizes_output_{$this->checksum}", '' );
		
		if ( ! $this->cache_output || ! $font_variants_output ) {
			$this->createFontVariantsSelectorsAndFontSizes( $font_selectors );
		}
		
		// Enqueue Hosted Font Files
		if ( $this->enqueue_hosted_fonts ) {
			$font_combining = isset( $this->font_settings['font_combining'] ) ? $this->font_settings['font_combining'] : true;
			$this->enqueueHostedFonts( $font_combining );
		}
	}
	
	/**
	 *	Enqueue Hosted Fonts
	 */
	public function enqueueHostedFonts( $combine_files = true ) {
		$uploads = wp_upload_dir();
		
		// Combine Files
		if ( $combine_files ) {
			
			foreach ( $this->enqueue_hosted_fonts as $font_placement => $fonts ) {
				$files_to_include = array_map( 'rawurlencode', $fonts );
				
				// Get Combine File
				$combine_font_url = $this->combineFonts( $fonts );
				
				// Enqueue Font by URL
				$this->enqueueFontURL( $combine_font_url, $font_placement );
			}
			
		}
		// Enqueue Hosted Files Separately
		else {
			foreach ( $this->enqueue_hosted_fonts as $font_placement => $fonts ) {
				foreach ( $fonts as $font_url ) {
					$font_full_url = "{$uploads['baseurl']}/{$font_url}";
					$this->enqueueFontURL( $font_full_url, $font_placement );
				}
			}
		}
	}
	
	/**
	 *	Check if font can be loaded based on conditional statements
	 */
	public function canLoadThisFont( $conditional_statements ) {
		
		if ( ! count( $conditional_statements ) ) {
			return true;
		}
		
		foreach ( $conditional_statements as $condition ) {
			if ( $this->validateStatement( $condition ) ) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 *	Validate Conditional Statement
	 */
	public function validateStatement( $condition ) {
		global $wp_query;
		
		$result = false;
		
		if ( ! isset( $condition['statement'] ) || ! isset( $condition['operator'] ) || ! isset( $condition['criteria'] ) ) {
			return false;
		}
		
		// Register Post Types and Taxonomies
		if ( empty( $this->post_types ) ) {
			$this->post_types = array_merge( array( 'post', 'page' ), array_values( get_post_types( array( 'public' => true, '_builtin' => false ), 'names' ) ) );
			
			// Post Type Taxonomies
			foreach ( $this->post_types as $post_type ) {
				$this->taxonomies = array_merge( $this->taxonomies, get_object_taxonomies( $post_type, 'names' ) );
				
			}
		}
		
		// $statement, $operator, $criteria
		extract( $condition );
		
		// Post Type Validate
		if ( 'post_type' == $statement ) {
			
			$result = is_post_type_archive( $condition ) || is_singular( $criteria );
			
			// Special for blog posts check
			if ( 'post' == $criteria ) {
				$result = is_singular( $criteria ) || in_array( true, array( $wp_query->is_posts_page, $wp_query->is_category, $wp_query->is_tag, $wp_query->is_year, $wp_query->is_month, $wp_query->is_day, $wp_query->is_date ) );
			}
		}
		// Custom Page Types
		else if ( 'page_type' == $statement ) {
			
			switch ( $criteria ) {
				case 'frontpage':
					$result = is_front_page();
					break;
				
				case 'blog':
					$result = is_home();
					break;
				
				case 'search':
					$result = is_search();
					break;
					
				case 'not_found':
					$result = is_404();
					break;
			}
		}
		// Page Template
		else if ( 'page_template' == $statement ) {
			$page_template = basename( get_page_template() );

			$result = $criteria == $page_template;
		}
		// Post Item
		else if ( in_array( $statement, $this->post_types ) ) {
			$result = is_singular( $statement );
			
			// Check if is single item ID
			if ( $criteria && $result ) {
				//$result = is_single( $criteria );
				$result = $criteria == get_queried_object_id();
			}
		}
		// Taxonomy
		else if ( in_array( $statement, $this->taxonomies ) ) {
			$result = is_tax( $statement );
			
			switch ( $statement ) {
				case 'category':
					$result = is_category();
					break;
					
				case 'tag':
					$result = is_tag();
					break;
					
				default:
					$result = is_tax( $statement );
			}
			
			// Check if is current term
			if ( $result && $criteria ) {
			
				switch ( $statement ) {
					case 'category':
						$result = is_category( $criteria );
						break;
						
					case 'tag':
						$result = is_tag( $criteria );
						break;
						
					default:
						$result = is_tax( $statement, $criteria );
				}
			}
		}
			
		// Not equals operator
		if ( '!=' == $operator ) {
			$result = ! $result;
		}
	
		return $result;
	}
	
	/**
	 *	Enqueue Fonts to page
	 */
	public function enqueueFonts( $font_source, $font_placement, $fonts ) {
		switch ( $font_source ) {
			// Google Fonts Enqueue
			case 'google':
				$this->enqueueFontsFromGoogle( $fonts, $font_placement );
				break;
			
			// Premium Fonts and Font Squirrel Enqueue
			case 'premium':
			case 'font-squirrel':
				$this->enqueueFontsFromHostedFiles( $fonts, $font_placement, $font_source );
				break;
				
			// TypeKit Enqueue
			case 'typekit':
				$this->enqueueFontsFromTypeKit( $fonts, $font_placement );
				break;
			
			// Custom Font
			case 'custom-font':
				$this->enqueueFontsFromCustomFonts( $fonts, $font_placement );
				break;
		}
	}
	
	/**
	 *	Enqueue Font Based on Placement
	 */
	public function enqueueFontURL( $font_url, $font_placement ) {
		$font_id = 'typolab-' . md5( $font_url );
		
		// Enqueue font in <head>
		if ( 'head' == $font_placement ) {
			wp_enqueue_style( $font_id, $font_url, null, $this->checksum );
		}
		// Enqueue font before footer
		else {
			$font_import_fn = kalium_hook_call_user_function( 'wp_enqueue_style', $font_id, $font_url, null, $this->checksum );
			add_action( 'wp_footer', $font_import_fn );
		}
	}
	
	/**
	 *	Compress CSS
	 */
	public function compressCSS( $buffer ){
		/* remove comments */
		$buffer = preg_replace( "!/\*[^*]*\*+([^/][^*]*\*+)*/!", "", $buffer );
		/* remove tabs, spaces, newlines, etc. */
		$arr = array( "\r\n", "\r", "\n", "\t", "  ", "    ", "    " );
		$rep = array( "", "", "", "", " ", " ", " " );
		$buffer = str_replace( $arr, $rep, $buffer );
		/* remove whitespaces around {}:, */
		$buffer = preg_replace( "/\s*([\{\}:,])\s*/", "$1", $buffer );
		/* remove last ; */
		$buffer = str_replace( ';}', "}", $buffer );
		
		return $buffer;
	}
	
	/**
	 *	Combine Fonts
	 */
	public function combineFonts( $fonts ) {
		// File ID
		$file_id = md5( maybe_serialize( $fonts ) );
		
		// Get File Combines
		$font_import_files = self::getSetting( 'combined_font_import_files', array() );
		
		if ( isset( $font_import_files[ $file_id ] ) && file_exists( $font_import_files[ $file_id ]['path'] ) ) {
			return $font_import_files[ $file_id ]['url'];
		}
		
		// Uploads and TypoLab Directory
		$uploads = wp_upload_dir();
		$fonts_path = rtrim( self::$fonts_path, '/' );
		$fonts_url = rtrim( self::$fonts_url, '/' );
		
		// CSS File
		$css_file      = "fonts-{$file_id}.css";
		$css_file_path = "{$fonts_path}/{$css_file}";
		$css_file_url  = "{$fonts_url}/{$css_file}";
		
		// Combine Font Files
		$contents = array();
		
		foreach ( $fonts as $font ) {
			$file_path = "{$uploads['basedir']}/{$font}";
			
			if ( file_exists( $file_path ) ) {
				$font_relative_url = ltrim( str_replace( basename( $fonts_path ), '', dirname( $font ) ), '/' ) . '/';
				
				$file_contents = file_get_contents( $file_path );
				$file_contents = preg_replace( "/url\('/i", "url('" . $font_relative_url, $file_contents );
				
				// Append CSS Output to contents
				$contents[] = $file_contents;
			}
		}
		
		// Join CSS Contents
		$css = implode( PHP_EOL, $contents );
		
		// Compress CSS
		if ( apply_filters( 'typolab_combined_fonts_compress_output', true ) ) {
			$css = self::compressCSS( $css );
		}
		
		// Create File
		if ( $css ) {
			$font_import_files[ $file_id ] = array(
				'path' => $css_file_path,
				'url' => $css_file_url
			);
			
			self::setSetting( 'combined_font_import_files', $font_import_files );
			
			@file_put_contents( $css_file_path, $css );
		}
		
		return $css_file_url;
		
	}
	
	/**
	 *	Group Enqueue Google Fonts
	 */
	public function enqueueFontsFromGoogle( $fonts, $font_placement ) {
		$google_fonts_url = apply_filters( 'typolab_google_font_import_url', 'https://fonts.googleapis.com/css' );
		
		$font_families = array();
		$font_subsets = array();
		
		// Group Google Fonts Import
		foreach ( $fonts as $font ) {
			if ( isset( $font['options']['data'] ) ) {
				$google_font = $font['options']['data'];
				$font_family = urlencode( $google_font->family );
				
				if ( ! empty( $google_font->variants ) ) {
					$font_family .= ':' . implode( ',', $google_font->variants );
				}
				
				$font_families[] = $font_family;
				$font_subsets = array_merge( $font_subsets, $google_font->subsets );
			}
		}
		
		// Enqueue Fonts
		if ( $font_families ) {
			$font_families = implode( '|', $font_families );
			$font_subsets = implode( ',', array_unique( $font_subsets ) );
			
			$font_import_url = "{$google_fonts_url}?family={$font_families}&amp;subsets={$font_subsets}";
			$font_id = 'google-fonts-' . md5( $font_import_url );
			
			$this->enqueueFontURL( $font_import_url, $font_placement );
		}
	}
	
	/**
	 *	Group Enqueue Hosted Fonts
	 */
	public function enqueueFontsFromHostedFiles( $fonts, $font_placement, $font_source ) {

		switch ( $font_source ) {
			// Downloaded Fonts from Premium Fonts
			case 'premium':
				$downloaded_fonts = TypoLab_Premium_Fonts::getDownloadedFonts();
				break;
			
			// Downloaded Fonts from Font Squirrel
			case 'font-squirrel':
				$downloaded_fonts = TypoLab_Font_Squirrel::getDownloadedFonts();
				break;
		}
		
		if ( ! isset( $downloaded_fonts ) || ! $downloaded_fonts ) {
			return false;
		}
		
		foreach ( $fonts as $font ) {
			if ( isset( $font['options']['data'] ) ) {
				$font_data = $font['options']['data'];
				
				switch ( $font_source ) {
					// Premium Font path determine
					case 'premium':
						$family_urlname = $font_data->family_urlname;
						break;
					
					// Font Squirrel path determine
					case 'font-squirrel':
						$font_data = is_array( $font_data ) ? reset( $font_data ) : $font_data;
						$family_urlname = $font_data->family_urlname;
						break;
				}
				
				// Check if downloaded font exists and enqueue it
				if ( isset( $downloaded_fonts[ $family_urlname ] ) ) {
					$font_path = $downloaded_fonts[ $family_urlname ]['path'];
					$font_file = "{$font_path}/load.css";
					
					// Add font to enqueue hosted fonts list
					if ( ! isset( $this->enqueue_hosted_fonts[ $font_placement ] ) ) {
						$this->enqueue_hosted_fonts[ $font_placement ] = array();
					}
					
					$this->enqueue_hosted_fonts[ $font_placement ][] = $font_file;
				}
			}
		}

	}
	
	/**
	 *	Group Enqueue TypeKit Fonts
	 */
	public function enqueueFontsFromTypeKit( $fonts, $font_placement ) {
		
		foreach ( $fonts as $font ) {
			$kit_id = isset( $font['kit_id'] ) ? $font['kit_id'] : '';
			
			if ( $kit_id ) {
				$font_import_fn = kalium_hook_call_user_function( 'TypoLab_TypeKit_Fonts::embedKitJS', $kit_id );
				
				if ( 'head' == $font_placement ) {
					add_action( 'wp_print_styles', $font_import_fn );
				} else {
					add_action( 'wp_footer', $font_import_fn );
				}
			}
		}
	}
	
	/**
	 *	Group Enqueue Custom Fonts
	 */
	public function enqueueFontsFromCustomFonts( $fonts, $font_placement ) {
		
		foreach ( $fonts as $font ) {
			$font_url = isset( $font['options']['font_url'] ) ? $font['options']['font_url'] : '';
			
			if ( $font_url ) {
				// Enqueue Font
				$this->enqueueFontURL( $font_url, $font_placement );
			}
		}
	}
	
	/**
	 *	Create Font Variants CSS
	 */
	private function createFontVariantsSelectorsAndFontSizes( $variants ) {
		foreach ( $variants as $variant ) {
			$font_selector = $variant['selector'];
			$font_variant = $variant['variant'];
			$font_source = $variant['source'];
			$font_data = $variant['data'];
			
			$css_variant_selector = array(
				'path' => $font_selector,
				'properties' => ''
			);
			
			// Generate Font Variant CSS Selectors
			switch ( $font_source ) {
				// Google Font Variant
				case 'google':
					$font_family = $font_data->family;
					$font_weight = $this->numericValue( $font_variant );
					$is_italic = strpos( $font_variant, 'italic' ) !== false;
					
					if ( ! $font_weight ) {
						$font_weight = 400;
					}
					
					// Font Family
					$css_variant_selector['properties'] .= "font-family: '" . addslashes( $font_family ) . "';";//, sans-serif;";
					
					if ( $font_weight ) {
						$css_variant_selector['properties'] .= "font-weight: {$font_weight};";
					}
					
					if ( $is_italic ) {
						$css_variant_selector['properties'] .= 'font-style: italic;';
					}
					break;
				
				// Default Font Handler
				default:
					$css_variant_selector['properties'] .= "font-family: '" . addslashes( $font_variant ) . "';";//, sans-serif;";
			}
			
			// Add Variant
			$this->font_variants_selectors[] = $css_variant_selector;
		}
		
		// Defined Font Sizes
		$general_sizes = $desktop_sizes = $tablet_sizes = $mobile_sizes = $font_cases = array();
		
		// Generate Font Sizes from Vraiants
		foreach ( $variants as $variant ) {
			$font_selector = $variant['selector'];
			$font_sizes = $variant['font-sizes'];
			$text_transform = get_array_key( $variant, 'text-transform' );
			
			// Viewport Font Sizes
			$size_general = $this->numericValue( $font_sizes['general'] );
			$size_desktop = $this->numericValue( $font_sizes['desktop'] );
			$size_tablet  = $this->numericValue( $font_sizes['tablet'] );
			$size_mobile  = $this->numericValue( $font_sizes['mobile'] );

			// Font Unit
			$size_unit = $font_sizes['unit'];
			
			// Font size: General
			if ( $size_general ) {
				$general_sizes[] = array(
					'path' => $font_selector,
					'size' => $size_general,
					'unit' => $size_unit
				);
			}
			
			// Font size: Desktop
			if ( $size_desktop ) {
				$desktop_sizes[] = array(
					'path' => $font_selector,
					'size' => $size_desktop,
					'unit' => $size_unit
				);
			}
			
			// Font size: Tablet
			if ( $size_tablet ) {
				$tablet_sizes[] = array(
					'path' => $font_selector,
					'size' => $size_tablet,
					'unit' => $size_unit
				);
			}
			
			// Font size: Mobile
			if ( $size_mobile ) {
				$mobile_sizes[] = array(
					'path' => $font_selector,
					'size' => $size_mobile,
					'unit' => $size_unit
				);
			}
			
			// Font cases
			if ( $text_transform ) {
				$font_cases[] =	array(
					'path' => $font_selector,
					'case' => $text_transform,
				);
			}
		}
		
		// Generate Global Custom Font Sizes
		foreach ( $this->font_sizes as $font_size_group ) {
			$selectors       = $font_size_group['selectors'];
			$selector_sizes  = $font_size_group['sizes'];
			$unit            = $font_size_group['unit'];
			
			foreach ( $selector_sizes as $selector_id => $sizes ) {
				// Get CSS selector from alias
				$selectors_ids = array_keys( $selectors );
				$sanitized_selectors = array_map( 'sanitize_title', $selectors_ids );
				$selector_path = array_search( $selector_id, $sanitized_selectors );
				
				$text_transform	 = get_array_key( $sizes, 'text-transform' );
				
				if ( false !== $selector_path ) {
					$selector_path = $selectors[ $selectors_ids[ $selector_path ] ];
					
					// Font sizes
					foreach ( self::$viewport_breakpoints as $device_type => $breakpoints ) {
						if ( ! empty( $sizes[ $device_type ] ) ) {
							$size = $this->numericValue( $sizes[ $device_type ] );
							
							// Get viewport variable
							switch ( $device_type ) {
								case 'general':
									$viewport_var = 'general_sizes';
									break;
									
								case 'desktop':
									$viewport_var = 'desktop_sizes';
									break;
									
								case 'tablet':
									$viewport_var = 'tablet_sizes';
									break;
									
								case 'mobile':
									$viewport_var = 'mobile_sizes';
									break;
							}
							
							// Add size to selectors
							${$viewport_var}[] = array(
								'path' => $selector_path,
								'size' => $size,
								'unit' => $unit
							);
						}
					}
					
					// Font case
					if ( ! empty( $text_transform ) ) {
						$font_cases[] = array(
							'path' => $selector_path,
							'case' => $text_transform
						);
					}
				}
			}
		}

		// Assign Sizes
		if ( $general_sizes ) {
			$this->font_variants_selectors = array_merge( $this->font_variants_selectors, $this->generateFontSizes( $general_sizes, 'general' ) );
		}
		
		if ( $desktop_sizes ) {
			$this->font_variants_selectors = array_merge( $this->font_variants_selectors, $this->generateFontSizes( $desktop_sizes, 'desktop' ) );
		}
		
		if ( $tablet_sizes ) {
			$this->font_variants_selectors = array_merge( $this->font_variants_selectors, $this->generateFontSizes( $tablet_sizes, 'tablet' ) );
		}
		
		if ( $mobile_sizes ) {
			$this->font_variants_selectors = array_merge( $this->font_variants_selectors, $this->generateFontSizes( $mobile_sizes, 'mobile' ) );
		}
		
		if ( $font_cases ) {
			$this->font_variants_selectors = array_merge( $this->font_variants_selectors, $this->generateFontCases( $font_cases ) );
		}
	}
	
	/**
	 *	Generate Font Size CSS
	 */
	public function generateFontSizes( $font_sizes, $viewport = 'general' ) {
		$css_selectors = array();
		$iewport       = isset( self::$viewport_breakpoints[ $viewport ] ) ? self::$viewport_breakpoints[ $viewport ] : array( null, null );
		$min_width     = $iewport[0];
		$max_width     = $iewport[1];
		
		$media = '';
		
		// Set current media
		if ( $min_width || $max_width ) {
			$media .= '@media screen';
			
			// Min width viewport
			if ( $min_width ) {
				$media .= " and (min-width: {$min_width}px)";
			}
			
			// Max width viewport
			if ( $max_width ) {
				$media .= " and (max-width: {$max_width}px)";
			}
		}
		
		foreach ( $font_sizes as $font_size ) {
			$selector = $font_size['path'];
			$size = $font_size['size'];
			$unit = $font_size['unit'];
			
			$css_selectors[] = array(
				'media' => $media,
				'path' => $selector,
				'properties' => "font-size: {$size}{$unit};"
			);
		}
		
		return $css_selectors;
	}
	
	/**
	 *	Generate Font Case CSS
	 */
	public function generateFontCases( $font_cases ) {
		$css_selectors = array();
		
		foreach ( $font_cases as $font_case ) {
			$selector = $font_case['path'];
			$case = $font_case['case'];
			
			$css_selectors[] = array(
				'path' => $selector,
				'properties' => "text-transform: {$case};"
			);
		}
		
		return $css_selectors;
	}
	
	/**
	 *	Print Font Variants in <head> section
	 */
	public function printFontVariants() {
		$styles = '';
		$media_query_grouped = array();
		$font_variants_and_sizes_var = "typolab_font_variants_and_sizes_output_{$this->checksum}";
		
		// Serve cached output
		if ( $this->cache_output && $font_variants_output = get_theme_mod( $font_variants_and_sizes_var, '' ) ) {
			echo $font_variants_output;
			return;
		}
		
		foreach ( $this->font_variants_selectors as $selector ) {
			$media = isset( $selector['media'] ) ? $selector['media'] : '';
			
			if ( ! isset( $media_query_grouped[ $media ] ) ) {
				$media_query_grouped[ $media ] = array();
			}
			
			$media_query_grouped[ $media ][] = $selector;
		}
		
		foreach ( $media_query_grouped as $media => $selectors ) {
			// Media Selector Container
			if ( $media ) {
				$styles .= $media . ' {' . PHP_EOL;
			}
			
			foreach ( $selectors as $selector ) {
				$styles .=  $selector['path'] . ' {' . $selector['properties'] . '}';
				$styles .= PHP_EOL;
			}
			
			// Media Selector End
			if ( $media ) {
				$styles .= '}' . PHP_EOL;
			}
		}
		
		if ( apply_filters( 'typolab_font_variants_compress_output', true ) ) {
			$styles = self::compressCSS( $styles );
		}
		
		$output = "<style id=\"typolab-font-variants\">{$styles}</style>";
		
		// Save generated style output for caching
		set_theme_mod( $font_variants_and_sizes_var, $output );
				
		echo $output;
	}
	
	/**
	 *	Get Numeric Value from String
	 */
	public function numericValue( $str ) {
		if ( preg_match( "/([0-9\.]+)/", $str, $matches ) ) {
			$number = $matches[1];
			
			if ( false !== strpos( $number, '.' ) ) {
				return floatval( $number );
			}
			
			return intval( $number );
			
		}
		return '';
	}
	
	
	/**
	 *	Delete Font CSS Cache
	 */
	public static function deleteFontSelectorsCache() {
		$key = 'typolab_font_variants_and_sizes_output_';
		$theme_mods = array_keys( get_theme_mods() );
		
		foreach ( $theme_mods as $theme_mod ) {
			if ( false !== strpos( $theme_mod, 'typolab_font_variants_and_sizes_output_' ) ) {
				remove_theme_mod( $theme_mod );
			}
		}
	}
}