<?php
/**
 *	Product Registration Page
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

wp_enqueue_script( 'laborator-product-activation' );
wp_enqueue_style( 'font-awesome' );
?>
<div class="wrap about-wrap product-activation-wrap">
	<?php require 'about-header.php'; ?>
	
	<?php
	
	// Activated license information
	if ( Kalium_Theme_License::isValid() ) :
		$license = Kalium_Theme_License::license();
		$uploads_directory = wp_upload_dir();
		$base_dir = $uploads_directory['basedir'];
		
		$theme_backups = glob( $base_dir . '/' . preg_replace( '/\{.*?\}/i', '*', Kalium_Theme_Upgrader::$backup_file_name ) );
		
		$last_validation = get_theme_mod( 'theme_license_last_validation', 0 );
		$can_validate_action = 0 == $last_validation || time() - $last_validation > DAY_IN_SECONDS;
		
		$validate_license_url = add_query_arg( array( 'action' => 'validate-theme-activation', '_nonce' => wp_create_nonce( 'validate-theme-activation' ) ) );
		
		
		?>
		<div class="laborator-activate-theme-box license-details">
			
			<?php if ( false == kalium()->theme_license->isEnvatoHostedSite() ) : ?>
				<?php if ( $can_validate_action ) : ?>
				<a href="<?php echo $validate_license_url; ?>" title="Validate activation" class="validate-theme-activation">
					<i class="fa fa-refresh"></i>
				</a>
				<?php endif; ?>
				<a href="<?php echo add_query_arg( array( 'action' => 'delete-theme-activation', '_nonce' => wp_create_nonce( 'delete-theme-activation' ) ) ); ?>" title="Delete theme activation" class="delete-theme-activation">
					&times;
				</a>
			<?php endif; ?>
			
			<div class="theme-activation-button-container">
				<div class="theme-activation-description full-width">
					<h3>Theme Activated</h3>
					<p>License information granted to this website and backup settings</p>
				</div>
			</div>
		
			<div class="laborator-about-row">
				<div class="col col-12">
					<div class="license-detail">
						Licensed domain:
						<strong><?php echo kalium()->theme_license->convertIDNtoUTF8( $license->domain ); ?></strong>
					</div>
					
					<?php if ( isset( $license->licensee ) ) : ?>
					<div class="license-detail">
						Licensee:
						<strong><?php echo $license->licensee; ?></strong>
					</div>
					<?php endif; ?>
					
					<div class="license-detail">
						License key:
						<strong><?php echo $license->license_key; ?></strong>
					</div>
					
					<div class="license-detail">
						Activation Date:
						<strong><?php echo date( 'r', $license->timestamp ); ?></strong>
					</div>
					
					<?php if ( $license->support_available ) : ?>
					<div class="license-detail">
						Support status
						<strong>
						<?php kalium()->theme_license->nearlyExpiring(); if ( $license->support_expired ) : ?>
							<span class="label-error">Support Expired</span>
							<span class="renew-support">
								<?php if ( $can_validate_action ) : ?>
								<a href="<?php echo $validate_license_url; ?>" title="If you have renewed the support package, click this link to fetch new support package information from your Envato account (one request per day is allowed)" class="validate-support-link tooltip">
									<i class="fa fa-refresh"></i>
								</a>
								<?php endif; ?>
								
								Click here to <a href="https://themeforest.net/item/kalium-creative-theme-for-professionals/10860525?ref=Laborator" target="_blank">renew your support package</a>. 
								<span class="note-about-updates">Note: Support package is not required to get theme updates.</span>
							</span>
						<?php else : $supported_until_time = strtotime( $license->supported_until ); ?>
							<span class="label-success">Supported</span>
							<span class="days-left"><?php echo human_time_diff( $supported_until_time, time() ) . ' left'; ?></span>
							
							<?php if ( kalium()->theme_license->nearlyExpiring() ) : ?>
							<br>
							<span class="before-expiration">
								<i class="dashicons dashicons-warning"></i> 
								Your support package will expire soon, <a href="https://themeforest.net/item/kalium-creative-theme-for-professionals/10860525?ref=Laborator" target="_blank">renew support</a> with 30% discount before it expires. <span class="note-about-updates">Note: Support package is not required to get theme updates.</span>
							</span>
							<?php endif; ?>
						<?php endif; ?>
						</strong>
					</div>
						
						<?php if ( $license->support_expired ) : ?>
						<?php else : ?>
							<div class="license-detail">
								Support Expire Date:
								<strong><?php echo date( 'r', strtotime( $license->supported_until ) ); ?></strong>
							</div>
						<?php endif; ?>
					<?php endif; ?>
					
					<div class="license-detail">
						Theme Backups:
						<strong><?php echo $license->save_backups ? 'Enabled' : 'Disabled'; ?> <a href="#" class="change-theme-backups">(change)</a></strong>
					</div>
					
					<form id="laborator-save-theme-backups" action="" method="post" enctype="application/x-www-form-urlencoded">
						<select name="theme_backups" id="theme_backups">
							<option value="1" <?php echo selected( 1, $license->save_backups ); ?>>Enable Backups</option>
							<option value="0" <?php echo selected( 0, $license->save_backups ); ?>>Disable Backups</option>
						</select>
						<button type="submit" class="button button-primary">Save</button>
					</form>
					
					<?php if ( count( $theme_backups ) ) : ?>
					<div class="license-detail">
						Saved Theme Backups
						<?php foreach( $theme_backups as $i => $backup_file ) : $relative_backup_file = str_replace( ABSPATH, '', $backup_file ); ?>
							<div class="theme-backup-link"><?php echo $relative_backup_file; ?> 
								<span>(<?php echo size_format( filesize( $backup_file ) ); ?>)</span> - 
								<a href="<?php echo site_url( $relative_backup_file ); ?>">Download</a>
							</div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php	

	// License is not activated yet
	else : 
	?>
	<div class="about-description">
		You are almost done, activate the theme to get demo content, theme support, patches and latest updates including premium plugins bundled with this theme. <br>
		If you haven't purchased Kalium, <a href="https://themeforest.net/item/kalium-creative-theme-for-professionals/10860525?ref=Laborator" target="_blank">click here to buy a license</a> and activate theme.
	</div>
	
	<div class="laborator-activate-theme-box">
		
		<div class="theme-activation-button-container">
			<div class="theme-activation-description">
				<h3>Theme Activation</h3>
				<p>Theme activation process is automatic, you don't need to enter purchase code manually. Follow these steps to activate the theme</p>
			</div>
			
			<div class="theme-activation-button">
				<a href="#" id="laborator-theme-activate" class="button button-primary">Activate Product</a>
			</div>
		</div>
		
		<div class="laborator-about-row laborator-theme-steps">
			<div class="col col-12">
				
				<div class="laborator-theme-step">
					<span class="step-num"><em>1</em></span> 
					<span class="step-description">Click <strong>Activate Product</strong> button</span>
				</div>
				
				<div class="laborator-theme-step">
					<span class="step-num"><em>2</em></span> 
					<span class="step-description">You will be asked to login with your <strong>Envato account</strong> (used to purchase this theme) to authorize theme purchase code</span>
				</div>
				
				<div class="laborator-theme-step">
					<span class="step-num"><em>3</em></span> 
					<span class="step-description">Choose one of valid purchase codes from the list and click <strong>Proceed</strong></span>
				</div>
				
				<div class="laborator-theme-step is-finished">
					<span class="step-num">
						<em>
							<i class="fa fa-check"></i>
						</em>
					</span> 
					<span class="step-description">Product is activated and you can get latest updates!</span>
				</div>
				
			</div>
		</div>
	</div>
	<?php endif; ?>
	
</div>

<?php do_action( 'kalium_product_registration_page' ); ?>