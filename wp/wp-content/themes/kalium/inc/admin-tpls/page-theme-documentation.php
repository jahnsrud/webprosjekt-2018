<?php
/**
 *	Theme Help Page
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

$faq_articles = array(
	
	// What are the requirements for using Kalium?
	array(
		'id'      => 'theme-requirements',
		'title'   => 'Server requirements for Kalium',
		'content' => 'Kalium requirements can be found in our documentation site, click the link below to learn more.',
		'link'	  => 'http://documentation.laborator.co/kb/kalium/kalium-server-requirements'
	),
	
	// Activating the theme
	array(
		'id'      => 'activating-the-theme',
		'title'   => 'Activating Kalium (product registration)',
		'content' => 'Your Kalium purchase requires product registration to receive the Kalium demos, WPB Page Builder, Slider Revolution, Layer Slider and automatic theme updates. You can easily register your product on the <a href="' . admin_url( 'admin.php?page=kalium-product-registration' )  . '">Product Registration</a> tab.',
		'link'	  => 'http://documentation.laborator.co/kb/kalium/activating-the-theme/'
	),
	
	// Recommended plugins
	array(
		'id'      => 'recommended-plugins',
		'title'   => 'Required and recommended plugins for Kalium',
		'content' => 'Kalium can be used itself without any additional plugin. However, to utilize all the features Kalium offers, <strong>Advanced Custom Fields</strong> <em>(including ACF related plugins)</em> and <strong>WPB Page Builder</strong> plugins must be installed and activated. 
		
		The plugins mentioned above are fundamental in order to use core theme features as demonstrated in <a href="https://kaliumtheme.com" target="_blank">our demo sites</a>. 
		
		Recommended plugins are either premium plugins we bundle with the theme such as <em>Slider Revolution</em> and <em>Layer Slider</em>, or other Kalium compatible plugins such as <em>WooCommerce</em>.
		
		Some of these plugins can be installed on the <strong>Appearance</strong> &gt; <strong>Install Plugins</strong> section.',
		'link'	  => 'http://documentation.laborator.co/kb/general/installing-and-updating-premium-plugins/'
	),
	
	// Importing demo content
	array(
		'id'      => 'demo-content-import',
		'title'   => 'Importing demo content',
		'content' => 'Firstly, you need to <strong>activate the theme</strong> in order to import any of demo content packages for Kalium.
		
		After you have successfully activated the theme, you can import demo content from <strong>Laborator > Demo Content Install</strong> and choose any of the demo content packages available.
		
		For detailed instructions click on the link below to learn more.',
		'link'	  => 'http://documentation.laborator.co/kb/kalium/demo-content-import/'
	),
	
	// Before updating to new woocommerce
	array(
		'id'      => 'updating-woocommerce',
		'title'   => 'Before updating to new WooCommerce version',
		'content' => 'Every time when there is new update for WoCommerce, make sure that Kalium is compatible with that version <em>(in our <a href="https://themeforest.net/item/kalium-creative-theme-for-professionals/10860525?ref=Laborator" target="_blank">item page</a>)</em> before updating to latest version of WooCommerce.
		
		Kalium is fully compatible with WooCommerce and it takes few days to release a compatibility patch for WooCommerce, especially when there is a big update.',
		'link'	  => 'http://documentation.laborator.co/kb/general/theme-contains-outdated-copies-of-some-woocommerce-template-files/'
	),
	
	// Regenerate thumbnails
	array(
		'id'      => 'regenerate-thumbnails',
		'title'   => 'Regenerate thumbnails',
		'content' => 'If your thumbnails are not correctly cropped, you can regenerate them by following these steps:
		
		<ul>
			<li>Go to <strong>Plugins > Add New</strong></li>
			<li>Search for <strong>Regenerate Thumbnails</strong> (created by Viper007Bond)</li>
			<li>Install and activate that plugin</li>
			<li>Go to <strong>Tools > Regen. Thumbnails</strong></li>
			<li>Click <strong>Regenerate All Thumbnails</strong> button and let the regeneration process <strong>finish to 100%</strong></li>
		</ul>',
		'link'	  => 'http://documentation.laborator.co/kb/kalium/regenerate-thumbnails-kalium/'
	),
	
	// Flush rewrite rules
	array(
		'id'      => 'flush-rewrite-rules',
		'title'   => 'Flush rewrite rules',
		'content' => 'Flushing rewrite rules is required when you are receiving <strong>error 404</strong> on pages you know they exist or you activate any new plugin and its not accessible on front-end. 
		
		This is a simple task and you don’t need to change anything, just click a button. On your admin page go to <strong>Settings &gt; Permalinks</strong> and click <strong>Save Changes</strong> button, thats all.',
		'link'	  => 'http://documentation.laborator.co/kb/kalium/flush-rewrite-rules/'
	),
	
	// Google API key
	array(
		'id'      => 'google-map-not-displaying',
		'title'   => 'Google map is not displaying',
		'content' => 'Google maps requires an <strong>API key</strong> in order to show the map. 
		
		If you see an error: <em>Ooops! Something went wrong...</em> then you have to add a Google API key to your site that will allow you to use Google maps. Click the link below to learn more.',
		'link'	  => 'http://documentation.laborator.co/kb/kalium/fix-the-missing-google-maps-api-key/'
	),
	
	// Google API key
	array(
		'id'      => 'speed-up-the-site',
		'title'   => 'How to speed up the site',
		'content' => 'Recommendations to speed up the site can be found on the link below.',
		'link'	  => 'http://documentation.laborator.co/kb/kalium/how-to-speed-up-my-site/'
	),
	
	// Google API key
	array(
		'id'      => 'custom-css-not-being-apploed',
		'title'   => 'Custom CSS is not being applied',
		'content' => 'This issue mainly happens when you have forgotten to add a closing/opening bracket <strong>{</strong> or <strong>}</strong> in your CSS code or when other CSS rule is taking the precedence over yours and <strong>!important</strong> is not applied.',
		'link'	  => 'http://documentation.laborator.co/kb/kalium/custom-css-is-not-being-applied/'
	),
);


add_thickbox();

$support_allowed = Kalium_Theme_License::license() && false == kalium()->theme_license->isEnvatoHostedSite();
?>
<div class="wrap about-wrap">
	<?php include 'about-header.php'; ?>
	
	<div class="kalium-help">
	
		<div class="docs-and-support">
			
			<h2 class="text-left"><?php echo $support_allowed ? 'Documentation and Support' : 'Documentation'; ?></h2>
			
			<p>In this page you can view general frequently asked questions to help you get started. For more, refer to our <a href="http://documentation.laborator.co/item/kalium/" target="_blank">documentation site</a> or click the links below:</p>
		
		
			<div class="docs-links clearfix">
				<a href="//documentation.laborator.co/item/kalium" class="documentation-button" id="lab_read_docs" target="_blank">Read Documentation</a>
				
				<?php if ( $support_allowed ) : ?>
				<a href="https://laborator.ticksy.com/" target="_blank" class="support-button">
					Theme Support
				</a>
				<?php endif; ?>
			</div>
		
		</div>
	
	
		<h2>Frequently Asked Questions</h2>
		<hr />
	
		<ul class="kalium-faq-links">
			<?php foreach ( $faq_articles as $i => $faq ) : ?>
			<li id="<?php echo $faq['id']; ?>">
				<h3 class="kalium-faq-title">
					<a href="#<?php echo $faq['id']; ?>">
						<i></i>
						<?php echo $faq['title']; ?>
						
						<em><?php echo $i + 1; ?></em>
					</a>
				</h3>
				<div class="kalium-faq-content">
					<?php echo wpautop( $faq['content'] ) ?>
					<?php if ( ! empty( $faq['link'] ) ) : ?>
					<a href="<?php echo $faq['link']; ?>" target="_blank" class="kalium-faq-view-article">View full article</a>
					<?php endif; ?>
				</div>
			</li>
			<?php endforeach; ?>
			
			<li class="more">
				<h3 class="kalium-faq-title">
					<a href="http://documentation.laborator.co/item/kalium/" target="_blank">See more articles</a>
				</h3>
			</li>
		</ul>
	
	</div>
	
</div>

<script type="text/javascript">
jQuery( document ).ready( function( $ ) {
	
	// FAQs
	var $faqs = $( '.kalium-faq-links li:not(.more)' ),
		animationDuration = 300;
	
	$faqs.find( '.kalium-faq-title a' ).on( 'click', function( ev ) {
		ev.preventDefault();
		var id = $( this ).closest( 'li' ).attr( 'id' );
		
		expandFaq( id );
	} );
	
	var expandFaq = function( id ) {
		var $toExpand = $faqs.filter( '#' + id );
		
		if ( $toExpand.hasClass( 'current' ) ) {
			collapseFaqs( 'null' );
			return;
		}
		
		if ( ! $toExpand.length ) {
			return;
		}
		
		collapseFaqs( id );
		
		$toExpand.addClass( 'current' );
		$toExpand.find( '.kalium-faq-content' ).stop().slideDown( animationDuration );
		
		var top = jQuery( window ).scrollTop();
		window.location.hash = id;
		jQuery( window ).scrollTop( top );
	}
	
	var collapseFaqs = function( except_id ) {
		var $toCollapse = $faqs.not( '#' + except_id ).filter( '.current' );
		
		$toCollapse.removeClass( 'current' ).find( '.kalium-faq-content' ).stop().slideUp( animationDuration );
	}
	
	// Open FAQ from URL HASH
	var hash = window.location.hash.toString().replace( '#', '' );
	
	if ( hash && $faqs.filter( '#' + hash ).length ) {
		var $faq = $faqs.filter( '#' + hash ),
			topOffset = $faq.offset().top - 100;
		
		$( 'html, body' ).delay( 300 ).animate( {
			scrollTop: topOffset
		}, function() {
			$faq.addClass( 'blink' );
		} );
		
		expandFaq( hash );
	}
} );
</script>