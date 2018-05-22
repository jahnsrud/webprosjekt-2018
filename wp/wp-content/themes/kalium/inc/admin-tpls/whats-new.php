<?php
/**
 *	Whats New
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
$version = kalium()->getVersion();
?>
<div class="kalium-whats-new">
	
	<?php if ( kalium()->url->get( 'welcome', true ) ) : ?>
	<div class="kalium-activated">
		<h3>
			Thanks for choosing Kalium theme!
			<br>
			<small>Here are the first steps to setup the theme:</small>
		</h3>
		
		<ol>
			<li>Install and activate required plugins by <a href="<?php echo admin_url('themes.php?page=kalium-install-plugins' ); ?>" target="_blank">clicking here</a></li>
			<?php if ( ! kalium()->theme_license->isValid() ) : ?>
			<li>Activate the theme on <a href="<?php echo admin_url( 'admin.php?page=kalium-product-registration' ); ?>" target="_blank">Product Registration</a> tab</li>
			<?php endif; ?>
			<li>Install demo content via <a href="<?php echo admin_url( 'admin.php?page=laborator-demo-content-installer' ); ?>" target="_blank">One-Click Demo Content</a> installer (requires <a href="http://documentation.laborator.co/kb/kalium/activating-the-theme/" target="_blank">theme activation</a>)</li>
			<li>Configure <a href="<?php echo admin_url( 'admin.php?page=laborator_options' ); ?>" target="_blank">theme options</a> (optional)</li>
			<li>Refer to our <a href="<?php echo admin_url( 'admin.php?page=laborator-docs' ); ?>">theme documentation</a> and learn how to setup Kalium (recommended)</li>
		</ol>
	</div>
	<?php endif; ?>
	
	<div class="kalium-version">
		<div class="kalium-version-gradient">
			<span class="numbers-<?php echo strlen( str_replace( '.', '', $version ) ); ?>"><?php echo $version; ?></span>
		</div>
		
		<div class="kalium-version-info">
			<h2>Kalium: What’s New!</h2>
			<p>
				Kalium continuously expands with new features, bug fixes and other adjustments to provide smoother experience for everyone. <br>
				Scroll down to see main features implemented in current version. For complete list of changes <a href="http://documentation.laborator.co/kb/kalium/kalium-changelog/" target="_blank">view full changelog here</a>.
			</p>
		</div>
	</div>
	
	<div class="feature-section two-col">
		<div class="col">
			<a href="https://demo.kaliumtheme.com/restaurant" target="_blank"><img src="<?php echo kalium()->assetsUrl( 'images/admin/whats-new/restaurant-demo.jpg' ); ?>"></a>
			<h3>Restaurant demo</h3>
			<p>
				This is our newest professionally designed demo. If you need a restaurant, bar, bistro, bakery, pubs, coffee shop, pizzerias or other restaurant related businesses, this demo is for you.
				<br>
				<a href="https://demo.kaliumtheme.com/restaurant" target="_blank">Click to preview &raquo;</a>
			</p>
		</div>
		<div class="col">
			<a href="https://demo.kaliumtheme.com/construction" target="_blank"><img src="<?php echo kalium()->assetsUrl( 'images/admin/whats-new/construction-demo.jpg' ); ?>"></a>
			<h3>Construction demo</h3>
			<p>
				Here comes one of the most requested demos for Kalium, the construction demo. We&rsquo;ve built a new demo site for those who built our cities.
				<br>
				<a href="https://demo.kaliumtheme.com/construction" target="_blank">Click to preview &raquo;</a>
			</p>
		</div>
	</div>
	
	
	<div class="whats-new-secondary feature-section three-col">
		<div class="col">
			<img src="<?php echo kalium()->assetsUrl( 'images/admin/whats-new/woocommerce-33.jpg' ); ?>">
			<h3>WooCommerce compatibility</h3>
			<p>Added compatibility for latest stable WooCommerce version (3.3.x). Feel safe to update!</p>
		</div>
		<div class="col">
			<img src="<?php echo kalium()->assetsUrl( 'images/admin/whats-new/premium-plugins.jpg' ); ?>">
			<h3>Premium plugins updates</h3>
			<p>Alongside with the theme update, latest versions of premium plugins are included as well.</p>
		</div>
		<div class="col">
			<img src="<?php echo kalium()->assetsUrl( 'images/admin/whats-new/wordpress-49.jpg' ); ?>">
			<h3>WordPress 4.9 compatibility</h3>
			<p>Kalium is fully compatible with latest version of WordPress 4.9 aka &ldquo;Tipton&rdquo; as it is nicknamed by WordPress team.</p>
		</div>
	</div>
	
	
	<div class="whats-new-secondary feature-section three-col">
		<div class="col">
			<img src="<?php echo kalium()->assetsUrl( 'images/admin/whats-new/php7-compatible.jpg' ); ?>">
			<h3>PHP 7 compatibility</h3>
			<p>
				Kalium is now friendly with PHP version 7&ndash;7.2.
				<?php if ( ! function_exists( 'phpversion' ) || version_compare( phpversion(), '7.0', '<' ) ) : ?>
				We recommend you to switch to PHP 7 and see the magic.
				<?php else : ?>
				Its made to work with your hosting environment.
				<?php endif; ?>
			</p>
		</div>
		<div class="col">
			<img src="<?php echo kalium()->assetsUrl( 'images/admin/whats-new/finnish-translation.jpg' ); ?>">
			<h3>Finnish translation</h3>
			<p>
				New front-end translation in Finnish language is now available for Kalium. Thanks to <abbr title="A happy customer that uses Kalium">Okko Alitalo</abbr> for contributing this translation.
			</p>
		</div>
		<div class="col">
			<img src="<?php echo kalium()->assetsUrl( 'images/admin/whats-new/custom-sidebars.jpg' ); ?>">
			<h3>Custom sidebars</h3>
			<p>Full compatibility with Custom Sidebars plugin. Widgets sections won't disappear even if you disable the plugin.</p>
		</div>
	</div>
	
	<a href="http://documentation.laborator.co/kb/kalium/kalium-changelog/" target="_blank" class="view-changelog">See full changelog &#65515;</a>
	
</div>