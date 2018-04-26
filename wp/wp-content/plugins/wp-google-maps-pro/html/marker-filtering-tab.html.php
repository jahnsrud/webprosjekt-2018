<?php

require_once(plugin_dir_path(__DIR__) . 'includes/custom-fields/class.custom-fields.php');

global $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS;

?>

<div id="marker-filtering">

	<fieldset>
		<label>
			<?php
			_e('Enable custom field filtering on', 'wp-google-maps');
			?>
		</label>
		<div>
			<ul>
				<?php
				
				$map_id = (int)$_GET['map_id'];
				
				$checked_fields = $wpdb->get_col("SELECT field_id FROM $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS WHERE map_id=$map_id");
				$custom_fields = new WPGMZA\CustomFields();
				
				if(!count($custom_fields))
				{
					?>
					<p class="notice notice-warning">
						<?php
						_e('You have no custom fields to filter on. Please add some in order to add custom field filters.', 'wp-google-maps');
						?>
					</p>
					<?php
				}
				else
					foreach($custom_fields as $field)
					{
						$checked = (array_search($field->id, $checked_fields) !== false ? "checked='checked'" : '');
						$disabled = ($field->widget_type == 'none');
						$title = __('Toggle filter', 'wp-google-maps');
						
						if($disabled)
						{
							$title = __('No widget type selected', 'wp-google-maps');
							$checked = false;
						}
					
						echo "
							<li>
								<input 
									type='checkbox' 
									name='enable_filter_custom_field_{$field->id}' 
									class='wpgmza-enable-custom-field-filter'
									$checked
									" . ($disabled ? 'readonly="readonly"' : '') . "
									title='$title'
									/>
								" . htmlentities($field->name);
								
						if($disabled)
						{
							echo "
								<p class='notice notice-warning' style='display: none;'>
									" . __('You must choose a widget type for this field to enable filtering on it', 'wp-google-maps') . "
								</p>
							";
						}
								
						echo "
							</li>
						";
					}
				
				?>
			</ul>
		</div>
	</fieldset>
	
</div>