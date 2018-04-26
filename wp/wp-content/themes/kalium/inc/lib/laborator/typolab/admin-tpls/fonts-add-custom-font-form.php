<?php
/**
 *	Add Custom Font Form
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

$font_url = isset( $font['options']['font_url'] ) ? $font['options']['font_url'] : '';
$font_variants = isset( $font['options']['font_variants'] ) ? $font['options']['font_variants'] : '';
$main_font_variant = is_array( $font_variants ) && count( $font_variants ) ? array_shift( $font_variants ) : '';
?>
<div class="custom-font-form-layout">
	
	<table class="typolab-table">
		<thead>
			<th colspan="2">Font Face</th>
		</thead>
		<tbody>
			<tr class="hover vtop">
				<th width="35%">
					<label for="font_url">Font Stylesheet URL:</label>
				</th>
				<td class="no-bg">
					<input type="text" name="font_url" id="font_url" value="<?php echo esc_attr( $font_url ); ?>" required="required">
					
					<p class="description">
						Enter absolute URL of CSS file which will import custom font.
					</p>
				</td>
			</tr>
			<tr class="hover vtop">
				<th>
					<label for="font_variants_1">Font Family Name:</label>
				</th>
				<td class="no-bg">
					
					<div id="font-family-names" class="typolab-font-input">
						<input type="text" name="font_variants[]" id="font_variants_1" value="<?php echo esc_attr( $main_font_variant ); ?>" placeholder="e.g. Proxima Nova, Helvetica, sans-serif" required="required">
						
						<ul class="font-family-entries">
							<?php if ( is_array( $font_variants ) ) : foreach ( $font_variants as $variant ) : ?>
							<li>
								<input type="text" name="font_variants[]" value="<?php echo esc_attr( $variant ); ?>">
								<a href="#" class="remove"><i class="fa fa-remove"></i></a>
							</li>
							<?php endforeach; endif; ?>
						</ul>
					</div>
					
				</td>
			</tr>
		</tbody>
	</table>
	
	<a href="#" class="button" id="add-custom-font-input">
		<i class="fa fa-plus"></i>
		Add another font family 
	</a>
	
	<a href="#" class="button" id="custom-font-generate-preview">
		<i class="fa fa-repeat"></i>
		Generate Preview
	</a>
	
</div>