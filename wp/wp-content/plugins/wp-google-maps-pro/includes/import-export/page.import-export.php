<?php
/**
 * WP Google Maps Pro Import / Export API
 *
 * @package WPGMapsPro\ImportExport
 * @since 7.0.0
 */

namespace WPGMZA;

/**
 * Import and export classes.
 */

$path = plugin_dir_path( __FILE__ );

require_once( $path . 'class.import.php' );
require_once( $path . 'class.import-csv.php' );
require_once( $path . 'class.import-gpx.php' );
require_once( $path . 'class.import-json.php' );
require_once( $path . 'class.import-kml.php' );
require_once( $path . 'class.export.php' );

add_action( 'load-maps_page_wp-google-maps-menu-advanced', 'WPGMZA\\import_export_download' );
/**
 * Export downloading processing.
 */
function import_export_download() {

	// Export download.
	if ( wpgmza_user_can_edit_maps() && isset( $_GET['action'], $_GET['export_nonce'] ) &&
	     wp_verify_nonce( $_GET['export_nonce'], 'wpgmza_export_file' ) && 'export_json' === $_GET['action'] ) {

		$export_args = array(
			'maps'         => isset( $_GET['maps'] ) ? explode( ',', $_GET['maps'] ) : array(),
			'categories'   => isset( $_GET['categories'] ) ? true : false,
			'customfields' => isset( $_GET['customfields'] ) ? true : false,
			'markers'      => isset( $_GET['markers'] ) ? true : false,
			'circles'      => isset( $_GET['circles'] ) ? true : false,
			'polygons'     => isset( $_GET['polygons'] ) ? true : false,
			'polylines'    => isset( $_GET['polylines'] ) ? true : false,
			'rectangles'   => isset( $_GET['rectangles'] ) ? true : false,
			'datasets'     => isset( $_GET['datasets'] ) ? true : false,
		);

		$export = new Export( $export_args );
		$export->download();
		die();

	}

	wp_enqueue_script( 'wp-util' );
	wp_enqueue_script( 'jquery-ui-slider' );

}

add_action( 'wp_ajax_wpgmza_import_upload', 'WPGMZA\\import_ajax_handle_upload' );
/**
 * Import AJAX handle upload file.
 */
function import_ajax_handle_upload() {

	if ( ! wpgmza_user_can_edit_maps() || ! current_user_can( 'upload_files' ) ) {

		wp_send_json_error( __( "You don't have permission to upload files.", 'wp-google-maps' ) );

	}

	if ( ! isset( $_FILES['wpgmaps_import_file'] ) || ! wp_verify_nonce( $_POST['wpgmaps_security'], 'wpgmaps_import' ) ) {

		wp_send_json_error( __( 'No file upload or failed security check.', 'wp-google-maps' ) );

	}

	if ( ! function_exists( 'wp_handle_upload' ) ) {

		require_once( ABSPATH . 'wp-admin/includes/file.php' );

	}

	$overrides = array(
		'test_form' => false,
		'mimes'     => import_mimes(),
	);

	$upload = wp_handle_upload( $_FILES['wpgmaps_import_file'], $overrides );

	if ( isset( $upload['error'] ) ) {

		wp_send_json_error( $upload['error'] );

	}

	$id = wp_insert_attachment( array(
		'post_title'     => basename( $upload['file'] ),
		'post_content'   => $upload['url'],
		'post_mime_type' => $upload['type'],
		'guid'           => $upload['url'],
		'context'        => 'wpgmaps-import',
		'post_status'    => 'private',
	), $upload['file'] );

	if ( $id > 0 ) {

		wp_send_json_success( array(
			'id'    => $id,
			'title' => basename( $upload['file'] ),
		) );

	}

	wp_send_json_error( __( 'Unable to add file to database.', 'wp-google-maps' ) );

}

add_action( 'wp_ajax_wpgmza_import_delete', 'WPGMZA\\import_ajax_handle_delete' );
/**
 * Import AJAX delete file handle.
 */
function import_ajax_handle_delete() {

	if ( ! wpgmza_user_can_edit_maps() || ! isset( $_POST['import_id'], $_POST['wpgmaps_security'] ) ||
	     ! wp_verify_nonce( $_POST['wpgmaps_security'], 'wpgmaps_import' ) ) {

		wp_send_json_error( __( 'No file specified or failed security check.', 'wp-google-maps' ) );

	}

	$id = absint( $_POST['import_id'] );

	if ( $id < 1 || get_post_meta( $id, '_wp_attachment_context', true ) !== 'wpgmaps-import' ) {

		wp_send_json_error( __( 'Deletion not allowed. File is not a valid WP Google Maps import upload.', 'wp-google-maps' ) );

	}

	wp_delete_attachment( $id, true );

	wp_send_json_success( array(
		'id' => $id,
	) );
}

add_action( 'wp_ajax_wpgmza_import_file_options', 'WPGMZA\\import_ajax_file_options' );
/**
 * Import AJAX retrieve options html for import file.
 */
