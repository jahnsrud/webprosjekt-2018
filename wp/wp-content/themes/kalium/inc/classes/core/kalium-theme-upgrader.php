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

class Kalium_Theme_Upgrader {
	
	/**
	 *	Theme ID based on folder name
	 */
	private $theme_id;
	
	/**
	 * Update data
	 */
	private $update_data;
	
	/**
	 *	Theme Backups Folder
	 */
	public static $backup_folder = '';
	
	/**
	 *	Theme Backup File Name
	 */
	public static $backup_file_name = 'kalium-{version}-{date}.zip';
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->theme_id = basename( kalium()->locateFile() );
		$uploads_dir = wp_upload_dir();
		self::$backup_folder = $uploads_dir['basedir'];
	}
	
	/**
	 * After setup theme
	 */
	public function after_setup_theme() {
		
		// Retrieve remote version data
		$this->retrieveVersionData();
		
		// Theme Version Checker
		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check' ), 100 );
		add_filter( 'pre_set_transient_update_themes', array( $this, 'check' ), 100 );
		
		// Theme Upgrader Information
		add_filter( 'upgrader_pre_download', array( $this, 'beforeUpdatingThemeFilter' ), 1000, 3 );
		add_filter( 'upgrader_process_complete', array( $this, 'afterThemeUpdateFilter' ), 100, 2 );
		
		// Theme update notices
		add_action( 'admin_footer', array( $this, 'displayThemeUpdateNotices' ), 10 );
		
		// Theme Updated Redirect to What's New
		if ( true == get_option( 'kalium_updated' ) && ( is_admin() && ! defined( 'DOING_AJAX' ) && 'laborator_options' !== kalium()->url->get( 'page' ) ) ) {
			wp_redirect( admin_url( 'admin.php?page=laborator-about&updated' ) );
			delete_option( 'kalium_updated' );
			exit;
		}
	}
	
	/**
	 * Retrieve version data from server
	 */
	private function retrieveVersionData() {
		global $pagenow;
		
		if ( ! is_admin() ) {
			return;
		}

		$update_data = get_option( 'kalium_upgrader', array(
			'current_version' => '',
			'version_data' => array(),
			'last_check' => 0
		) );
		
		$check_interval = 172800; // 2 days
		
		// Force check updates (lower interval)
		if ( 'update-core.php' == $pagenow && kalium()->get( 'force-check', true ) ) {
			$check_interval = 300; // 5 minutes
		}
		
		// Check for updates
		if ( $update_data['last_check'] < ( time() - $check_interval ) ) {
		
			// Get latest theme version
			$license = Kalium_Theme_License::license();
			
			$response = wp_remote_post( Kalium_Theme_License::getAPIServerURL(), array(
				'body' => array(
					'version_check' => 'kalium',
					'license_key' => $license ? $license->license_key : ''
				)
			) );
			
			$response_body = wp_remote_retrieve_body( $response );
			$response_code = wp_remote_retrieve_response_code( $response );
			
			// Version data
			$version_data = json_decode( $response_body );
			
			if ( is_object( $version_data ) ) {
				// Version data
				$update_data['version_data'] = $version_data;
				
				// Current version
				$update_data['current_version'] = $version_data->new_version;
				
				// Set last checked
				$update_data['last_check'] = time();
			}
			
			// Update data
			update_option( 'kalium_upgrader', $update_data );
		}
		
		// Set update data
		$this->update_data = $update_data;
		
	}
	
	/**
	 *	Theme Updates Notification
	 */
	public function admin_init() {
		global $pagenow;
		
		$themes = get_theme_updates();
		$theme_version_id = 'theme-version-' . kalium()->getVersion( true );
		
		if ( isset( $themes[ $this->theme_id ] ) && ( 'kalium-product-registration' != $this->admin_page && 'update-core.php' != $pagenow ) && ! get_theme_mod( $theme_version_id ) ) {
			$theme_update = $themes[ $this->theme_id ];
			
			if ( isset( $theme_update->update['new_version'] ) && version_compare( $theme_update->update['new_version'], kalium()->getVersion(), '>' ) ) {				
				$new_version = $theme_update->update['new_version'];
				$current_version = kalium()->getVersion();
				
				$update_url = admin_url( 'update-core.php' );
				
				if ( ! Kalium_Theme_License::license() ) {
					$update_url = admin_url( 'admin.php?page=kalium-product-registration' );
				}
				
				// Update Notification Dismiss
				$dismiss_update_notification_name = 'laborator_dismiss_update_notification';
				
				Kalium_Helpers::addAdminNotice( sprintf( '<a href="%s" class="notice-dismiss"></a>There is an update for <strong>%s</strong> theme, your current version is <strong>%s</strong> and latest version is <strong>%s</strong>. <a href="%s">Click here to update the theme &raquo;</a>', add_query_arg( array( $dismiss_update_notification_name => wp_create_nonce( $theme_version_id ) ) ), $theme_update, $current_version, $new_version, $update_url ), 'warning', false );
				
				if ( isset( $_GET[ $dismiss_update_notification_name ] ) && check_admin_referer( $theme_version_id, $dismiss_update_notification_name ) ) {
					set_theme_mod( $theme_version_id, true );
					wp_redirect( remove_query_arg( $dismiss_update_notification_name ) );
					die();
				}
			}
		}
	}
	
	/**
	 *	Check for newer versions
	 */
	public function check( $transient ) {
		if ( empty( $transient->checked[ $this->theme_id ] ) ) {
			return $transient;
		}
		
		// Get latest theme version
		$update_data = $this->update_data;
		
		if ( ! empty( $update_data['version_data'] ) && version_compare( $update_data['current_version'], kalium()->getVersion(), '>' ) ) {
			$transient->response[ $this->theme_id ] = (array) $update_data['version_data'];
		}
		
		return $transient;
	}
	
	/**
	 *	Before updating the theme, show necesarry information
	 */
	public function beforeUpdatingThemeFilter( $reply, $package, $updater ) {
		$license = Kalium_Theme_License::license();
		
		// Check if its Theme_Upgrader object
		if ( ! $updater instanceof Theme_Upgrader || ! isset( $updater->skin->theme_info ) || 'kalium' !== $updater->skin->theme_info->get( 'TextDomain' ) ) {
			return $reply;
		}
		
		// Theme is not activated
		if ( ! $license ) {
			return new WP_Error( 'product_not_activated', 'Theme is not activated, please <a href="' . admin_url( 'admin.php?page=kalium-product-registration' ) . '" target="_parent">activate the theme</a> before updating it.' );
		}
		// Check license status
		else {
			$response = wp_remote_post( Kalium_Theme_License::getAPIServerURL(), array(
				'body' => array(
					'action'       => 'license-status',
					'theme_id'     => 'kalium',
					'license_key'  => $license->license_key
				)
			) );
			
			$response_body = json_decode( wp_remote_retrieve_body( $response ) );
			
			// Show update errors
			if ( $response_body->has_errors ) {
				return new WP_Error( 'product_license_errors', $response_body->error_msg );
			} 
			// Download permitted
			else {
				// Backup File name and Path
				$file_name = str_replace( array( '{version}', '{date}' ), array( kalium()->getVersion( true ), date( 'dmy-Hi' ) ), self::$backup_file_name  );
				$backup_file_path = self::$backup_folder . '/' . $file_name;
				
				$updater->strings['creating_theme_backup'] = 'Creating theme backup&hellip;<br>';
				$updater->strings['theme_backup_created']  = 'Backup file created in <strong>' . str_replace( ABSPATH, '', $backup_file_path ) . '</strong><br>';
				$updater->strings['product_update_valid']  = 'License key and WP site is permitted, download can start.<br>';
				
				// Create Theme Backup
				if ( $license->save_backups ) {
					if ( ! class_exists( 'PclZip' ) ) {
						// Load class file if it's not loaded yet
						include ABSPATH . 'wp-admin/includes/class-pclzip.php';
					}
					
					// Creating Theme Backup message
					$updater->skin->feedback( 'creating_theme_backup' );
					
					try {
						$archive = new PclZip( $backup_file_path );
						
						// Theme backup created
						if ( @$archive->add( kalium()->locateFile(), PCLZIP_OPT_REMOVE_PATH, dirname( kalium()->locateFile() ) ) ) {
							$updater->skin->feedback( 'theme_backup_created' );
						} 
						// Theme backup couldn't be created
						else {
							return new WP_Error( 'theme_backup_creation_error', '<span title="' . esc_attr( $archive->error_string ) . '">Cannot create theme backup, upgrade process failed.</span>' );
						}
					} catch ( Exception $e ) {
						return new WP_Error( 'theme_backup_creation_error', $e->getMessage() );
					}
					
				}	
				
				// Valid Product Update Feedback
				$updater->skin->feedback( 'product_update_valid' );
			}
			
		}
		
		return $reply;
	}
	
	/**
	 *	Theme update was successful
	 */
	public function afterThemeUpdateFilter( $updater, $data ) {
		// Check if current theme is updated
		if ( 'update' == $data['action'] && 'theme' == $data['type'] && $updater instanceof Theme_Upgrader && 'kalium' == $updater->skin->theme_info->get( 'TextDomain' ) ) {
			update_option( 'kalium_updated', true );
			
			// Clean update cache
			$update_data = get_option( 'kalium_upgrader', array(
				'last_check' => 0
			) );
			
			$update_data['current_version'] = '';
			$update_data['version_data'] = array();
			
			update_option( 'kalium_upgrader', $update_data );
		}
	}
	
	/**
	 * Get theme update notice to warn user about import update changes
	 *
	 * @since 2.1
	 */
	public function getThemeUpdateNotice() {
		$update_themes = get_site_transient( 'update_themes' );
		
		if ( empty( $update_themes ) ) {
			$update_themes = get_transient( 'update_themes' );
		}
		
		if ( $update_themes && ! empty( $update_themes->response['kalium']['update_notice'] ) ) {
			return wp_kses( $update_themes->response['kalium']['update_notice'], array(
				'a' => array(
					'href' => array(),
					'target' => array(),
					'title' => array()
				),
				'strong' => array(),
				'br' => array()
			) );
		}
		
		return null;
	}
	
	/**
	 * Show theme update notices
	 */
	public function displayThemeUpdateNotices() {
		global $pagenow;
		
		if ( current_user_can( 'update_themes' ) && in_array( $pagenow, array( 'update-core.php', 'themes.php' ) ) ) {
			$update_message = $this->getThemeUpdateNotice();
			
			if ( $update_message ) {
				wp_enqueue_script( 'kalium-theme-update-notice', kalium()->assetsUrl( 'js/admin/theme-update-notice.min.js' ), null );
				wp_localize_script( 'kalium-theme-update-notice', 'kaliumThemeUpdateNotice', array(
					'updateMessage' => $update_message
				) );
			}
		}
	}
}