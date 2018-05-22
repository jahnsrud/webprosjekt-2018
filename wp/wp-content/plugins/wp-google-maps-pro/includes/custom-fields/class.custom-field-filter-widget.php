<?php

namespace WPGMZA;

require_once(plugin_dir_path(__FILE__) . 'class.custom-field-filter.php');

class CustomFieldFilterWidget
{
	protected $filter;
	
	public function __construct($filter)
	{
		$this->filter = $filter;
	}
	
	public function getAttributes()
	{
		// TODO: Add filter field attribtues too
		$result = array(
			'data-wpgmza-filter-widget-class'	=> get_class($this),
			'data-map-id'						=> $this->filter->getMapID(),
			'data-field-id'						=> $this->filter->getFieldID()
		);
		
		return $result;
	}
	
	public function getAttributesString()
	{
		$attributes = $this->getAttributes();
		$items = array();
		
		foreach($attributes as $name => $value)
			$items[] = $name . '="' . htmlspecialchars($value) . '"';
			
		return implode(' ', $items);
	}
	
	public function html()
	{
		return '';
	}
}

add_filter('wpgmza_get_custom_field_filter_widget', 'WPGMZA\\get_custom_field_filter_widget');

function get_custom_field_filter_widget($filter)
{
	$dir = plugin_dir_path(__DIR__);
	
	switch($filter->getFieldData()->widget_type)
	{
		case 'text':
			require_once("{$dir}custom-field-filter-widgets/class.text.php");
			return new CustomFieldFilterWidget\Text($filter);
			break;
			
		case 'dropdown':
			require_once("{$dir}custom-field-filter-widgets/class.dropdown.php");
			return new CustomFieldFilterWidget\Dropdown($filter);
			break;
			
		case 'checkboxes':
			require_once("{$dir}custom-field-filter-widgets/class.checkboxes.php");
			return new CustomFieldFilterWidget\Checkboxes($filter);
			break;
		
		default:
			return new CustomFieldFilterWidget($filter);
	}
}

add_filter('wpgooglemaps_filter_map_div_output', 'WPGMZA\\add_custom_filter_widgets');

function add_custom_filter_widgets($html)
{
	$document = new \DOMDocument();
	$document->loadHTML($html);
	$xpath = new \DOMXPath($document);
	$results = $xpath->query('//div[@class="wpgmza_map"]');
	
	if($results->length == 0)
		return $html;
	
	$element = $results->item(0);
	
	if(!preg_match('/\d+/', $element->getAttribute('id'), $m))
		return $html;
	
	$map_id = (int)$m[0];
	
	$custom_fields = new CustomFields($map_id);
	
	$widget_html = '<div class="wpgmza-filter-widgets" data-map-id="' . $map_id . '">';
	
	foreach($custom_fields as $field)
	{
		$filter = apply_filters('wpgmza_get_custom_field_filter', $field->id, $map_id);
		$widget = apply_filters('wpgmza_get_custom_field_filter_widget', $filter);
		$widget_html .= $widget->html();
	}
	
	$widget_html .= '</div>';
	
	return $widget_html . $html;
}