function import_ajax_file_options() {

	if ( ! empty( $_POST['schedule_id'] ) ) {

		$import_schedule = get_option( 'wpgmza_import_schedule' );
		$import_options = $import_schedule[ $_POST['schedule_id'] ]['options'];
		$import_options['start'] = get_date_from_gmt( date( 'Y-m-d H:i:s', $import_schedule[ $_POST['schedule_id'] ]['start'] ), 'Y-m-d' );
		$import_options['interval'] = $import_schedule[ $_POST['schedule_id'] ]['interval'];
		$_POST['import_id'] = $import_schedule[ $_POST['schedule_id'] ]['import_id'];
		$_POST['import_url'] = $import_schedule[ $_POST['schedule_id'] ]['import_url'];

	} else {

		$import_options = array();

	}

	if ( ! wpgmza_user_can_edit_maps() || ( ! isset( $_POST['import_id'] ) && ! isset( $_POST['import_url'] ) ) || ! isset( $_POST['wpgmaps_security'] ) ||
	     ! wp_verify_nonce( $_POST['wpgmaps_security'], 'wpgmaps_import' ) ) {

		wp_send_json_error( __( 'No file or url specified or failed security check.', 'wp-google-maps' ) );

	}

	$import_mimes = import_mimes();

	if ( ! empty( $_POST['import_id'] ) && is_numeric( $_POST['import_id'] ) ) {

		$id = absint( $_POST['import_id'] );

		if ( $id < 1 || get_post_meta( $id, '_wp_attachment_context', true ) !== 'wpgmaps-import' ) {

			wp_send_json_error( __( 'Importing not allowed. File is not a valid WP Google Maps import upload.', 'wp-google-maps' ) );

		}

		$import_file     = get_attached_file( $id );
		$import_file_url = wp_get_attachment_url( $id );
		$extension       = pathinfo( $import_file, PATHINFO_EXTENSION );

	} elseif ( ! empty( $_POST['import_url'] ) ) {

		$import_file      = '';
		$import_file_url  = $_POST['import_url'];
		$extension        = pathinfo( $import_file_url, PATHINFO_EXTENSION );
		$google_sheets_id = array();

		if ( preg_match( '@/spreadsheets/d/([a-zA-Z0-9-_]+)@', $import_file_url, $google_sheets_id ) ) {

			$import_file_url = "https://docs.google.com/spreadsheets/d/{$google_sheets_id[1]}/gviz/tq?tqx=out:csv";
			$extension = 'csv';

		}
	}

	if ( ! empty( $extension ) && array_key_exists( strtolower( $extension ), $import_mimes ) ) {

		$import_class = 'WPGMZA\\Import' . strtoupper( $extension );

		if ( class_exists( $import_class ) ) {

			try {

				$import       = new $import_class( $import_file, $import_file_url, $import_options );
				$options_html = $import->admin_options();
				$notices_html = $import->get_admin_notices();
				
				wp_send_json_success( array(
					'id'			=> empty( $id ) ? 0 : $id,
					'url'			=> empty( $import_file_url ) ? '' : $import_file_url,
					'options_html'	=> $options_html,
					'notices_html'	=> $notices_html
				) );

			} catch ( \Exception $e ) {

				wp_send_json_error( $e->getMessage() );

			}
		}
	}

	wp_send_json_error( __( 'Unable to import file.', 'wp-google-maps' ) );

}

add_action( 'wp_ajax_wpgmza_import', 'WPGMZA\\import_ajax_import' );
/**
 * Import AJAX do import.
 */
function import_ajax_import() {

	if ( ! wpgmza_user_can_edit_maps() || ( ! isset( $_POST['import_id'] ) && ! isset( $_POST['import_url'] ) ) ||
	     ! isset( $_POST['wpgmaps_security'] ) || ! wp_verify_nonce( $_POST['wpgmaps_security'], 'wpgmaps_import' ) ) {

		wp_send_json_error( __( 'No file specified or failed security check.', 'wp-google-maps' ) );

	}

	$import_mimes = import_mimes();

	if ( ! empty( $_POST['import_id'] ) && is_numeric( $_POST['import_id'] ) ) {

		$id = absint( $_POST['import_id'] );

		if ( $id < 1 || get_post_meta( $id, '_wp_attachment_context', true ) !== 'wpgmaps-import' ) {

			wp_send_json_error( __( 'Importing not allowed. File is not a valid WP Google Maps import upload.', 'wp-google-maps' ) );

		}

		$import_file     = get_attached_file( $id );
		$import_file_url = wp_get_attachment_url( $id );
		$extension       = pathinfo( $import_file, PATHINFO_EXTENSION );

	} elseif ( ! empty( $_POST['import_url'] ) ) {

		$import_file      = '';
		$import_file_url  = esc_url_raw( $_POST['import_url'] );
		$extension        = pathinfo( $import_file_url, PATHINFO_EXTENSION );
		$google_sheets_id = array();

		if ( preg_match( '@/spreadsheets/d/([a-zA-Z0-9-_]+)@', $import_file_url, $google_sheets_id ) ) {

			$import_file_url = "https://docs.google.com/spreadsheets/d/{$google_sheets_id[1]}/gviz/tq?tqx=out:csv";
			$extension = 'csv';

		}
	}

	if ( ! empty( $extension ) && array_key_exists( strtolower( $extension ), $import_mimes ) ) {

		$import_class = 'WPGMZA\\Import' . strtoupper( $extension );

		if ( class_exists( $import_class ) ) {

			try {

				set_time_limit( 1200 );
				$import = new $import_class( $import_file, $import_file_url, $_POST['options'] );
				$import->import();

				$delete = 0;

				if ( ! empty( $id ) && isset( $_POST['options']['delete'] ) ) {

					wp_delete_attachment( $id, true );
					$delete = 1;

				}

				wp_send_json_success( array(
					'id'  => empty( $id ) ? 0 : $id,
					'url' => empty( $import_file_url ) ? '' : $import_file_url,
					'del' => $delete,
					'notices' => $import->get_admin_notices()
				) );

			} catch ( \Exception $e ) {

				wp_send_json_error( $e->getMessage() );

			}
		}
	}

	wp_send_json_error( __( 'Unable to import file.', 'wp-google-maps' ) );

}

add_action( 'wpgmza_import_cron', 'WPGMZA\\import_cron_import' );
/**
 * Import CRON.
 *
 * @param string $schedule_id Schedule id to import.
 */
