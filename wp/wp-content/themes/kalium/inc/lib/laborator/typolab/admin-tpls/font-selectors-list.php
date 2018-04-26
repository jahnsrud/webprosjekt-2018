<?php
/**
 *	Font Selector List
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

$default_selector = array(
	'selector' => ".{$font['id']}"
);

$selectors = isset( $font['options']['selectors'] ) ? $font['options']['selectors'] : array( $default_selector );

foreach ( $selectors as & $selector ) {
	if ( isset( $selector['font-sizes'] ) ) {
		$selector['fontSizes'] = $selector['font-sizes'];
		unset( $selector['font-sizes'] );
	}
}

// Unique Selectors
$selectors = array_intersect_key( $selectors , array_unique( array_map( 'serialize', $selectors ) ) );


// Get Predefined CSS Selectors
$predefined_css_selectors = TypoLab_Font_Sizes::getFontSizes();
?>
<script id="font-selectors" type="text/template"><?php echo json_encode( $selectors ); ?></script>

<table class="typolab-table">
	<thead>
		<tr>
			<th>Selectors and Sizes</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="no-padding">
				<!-- Selectors -->
				<table class="font-selectors-list">
					<thead>
						<tr>
							<td class="font-drag"></td>
							<th class="font-selector">CSS Selector</th>
							<th class="font-variants">Font Variant</th>
							<th class="font-case">Font Case</th>
							<th class="font-sizes">Font Sizes (Optional)</th>
							<th class="font-selector-action"></th>
						</tr>
					</thead>
					<tbody>
						<tr class="no-records">
							<td colspan="5">No defined CSS Selectors</td>
						</tr>
					</tbody>
				</table>

			</td>
		</tr>
		<tr class="hover">
			<td>
				<a href="#" id="add-new-selector" class="button">
					<i class="fa fa-plus"></i>
					Add New Selector
				</a>
				
				<?php if ( count( $predefined_css_selectors ) ) : ?>
				<div class="predefined-css-selectors">
				
					<a href="#" class="button">
						<i class="fa fa-bars"></i>
						Choose From Predefined Selectors
					</a>
					
					<ul>
					<?php foreach ( $predefined_css_selectors as $css_selector_group ) : ?>
						<li>
							<a href="#" data-selectors="<?php echo esc_attr( json_encode( $css_selector_group['selectors'] ) ); ?>"><?php echo $css_selector_group['title']; ?></a>
						</li>
					<?php endforeach; ?>
					</ul>
				</div>
				<?php endif; ?>
			</td>
		</tr>
	</tbody>
</table>