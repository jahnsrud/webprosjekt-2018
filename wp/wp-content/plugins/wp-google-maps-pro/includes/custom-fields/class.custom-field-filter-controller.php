<?php

namespace WPGMZA;

class CustomFieldFilterController
{
	public $params;
	
	public function __construct($params)
	{
		$this->params = $params;
	}
	
	protected function getFilterQueries()
	{
		$params = $this->params;
		
		$map_id = $params['map_id'];
		$queries = array();
		
		foreach($params['widgetData'] as $widgetData)
		{
			$field_id = $widgetData['field_id'];
			$filter = apply_filters('wpgmza_get_custom_field_filter', $field_id, $map_id);
			
			if(!empty($widgetData['value']))
				$queries[] = $filter->getFilteringSQL($widgetData['value']);
		}
		
		return $queries;
	}
	
	/**
	 * This function combines all the widget queries in a manner
	 * that emulates the INTERSECT operator (Not available in MySQL)
	 * @return string The query string
	 */
	protected function getQuery()
	{
		global $wpdb;
		
		$queries = $this->getFilterQueries();
		
		if(empty($queries))
			return "SELECT id FROM {$wpdb->prefix}wpgmza WHERE map_id=" . (int)$params['map_id'];
		
		$numQueries = count($queries);
		
		foreach($queries as $key => $qstr)
			$queries[$key] = "($qstr)";
		
		$body = implode(' UNION ALL ', $queries);
		
		$query = "
			SELECT temp.id FROM (
				$body
			) AS temp
			GROUP BY id HAVING COUNT(id) >= $numQueries
		";
		
		return $query;
	}
	
	public function getFilteredMarkerIDs()
	{
		global $wpdb;
		
		$sql = $this->getQuery();
		$ids = $wpdb->get_col($sql);
		
		return $ids;
	}
}

add_filter('wpgmza_get_custom_field_filter_controlller', 'WPGMZA\\get_custom_field_filter_controller');

function get_custom_field_filter_controller($params)
{
	return new CustomFieldFilterController($params);
}

add_action('wp_ajax_nopriv_wpgmza_custom_field_filter_get_filtered_marker_ids', 'WPGMZA\\custom_field_filter_get_filtered_marker_ids');
add_action('wp_ajax_wpgmza_custom_field_filter_get_filtered_marker_ids', 'WPGMZA\\custom_field_filter_get_filtered_marker_ids');

function custom_field_filter_get_filtered_marker_ids() {
	
	$controller = apply_filters('wpgmza_get_custom_field_filter_controlller', $_POST);
	$filtered_marker_ids = $controller->getFilteredMarkerIDs();
	
	$result = (object)array(
		'marker_ids' => $filtered_marker_ids
	);
	
	wp_send_json($result);
	
	exit;
}