function import_cron_import( $schedule_id ) {

	$import_schedule = get_option( 'wpgmza_import_schedule' );

	if ( ! isset( $import_schedule[ $schedule_id ] ) ) {

		wp_clear_scheduled_hook( 'wpgmza_import_cron', array( $schedule_id ) );
		return;

	}

	$import_schedule[ $schedule_id ]['last_run_message'] = __( 'Last Run', 'wp-google-maps' ) . ': ' . current_time( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) . ' ';

	$import_mimes = import_mimes();

	if ( ! empty( $import_schedule[ $schedule_id ]['import_id'] ) && is_numeric( $import_schedule[ $schedule_id ]['import_id'] ) ) {

		$id = absint( $import_schedule[ $schedule_id ]['import_id'] );

		if ( $id < 1 || get_post_meta( $id, '_wp_attachment_context', true ) !== 'wpgmaps-import' ) {

			$import_schedule[ $schedule_id ]['last_run_message'] .= __( 'Importing not allowed. File is not a valid WP Google Maps import upload.', 'wp-google-maps' );
			update_option( 'wpgmza_import_schedule', $import_schedule );
			return;

		}

		$import_file     = get_attached_file( $id );
		$import_file_url = wp_get_attachment_url( $id );
		$extension       = pathinfo( $import_file, PATHINFO_EXTENSION );

	} elseif ( ! empty( $import_schedule[ $schedule_id ]['import_url'] ) ) {

		$import_file      = '';
		$import_file_url  = esc_url_raw( $import_schedule[ $schedule_id ]['import_url'] );
		$extension        = pathinfo( $import_file_url, PATHINFO_EXTENSION );
		$google_sheets_id = array();

		if ( preg_match( '@/spreadsheets/d/([a-zA-Z0-9-_]+)@', $import_file_url, $google_sheets_id ) ) {

			$import_file_url = "https://docs.google.com/spreadsheets/d/{$google_sheets_id[1]}/gviz/tq?tqx=out:csv";
			$extension = 'csv';

		}
	}

	if ( ! empty( $extension ) && array_key_exists( strtolower( $extension ), $import_mimes ) ) {

		$import_class = 'WPGMZA\\Import' . strtoupper( $extension );

		if ( class_exists( $import_class ) ) {

			try {

				set_time_limit( 1200 );
				$import = new $import_class( $import_file, $import_file_url, $import_schedule[ $schedule_id ]['options'] );
				$import->import();

				$import_schedule[ $schedule_id ]['last_run_message'] .= __( 'Import completed.', 'wp-google-maps' );
				update_option( 'wpgmza_import_schedule', $import_schedule );
				return;

			} catch ( \Exception $e ) {

				$import_schedule[ $schedule_id ]['last_run_message'] .= $e->getMessage();
				update_option( 'wpgmza_import_schedule', $import_schedule );
				return;

			}
		}
	}

	$import_schedule[ $schedule_id ]['last_run_message'] .= __( 'Unable to import file.', 'wp-google-maps' );
	update_option( 'wpgmza_import_schedule', $import_schedule );

}

/**
 * Import allowed mime types.
 */
function import_mimes() {

	return array( 
		'csv'  => 'text/csv',
		'gpx'  => 'application/xml',
		'json' => 'application/json',
		'kml'  => 'application/xml',
	);

}

add_action( 'wpgmza_admin_advanced_options_tabs', 'WPGMZA\\import_export_admin_tabs' );
/**
 * Import/export admin page tabs.
 */
function import_export_admin_tabs() {

	?>
	<li><a href="#import-tab"><?php esc_html_e( 'Import' , 'wp-google-maps' ); ?></a></li>
	<li><a href="#schedule-tab"><?php esc_html_e( 'Schedule', 'wp-google-maps' ); ?></a></li>
	<li><a href="#export-tab"><?php esc_html_e( 'Export' , 'wp-google-maps' ); ?></a></li>
	<?php

}

add_filter( 'cron_schedules', 'WPGMZA\\import_cron_schedules' );
/**
 * Adds custom cron schedules.
 *
 * @param array $schedules An array of non-default cron schedules.
 * @return array Filtered array of non-default cron schedules.
 */
function import_cron_schedules( $schedules ) {

	$schedules['weekly'] = array(
		'interval' => WEEK_IN_SECONDS,
		'display'  => __( 'Once Weekly', 'wp-google-maps' ),
	);

	$schedules['monthly'] = array(
		'interval' => MONTH_IN_SECONDS,
		'display'  => __( 'Once Monthly', 'wp-google-maps' ),
	);

	return $schedules;

}

/**
 * Get maps list helper function.
 *
 * @global wpdb   $wpdb                WordPress database class.
 * @global string $wpgmza_tblname_maps Maps database table name.
 *
 * @param string     $context  Context of the list, used to create ids and classes.
 * @param array|bool $selected Array of selected map ids.
 * @return array|string Table rows and columns of maps. Array of map ids if $content passed as 'ids'.
 */
function import_export_get_maps_list( $context, $selected = false ) {

	static $maps = null;

	if ( null === $maps ) {

		global $wpdb;
		global $wpgmza_tblname_maps;

		$maps = $wpdb->get_results( "SELECT `id`, `map_title` FROM `$wpgmza_tblname_maps` WHERE `active`=0 ORDER BY `id` DESC" );

	}

	if ( empty( $maps ) ) {

		return 'ids' === $context ? array() : '';

	}

	$ret = 'ids' === $context ? array() : '';

	$context = sanitize_html_class( $context );

	foreach ( $maps as $map ) {

		$id = intval( $map->id );

		if ( 'ids' === $context ) {

			$ret[] = $id;

		} else {

			$title = esc_html( stripslashes( $map->map_title ) );
			$ret .= "<tr style='display:block;width:100%;'><td style='width:2.2em;'><div class='switch'><input id='maps_{$context}_{$id}' type='checkbox' value='{$id}' class='maps_{$context} cmn-toggle cmn-toggle-round-flat' " . ( false === $selected ? 'checked' : ( is_array( $selected ) && in_array( $id, $selected ) ? 'checked' : '' ) ) . "><label for='maps_{$context}_{$id}'></label></div></td><td style='width:80px;'>{$id}</td><td>{$title}</td></tr>";
		}
	}

	return $ret;

}

