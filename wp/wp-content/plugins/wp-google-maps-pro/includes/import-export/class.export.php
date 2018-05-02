<?php
/**
 * WP Google Maps Pro Import / Export API: Export class
 *
 * @package WPGMapsPro\ImportExport
 * @since 7.0.0
 */

namespace WPGMZA;
 
/**
 * Exporter for WP Google Maps Pro
 *
 * This handles exporting of maps, categories, markers, circles,
 * polygons, polylines, rectangles and datasets.
 *
 * @since 7.0.0
 */
class Export {

	/**
	 * Export arguments.
	 *
	 * @var array $export_args Documented in constructor.
	 */
	private $export_args = array();

	/**
	 * Export constructor.
	 *
	 * @param array $args {
	 *     Optional. Export arguments.
	 *
	 *     @type array $maps       Integer array of map ids to export.
	 *                             Empty array for all. Default empty.
	 *     @type bool  $categories Export categories? Default true.
	 *     @type bool  $markers    Export markers? Default true.
	 *     @type bool  $circles    Export circles? Default true.
	 *     @type bool  $polygons   Export polygons? Default true.
	 *     @type bool  $polylines  Export polylines? Default true.
	 *     @type bool  $rectangles Export rectangles? Default true.
	 *     @type bool  $datasets   Export datasets? Default true.
	 * }
	 */
	public function __construct( $args = array() ) {

		$export_args = wp_parse_args( $args, array(
			'maps'         => array(),
			'categories'   => true,
			'customfields' => true,
			'markers'      => true,
			'circles'      => true,
			'polygons'     => true,
			'polylines'    => true,
			'rectangles'   => true,
			'datasets'     => true,
		) );

		if ( is_array( $export_args['maps'] ) ) {

			$export_args['maps'] = $this->sanitize_map_ids( $export_args['maps'] );

		} else {

			$export_args['maps'] = array();

		}

		$this->export_args = $export_args;

	}

	/**
	 * Sanitize map ids.
	 *
	 * @param array $maps Integer array of map ids.
	 * @return array Integer array of map ids.
	 */
	private function sanitize_map_ids( $maps ) {

		$map_count = count( $maps );

		for ( $i = 0; $i < $map_count; $i++ ) {

			if ( ! is_numeric( $maps[ $i ] ) ) {

				unset( $maps[ $i ] );
				continue;

			}

			$maps[ $i ] = absint( $maps[ $i ] );

			if ( $maps[ $i ] < 1 ) {

				unset( $maps[ $i ] );

			}
		}

		return $maps;

	}

