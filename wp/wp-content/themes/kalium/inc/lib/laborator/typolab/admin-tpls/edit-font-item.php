<?php
/**
 *	Edit Single Font
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

$font_source = $font['source'];
?>

<form id="edit-font-form" method="post" enctype="application/x-www-form-urlencoded">
	<div class="row-layout edit-font-layout">
		
		<?php
		/**
		 *	Google, Font Squirrel, Premium Fonts and Custom Fonts
		 */
		if ( in_array( $font_source, array( 'google', 'font-squirrel', 'premium', 'typekit', 'custom-font' ) ) ) : 
		?>
		<div class="col col-7">
		<?php 
		switch ( $font_source ) {
			case 'google':
				require 'fonts-list-google.php';
				break;
				
			case 'font-squirrel':
				require 'fonts-list-font-squirrel.php';
				break;
				
			case 'premium':
				require 'fonts-list-premium.php';
				break;
			
			case 'typekit':
				require 'fonts-add-typekit-form.php';
				break;
			
			case 'custom-font':
				require 'fonts-add-custom-font-form.php';
				break;
		}
		?>
		</div>
		<div class="col col-5">
			<?php /* Font Data */ ?>
			<input type="hidden" name="font_family">
			<textarea class="hidden" name="font_data"></textarea>
			
			<div class="font-preview-container">
				<p class="description">
				<?php if ( 'custom-font' == $font_source ) : ?>
					Font preview will be shown here after you fill font style URL and font family name.
				<?php elseif ( 'typekit' == $font_source ) : ?>
					Enter TypeKit ID to preview font/s here
				<?php else : ?>
					Select a font from list to preview it here.
				<?php endif; ?>
				</p>
			</div>
		</div>
		<?php endif; ?>
	</div>
	
	<?php
	// Not supported for TypeKit
	if ( 'typekit' != $font['source'] ) :
		require 'font-selectors-list.php';
	endif; 
	?>
	
	<a href="#" id="typolab-toggle-advanced-options" data-hide-options="Hide Advanced Options" data-show-options="Show Advanced Options"></a>
	
	<div id="typolab-advanced-options">
		<div class="row-layout edit-font-layout">
			<div class="col col-7">
				<?php require 'font-conditional-loading.php'; ?>
			</div>
			<div class="col col-5">
				<?php require 'font-other-options.php'; ?>
			</div>
		</div>
	</div>
	
	<div class="save-changes-container">
		<?php wp_nonce_field( 'typolab-save-font-changes' ); ?>
		<?php submit_button( 'Save Changes', 'primary', 'save_font_changes' ); ?>
	</div>
</form>