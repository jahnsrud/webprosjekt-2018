<?php
/**
 *	Kalium About Page Tabs
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

$links = array(
	'laborator-about'			  => 'What&#8217;s New',
	'kalium-product-registration' => 'Product Registration',
	'laborator-system-status'     => 'System Status',
	'laborator-docs'              => 'Help',
);

?>
<h2 class="nav-tab-wrapper wp-clearfix">
	<?php
	foreach ( $links as $link_id => $title ) {
		?>
		<a href="<?php echo "admin.php?page={$link_id}"; ?>" class="nav-tab<?php when_match( $link_id == kalium()->url->get( 'page' ), 'nav-tab-active' ); ?>"><?php echo $title; ?></a>
		<?php
	}
	?>
</h2>