	/**
	 * Generates the JSON export file for download.
	 *
	 * @global string $wpgmza_pro_version WP Google Maps Pro version number.
	 */
	public function download() {

		global $wpgmza_pro_version;

		$site_name = sanitize_key( get_bloginfo( 'name' ) );

		if ( ! empty( $site_name ) ) {

			$site_name .= '.';

		}

		$file_name = $site_name . 'wpgooglemaps.' . current_time( 'Y-m-d' ) . '.json';

		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $file_name );
		header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ), true );

		$pretty_print = version_compare( PHP_VERSION, '5.4', '>=' ) && defined( 'JSON_PRETTY_PRINT' ) ? JSON_PRETTY_PRINT : 0;

		echo wp_json_encode( array(
			'creator'      => 'WPGoogleMapsPro',
			'version'      => $wpgmza_pro_version,
			'maps'         => $this->get_maps_data(),
			'categories'   => $this->get_categories_data(),
			'customfields' => $this->get_custom_fields_data(),
			'markers'      => $this->get_markers_data(),
			'circles'      => $this->get_circles_data(),
			'polygons'     => $this->get_polygons_data(),
			'polylines'    => $this->get_polylines_data(),
			'rectangles'   => $this->get_rectangles_data(),
			'datasets'     => $this->get_datasets_data(),
		), $pretty_print );

	}

	/**
	 * Get maps data.
	 *
	 * @global wpdb   $wpdb                WordPress database object.
	 * @global string $wpgmza_tblname_maps Maps database table name.
	 *
	 * @return array Maps database rows as associative array.
	 */
	private function get_maps_data() {

		global $wpdb;
		global $wpgmza_tblname_maps;

		$where = empty( $this->export_args['maps'] ) ? '1=1' : '`id` IN (' . implode( ',', $this->export_args['maps'] ) . ')';

		$results = $wpdb->get_results( "SELECT * FROM `$wpgmza_tblname_maps` WHERE $where", ARRAY_A );

		return empty( $results ) ? array() : $results;

	}

	/**
	 * Get categories data.
	 *
	 * @global wpdb   $wpdb                         WordPress database object.
	 * @global string $wpgmza_tblname_category_maps Categories maps database table name.
	 * @global string $wpgmza_tblname_categories    Categories database table name.
	 *
	 * @return array Categories database rows as associative array.
	 */
	private function get_categories_data() {

		if ( ! $this->export_args['categories'] ) {

			return array();

		}

		global $wpdb;
		global $wpgmza_tblname_category_maps;
		global $wpgmza_tblname_categories;

		$where = empty( $this->export_args['maps'] ) ? '1=1' : "`$wpgmza_tblname_category_maps`.`map_id` = 0 OR `$wpgmza_tblname_category_maps`.`map_id` IN (" . implode( ',', $this->export_args['maps'] ) . ')';

		$results = $wpdb->get_results( "SELECT `$wpgmza_tblname_categories`.*, `$wpgmza_tblname_category_maps`.`map_id` FROM `$wpgmza_tblname_categories` JOIN `$wpgmza_tblname_category_maps` ON `$wpgmza_tblname_categories`.`id` = `$wpgmza_tblname_category_maps`.`cat_id` WHERE $where", ARRAY_A );

		return empty( $results ) ? array() : $results;

	}

	/**
	 * Get custom fields data.
	 *
	 * @global wpdb   $wpdb                         WordPress database object.
	 * @global string $wpgmza_tblname_category_maps Categories maps database table name.
	 * @global string $wpgmza_tblname_categories    Categories database table name.
	 *
	 * @return array Custom fields database rows as associative array.
	 */
	private function get_custom_fields_data() {

		if ( ! $this->export_args['customfields'] ) {

			return array();

		}

		global $wpdb;
		global $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS;
		global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;

		$where = empty( $this->export_args['maps'] ) ? '1=1' : "`$WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS`.`map_id` IN (" . implode( ',', $this->export_args['maps'] ) . ')';

		$results = $wpdb->get_results( "SELECT `$WPGMZA_TABLE_NAME_CUSTOM_FIELDS`.*, `$WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS`.`map_id`, `$WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS`.`object_id`, `$WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS`.`value` FROM `$WPGMZA_TABLE_NAME_CUSTOM_FIELDS` LEFT JOIN `$WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS` ON `$WPGMZA_TABLE_NAME_CUSTOM_FIELDS`.`id` = `$WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS`.`field_id` LEFT JOIN `$WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS` ON `$WPGMZA_TABLE_NAME_CUSTOM_FIELDS`.`id` = `$WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS`.`field_id` WHERE $where", ARRAY_A );

		return empty( $results ) ? array() : $results;

	}

	/**
	 * Get markers data.
	 *
	 * @global wpdb   $wpdb           WordPress database object.
	 * @global string $wpgmza_tblname Markers database table name.
	 *
	 * @return array Markers database rows as associative array.
	 */
	private function get_markers_data() {

		if ( ! $this->export_args['markers'] ) {

			return array();

		}

		global $wpdb;
		global $wpgmza_tblname;

		$where = empty( $this->export_args['maps'] ) ? '1=1' : '`map_id` IN (' . implode( ',', $this->export_args['maps'] ) . ')';

		$results = $wpdb->get_results( "SELECT `id`, `map_id`, `address`, `description`, `pic`, `link`, `icon`, `lat`, `lng`, `anim`, `title`, `infoopen`, `category`, `approved`, `retina`, `type`, `did`, `other_data` FROM `$wpgmza_tblname` WHERE $where", ARRAY_A );

		return empty( $results ) ? array() : $results;

	}

	/**
	 * Get circles data.
	 *
	 * @global wpdb   $wpdb                   WordPress database object.
	 * @global string $wpgmza_tblname_circles Circles database table name.
	 *
	 * @return array Circles database rows as associative array.
	 */
	private function get_circles_data() {

		if ( ! $this->export_args['circles'] ) {

			return array();

		}

		global $wpdb;
		global $wpgmza_tblname_circles;

		$where = empty( $this->export_args['maps'] ) ? '1=1' : '`map_id` IN (' . implode( ',', $this->export_args['maps'] ) . ')';

		$results = $wpdb->get_results( "SELECT `id`, `map_id`, `name`, X(`center`) AS `centerX`, Y(`center`) AS `centerY`, `radius`, `color`, `opacity` FROM `$wpgmza_tblname_circles` WHERE $where", ARRAY_A );

		return empty( $results ) ? array() : $results;

	}

	/**
	 * Get polygons data.
	 *
	 * @global wpdb   $wpdb                WordPress database object.
	 * @global string $wpgmza_tblname_poly Polygons database table name.
	 *
	 * @return array Polygons database rows as associative array.
	 */
	private function get_polygons_data() {

		if ( ! $this->export_args['polygons'] ) {

			return array();

		}

		global $wpdb;
		global $wpgmza_tblname_poly;

		$where = empty( $this->export_args['maps'] ) ? '1=1' : '`map_id` IN (' . implode( ',', $this->export_args['maps'] ) . ')';

		$results = $wpdb->get_results( "SELECT * FROM `$wpgmza_tblname_poly` WHERE $where", ARRAY_A );

		return empty( $results ) ? array() : $results;

	}

	/**
	 * Get polylines data.
	 *
	 * @global wpdb   $wpdb                     WordPress database object.
	 * @global string $wpgmza_tblname_polylines Polylines database table name.
	 *
	 * @return array Polylines database rows as associative array.
	 */
	private function get_polylines_data() {

		if ( ! $this->export_args['polylines'] ) {

			return array();

		}

		global $wpdb;
		global $wpgmza_tblname_polylines;

		$where = empty( $this->export_args['maps'] ) ? '1=1' : '`map_id` IN (' . implode( ',', $this->export_args['maps'] ) . ')';

		$results = $wpdb->get_results( "SELECT * FROM `$wpgmza_tblname_polylines` WHERE $where", ARRAY_A );

		return empty( $results ) ? array() : $results;

	}

	/**
	 * Get rectangles data.
	 *
	 * @global wpdb   $wpdb                      WordPress database object.
	 * @global string $wpgmza_tblname_rectangles Rectangles database table name.
	 *
	 * @return array Rectangles database rows as associative array.
	 */
	private function get_rectangles_data() {

		if ( ! $this->export_args['rectangles'] ) {

			return array();

		}

		global $wpdb;
		global $wpgmza_tblname_rectangles;

		$where = empty( $this->export_args['maps'] ) ? '1=1' : '`map_id` IN (' . implode( ',', $this->export_args['maps'] ) . ')';

		$results = $wpdb->get_results( "SELECT `id`, `map_id`, `name`, X(`cornerA`) AS `cornerAX`, Y(`cornerA`) AS `cornerAY`, X(`cornerB`) AS `cornerBX`, Y(`cornerB`) AS `cornerBY`, `color`, `opacity` FROM `$wpgmza_tblname_rectangles` WHERE $where", ARRAY_A );

		return empty( $results ) ? array() : $results;

	}

	/**
	 * Get datasets data.
	 *
	 * @global wpdb   $wpdb                    WordPress database object.
	 * @global string $wpgmza_tblname_datasets Datasets database table name.
	 *
	 * @return array Datasets database rows as associative array.
	 */
	private function get_datasets_data() {

		if ( ! $this->export_args['datasets'] ) {

			return array();

		}

		global $wpdb;
		global $wpgmza_tblname_datasets;

		$where = empty( $this->export_args['maps'] ) ? '1=1' : '`map_id` IN (' . implode( ',', $this->export_args['maps'] ) . ')';

		$results = $wpdb->get_results( "SELECT * FROM `$wpgmza_tblname_datasets` WHERE $where", ARRAY_A );

		return empty( $results ) ? array() : $results;

	}
}
