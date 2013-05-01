<?php
namespace Jenga;

class Helpers {
	
	public static function get_field_type($field) {
		if(is_string($field) && in_array($field, \Jenga::$MODEL_FIELD_LIST)) {
			return $field;
		} else if(is_array($field) && (array_key_exists(0, $field) || array_key_exists('type', $field))) {
			if(is_string($field[0]) && in_array($field[0], \Jenga::$MODEL_FIELD_LIST)) {
				return $field[0];
			} else if(isset($field['type']) && in_array($field['type'], \Jenga::$MODEL_FIELD_LIST)) {
				return $field['type'];
			}
		} else
			return null;
	}
	
	/**
	 * Instantiates the actual field class. Uses reflection to view fields and creates each field's class.
	 */
	public static function instantiate_skeleton_model($model_name) {
		$reflection = new \ReflectionClass($model_name);
		$properties = $reflection->getDefaultProperties();
		$model = new $model_name();
		$model->_meta['fields'] = array();
		
		foreach($properties as $field_name => $field) {
			$field_class_name = self::get_field_type($field);
			
			if($field_class_name !== null) {
				$field_class_name = 'Jenga\\DB\\Fields\\' . $field_class_name;
				$model->$field_name = new $field_class_name($field);
				$model->$field_name->name = $field_name;
				$model->_meta['fields'][$field_name] = $model->$field_name;	
			}
		}
		return $model;
	}
}