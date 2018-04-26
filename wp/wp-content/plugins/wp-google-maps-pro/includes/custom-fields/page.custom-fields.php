<?php

namespace WPGMZA;

require_once(__DIR__ . '/class.custom-fields.php');
require_once(__DIR__ . '/class.custom-marker-fields.php');

class CustomFieldsPage
{
	public function __construct()
	{
		if(!CustomFields::installed())
			CustomFields::install();
		
		wp_enqueue_script('wpgmza-fontawesome-iconpicker', plugin_dir_url(WPGMZA_PRO_FILE) . 'lib/fontawesome-iconpicker/js/fontawesome-iconpicker.min.js');
		wp_enqueue_style('wpgmza-fontawesome-iconpicker', plugin_dir_url(WPGMZA_PRO_FILE) . 'lib/fontawesome-iconpicker/css/fontawesome-iconpicker.min.css');
		
		wp_enqueue_script('wpgmza-custom-fields-page', plugin_dir_url(WPGMZA_PRO_FILE) . 'js/custom-fields-page.js');
	}
	
	/**
	 * Called when POSTing custom field data through WP admin post hook
	 * @return void
	 */
	public static function POST()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;
		
		$numFields = count($_POST['ids']);
		
		// Remove fields which aren't in POST from the DB
		$qstr = "DELETE FROM $WPGMZA_TABLE_NAME_CUSTOM_FIELDS";
		if($numFields > 0)
			$qstr .= " WHERE id NOT IN (" . implode(',', array_map('intval', $_POST['ids'])) . ")";
		$wpdb->query($qstr);
		
		// Iterate over fields in POST
		for($i = 0; $i < $numFields; $i++)
		{
			$id 			= $_POST['ids'][$i];
			$name 			= $_POST['names'][$i];
			$icon			= $_POST['icons'][$i];
			$attributes		= stripslashes($_POST['attributes'][$i]);
			$widget_type	= $_POST['widget_types'][$i];
			
			if(!json_decode($attributes))
				throw new \Exception('Invalid attribute JSON');
			
			if($id == -1 || empty($id))
			{
				$qstr = "INSERT INTO $WPGMZA_TABLE_NAME_CUSTOM_FIELDS (name, icon, attributes, widget_type) VALUES (%s, %s, %s, %s)";
				$params = array($name, $icon, $attributes, $widget_type);
			}
			else
			{
				$qstr = "UPDATE $WPGMZA_TABLE_NAME_CUSTOM_FIELDS SET name=%s, icon=%s, attributes=%s, widget_type=%s WHERE id=%s";
				$params = array($name, $icon, $attributes, $widget_type, $id);
			}
			
			$stmt = $wpdb->prepare($qstr, $params);
			$wpdb->query($stmt);
		}
		
