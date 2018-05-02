<?php
/**
 *	Google Fonts List
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

$fonts_list         = TypoLab_Google_Fonts::getFontsList();
$alphabetic_letters = TypoLab_Google_Fonts::groupFontsByFirstLetter();
$font_categories    = TypoLab_Google_Fonts::groupFontsByCategory();


$selected_letter = reset( $alphabetic_letters );
$selected_font = '';

$font_variants = $font_subsets = array();

// Set Selected Font
if ( isset( $font['valid'] ) && true === $font['valid'] ) {
	$font_family   = $font['family'];
	$font_variants = $font['variants'];
	$font_subsets  = $font['subsets'];
	$font_data     = isset( $font['options']['data'] ) ? $font['options']['data'] : array();
	
	$selected_font = $font_family;
	$selected_letter = $alphabetic_letters[ strtoupper( substr( $font_family, 0, 1 ) ) ];
}
?>
<div class="fonts-list-select" data-font-source="google-fonts" data-current-font="<?php echo esc_attr( $selected_font ); ?>" data-current-font-variants="<?php echo implode( ',', $font_variants ); ?>" data-current-font-subsets="<?php echo implode( ',', $font_subsets ); ?>">
	<div class="alphabet">
	<?php foreach ( $alphabetic_letters as $letter_group ) : ?>
		<a href="#" data-letter="<?php echo $letter_group['letter']; ?>" class="<?php echo $selected_letter['letter']== $letter_group['letter'] ? 'current' : ''; ?>" title="<?php echo $letter_group['count']; ?> fonts"><?php echo $letter_group['letter']; ?></a>
	<?php endforeach; ?>
	</div>
	<div class="search-bar">
		<input type="text" name="search-fonts" class="regular-text" placeholder="Search fonts...">
		<select name="search-category">
			<optgroup label="Filter by Font Category">
				<option value="">- Font Category -</option>
				<?php
				foreach ( $font_categories as $category => $font_category ) :
					?>
					<option value="<?php echo $category; ?>"><?php echo "{$font_category['name']} ({$font_category['count']})"; ?></option>
					<?php
				endforeach;
				?>
			</optgroup>
		</select>
	</div>
	<div class="font-list">
		<span class="loading-fonts">Loading fonts...</span>
	</div>
	<script type="text/template" class="font-list-data"><?php echo json_encode( $fonts_list ); ?></script>
</div>