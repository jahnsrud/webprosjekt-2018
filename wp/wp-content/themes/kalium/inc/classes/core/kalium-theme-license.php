<?php
/**
 *	Kalium WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class Kalium_Theme_License {
	
	/**
	 *	Current activated license
	 */
	private static $license;
	
	/**
	 *	Laborator API Server URL
	 */
	public static $api_server = 'https://api.laborator.co';
	
	public function __construct() {
		// Transfer license from theme mods to options
		if ( $current_license = get_theme_mod( 'license' ) ) {
			update_option( 'kalium_license', $current_license );
			remove_theme_mod( 'license' );
		}
		
		// Product registration page
		add_action( 'admin_menu', array( & $this, 'productRegistrationMenuItem' ) );
		
		// Initialize License
		$this->initLicenseVar();
	}
	
	/**
	 *	Admin Actions for this class
	 */
	public function admin_init() {
		global $wp_version;
		
		// Do not execute on AJAX
		if ( defined( 'DOING_AJAX' ) ) {
			return false;
		}
		
		// Product activated page check referer and verify license
		if ( 'kalium-product-registration' == $this->admin_page && isset( $_GET['license_key'] ) ) {
			$license_key = kalium()->url->get( 'license_key' );
			$referer = parse_url( kalium()->server( 'HTTP_REFERER' ) );
			
			// Verify License from Laborator API Server
			$this->product_activated = true;
			
			if ( kalium()->url->get( 'perform_verification' ) ) {
				$verify_license_response = wp_remote_post( self::getAPIServerURL() . "/verify-license/{$license_key}/" );
				$license_data = json_decode( wp_remote_retrieve_body( $verify_license_response ) );
				
				if ( $license_data->valid && false !== stripos( $this->convertIDNtoASCII( home_url() ), $license_data->domain ) ) {
					unset( $license_data->valid );
					update_option( 'kalium_license', $license_data );
					wp_redirect( remove_query_arg( 'perform_verification' ) );
					die();
				} else {
					wp_nonce_ays( '' );
				}
			}
		}
		
		// Restore activation
		if ( 'kalium-product-registration' == $this->admin_page && kalium()->url->get( 'restore-license', true ) ) {
			/*$restore_activation_request = wp_remote_post( $this->getAPIServerURL() . '/restore-license/', array(
				'body' => array(
					'site_address' => home_url()
				)
			) );
			
			$restore_activation_response = json_decode( wp_remote_retrieve_body( $restore_activation_request ) );
			
			// Successful license restore
			if ( ! empty( $restore_activation_response->success ) ) {
				$license_data = json_decode( $restore_activation_response->data );
				//print_r( $license_data );
			}
			die();
			*/
		}
		
		// Theme Backups
		if ( 'kalium-product-registration' == $this->admin_page && isset( $_POST['theme_backups'] ) && self::isValid() ) {
			self::$license->save_backups = $_POST['theme_backups'];
			update_option( 'kalium_license', self::$license );
			Kalium_Helpers::addAdminNotice( 'Theme backup settings have been saved!' );
		}
		
		// Theme Activation Actions
		if ( 'kalium-product-registration' == $this->admin_page && isset( $_GET['action'] ) ) {
			
			switch ( kalium()->url->get( 'action' ) ) {
				
				// Delete Theme Activation
				case 'delete-theme-activation' :
					Kalium_Helpers::addAdminNotice( 'Theme activation has been deleted!', 'warning' );
					
					if ( isset( $_GET['_nonce'] ) && wp_verify_nonce( $_GET['_nonce'], 'delete-theme-activation' ) ) {
						delete_option( 'kalium_license' );
						wp_redirect( remove_query_arg( array( '_nonce' ) ) ); 
						die();
					}
					break;
				
				// Validate Theme Activation
				case 'validate-theme-activation' :
					Kalium_Helpers::addAdminNotice( 'Theme activation has been validated!', 'info' );
					
					if ( isset( $_GET['_nonce'] ) && wp_verify_nonce( $_GET['_nonce'], 'validate-theme-activation' ) ) {
						self::validateLicense();
						wp_redirect( remove_query_arg( array( '_nonce' ) ) ); 
						die();
					}
					break;
					
			}
		}
					
		// Other actions on product registration page
		if ( 'kalium-product-registration' == $this->admin_page ) {
			// Theme Activation Instance
			if ( ! $this->isEnvatoHostedSite() ) {
				add_action( 'kalium_product_registration_page', array( & $this, 'themeActivationVars' ) );
			}
			
			// Tooltips library
			wp_enqueue_style( 'tooltipster-bundle', 'https://cdn.jsdelivr.net/jquery.tooltipster/4.1.4/css/tooltipster.bundle.min.css', null, '4.1.4' );
			wp_enqueue_script( 'tooltipster-bundle', 'https://cdn.jsdelivr.net/jquery.tooltipster/4.1.4/js/tooltipster.bundle.min.js', null, '4.1.4' );
		}
		
		// Envato Hosted Site
		if ( $this->isEnvatoHostedSite() ) {
			$request_verification = false == ( self::license() || get_theme_mod( 'purchase_code_request_verification' ) );
			
			if ( current_user_can( 'manage_options' ) && kalium()->url->get( 'purchase_code_request_verification', true ) ) {
				$request_verification = true;
			}
			
			// Verify the purchase code
			if ( $request_verification ) {
				$response = $this->purchaseCodeActivate( true );

				if ( $response->success ) {
					$license = $response->data->license;

					update_option( 'kalium_license_hosted', $license );
					remove_theme_mod( 'purchase_code_request_verification_error' );
				} else if ( ! empty( $response->data->error_description ) ) {
					set_theme_mod( 'purchase_code_request_verification_error', $response->data->error_description );
				}
				
				set_theme_mod( 'purchase_code_request_verification', true );
			}
			
			// Verification errors
			if ( $license_errors = get_theme_mod( 'purchase_code_request_verification_error' ) ) {
				$license_errors = sprintf( 'An error occurred during product activation: <strong>%s</strong>', esc_html( $license_errors ) );
				kalium()->helpers->addAdminNotice( $license_errors, 'error' );
			}
		}
		
		// Nearly expiring notification
		if ( $this->nearlyExpiring() ) {
			$this->displayNearlyExpiringNotice();
		}
	}
	
	/**
	 *	Add "Product Registration" item in admin menu
	 */
	public function productRegistrationMenuItem() {
		// Product Registration
		add_submenu_page( 'laborator_options', 'Product Registration', 'Product Registration', 'edit_theme_options', 'kalium-product-registration', array( $this, 'adminPageProductRegistration' ) );
	}
	
	/**
	 *	Product Registration Page
	 */
	public function adminPageProductRegistration() {
		if ( isset( $this->product_activated ) && $this->product_activated ) {
			include kalium()->locateFile( 'inc/admin-tpls/page-product-activated.php' );
		} else {
			include kalium()->locateFile( 'inc/admin-tpls/page-product-registration.php' );
		}
	}
	
	/**
	 *	Product Activation JSON Instance
	 */
	public function themeActivationVars() {
		$server_ip = $_SERVER['SERVER_ADDR'];
		
		if ( empty( $server_ip ) && function_exists( 'gethostbyname' ) ) {
			$server_ip = gethostbyname( $_SERVER['HTTP_HOST'] );
		}
		?>
		<script id="laborator-form-data-json" type="text/template"><?php echo json_encode( array(
			'action'       => 'activate-product',
			'theme_id'     => 'kalium',
			'api'          => self::$api_server,
			'version'      => kalium()->getVersion(),
			'url'          => $this->convertIDNtoASCII( home_url() ),
			'ref_url'      => $this->convertIDNtoASCII( admin_url( 'admin.php?page=' . $this->admin_page ) ),
			'server_ip'    => $server_ip
		) ); ?></script>
		<?php
	}
	
	/**
	 * Convert domain name to ASCII
	 */
	public function convertIDNtoASCII( $url ) {
		$protocol = '';
		
		if ( preg_match( '/^(https?:\/\/)/', $url, $matches ) ) {
			$protocol = $matches[1];
			$url = str_replace( $protocol, '', $url );
		}
		
		if ( function_exists( 'idn_to_ascii' ) && idn_to_ascii( $url ) ) {
			$url = idn_to_ascii( $url );
		}
		
		return $protocol . utf8_uri_encode( $url );
	}
	
	/**
	 * Convert IDN to UTF8
	 */
	public function convertIDNtoUTF8( $url ) {
		$protocol = '';
		
		if ( preg_match( '/^(https?:\/\/)/', $url, $matches ) ) {
			$protocol = $matches[1];
			$url = str_replace( $protocol, '', $url );
		}
		
		if ( function_exists( 'idn_to_utf8' ) && idn_to_utf8( $url ) ) {
			$url = idn_to_utf8( $url );
		}
		
		return $protocol . $url;
	}
	
	
	/**
	 *	Get API Server URL
	 */
	public static function getAPIServerURL() {
		// When OpenSSL version is not supported, remove https protocol
		if ( function_exists( 'get_openssl_version_number' ) && version_compare( get_openssl_version_number(), '1.0', '<' ) ) {
			return str_replace( 'https://', 'http://', self::$api_server );
		}
		
		return self::$api_server;
	}
	
	/**
	 * Check if hosted theme
	 */
	public function isEnvatoHostedSite() {
		return defined( 'ENVATO_HOSTED_SITE' ) && defined( 'SUBSCRIPTION_CODE' ) && strlen( SUBSCRIPTION_CODE ) > 35;
	}
	
	/**
	 * Activate with Purchase Code
	 */
	private function purchaseCodeActivate( $envato_hosted = false ) {
		$request = wp_remote_post( self::getAPIServerURL(), array(
			'body' => array(
				'action'    	=> 'activate-product-purchase-code',
				'theme_id'  	=> 'kalium',
				'version'   	=> kalium()->getVersion(),
				'url'       	=> home_url(),
				'ref_url' 		=> admin_url( 'admin.php?page=' . $this->admin_page ),
				'purchase_code'	=> SUBSCRIPTION_CODE,
				'envato_hosted' => $envato_hosted
			)
		) );
		
		$license_data = json_decode( wp_remote_retrieve_body( $request ) );
		
		return $license_data;
	}
	
	/**
	 *	Diplay Nearly Expiring Notices
	 */
	private function displayNearlyExpiringNotice() {
		$days_left            = $this->nearlyExpiring( true );
		$supported_until      = self::license()->supported_until;
		$supported_until_var  = 'theme-support-expiration-' . sanitize_title( $supported_until );
		$support_package_link = 'https://themeforest.net/item/kalium-creative-theme-for-professionals/10860525?ref=Laborator';

		// Display expiration notice if its not dismissed
		if ( ! get_theme_mod( $supported_until_var ) ) {
			$dismiss_notice_link = sprintf( '<a href="%s">Dismiss this notice</a>', add_query_arg( array( 'laborator_dismiss_expiration' => wp_create_nonce( $supported_until_var ) ) ) );
			
			if ( $days_left > 0 ) {
				$date = date( 'r', strtotime( $supported_until ) );
				$days = $days_left == 1 ? '1 day' : "{$days_left} days";
				
				Kalium_Helpers::addAdminNotice( sprintf( 'Your support package for this theme is about to expire (<span title="%s">%s</span> left). <a href="%s" target="_blank">Renew support</a> package with 30%% discount before it expires. <span class="note-about-updates">Note: Support package is not required to get theme updates | Read more about <a href="%s" target="_blank">Envato Item Support</a> | %s</span>', "Expiration: $date", $days, $support_package_link, 'https://help.market.envato.com/hc/en-us/articles/207886473-Extending-and-Renewing-Item-Support', $dismiss_notice_link ), 'warning' );
			}
			
			// Dismiss the notice
			if ( isset( $_GET['laborator_dismiss_expiration'] ) && check_admin_referer( $supported_until_var, 'laborator_dismiss_expiration' ) ) {
				set_theme_mod( $supported_until_var, true );
				wp_redirect( remove_query_arg( 'laborator_dismiss_expiration' ) );
				die();
			}
		}
	}
	
	/**
	 *	Initialize Current Activated License
	 */
	private function initLicenseVar() {
		$license = get_option( $this->isEnvatoHostedSite() ? 'kalium_license_hosted' : 'kalium_license' );
		
		if ( is_object( $license ) && ! empty( $license->license_key ) && isset( $license->purchase_date ) && isset( $license->save_backups ) ) {
			$license->support_available = ! empty( $license->supported_until );
			
			// Support availability
			if ( $license->support_available ) {
				$supported_until_time = strtotime( $license->supported_until );
				$support_expired = $supported_until_time < time();
				
				$license->support_expired = $support_expired;
			}
			
			self::$license = $license;
		}
	}
	
	/**
	 *	Check if license is nearly expiring
	 */
	public function nearlyExpiring( $num_days = false ) {
		$license = self::license();
		$days_before_expiring = 15;
		
		if ( $license && $license->support_available ) {
			
			$supported_until_time = strtotime( $license->supported_until );
			$days_left = round( ( $supported_until_time - time() ) / ( 3600 * 24 ) );
			
			if ( $supported_until_time - time() <= $days_before_expiring * 86400 ) {
				return $num_days ? $days_left : true;
			}
		}
		
		return false;
	}
	
	/**
	 *	Get current license
	 */
	public static function license() {
		return self::$license;
	}
	
	/**
	 *	Check validity of license
	 */
	public static function isValid() {
		$license = self::$license;
		
		if ( is_object( $license ) && isset( $license->license_key ) && isset( $license->purchase_date ) ) {
			return true;
		}
		
		return false;
	}
	
	/**
	 *	Validate current license
	 */
	private static function validateLicense() {
		if ( self::isValid() ) {
			$license = self::license();
			$license_key = $license->license_key;
				
			$validate_license_response = wp_remote_post( self::getAPIServerURL() . "/validate-license/{$license_key}/" );
			$validated_license_data = json_decode( wp_remote_retrieve_body( $validate_license_response ) );
			
			set_theme_mod( 'theme_license_last_validation', time() );
			
			if ( isset( $validated_license_data->valid ) ) {
				$updated_license = (object) array_merge( (array) $license, (array) $validated_license_data );
				update_option( 'kalium_license', $updated_license );
				return true;
			}
			
			return false;
		}
	}
}