add_action( 'wp_ajax_wpgmza_import_schedule', 'WPGMZA\\import_ajax_schedule' );
/**
 * AJAX schedule an import CRON event.
 */
function import_ajax_schedule() {

	if ( ! wpgmza_user_can_edit_maps() || ( ! isset( $_POST['import_id'] ) && ! isset( $_POST['import_url'] ) ) || 
	     ! isset( $_POST['wpgmaps_security'], $_POST['options'] ) || ! wp_verify_nonce( $_POST['wpgmaps_security'], 'wpgmaps_import' ) ) {

		wp_send_json_error( __( 'No file specified or failed security check.', 'wp-google-maps' ) );

	}

	$import_mimes = import_mimes();

	if ( is_numeric( $_POST['import_id'] ) ) {

		$id = absint( $_POST['import_id'] );

		if ( $id < 1 || get_post_meta( $id, '_wp_attachment_context', true ) !== 'wpgmaps-import' ) {

			wp_send_json_error( __( 'Importing not allowed. File is not a valid WP Google Maps import upload.', 'wp-google-maps' ) );

		}

		$import_file     = get_attached_file( $id );
		$import_file_url = wp_get_attachment_url( $id );
		$extension       = pathinfo( $import_file, PATHINFO_EXTENSION );

	} elseif ( ! empty( $_POST['import_url'] ) ) {

		$import_file     = '';
		$import_file_url = esc_url_raw( $_POST['import_url'] );
		$extension       = pathinfo( $import_file_url, PATHINFO_EXTENSION );

	}

	if ( ! empty( $extension ) && array_key_exists( strtolower( $extension ), $import_mimes ) ) {

		$import_schedule = get_option( 'wpgmza_import_schedule' );

		if ( empty( $import_schedule ) || ! is_array( $import_schedule ) ) {

			$import_schedule = array();

		}

		if ( ! empty( $_POST['schedule_id'] ) ) {

			$schedule_id = $_POST['schedule_id'];

		} else {

			$schedule_id = md5( ( ! empty( $import_file ) ? $import_file : ( ! empty( $import_file_url ) ? $import_file_url : '' ) ) . time() );

		}

		$start    = get_gmt_from_date( $_POST['start'], 'U' );
		$interval = sanitize_text_field( $_POST['interval'] );
		$next_run = wp_next_scheduled( 'wpgmza_import_cron', array( $schedule_id ) );

		if ( false !== $next_run ) {

			if ( ! isset( $import_schedule[ $schedule_id ]['start'], $import_schedule[ $schedule_id ]['interval'] ) ||
				 $import_schedule[ $schedule_id ]['start'] !== $start || $import_schedule[ $schedule_id ]['interval'] !== $interval ) {

				wp_clear_scheduled_hook( 'wpgmza_import_cron', array( $schedule_id ) );
				$next_run = wp_next_scheduled( 'wpgmza_import_cron', array( $schedule_id ) );

			}
		}

		if ( isset( $import_schedule[ $schedule_id ] ) ) {

			unset( $import_schedule[ $schedule_id ] );

		}

		$import_schedule = array(
			$schedule_id => array(
				'start'      => $start,
				'interval'   => $interval,
				'title'      => sanitize_text_field( ! empty( $import_file ) ? basename( $import_file ) : $import_file_url ),
				'options'    => $_POST['options'],
				'import_id'  => ! empty( $id ) ? $id : 0,
				'import_url' => $import_file_url,
		) ) + $import_schedule;

		update_option( 'wpgmza_import_schedule', $import_schedule );

		if ( false === $next_run ) {

			wp_schedule_event( $import_schedule[ $schedule_id ]['start'], $import_schedule[ $schedule_id ]['interval'], 'wpgmza_import_cron', array( $schedule_id ) );
			$next_run = wp_next_scheduled( 'wpgmza_import_cron', array( $schedule_id ) );

		}

		if ( ! empty( $next_run ) ) {

			$import_schedule[ $schedule_id ]['next_run'] = get_date_from_gmt( date( 'Y-m-d H:i:s', $next_run ), get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );
			$import_schedule[ $schedule_id ]['schedule_id'] = $schedule_id;
			wp_send_json_success( $import_schedule[ $schedule_id ] );

		}
	} // End if().

	wp_send_json_error( __( 'Unable to schedule import.', 'wp-google-maps' ) );

}

add_action( 'wp_ajax_wpgmza_import_delete_schedule', 'WPGMZA\\import_ajax_delete_schedule' );
/**
 * AJAX delete import CRON schedule.
 */
function import_ajax_delete_schedule() {

	if ( ! wpgmza_user_can_edit_maps() || ! isset( $_POST['schedule_id'], $_POST['wpgmaps_security'] ) ||
	     ! wp_verify_nonce( $_POST['wpgmaps_security'], 'wpgmaps_import' ) ) {

		wp_send_json_error( __( 'No scheduled import specified or failed security check.', 'wp-google-maps' ) );

	}

	$import_schedule = get_option( 'wpgmza_import_schedule' );

	if ( ! isset( $import_schedule[ $_POST['schedule_id'] ] ) ) {

		wp_send_json_error( __( 'Scheduled import not found.', 'wp-google-maps' ) );

	}

	wp_clear_scheduled_hook( 'wpgmza_import_cron', array( $_POST['schedule_id'] ) );
	$next_run = wp_next_scheduled( 'wpgmza_import_cron', array( $_POST['schedule_id'] ) );

	if ( false === $next_run ) {

		unset( $import_schedule[ $_POST['schedule_id'] ] );
		update_option( 'wpgmza_import_schedule', $import_schedule );
		wp_send_json_success( array(
			'schedule_id' => $_POST['schedule_id'],
		) );

	}

	wp_send_json_error( __( 'Unable to remove scheduled import.', 'wp-google-maps' ) );

}

add_action( 'wp_ajax_wpgmaps_get_import_progress', 'WPGMZA\\import_get_progress' );
/**
 * AJAX get import progress.
 */
