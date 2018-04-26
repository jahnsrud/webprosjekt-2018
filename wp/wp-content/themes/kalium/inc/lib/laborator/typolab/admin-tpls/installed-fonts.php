<?php
/**
 *	Installed Fonts List
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

$fonts = $this->getFonts( true );
$total_fonts = 0;

$page = kalium()->url->get( 'page' );
?>
<div class="typolab-fonts-list">
	<table class="widefat">
		<thead>
			<th class="font-name-col">Name</th>
			<th class="font-preview-col">Font Preview</th>
			<th class="font-source-col">Source</th>
			<th class="font-status-col">Status</th>
		</thead>
		
		<tbody>
		<?php 
		if ( count( $fonts ) ) :
		
			foreach ( $fonts as $font ) :
			
				if ( ! empty( $font['valid'] ) ) :
				
					$font_id = $font['id'];
					$font_family = isset( $font['family'] ) ? $font['family'] : '(No font was specified)';
					$font_source = self::$font_sources[ $font['source'] ];
					$font_status = 'published' == $font['font_status'] ? '<span class="published">Published</span>' : '<span class="unpublished">Unpublished</span>';
					
					$edit_link = admin_url( "admin.php?page={$page}&typolab-action=edit-font&font-id={$font_id}" );
					$delete_link = admin_url( "admin.php?page={$page}&typolab-action=delete-font&font-id={$font_id}" );
					
					$missing_font = isset( self::$missing_fonts[ $font_id ] );
					
					$preview_url = '';
					
					switch ( $font['source'] ) {
						// Google Fonts Preview Link
						case 'google':
							$preview_url = TypoLab_Google_Fonts::singleLinePreview( $font );
							break;
							
						// Font Squirrel Preview Link
						case 'font-squirrel':
							$preview_url = TypoLab_Font_Squirrel::singleLinePreview( $font );
							break;
							
						// Premium Font Preview Link
						case 'premium':
							$preview_url = TypoLab_Premium_Fonts::singleLinePreview( $font );
							break;
							
						// TypeKit Preview Link
						case 'typekit':
							$preview_url = TypoLab_TypeKit_Fonts::singleLinePreview( $font );
							break;
							
						// Custom Font Preview Link
						case 'custom-font':
							$preview_url = TypoLab_Custom_Font::singleLinePreview( $font );
							break;
					}
					
					// Provider Image
					$provider_image = self::$typolab_assets_url . "/img/{$font['source']}.png";
					
					// Install Font Link
					if ( $missing_font ) {
						$edit_link .= '#premium-font-downloader';
					}
					?>
					<tr<?php when_match( $missing_font, 'class="not-installed"' ); ?>>
						<td class="font-name-col">
							<?php if ( $missing_font ) : ?>
							<span class="font-warning tooltip" title="This font is not installed in your site, click Install to proceed with font installation.">
								<i class="fa fa-warning"></i>
							</span>
							<?php endif; ?>
							<a href="<?php echo $edit_link; ?>" class="font-family-name"><?php echo $font_family; ?></a>
							
							<div class="typolab-actions">
								<a href="<?php echo $edit_link; ?>" class="edit"><?php echo $missing_font ? 'Install' : 'Edit'; ?></a> |
								<a href="<?php echo $delete_link; ?>" class="trash">Delete</a>
							</div>
						</td>
						<td class="font-preview-col">
							<div class="font-preview-iframe">
							<?php if ( $preview_url && ! $missing_font ) : ?>
								<div class="is-loading">Loading font preview&hellip;</div>
								<iframe src="<?php echo $preview_url; ?>"></iframe>
							<?php else : ?>
								<div class="is-loading">No preview available</div>
							<?php endif; ?>
							</div>
						</td>
						<td class="font-source-col">
							<img src="<?php echo $provider_image; ?>" class="provider-logo-img">
						</td>
						<td class="font-status-col">
							<?php echo $font_status; ?>
						</td>
					</tr>
					<?php
						
				endif;
				
				$total_fonts++;
			endforeach; 
			?>
		<?php else: ?>
			<tr>
				<td colspan="4" class="no-records">
					There are no installed fonts in your site.
					<a href="<?php echo admin_url( 'admin.php?page=typolab&typolab-action=add-font' ); ?>" class="button button-primary">Add Font</a>
				</td>
			</tr>
		<?php endif; ?>
		</tbody>
	</table>
	
	<?php if ( $total_fonts > 0 ) : ?>
	<p class="installed-fonts-count">Total fonts installed: <?php echo $total_fonts; ?></p>
	<?php endif; ?>
</div>