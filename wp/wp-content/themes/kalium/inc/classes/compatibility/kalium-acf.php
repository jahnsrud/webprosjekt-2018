<?php
/**
 *	Kalium ACF fallback for "get_field"
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class Kalium_ACF {
	
	/**
	 * ACF plugin is active
	 */
	public $acf_installed = false;
	
	/**
	 * Construct
	 */
	public function __construct() {
		$this->acf_installed = kalium()->helpers->isPluginActive( 'advanced-custom-fields/acf.php' );
		
		// Add filters
		if ( ! $this->acf_installed ) {
			add_filter( 'acf/load_field', array( $this, 'api_acf_load_field' ), 1, 2 );
			add_filter( 'acf/load_field', array( $this, 'load_field' ), 5, 3 );
			add_filter( 'acf/load_value', array( $this, 'load_value' ), 5, 3 );
			add_filter( 'acf/get_post_id', array( $this, 'get_post_id' ), 1, 1 );
		}
	}
	
	/**
	 * Get field with fallback function
	 */
	public function get_field( $field_key, $post_id = false, $format_value = true ) {
		
		if ( $this->acf_installed ) {
			return get_field( $field_key, $post_id, $format_value );
		}
		
		// Use ACF functions added on this class
		return $this->fallback_get_field( $field_key, $post_id, $format_value );
	}
	
	/**
	 * ACF Functions
	 */
	public function get_field_reference( $field_name, $post_id ) {
		
		// cache
		$found = false;
		$cache = wp_cache_get( 'field_reference/post_id=' .  $post_id . '/name=' .  $field_name, 'acf', false, $found );
	
		if ( $found ) {
			return $cache;
		}
		
		// vars
		$return = '';
		
		// get field key
		if ( is_numeric( $post_id ) ) {
			$return = get_post_meta( $post_id, '_' . $field_name, true );
		} elseif ( strpos( $post_id, 'user_' ) !== false ) {
			$temp_post_id = str_replace('user_', '', $post_id);
			$return = get_user_meta( $temp_post_id, '_' . $field_name, true );
		} else {
			$return = get_option('_' . $post_id . '_' . $field_name); 
		}
		
		
		// set cache
		wp_cache_set( 'field_reference/post_id=' .  $post_id . '/name=' .  $field_name, $return, 'acf' );
			
		
		// return	
		return $return;
	}
	 
	public function fallback_get_field( $field_key, $post_id = false, $format_value = true ) {
		
		// vars
		$return = false;
		$options = array(
			'load_value' => true,
			'format_value' => $format_value
		);
	
		$field = $this->get_field_object( $field_key, $post_id, $options );
		
		if ( is_array( $field ) ) {
			$return = $field['value'];
		}
		
		
		return $return;
		 
	}
	
	public function get_field_object( $field_key, $post_id = false, $options = array() ) {
			
		// filter post_id
		$post_id = apply_filters( 'acf/get_post_id', $post_id );
		$field = false;
		$orig_field_key = $field_key;
		
		
		// defaults for options
		$defaults = array(
			'load_value'	=>	true,
			'format_value'	=>	true,
		);
		
		$options = array_merge( $defaults, $options );
		
		
		// is $field_name a name? pre 3.4.0
		if ( substr( $field_key, 0, 6 ) !== 'field_' ) {
			// get field key
			$field_key = $this->get_field_reference( $field_key, $post_id );
		}
		
		
		// get field
		if ( substr( $field_key, 0, 6 ) === 'field_' ) {
			$field = apply_filters( 'acf/load_field', false, $field_key );
		}
		
		
		// validate field
		if ( ! $field ) {
			// treat as text field
			$field = array(
				'type' => 'text',
				'name' => $orig_field_key,
				'key' => 'field_' . $orig_field_key,
			);
			$field = apply_filters( 'acf/load_field', $field, $field['key'] );
		}
	
	
		// load value
		if ( $options['load_value'] ) {
			$field['value'] = apply_filters( 'acf/load_value', false, $post_id, $field );
			
			
			// format value
			if ( $options['format_value'] ) {
				$field['value'] = apply_filters( 'acf/format_value_for_api', $field['value'], $post_id, $field );
			}
		}
	
	
		return $field;
	
	}
	
	/**
	 * ACF Filters
	 */
	public function api_acf_load_field( $field, $field_key ) {
		// validate
		if ( ! empty( $GLOBALS['acf_register_field_group'] ) ) {
			foreach ( $GLOBALS['acf_register_field_group'] as $acf ) {
				if ( ! empty( $acf['fields'] ) ) {
					foreach ( $acf['fields'] as $f ) {
						if ( $f['key'] == $field_key ) {
							$field = $f;
							break;
						}
					}
				}
			}
		}
	
		return $field;
	}
	
	public function get_post_id( $post_id ) {
		
		// if not $post_id, load queried object
		if ( ! $post_id ) {
			
			// try for global post (needed for setup_postdata)
			$post_id = (int) get_the_ID();
			
			
			// try for current screen
			if ( ! $post_id ) {
				
				$post_id = get_queried_object();
					
			}
			
		}
		
		
		// $post_id may be an object
		if ( is_object( $post_id ) ) {
			
			// user
			if ( isset( $post_id->roles, $post_id->ID ) ) {
			
				$post_id = 'user_' . $post_id->ID;
			
			// term
			} elseif ( isset( $post_id->taxonomy, $post_id->term_id ) ) {
			
				$post_id = $post_id->taxonomy . '_' . $post_id->term_id;
			
			// comment
			} elseif ( isset( $post_id->comment_ID ) ) {
			
				$post_id = 'comment_' . $post_id->comment_ID;
			
			// post
			} elseif ( isset( $post_id->ID ) ) {
			
				$post_id = $post_id->ID;
			
			// default
			} else {
				
				$post_id = 0;
				
			}
			
		}
		
		
		// allow for option == options
		if ( $post_id === 'option' ) {
		
			$post_id = 'options';
			
		}
		
		
		/*
		*  Override for preview
		*  
		*  If the $_GET['preview_id'] is set, then the user wants to see the preview data.
		*  There is also the case of previewing a page with post_id = 1, but using get_field
		*  to load data from another post_id.
		*  In this case, we need to make sure that the autosave revision is actually related
		*  to the $post_id variable. If they match, then the autosave data will be used, otherwise, 
		*  the user wants to load data from a completely different post_id
		*/
		
		if ( isset( $_GET['preview_id'] ) ) {
		
			$autosave = wp_get_post_autosave( $_GET['preview_id'] );
			
			if ( $autosave && $autosave->post_parent == $post_id ) {
			
				$post_id = (int) $autosave->ID;

			}
		}
		
		// return
		return $post_id;
	}
	
	public function load_field( $field, $field_key, $post_id = false ) {
		// load cache
		if ( ! $field ) {
			$field = wp_cache_get( 'load_field/key=' . $field_key, 'acf' );
		}
		
		
		// load from DB
		if ( ! $field ) {
			// vars
			global $wpdb;
			
			
			// get field from postmeta
			$sql = $wpdb->prepare("SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = %s", $field_key);
			
			if ( $post_id ) {
				$sql .= $wpdb->prepare("AND post_id = %d", $post_id);
			}
	
			$rows = $wpdb->get_results( $sql, ARRAY_A );
			
			
			
			// nothing found?
			if ( ! empty( $rows ) ) {
				$row = $rows[0];
				
				
				/*
				*  WPML compatibility
				*
				*  If WPML is active, and the $post_id (Field group ID) was not defined,
				*  it is assumed that the load_field functio has been called from the API (front end).
				*  In this case, the field group ID is never known and we can check for the correct translated field group
				*/
				
				if ( defined( 'ICL_LANGUAGE_CODE' ) && ! $post_id ) {
					$wpml_post_id = icl_object_id( $row['post_id'], 'acf', true, ICL_LANGUAGE_CODE );
					
					foreach ( $rows as $r ) {
						if ( $r['post_id'] == $wpml_post_id ) {
							// this row is a field from the translated field group
							$row = $r;
						}
					}
				}
				
				
				// return field if it is not in a trashed field group
				if ( get_post_status( $row['post_id'] ) != "trash" ) {
					$field = $row['meta_value'];
					$field = maybe_unserialize( $field );
					$field = maybe_unserialize( $field ); // run again for WPML
					
					
					// add field_group ID
					$field['field_group'] = $row['post_id'];
				}
				
			}
		}
		
		
		// apply filters
		$field = apply_filters( 'acf/load_field_defaults', $field );
		
		
		// apply filters
		foreach ( array('type', 'name', 'key') as $key ) {
			// run filters
			$field = apply_filters( 'acf/load_field/' . $key . '=' . $field[ $key ], $field ); // new filter
		}
		
	
		// set cache
		wp_cache_set( 'load_field/key=' . $field_key, $field, 'acf' );
		
		return $field;
	}
	
	public function load_value( $value, $post_id, $field ) {
		$found = false;
		$cache = wp_cache_get( 'load_value/post_id=' . $post_id . '/name=' . $field['name'], 'acf', false, $found );
		
		if ( $found ) {
			return $cache;
		}
		
		
		// set default value
		$value = false;
		
		
		// if $post_id is a string, then it is used in the everything fields and can be found in the options table
		if ( is_numeric( $post_id ) ) {
			$v = get_post_meta( $post_id, $field['name'], false );
			
			// value is an array
			if ( isset( $v[0] ) ) {
			 	$value = $v[0];
		 	}

		} elseif ( strpos( $post_id, 'user_') !== false ) {
			$user_id = str_replace('user_', '', $post_id);
			
			$v = get_user_meta( $user_id, $field['name'], false );
			
			// value is an array
			if ( isset( $v[0] ) ) {
			 	$value = $v[0];
		 	}
		 	
		} else {
			$v = get_option( $post_id . '_' . $field['name'], false );
			
			if ( ! is_null( $value ) ) {
				$value = $v;
		 	}
		}
		
		
		// no value?
		if ( $value === false ) {
			if ( isset( $field['default_value'] ) && $field['default_value'] !== "" ) {
				$value = $field['default_value'];
			}
		}
		
		
		// if value was duplicated, it may now be a serialized string!
		$value = maybe_unserialize( $value );
		
		
		// apply filters
		foreach ( array('type', 'name', 'key') as $key ) {
			// run filters
			$value = apply_filters( 'acf/load_value/' . $key . '=' . $field[ $key ], $value, $post_id, $field ); // new filter
		}
		
		
		//update cache
		wp_cache_set( 'load_value/post_id=' . $post_id . '/name=' . $field['name'], $value, 'acf' );

		
		return $value;
	}
}