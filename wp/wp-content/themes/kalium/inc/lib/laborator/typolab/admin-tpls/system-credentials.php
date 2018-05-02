<?php
/**
 *	Typolab Tabs
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

ob_start();
$request_system_credentials = request_filesystem_credentials( admin_url( 'admin-ajax.php' ) );	
$request_system_credentials_form = ob_get_clean();

if ( ! $request_system_credentials ) {
	// Remove form
	$request_system_credentials_form = str_replace( array( '<form', '</form' ), array( '<div', '</div' ), $request_system_credentials_form );
	
	?>
	<script>
		var typolab_request_system_credentials_form = <?php echo json_encode( $request_system_credentials_form ); ?>;
	</script>
	<?php
}