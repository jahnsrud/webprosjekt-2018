<?php
/**
 *	System Status Page
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

global $wpdb;

$mark_yes = '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
$mark_no = '<mark class="no"><span class="dashicons dashicons-no-alt"></span></mark>';

$memory_limit = Kalium_Helpers::letToNum( WP_MEMORY_LIMIT );

if ( function_exists( 'memory_get_usage' ) ) {
	$wp_memory_limit = max( $memory_limit, Kalium_Helpers::letToNum( @ini_get( 'memory_limit' ) ) );
}

if ( function_exists( 'memory_get_usage' ) ) {
	$system_memory = Kalium_Helpers::letToNum( @ini_get( 'memory_limit' ) );
	$memory_limit = max( $memory_limit, $system_memory );
}


$active_plugins = (array) get_option( 'active_plugins', array() );

if ( is_multisite() ) {
	$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
	$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
}
?>
<div class="wrap about-wrap product-activation-wrap">
	<?php require 'about-header.php'; ?>
	
	<div class="laborator-system-status-data">
		
		<?php /* Copy server status report */ ?>
		<div class="get-status-report">
			<p class="status-info">
				<span>Please copy and paste this information in your ticket when contacting support:</span>
				<a href="#" class="button-primary debug-report">Get System Report</a>
			</p>
			
			<p class="status-data">
				<textarea id="system-status-report" readonly="readonly"></textarea>
				<button id="system-status-report-button" data-clipboard-target="#system-status-report" class="button">Copy for Support</button>
			</p>
		</div>
		
		<?php /* Theme Information */ ?>
		<table class="widefat">
			<thead>
				<tr>
					<th colspan="3">Theme Information</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Theme Name:</td>
					<td class="help"></td>
					<th><?php echo wp_get_theme(); ?></th>
				</tr>
				<tr>
					<td>Current Version:</td>
					<td class="help"></td>
					<th><?php echo kalium()->getVersion(); ?></th>
				</tr>
				<tr>
					<td>Theme Directory:</td>
					<td class="help"></td>
					<th><?php echo kalium()->getThemeDir( '~/' ); ?></th>
				</tr>
				<tr>
					<td>Child Theme:</td>
					<td class="help"></td>
					<th><?php echo is_child_theme() ? $mark_yes : 'No'; ?></th>
				</tr>
				<?php if ( is_child_theme() ) : ?>
				<tr>
					<td>Child Theme Directory:</td>
					<td class="help"></td>
					<th><?php echo str_replace( ABSPATH, '~/', get_stylesheet_directory() . '/' ); ?></th>
				</tr>
				<?php endif; ?>
				<tr>
					<td>License Activated:</td>
					<td class="help"></td>
					<th><?php echo Kalium_Theme_License::isValid() ? $mark_yes : $mark_no; ?></th>
				</tr>
			</tbody>
		</table>
	
	
		<?php /* WordPress Environment */ ?>
		<table class="widefat">
			<thead>
				<tr>
					<th colspan="3">WordPress Environment</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Home URL:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( 'The URL of your site\'s homepage.' ); ?></td>
					<th>
						<?php echo home_url(); ?>
					</th>
				</tr>
				<tr>
					<td>Site URL:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( 'The root URL of your WordPress installation.' ); ?></td>
					<th>
						<?php echo site_url(); ?>
					</th>
				</tr>
				<tr>
					<td>WordPress Version:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( 'The version of WordPress installed on your site.' ); ?></td>
					<th>
						<?php echo bloginfo( 'version' ); ?>
					</th>
				</tr>
				<tr>
					<td>WordPress Multisite:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( 'Whether or not you have WordPress Multisite enabled.' ); ?></td>
					<th>
						<?php echo is_multisite() ? $mark_yes : '-'; ?>
					</th>
				</tr>
				<tr>
					<td>WordPress Memory Limit:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( 'The maximum amount of memory (RAM) that your site can use at one time.' ); ?></td>
					<th>
						<?php
						if ( $memory_limit < 128 * MB_IN_BYTES ) {
							echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( '%s - We recommend setting memory to at least 128MB. See: %s', size_format( $memory_limit ), '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank"> Increasing memory allocated to PHP </a>' ) . '</mark>';
						} else {
							echo '<mark class="yes">' . size_format( $memory_limit ) . '</mark>';
						}
						?>
					</th>
				</tr>
				<tr>
					<td>WordPress Debug Mode:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( 'Displays whether or not WordPress is in Debug Mode.' ); ?></td>
					<th>
						<?php echo ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? $mark_yes : '-'; ?>
					</th>
				</tr>
				<tr>
					<td>Language:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( 'The current language used by WordPress.' ); ?></td>
					<th>
						<?php echo get_locale(); ?>
					</th>
				</tr>
			</tbody>
		</table>
	
	
		<?php /* Server Environment */ ?>
		<table class="widefat">
			<thead>
				<tr>
					<th colspan="3">Server Environment</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Server Info:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( 'Information about the web server that is currently hosting your site.' ); ?></td>
					<th>
						<?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ); ?>
					</th>
				</tr>
				<tr>
					<td>PHP Version:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( 'The version of PHP installed on your hosting server.' ); ?></td>
					<th>
						<?php
						if ( function_exists( 'phpversion' ) ) {
							$php_version = phpversion();

							if ( version_compare( $php_version, '5.6', '<' ) ) {
								echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( '%s - We recommend a minimum PHP version of 5.6. See: %s', esc_html( $php_version ), '<a href="https://docs.woocommerce.com/document/how-to-update-your-php-version/" target="_blank">How to update your PHP version</a>' ) . '</mark>';
							} else {
								echo '<mark class="yes">' . esc_html( $php_version ) . '</mark>';
							}
						} else {
							echo "Couldn't determine PHP version because phpversion() doesn't exist.";
						}
					?>
					</th>
				</tr>
				<tr>
					<td>PHP Post Max Size:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( 'The largest filesize that can be contained in one post.' ); ?></td>
					<th>
						<?php echo size_format( Kalium_Helpers::letToNum( ini_get( 'post_max_size' ) ) ); ?>
					</th>
				</tr>
				<tr>
					<td>PHP Time Limit:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( 'The amount of time (in seconds) that your site will spend on a single operation before timing out (to avoid server lockups)' ); ?></td>
					<th>
					<?php
						$time_limit = ini_get( 'max_execution_time' );

						if ( 90 > $time_limit && 0 != $time_limit ) {
							echo '<mark class="error soft">' . sprintf( '%1$s - We recommend setting max execution time to at least 90 <em>(only when importing demo content)</em>.<br />See: <a href="%2$s" target="_blank" rel="noopener noreferrer">Increasing max execution to PHP</a>', $time_limit, 'http://codex.wordpress.org/Common_WordPress_Errors#Maximum_execution_time_exceeded' ) . '</mark>';
						} else {
							echo '<mark class="yes">' . $time_limit . '</mark>';
						}
					?>
					</th>
				</tr>
				<tr>
					<td>PHP Max Input Vars:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( 'The maximum number of variables your server can use for a single function to avoid overloads.' ); ?></td>
					<th>
					<?php
						$registered_navs = get_nav_menu_locations();
						$menu_items_count = array( '0' => '0' );
						foreach ( $registered_navs as $handle => $registered_nav ) {
							$menu = wp_get_nav_menu_object( $registered_nav );
							if ( $menu ) {
								$menu_items_count[] = $menu->count;
							}
						}

						$max_items = max( $menu_items_count );
						$required_input_vars = $max_items * 20;
						
						$max_input_vars = ini_get( 'max_input_vars' );
						$required_input_vars = $required_input_vars + ( 500 + 1000 );
						
						if ( $max_input_vars < $required_input_vars ) {
							echo '<mark class="error">' . sprintf('%1$s - Recommended Value: %2$s.<br />Max input vars limitation will truncate POST data such as menus. See: <a href="%3$s" target="_blank" rel="noopener noreferrer">Increasing max input vars limit.</a>', $max_input_vars, '<strong>' . $required_input_vars . '</strong>', 'http://sevenspark.com/docs/ubermenu-3/faqs/menu-item-limit' ) . '</mark>';
						} else {
							echo '<mark class="yes">' . $max_input_vars . '</mark>';
						}
					?>
					</th>
				</tr>
				<tr>
					<td>Max Upload Size:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( 'The largest filesize that can be uploaded to your WordPress installation.' ); ?></td>
					<th>
						<?php echo size_format( wp_max_upload_size() ); ?>
					</th>
				</tr>
				<tr>
					<td>MySQL Version:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( 'The version of MySQL installed on your server.' ); ?></td>
					<th>
						<?php echo $wpdb->db_version(); ?>
					</th>
				</tr>
				<tr>
					<td>cURL version:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( 'The version of cURL installed on your server.' ); ?></td>
					<th>
						<?php
							$curl_version = '';
							$openssl_version = get_openssl_version_number();
							
							if ( function_exists( 'curl_version' ) ) {
								$curl_version = curl_version();
								$curl_version = $curl_version['version'] . ', ' . $curl_version['ssl_version'];
							}
							
							echo $curl_version;

							if ( version_compare( $openssl_version, '1.0', '<' ) ) {
								echo '<br><mark class="error">' . sprintf( 'Recommended version for OpenSSL is 1.0 or higher', $openssl_version ). '</mark>';
							}
						?>
					</th>
				</tr>
				<tr>
					<td>DOM Document:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( 'DOMDocument is required for the Demo Content Importer plugin to properly function.' ); ?></td>
					<th>
					<?php 
						echo class_exists( 'DOMDocument' ) ? $mark_yes : '<mark class="error">DOMDocument is not installed on your server, but is required if you want to Import Demo Content data.</mark>'; 
					?>
					</th>
				</tr>
				<?php
					$remote_method_tip = 'Kalium uses this method to communicate with different APIs such as Laborator API Server and Envato API.';
				?>
				<tr>
					<td>WP Remote Get:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( $remote_method_tip ); ?></td>
					<th>
					<?php
						
						// Envato API Check
						$response = wp_safe_remote_get( 'https://build.envato.com/api/', array( 'decompress' => false, 'user-agent' => 'kalium-test-wp-remote-get' ) ); 
						
						echo ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) ? $mark_yes : $mark_no;
						
					?>
					</th>
				</tr>
				<tr>
					<td>WP Remote Post:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( $remote_method_tip ); ?></td>
					<th>
					<?php
						
						// Laborator System Hello
						$laborator_server_access_error = false;
						$laborator_hello = Kalium_Theme_License::getAPIServerURL();
						
						$laborator_hello_response = wp_safe_remote_post( $laborator_hello, array( 
							'decompress' => false, 
							'body' => array( 
								'system' => 'hello' 
							) 
						) );
						
						if ( is_wp_error( $laborator_hello_response ) ) {
							$laborator_server_access_error = true;
						} else {
							$laborator_hello_response_body = json_decode( wp_remote_retrieve_body( $laborator_hello_response ) );
							
							if ( empty( $laborator_hello_response_body->success ) ) {
								$laborator_server_access_error = true;
							}
						}
						
						echo ! $laborator_server_access_error ? $mark_yes : $mark_no; 
						
						if ( $laborator_server_access_error ) {
							echo "<br><mark class=\"error\">Laborator API server is not accessible at this url: {$laborator_hello}</mark>";
						}
					?>
					</th>
				</tr>
				<tr>
					<td>GD Library:</td>
					<td class="help"><?php Laborator_System_Status::tooltip( 'GD Library is a program installed on your server that allows programs to manipulate graphics.' ); ?></td>
					<th>
					<?php
						$info = 'Not Installed';
						
						if ( extension_loaded( 'gd' ) && function_exists( 'gd_info' ) ) {
							$info = 'Installed';
							$gd_info = gd_info();
							if ( isset( $gd_info['GD Version'] ) ) {
								$info = $gd_info['GD Version'];
							}
						}
						echo $info;
					?>
					</th>
				</tr>
			</tbody>
		</table>
	
	
		<?php /* Active Plugins */ ?>
		<table class="widefat">
			<thead>
				<tr>
					<th colspan="3">Active Plugins (<?php echo count( $active_plugins ); ?>)</th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ( $active_plugins as $plugin ) {
	
				$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
				$dirname        = dirname( $plugin );
				$version_string = '';
				$network_string = '';
	
				if ( ! empty( $plugin_data['Name'] ) ) {
	
					// Link the plugin name to the plugin url if available.
					$plugin_name = esc_html( $plugin_data['Name'] );
	
					if ( ! empty( $plugin_data['PluginURI'] ) ) {
						$plugin_name = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="Visit plugin homepage" target="_blank">' . $plugin_name . '</a>';
					}
	
					?>
					<tr>
						<td><?php echo $plugin_name; ?></td>
						<th class="not-bold"><?php echo sprintf( 'by %s', $plugin_data['Author'] ) . ' &ndash; ' . esc_html( $plugin_data['Version'] ) . $version_string . $network_string; ?></th>
					</tr>
					<?php
				}
			}
			?>
			</tbody>
		</table>
	</div>
	
	
	<?php /* Initialize Tooltips */ ?>
	<script type="text/javascript">
	function generateSystemStatusDate() {
		var report = '',
			nl = "\n",
			nl2 = nl + nl,
			nl3 = nl2 + nl;
		
		jQuery( '.laborator-system-status-data table' ).each( function( i, el ) {
			var $table = jQuery( el ),
				title = $table.find( 'thead th' ).text(),
				$vars = $table.find( 'tbody tr' );
			
			report += '### ' + title + ' ###';
			report += nl2;
			
			$vars.each( function( j, row ) {
				var $row = jQuery( row ).clone();
				
				$row.find( 'a' ).prepend( '"' ).append( '"' );
				
				var $val = $row.find( 'th' ).first(),
					rowTitle = $row.find( 'td' ).first().text().trim(),
					rowValue = $val.text().trim()
				
				if ( $val.find( 'mark .dashicons-yes' ).length ) {
					rowValue = '✔';
				} else if ( $val.find( 'mark .dashicons-no-alt' ).length ) {
					rowValue = '❌';
				}
					
				report += rowTitle + ' ' + rowValue;
				report += nl;
			} );
			
			report += nl2;
		} );
		
		return report.trim();
	}
	
	jQuery( document ).ready( function( $ ) {
		var cpb = new Clipboard( '#system-status-report-button' ),
			$cpb_tooltip = $( '#system-status-report-button' ).tooltipster( { content: 'Copied!', side: 'bottom', trigger: 'click', theme : 'tooltipster-borderless' } );
		
		cpb.on( 'success', function() {
			$cpb_tooltip.tooltipster( 'show' );
		} );
		
		cpb.on( 'error', function() {
			$cpb_tooltip.tooltipster( 'close' );
		} );
		
		$( '.debug-report' ).on( 'click', function( ev ) {
			ev.preventDefault();
			$( '.get-status-report .status-data' ).slideToggle( 'fast', function() {
				$( '#system-status-report' ).select();
			} );
		} );
		
		$( '.tooltip' ).each( function( i, el ) {
			var $el = $( el );
			
			$el.tooltipster( {
				theme   : 'tooltipster-borderless',
				content : $( '#' + $el.data( 'content' ) ),
				side: 'top'
			} );
		} );
		
		$( '.status-data textarea' ).val( generateSystemStatusDate() );
	} );
	</script>
</div>