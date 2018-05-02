<?php
/**
 *	System Status Page
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class Laborator_System_Status {
	
	/**
	 *	Initialize System Status Page
	 */
	public function admin_init() {
		
		if ( 'laborator-system-status' == $this->admin_page ) {
			wp_enqueue_style( 'font-awesome' );
			
			// Tooltips
			wp_enqueue_style( 'tooltipster-bundle', 'https://cdn.jsdelivr.net/jquery.tooltipster/4.1.4/css/tooltipster.bundle.min.css', null, '4.1.4' );
			wp_enqueue_script( 'tooltipster-bundle', 'https://cdn.jsdelivr.net/jquery.tooltipster/4.1.4/js/tooltipster.bundle.min.js', null, '4.1.4' );
			
			// Clipboard
			wp_enqueue_script( 'clipboard', 'https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.13/clipboard.min.js', null, '1.5.13', true );
		}
	}
	
	/**
	 *	System Status menu item
	 */
	public function admin_menu() {
		add_submenu_page( 'laborator_options', 'System Status', 'System Status', 'edit_theme_options', 'laborator-system-status', array( & $this, 'systemStatusPage' ) );
	}
	
	/**
	 *	Display System Status Page
	 */
	public function systemStatusPage() {
		require kalium()->locateFile( 'inc/admin-tpls/page-system-status.php' );
	}
	
	/**
	 *	Show help tooltip
	 */
	public static function tooltip( $message ) {
		$tooltip_id = 'tooltip-' .  md5( $message ) . mt_rand( 100, 999 );
		
		?>
			<i class="tooltip fa fa-question-circle" data-content="<?php echo $tooltip_id; ?>"></i>
		</a>
		
		<div id="<?php echo $tooltip_id; ?>" class="tooltip-content">
			<?php echo $message; ?>
		</div>
		<?php
	}
}