		wp_redirect( admin_url('admin.php') . '?page=wp-google-maps-menu-custom-fields' );
		exit;
	}
	
	/**
	 * Echos attribute table HTML for the given field
	 * @return void
	 */
	protected function attributeTableHTML($field)
	{
		$attributes = json_decode($field->attributes);
		
		?>
		<input name="attributes[]" type="hidden"/>
		<table class="attributes">
			<tbody>
				<?php
				foreach($attributes as $key => $value)
				{
				?>
					<tr>
						<td>
							<input
								placeholder="<?php _e('Name', 'wp-google-maps'); ?>"
								class="attribute-name"
								value="<?php echo $key; ?>"
								/>
						</td>
						<td>
							<input 
								placeholder="<?php _e('Value', 'wp-google-maps'); ?>"
								class="attribute-value"
								value="<?php echo $value; ?>"
								/>
						</td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
		<?php
	}
	
	/**
	 * Echos the custom field page table
	 * @return void
	 */
	protected function tableBodyHTML()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;
		
		$fields = $wpdb->get_results("SELECT * FROM $WPGMZA_TABLE_NAME_CUSTOM_FIELDS");
		
		foreach($fields as $obj)
		{
			?>
			<tr>
				<td>
					<input name="ids[]" value="<?php echo $obj->id; ?>"/>
				</td>
				<td>
					<input name="names[]" value="<?php echo addslashes($obj->name); ?>"/>
				</td>
				<td>
					<input class="wpgmza-fontawesome-iconpicker" name="icons[]" value="<?php echo $obj->icon; ?>"/>
				</td>
				<td>
					<?php
					
					$this->attributeTableHTML($obj);
					
					?>
				</td>
				<td>
					<?php
					$options = array(
						'none'			=> 'None',
						'text'			=> 'Text',
						'dropdown'		=> 'Dropdown',
						'checkboxes'	=> 'Checkboxes'
					);
					?>
				
					<select name="widget_types[]">
						<?php
						foreach($options as $value => $text)
						{
							?>
							<option value="<?php echo $value; ?>"
							<?php
							if($obj->widget_type == $value)
								echo ' selected="selected"';
							?>
								>
								<?php echo __($text, 'wp-google-maps'); ?>
							</option>
							<?php
						}
						
						// Use this filter to add options to the dropdown
						$custom_options = apply_filters('wpgmza_custom_fields_widget_type_options', $obj);
						
						if(is_string($custom_options))
							echo $custom_options;
						
						?>
					</select>
				</td>
				<td>
					<button type='button' class='button wpgmza-delete-custom-field'><i class='fa fa-trash-o' aria-hidden='true'></i></button>
				</td>
			</tr>
			<?php
		}
	}
	
	/**
	 * Echos the custom fields page
	 * @return void
	 */
	public function html()
	{
		?>
		
		<form id="wpgmza-custom-fields" 
			action="<?php echo admin_url('admin-post.php'); ?>" 
			method="POST">
			
			<input name="action" value="wpgmza_save_custom_fields" type="hidden"/>
			
			<h1>
				<?php
				_e('WP Google Maps - Custom Fields', 'wp-google-maps');
				?>
			</h1>
			
			<table class="wp-list-table widefat fixed striped pages">
				<thead>
					<tr>
						<th scope="col" id="id" class ="manage-column column-id">
							<?php
							_e('ID', 'wp-google-maps');
							?>
						</th>
						<th scope="col" id="id" class ="manage-column column-id">
							<?php
							_e('Name', 'wp-google-maps');
							?>
						</th>
						<th>
							<?php
							_e('Icon', 'wp-google-maps');
							?>
						</th>
						<th>
							<?php
							_e('Attributes', 'wp-google-maps');
							?>
						</th>
						<th>
							<?php
							_e('Filter Type', 'wp-google-maps');
							?>
						</th>
						<th>
							<?php
							_e('Actions', 'wp-google-maps');
							?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$this->tableBodyHTML();
					?>
					
					<tr id="wpgmza-new-custom-field">
						<td>
							<input 
								name="ids[]"
								value="-1"
								readonly
								/>
						</td>
						<td>
							<input
								required
								name="names[]"
								/>
						</td>
						<td>
							<input name="icons[]" class="wpgmza-fontawesome-iconpicker"/>
						</td>
						<td>
							<input name="attributes[]" type="hidden"/>
							<table class="attributes">
								<tbody>
									<tr>
										<td>
											<input
												placeholder="<?php _e('Name', 'wp-google-maps'); ?>"
												class="attribute-name"
												/>
										</td>
										<td>
											<input 
												placeholder="<?php _e('Value', 'wp-google-maps'); ?>"
												class="attribute-value"
												/>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td>
							<select name="widget_types[]">
								<option value="none">
									<?php
									_e('None', 'wp-google-maps');
									?>
								</option>
								<option value="text">
									<?php
									_e('Text', 'wp-google-maps');
									?>
								</option>
								<option value="dropdown">
									<?php
									_e('Dropdown', 'wp-google-maps');
									?>
								</option>
								<option value="checkboxes">
									<?php
									_e('Checkboxes', 'wp-google-maps');
									?>
								</option>
								<?php
								// Use this filter to add options to the dropdown
								echo apply_filters('wpgmza_custom_fields_widget_type_options', null);
								?>
							</select>
						</td>
						<td>
							<button type="submit" class="button button-primary wpgmza-add-custom-field">
								<?php
								_e('Add', 'wp-google-maps');
								?>
							</button>
						</td>
					</tr>
				</tbody>
			</table>
			
			<p style="text-align: center;">
				<input 
					type="submit" 
					class="button button-primary" 
					value="<?php _e('Save', 'wp-google-maps'); ?>"
					/>
			</p>
		</form>
		
		<?php
	}
}

// Bind post listener
add_action('admin_post_wpgmza_save_custom_fields', array('WPGMZA\\CustomFieldsPage', 'POST'));

// Display function for menu hook
function show_custom_fields_page()
{
	$page = new CustomFieldsPage();
	$page->html();
}

