<?php

namespace WPGMZA;

global $wpdb;
global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;
global $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS;

$WPGMZA_TABLE_NAME_CUSTOM_FIELDS = $wpdb->prefix . 'wpgmza_custom_fields';
$WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS = $wpdb->prefix . 'wpgmza_maps_has_custom_fields_filters';

/**
 * This class deals with custom fields on a specific map object (marker, polygon, polyline, etc.)
 */
class CustomMapObjectFields implements \IteratorAggregate, \JsonSerializable
{
	private static $installed = null;
	
	protected static $field_names_by_id = null;
	protected static $field_ids_by_name = null;
	
	protected $meta_table_name = null;
	private $object_id;
	
	private $meta;
	private $attributes;
	private $icon;
	
	/**
	 * Constructor. DO NOT call this directly. Use the hooks, for example wpgmza_get_marker_custom_fields
	 * @return WPGMZA\CustomFields
	 */
	public function __construct($object_id)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;
		
		if(!CustomFields::installed())
			CustomFields::install();
		
		if(!CustomMapObjectFields::$field_names_by_id)
		{
			CustomMapObjectFields::$field_names_by_id = array();
			
			$fields = $wpdb->get_results("SELECT id, name FROM $WPGMZA_TABLE_NAME_CUSTOM_FIELDS");
			
			foreach($fields as $obj)
				CustomMapObjectFields::$field_names_by_id[(int)$obj->id] = $obj->name;
				
			CustomMapObjectFields::$field_ids_by_name = array_flip(CustomMapObjectFields::$field_names_by_id);
		}
		
		if(!$this->meta_table_name)
			throw new \Exception('No table name');
		
		$this->object_id = (int)$object_id;
		$this->meta = array();
		
		$qstr = "SELECT
			name, 
			value,
			icon, 
			attributes 
			FROM `$WPGMZA_TABLE_NAME_CUSTOM_FIELDS`
			LEFT JOIN `{$this->meta_table_name}`
			ON `id`=`field_id`
			WHERE `object_id`=%d";
		$params = array($object_id);
		$stmt = $wpdb->prepare($qstr, $params);
		
		$results = $wpdb->get_results($stmt);
		
		foreach($results as $obj)
		{
			$this->meta[$obj->name] = $obj->value;
			
			if(!empty($obj->icon))
				$this->icon[$obj->name] = $obj->icon;

			if(!empty($obj->attributes))
				$this->attributes[$obj->name] = json_decode($obj->attributes);
			else
				$this->attributes = (object)array();
		}
	}
	
	/**
	 * Get iterator for looping over fields with foreach
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->meta);
	}
	
	/**
	 * Gets the fields to be serialized as JSON, useful for export
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this->meta;
	}
	
	/**
	 * Returns true if the named meta field is set
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->vars[$name]);
	}
	
	/**
	 * Gets the named meta field from this objects cache
	 * @return mixed
	 */
	public function __get($name)
	{
		if($name == 'object_id')
			return $this->object_id;
		
		if(isset($this->meta[$name]))
			return $this->meta[$name];
		
		return null;
	}
	
	/**
	 * Sets the named meta field in this objects cache and the database
	 * @return void
	 */
	public function __set($name, $value)
	{
		global $wpdb;
		
		if($name == 'object_id')
			throw new \Exception('Property is read only');
		
		if(is_numeric($name))
		{
			$field_id = (int)$name;
			$name = CustomMapObjectFields::$field_names_by_id[ (int)$name ];
		}
		else
			$field_id = CustomMapObjectFields::$field_ids_by_name[$name];
		
		$this->meta[$name] = $value;
		
		$stmt = $wpdb->prepare("INSERT INTO {$this->meta_table_name}
			(field_id, object_id, value)
			VALUES
			(%d, %d, %s)
			ON DUPLICATE KEY UPDATE value = %s",
			array(
				$field_id,
				$this->object_id,
				$value,
				$value
			)
		);
		
		$wpdb->query($stmt);
	}
	
	/**
	 * Removes the named meta field from the cache and deletes it from the database
	 * @return void
	 */
	public function __unset($name)
	{
		global $wpdb;
		
		unset($this->meta[$name]);
		
		$stmt = $wpdb->prepare("DELETE FROM {$this->meta_table_name} WHERE name=%s AND object_id=%d", array($name, $this->object_id));
		$wpdb->query($stmt);
	}
	
	public function remove()
	{
		global $wpdb;
		
		$this->meta = array();
		
		$stmt = $wpdb->prepare("DELETE FROM {$this->meta_table_name} WHERE object_id=%d", array($this->object_id));
		$wpdb->query($stmt);
	}

	/**
	 * Returns the default HTML for the custom fields (front end)
	 * TODO: This should be changed to use DOMDocument instead of plain strings, it's vulnerable to XSS attacks through UGM at the moment.
	 * @return string
	 */
	public function html()
	{
		$html = '';

		foreach($this->meta as $key => $value)
		{
			$item = '<p data-custom-field-name="' . htmlspecialchars($key) . '" ';

			foreach($this->attributes[$key] as $attr_name => $attr_value)
				$item .= "$attr_name=\"" . addcslashes($attr_value, '"') . "\"";

			$item .= '>';
			
			if(!empty($this->icon[$key]))
				$item .= '<span class="wpgmza-custom-field fa ' . $this->icon[$key] . '"></span>';
			$item .= $value . '</p>';
			
			$html .= apply_filters('wpgmza_custom_fields_row_html', $item);
		}
		
		return apply_filters('wpgmza_custom_fields_html', $html);
	}
	
	/**
	 * Shows the admin controls for these custom fields
	 * @return string
	 */
	public static function adminHtml()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;
		
		if(!CustomFields::installed())
			CustomFields::install();
		
		$fields = $wpdb->get_results("SELECT * FROM $WPGMZA_TABLE_NAME_CUSTOM_FIELDS");
		$html = '';
		
		foreach($fields as $field)
		{
			$attributes = '';
			
			foreach(json_decode($field->attributes) as $attr_name => $attr_value)
				$attributes .= " $attr_name=\"" . addcslashes($attr_value, '"') . "\"";
			
			$item = '<tr>
				<td>
					' . htmlspecialchars($field->name) . '
				</td>
				<td>
					<input data-custom-field-name="' . addcslashes($field->name, '"') . '" name="wpgmza-custom-field-' . $field->id . '" ' . $attributes . '/>
				</td>
			</tr>';
			
			$html .= $item;
		}
		
		return $html;
	}
}
