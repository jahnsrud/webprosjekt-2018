<?php
/**
 *	TypoLab Main Screen
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

?>
<div id="typolab-wrapper" class="wrap">
	<?php require 'title.php'; ?>
	<?php require 'tabs.php'; ?>
	<?php require 'edit-font-item.php'; ?>
	<?php require 'footer.php'; ?>
	<?php require 'system-credentials.php'; ?>
</div>