function import_get_progress() {

	@session_start();

	$key = 'wpgmza_import_progress_' . $_POST['wpgmaps_security'];
	$json = (object) array( 'progress' => 0.0 );

	if ( isset( $_SESSION[ $key ] ) ) {

		$json = $_SESSION[ $key ];

	}

	session_write_close();
	wp_send_json_success( $json );

}

/**
 * Get import CRON schedule.
 *
 * @return array Array of scheduled imports.
 */
function import_get_schedule() {

	$import_schedule = get_option( 'wpgmza_import_schedule' );

	if ( empty( $import_schedule ) || ! is_array( $import_schedule ) ) {

		return array();

	}

	foreach ( $import_schedule as $schedule_id => $schedule ) {

		$next_run = wp_next_scheduled( 'wpgmza_import_cron', array( $schedule_id ) );

		if ( false === $next_run ) {

			wp_schedule_event( $schedule['start'], $schedule['interval'], 'wpgmza_import_cron', array( $schedule_id ) );
			$next_run = wp_next_scheduled( 'wpgmza_import_cron', array( $schedule_id ) );

		}

		if ( ! empty( $next_run ) ) {

			$next_run = get_date_from_gmt( date( 'Y-m-d H:i:s', $next_run ), get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );

		}

		$import_schedule[ $schedule_id ]['next_run'] = $next_run;

	}

	return $import_schedule;

}

add_action( 'wpgmza_admin_advanced_options', 'WPGMZA\\import_export_admin_options' );
/**
 * Import/export admin page options.
 */
