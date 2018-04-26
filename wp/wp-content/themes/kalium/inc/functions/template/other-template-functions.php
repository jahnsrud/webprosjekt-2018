<?php
/**
 *	Kalium WordPress Theme
 *
 *	Other Template Functions
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Header WPML Language Switcher
 */
if ( ! function_exists( 'kalium_wpml_language_switcher' ) ) {
	
	function kalium_wpml_language_switcher( $skin = '' ) {
		if ( ! function_exists( 'icl_object_id' ) || ! get_data( 'header_wpml_language_switcher' ) ) {
			return;
		}
		
		$languages = icl_get_languages( apply_filters( 'kalium_header_icl_get_languages_args', 'skip_missing=0' ) );
		
		if ( count( $languages ) > 1 ) {
			$current_language = null;
			
			foreach ( $languages as $lang_code => $language ) {
				if ( ICL_LANGUAGE_CODE == $lang_code ) {
					$current_language = $language;
					unset( $languages[ $lang_code ] );
				}
			}
		?>
		<div class="kalium-wpml-language-switcher <?php echo esc_attr( $skin ); ?>" data-show-on="<?php echo get_data( 'header_wpml_language_trigger' ); ?>">
			
			<div class="languages-list">
				
				<?php
						
					// Current Language
					kalium_wpml_language_switcher_item( $current_language );
				
					foreach ( $languages as $lang_code => $language ) {
						
						// Show language entry
						kalium_wpml_language_switcher_item( $language );
					}
				?>
				
			</div>
		</div>	
		<?php
		}
	}
}

/**
 * Inner function of "kalium_wpml_language_switcher"
 *
 * @private
 */
if ( ! function_exists( 'kalium_wpml_language_switcher_item' ) ) {
	
	function kalium_wpml_language_switcher_item( $language ) {
		// Language widget settings
		$flag_position = get_data( 'header_wpml_language_flag_position' );
		$display_text = get_data( 'header_wpml_language_switcher_text_display_type' );
		
		// Details
		$code			 = $language['code'];
		$native_name     = $language['native_name'];
		$translated_name = $language['translated_name'];
		$flag_url        = $language['country_flag_url'];
		$url             = $language['url'];
		
		$is_active       = 1 == $language['active'];
		
		// Display name
		$name = '';
		
		switch ( $display_text ) {
			case 'name':
				$name = $native_name;
				break;
				
			case 'translated':
				$name = $translated_name;
				break;
				
			case 'initials':
				$name = $code;
				break;
				
			case 'name-translated':
				$name = "{$native_name} <em>({$translated_name})</em>";
				break;
				
			case 'translated-name':
				$name = "{$translated_name} <em>({$native_name})</em>";
				break;
		}
		
		$classes = array( 'language-entry' );
		
		if ( $is_active ) {
			$classes[] = 'current-language';
		}
		
		$classes[] = 'flag-' . $flag_position;
		$classes[] = 'text-' . $display_text;
		
		?>
		<a href="<?php echo $url; ?>" class="<?php echo implode( ' ', $classes ); ?>">
			<?php if ( 'hide' !== $flag_position ) : ?>
			<span class="flag"><img src="<?php echo $flag_url; ?>" alt="<?php echo $code; ?>"></span>
			<?php endif; ?>
			
			<?php if ( $name ) : ?>
			<span class="text"><?php echo apply_filters( 'kalium_header_show_wpml_language_switcher_item_name', $name, $language ); ?></span>
			<?php endif; ?>
		</a>
		<?php
	}
}