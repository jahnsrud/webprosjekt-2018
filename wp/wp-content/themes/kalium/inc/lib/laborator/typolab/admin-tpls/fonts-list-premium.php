<?php
/**
 *	Font Squirrel Font List
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

$fonts_list         = TypoLab_Premium_Fonts::getFontsList();
$alphabetic_letters = TypoLab_Premium_Fonts::groupFontsByFirstLetter();
$font_categories    = TypoLab_Premium_Fonts::groupFontsByCategory();

$selected_letter = reset( $alphabetic_letters );
$selected_font = '';

$downloaded_fonts	= TypoLab_Premium_Fonts::getDownloadedFonts();

$font_variants = array();
$font_subsets = array();

// Set Selected Font
if ( isset( $font['valid'] ) && true === $font['valid'] ) {
	$font_family   = $font['family'];
	$font_variants = $font['variants'];
	$font_subsets = $font['subsets'];
	$font_data     = isset( $font['options']['data'] ) ? $font['options']['data'] : array();
	
	$selected_font = $font_family;
	$selected_letter = $alphabetic_letters[ strtoupper( substr( $font_family, 0, 1 ) ) ];
	
	if ( ! is_array( $font_variants ) ) {
		$font_variants = array();
	}
}

// Premium Font Data
$activated = Kalium_Theme_License::isValid();

$premium_font_data = array(
	'isActivated'     => $activated,
	'licenseKey'      => $activated ? Kalium_Theme_License::license()->license_key : '',
	'downloadedFonts' => $downloaded_fonts
);
?>
<div class="fonts-list-select" data-font-source="premium-fonts" data-current-font="<?php echo esc_attr( $selected_font ); ?>" data-current-font-variants="<?php echo implode( ',', $font_variants ); ?>" data-current-font-subsets="<?php echo implode( ',', $font_subsets ); ?>">
	<div class="alphabet<?php echo count( $alphabetic_letters ) < 10 ? ' hidden' : ''; ?>">
	<?php foreach ( $alphabetic_letters as $letter_group ) : ?>
		<a href="#" data-letter="<?php echo $letter_group['letter']; ?>" class="<?php echo $selected_letter['letter'] == $letter_group['letter'] ? 'current' : ''; ?>" title="<?php echo $letter_group['count']; ?> fonts"><?php echo $letter_group['letter']; ?></a>
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
	<script type="text/template" class="premium-font-data"><?php echo json_encode( $premium_font_data ); ?></script>
</div>