function import_export_admin_options() {

	$import_mimes = import_mimes();
	$import_accepts_attr = '';
	$import_accepts      = __( 'Accepts', 'wp-google-maps' ) . ': ';
	foreach ( $import_mimes as $ext => $mime ) {
		$import_accepts_attr .= "$mime,.$ext,";
		$import_accepts      .= "*.$ext, ";
	}
	$import_accepts_attr = rtrim( $import_accepts_attr, ',' );
	$import_accepts      = rtrim( $import_accepts, ', ' );

	$bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
	$size  = size_format( $bytes );

	?>
	<div id="import-tab">
		<div id="import_files">
			<h2><?php esc_html_e( 'Import Data', 'wp-google-maps' ); ?></h2>
			<table style="width:100%;">
				<tbody>
				<tr><td style="width:100px;vertical-align:top;">
					<p><?php esc_html_e( 'Import via:', 'wp-google-maps' ); ?></p>
				</td><td style="vertical-align:top;">
					<p>
						<label><input type="radio" name="import_data_type" class="import_data_type" value="URL" checked="checked"> <?php esc_html_e( 'URL', 'wp-google-maps' ); ?></label><br>
						<label><input type="radio" name="import_data_type" class="import_data_type" value="file"> <?php esc_html_e( 'File', 'wp-google-maps' ); ?></label>
					</p>
					<br>
					<div id="import_from_url">
						<p>
							<input id="wpgmaps_import_url" placeholder="<?php esc_attr_e( 'Import URL', 'wp-google-maps' ); ?>" type="text" style="max-width:500px;width:100%;"><br>
							<span class="description" style="display:inline-block;max-width:500px;"><?php esc_html_e( 'If using a Google Sheet URL, the sheet must be public or have link sharing turned on.', 'wp-google-maps' ); ?></span><br><br>
							<button id="wpgmaps_import_url_button" class="wpgmza_general_btn"><?php esc_html_e( 'Import', 'wp-google-maps' ); ?></button>
						</p>
					</div>
					<div id="import_from_file" style="display:none;">
						<p>
							<?php echo esc_html( $import_accepts ); ?><br><br>
							<input name="wpgmaps_import_file" id="wpgmaps_import_file" type="file" style="display:none;" accept="<?php echo esc_attr( $import_accepts_attr ); ?>">
							<label for="wpgmaps_import_file" class="wpgmza_file_select_btn"><i class="fa fa-download"></i> <?php esc_html_e( 'Select File', 'wp-google-map' ); ?>
							</label>
							<span id="wpgmaps_import_file_name" style="margin-left:10px;"></span><br><br>
							<?php esc_html_e( 'Max upload size', 'wp-google-maps' ); ?>: <?php echo esc_html( $size ); ?><br><br>
							<button id="wpgmaps_import_upload_button" class="wpgmza_general_btn"><?php esc_html_e( 'Upload', 'wp-google-maps' ); ?></button> <span id="wpgmaps_import_upload_spinner" class="spinner" style="float:none;margin-bottom:8px;"></span>
						</p>
						<?php
						$import_files = new \WP_Query( array( 
							'post_type'      => 'attachment',
							'meta_key'       => '_wp_attachment_context',
							'meta_value'     => 'wpgmaps-import',
							'posts_per_page' => - 1,
						) );
						?>
						<div id="wpgmaps_import_file_list" <?php echo $import_files->found_posts < 1 ? 'style="display:none;"' : ''; ?>>
							<br>
							<table id="wpgmap_import_file_list_table" class="wp-list-table widefat fixed striped wpgmza-listing" style="width:100%;padding:0;border:0 !important;">
								<thead>
								<tr>
									<th style="font-weight:bold;"><?php esc_html_e( 'Import Uploads', 'wp-google-maps' ); ?></th>
								</tr>
								</thead>
								<tbody>
								<?php foreach ( $import_files->posts as $import_file ) { ?>
									<tr id="import-list-item-<?php echo esc_attr( $import_file->ID ); ?>">
										<td>
											<strong><span class="import_file_title" style="font-size:larger;"><?php echo esc_html( $import_file->post_title ); ?></span></strong><br>
											<a href="javascript:void(0);" class="import_import" data-import-id="<?php echo esc_attr( $import_file->ID ); ?>"><?php esc_html_e( 'Import', 'wp-google-maps' ); ?></a>
											|
											<a href="javascript:void(0);" class="import_delete" data-import-id="<?php echo esc_attr( $import_file->ID ); ?>"><?php esc_html_e( 'Delete', 'wp-google-maps' ); ?></a>
										</td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						</div>
						<br>
					</div>
				</td></tr>
				</tbody>
			</table>
		</div>
		<div id="import_loader" style="display:none;">
			<div style="text-align:center;padding:50px 0;">
				<div class="spinner is-active" style="float:none;"></div>
				<div id="import_loader_text"></div>
			</div>
		</div>
		<div id="import_options" style="display:none;"></div>
		<script>
			var wpgmaps_import_security_nonce = '<?php echo esc_attr( wp_create_nonce( 'wpgmaps_import' ) ); ?>';

			jQuery(document).ready(function ($) {
				$('.import_data_type').change(function(){
					if ('URL' === $(this).val()) {
						$('#import_from_file').hide();
						$('#import_from_url').show();
					} else {
						$('#import_from_url').hide();
						$('#import_from_file').show();
					}
				});
				$('#wpgmaps_import_file').change(function () {
					if ($(this)[0].files.length > 0) {
						$('#wpgmaps_import_file_name').text($(this)[0].files[0].name);
					} else {
						$('#wpgmaps_import_file_name').html('');
					}
				});

				$('#wpgmaps_import_upload_button').click(function (e) {
					if ($('#wpgmaps_import_file')[0].files.length < 1) {
						alert('<?php echo wp_slash( __( 'Please select a file to upload.', 'wp-google-maps' ) ); ?>');
						return;
					}

					$('#wpgmaps_import_file,#wpgmaps_import_upload_button').prop('disabled', true);
					$('#wpgmaps_import_file + label,#wpgmaps_import_upload_button').css('opacity', '0.5');
					$('#wpgmaps_import_upload_spinner').addClass('is-active');

					var form_data = new FormData();
					form_data.append('action', 'wpgmza_import_upload');
					form_data.append('wpgmaps_security', wpgmaps_import_security_nonce);
					form_data.append('wpgmaps_import_file', $('#wpgmaps_import_file')[0].files[0]);

					wp.ajax.send({
						data: form_data,
						processData: false,
						contentType: false,
						cache: false,
						success: function (data) {
							if (typeof data !== 'undefined' && data.hasOwnProperty('id') && data.hasOwnProperty('title')) {
								$('#wpgmap_import_file_list_table tbody').prepend('<tr id="import-list-item-' + data.id + '"><td><strong><span class="import_file_title" style="font-size:larger;">' + data.title + '</span></strong><br>' +
									'<a href="javascript:void(0);" class="import_import" data-import-id="' + data.id + '"><?php esc_html_e( 'Import', 'wp-google-maps' ); ?></a>' +
									' | <a href="javascript:void(0);" class="import_delete" data-import-id="' + data.id + '"><?php esc_html_e( 'Delete', 'wp-google-maps' ); ?></a></td></tr>');
								wpgmaps_import_setup_file_links(data.id);
								$('#wpgmaps_import_file_list').show();
								$('#import-list-item-' + data.id + ' .import_import').click();
							}
						},
						error: function (data) {
							if (typeof data !== 'undefined') {
								wpgmaps_import_add_notice('<p>' + data + '</p>', 'error');
							}
						}
					}).always(function () {
						$('#wpgmaps_import_file_name').html('');
						$('#wpgmaps_import_file').replaceWith($('#wpgmaps_import_file').val('').clone(true));
						$('#wpgmaps_import_file,#wpgmaps_import_upload_button').prop('disabled', false);
						$('#wpgmaps_import_file + label,#wpgmaps_import_upload_button').css('opacity', '1.0');
						$('#wpgmaps_import_upload_spinner').removeClass('is-active');
					});
				});

				function wpgmaps_import_setup_file_links(id = '') {
					var del_select = '.import_delete';
					var imp_select = '.import_import';
					if (parseInt(id) > 1){
						del_select = '#import-list-item-' + id + ' ' + del_select;
						imp_select = '#import-list-item-' + id + ' ' + imp_select;
					}
					$(imp_select).click(function () {
						$('#import_files').hide();
						$('#import_loader_text').html('<br>Loading import options...');
						$('#import_loader').show();
						wp.ajax.send({
							data: {
								action: 'wpgmza_import_file_options',
								wpgmaps_security: wpgmaps_import_security_nonce,
								import_id: $(this).attr('data-import-id')
							},
							success: function (data) {
								if (typeof data !== 'undefined' && data.hasOwnProperty('options_html')) {
									$('#import_loader').hide();
									$('#import_options').html('<div style="margin:5px 0;"><a href="javascript:void(0);" onclick="jQuery(\'#import_options\').html(\'\').hide();jQuery(\'#import_files\').show();"><?php echo wp_slash( __( 'Back to Import Data', 'wp-google-maps' ) ); ?></a></div>' + data.options_html).show();
								}
							},
							error: function (data) {
								if (typeof data !== 'undefined') {
									wpgmaps_import_add_notice('<p>' + data + '</p>', 'error');
								}
								$('#import_loader').hide();
								$('#import_options').html('').hide();
								$('#import_files').show();
							}
						});
					});
					$(del_select).click(function () {
						if (confirm('<?php echo wp_slash( __( 'Are you sure you wish to delete this file?', 'wp-google-maps' ) ); ?> ' + $(this).parent().find('.import_file_title').text())) {
							wp.ajax.send({
								data: {
									action: 'wpgmza_import_delete',
									wpgmaps_security: wpgmaps_import_security_nonce,
									import_id: $(this).attr('data-import-id')
								},
								success: function (data) {
									if (typeof data !== 'undefined' && data.hasOwnProperty('id')) {
										$('#import-list-item-' + data.id).remove();
										wpgmaps_import_add_notice('<p><?php echo wp_slash( __( 'File deleted.', 'wp-google-maps' ) ); ?></p>');
									}
								},
								error: function (data) {
									if (typeof data !== 'undefined') {
										wpgmaps_import_add_notice('<p>' + data + '</p>', 'error');
									}
								}
							});
						}
					});
				}

				wpgmaps_import_setup_file_links();

				$('#wpgmaps_import_url_button').click(function () {
					var import_url = $('#wpgmaps_import_url').val();

					if (import_url.length < 1) {
						alert('<?php echo wp_slash( __( 'Please enter a URL to import from.', 'wp-google-maps' ) ); ?>');
						return;
					}
					$('#import_files').hide();
					$('#import_options').html('<div style="text-align:center;"><div class="spinner is-active" style="float:none;"></div></div>').show();
					wp.ajax.send({
						data: {
							action: 'wpgmza_import_file_options',
							wpgmaps_security: wpgmaps_import_security_nonce,
							import_url: import_url
						},
						success: function (data) {
							if (typeof data !== 'undefined' && data.hasOwnProperty('options_html')) {
								$('#import_options').html('<div style="margin:5px 0;"><a href="javascript:void(0);" onclick="jQuery(\'#import_options\').html(\'\').hide();jQuery(\'#import_files\').show();"><?php echo wp_slash( __( 'Back to Import Data', 'wp-google-maps' ) ); ?></a></div>' + data.options_html);
							}
						},
						error: function (data) {
							if (typeof data !== 'undefined') {
								wpgmaps_import_add_notice('<p>' + data + '</p>', 'error');
							}
							$('#import_options').html('').hide();
							$('#import_files').show();
						}
					});
				});
				function wpgmaps_import_add_notice( notice, type = 'success', noclear ) {
					if(!noclear)
						$('.notice').remove();

					var notice = '<div class="notice notice-' + type + ' is-dismissible">' + notice + '</div>';
					
					$('#wpgmaps_tabs').before(notice);
					
					$(notice).append('<button type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button>');
					$(notice).find(".notice-dismiss").on("click", function() {
						$(notice).fadeTo(100, 0, function() {
							$(notice).slideUp(100, function() {
								$(notice).remove();
							});
						});
					});
				}
				window.wpgmaps_import_add_notice = wpgmaps_import_add_notice;
			});
		</script>
	</div>
	<?php $import_schedule = import_get_schedule(); ?>
	<div id="schedule-tab" style="display:none;">
		<h2><?php esc_html_e( 'Schedule', 'wp-google-maps' ); ?></h2>
		<p class="description" style="max-width:600px;">
			<?php esc_html_e( 'Imports can be scheduled by url or uploaded file. To schedule an import, import as normal and select the Schedule button. Scheduled imports will be listed on this page and can be edited or deleted from here.', 'wp-google-maps' ); ?>
		</p>
		<div id="wpgmaps_import_schedule_list"<?php if ( empty( $import_schedule ) ) { ?> style="display:none;"<?php } ?>>
			<br>
			<table id="wpgmap_import_schedule_list_table" class="wp-list-table widefat fixed striped wpgmza-listing" style="width:100%;">
				<thead>
				<tr>
					<th><?php esc_html_e( 'URL / Filename', 'wp-google-maps' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php if ( ! empty( $import_schedule ) ) {
					foreach ( $import_schedule as $schedule_id => $schedule ) { ?>
						<tr id="import-schedule-list-item-<?php echo esc_attr( $schedule_id ); ?>">
							<td>
								<strong><span class="import_schedule_title" style="font-size:larger;"><?php echo esc_html( $schedule['title'] ); ?></span></strong><br>
								<a href="javascript:void(0);" class="import_schedule_edit" data-schedule-id="<?php echo esc_attr( $schedule_id ); ?>"><?php esc_html_e( 'Edit', 'wp-google-maps' ); ?></a>
								|
								<a href="javascript:void(0);" class="import_schedule_delete" data-schedule-id="<?php echo esc_attr( $schedule_id ); ?>"><?php esc_html_e( 'Delete', 'wp-google-maps' ); ?></a>
								|
								<?php if ( empty( $schedule['next_run'] ) ) { ?>
									<?php esc_html_e( 'No schedule found', 'wp-google-maps' ); ?>
								<?php } else { ?>
									<?php esc_html_e( 'Next Scheduled Run', 'wp-google-maps' ); ?>: <?php echo esc_html( $schedule['next_run'] ); ?>
								<?php } ?>
								<?php if ( ! empty( $schedule['last_run_message'] ) ) { ?>
									<br><?php echo esc_html( $schedule['last_run_message'] ); ?>
								<?php } ?>
							</td>
						</tr>
					<?php }
				} ?>
				</tbody>
			</table>
		</div>
		<script>
			jQuery(document).ready(function ($) {
				function wpgmaps_import_setup_schedule_links(id = '') {
					var del_select = '.import_schedule_delete';
					var edt_select = '.import_schedule_edit';
					if (id.length > 1){
						del_select = '#import-schedule-list-item-' + id + ' ' + del_select;
						edt_select = '#import-schedule-list-item-' + id + ' ' + edt_select;
					}
					$(edt_select).click(function () {
						$('a[href="#import-tab"]').click();
						$('#import_files').hide();
						$('#import_loader_text').html('<br><?php __( 'Loading import options...', 'wp-google-maps' ); ?>');
						$('#import_loader').show();
						wp.ajax.send({
							data: {
								action: 'wpgmza_import_file_options',
								wpgmaps_security: wpgmaps_import_security_nonce,
								schedule_id: $(this).attr('data-schedule-id'),
							},
							success: function (data) {
								if (typeof data !== 'undefined' && data.hasOwnProperty('options_html')) {
									$('#import_loader').hide();
									$('#import_options').html('<div style="margin:5px 0;"><a href="javascript:void(0);" onclick="jQuery(\'#import_options\').html(\'\').hide();jQuery(\'#import_files\').show();"><?php echo wp_slash( __( 'Back to Import Data', 'wp-google-maps' ) ); ?></a></div>' + data.options_html).show();
								}
							},
							error: function (data) {
								if (typeof data !== 'undefined') {
									wpgmaps_import_add_notice('<p>' + data + '</p>', 'error');
								}
								$('#import_loader').hide();
								$('#import_options').html('').hide();
								$('#import_files').show();
							}
						});
					});
					$(del_select).click(function () {
						if (confirm('<?php echo wp_slash( __( 'Are you sure you wish to delete this scheduled import?', 'wp-google-maps' ) ); ?> ' + $(this).parent().find('.import_schedule_title').text())) {
							wp.ajax.send({
								data: {
									action: 'wpgmza_import_delete_schedule',
									wpgmaps_security: wpgmaps_import_security_nonce,
									schedule_id: $(this).attr('data-schedule-id')
								},
								success: function (data) {
									if (typeof data !== 'undefined' && data.hasOwnProperty('schedule_id')) {
										$('#import-schedule-list-item-' + data.schedule_id).remove();
										wpgmaps_import_add_notice('<p><?php echo wp_slash( __( 'Scheduled import deleted.', 'wp-google-maps' ) ); ?></p>');
									}
								},
								error: function (data) {
									if (typeof data !== 'undefined') {
										wpgmaps_import_add_notice('<p>' + data + '</p>', 'error');
									}
								}
							});
						}
					});
				}
				window.wpgmaps_import_setup_schedule_links = wpgmaps_import_setup_schedule_links;

				wpgmaps_import_setup_schedule_links();
			});
		</script>
	</div>
	<?php $maps = import_export_get_maps_list( 'export' ); ?>
	<div id="export-tab" style="display:none;">
		<h2><?php esc_html_e( 'Export Data', 'wp-google-maps' ); ?></h2>
		<p class="description" style="max-width:600px;">
			<?php esc_html_e( 'Select which maps and map data you’d like to export. Click the Export button to download a JSON file of the exported maps and their data.', 'wp-google-maps' ); ?>
		</p>
		<div style="margin:0 0 1em 0;width:100%;">
			<?php if ( empty( $maps ) ) { ?>
				<br><?php esc_html_e( 'No maps available for export.', 'wp-google-maps' ); ?>
			<?php } else { ?>
				<table class="wp-list-table widefat fixed striped wpgmza-listing" style="width:100%;">
					<thead style="display:block;border-bottom:1px solid #e1e1e1;">
					<tr style="display:block;width:100%;">
						<th style="width:2.2em;border:none;"></th>
						<th style="width:80px;border:none;"><?php esc_html_e( 'ID', 'wp-google-maps' ); ?></th>
						<th style="border:none;"><?php esc_html_e( 'Title', 'wp-google-maps' ); ?></th>
					</tr>
					</thead>
					<tbody style="display:block;max-height:370px;overflow-y:scroll;">
					<?php echo $maps; ?>
					</tbody>
				</table>
				<button id="maps_export_select_all" class="wpgmza_general_btn"><?php esc_html_e( 'Select All', 'wp-google-maps' ); ?></button> <button id='maps_export_select_none' class='wpgmza_general_btn'><?php esc_html_e( 'Select None', 'wp-google-maps' ); ?></button><br><br>
			<?php } ?>
		</div>
		<p>
		<h2>Map Data</h2>
		</p>
		<div class="switch"><input id="categories_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked><label for="categories_export"></label></div><?php esc_html_e( 'Categories', 'wp-google-maps' ); ?><br>
		<div class="switch"><input id="customfields_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked><label for="customfields_export"></label></div><?php esc_html_e( 'Custom Fields', 'wp-google-maps' ); ?><br>
		<div class="switch"><input id="markers_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked><label for="markers_export"></label></div><?php esc_html_e( 'Markers', 'wp-google-maps' ); ?><br>
		<div class="switch"><input id="circles_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked><label for="circles_export"></label></div><?php esc_html_e( 'Circles', 'wp-google-maps' ); ?><br>
		<div class="switch"><input id="polygons_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked><label for="polygons_export"></label></div><?php esc_html_e( 'Polygons', 'wp-google-maps' ); ?><br>
		<div class="switch"><input id="polylines_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked><label for="polylines_export"></label></div><?php esc_html_e( 'Polylines', 'wp-google-maps' ); ?><br>
		<div class="switch"><input id="rectangles_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked><label for="rectangles_export"></label></div><?php esc_html_e( 'Rectangles', 'wp-google-maps' ); ?><br>
		<div class="switch"><input id="datasets_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked><label for="datasets_export"></label></div><?php esc_html_e( 'Heatmap Datasets', 'wp-google-maps' ); ?><br>
		<br>
		<p>
			<button id="export-json" class="wpgmza_general_btn"><?php esc_html_e( 'Export', 'wp-google-maps' ); ?></button>
		</p>
		<script>
			jQuery(document).ready(function($){
				$('#maps_export_select_all').click(function(){
					$('.maps_export').prop('checked',true);
				});
				$('#maps_export_select_none').click(function(){
					$('.maps_export').prop('checked',false);
				});
				$('#export-json').click(function(){
					var download_url = '?page=wp-google-maps-menu-advanced&action=export_json';
					var maps_check = $('.maps_export:checked');
					var map_ids = [];
					if (maps_check.length < 1){
						alert('<?php echo wp_slash( __( 'Please select at least one map to export.', 'wp-google-maps' ) ); ?>');
						return;
					}
					maps_check.each(function(){
						map_ids.push($(this).val());
					});
					if (map_ids.length < $('.maps_export').length){
						download_url += '&maps=' + map_ids.join(',');
					}
					$('.map_data_export').each(function(){
						if ($(this).prop('checked')){
							download_url += '&' + $(this).attr('id').replace('_export', '');
						}
					});
					window.open(download_url + '&export_nonce=<?php echo esc_attr( wp_create_nonce( 'wpgmza_export_file' ) ); ?>', '_blank');
				});
			});
		</script>
	</div>
	<?php

}
