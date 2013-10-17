<?php
namespace Jenga;
use jenga\db\fields as f;
use jenga\db\Models as models;

class Helpers {
	
	private static $reflection_classes = array();
	
	public static function get_model_reflection($model_name) {
		
		if(!isset(self::$reflection_classes[$model_name]))
			self::$reflection_classes[$model_name] = new \ReflectionClass($model_name);
		return self::$reflection_classes[$model_name];
	}
	
	public static function get_model_table_name($model_name) {
		$reflection = self::get_model_reflection($model_name);
		$properties = $reflection->getDefaultProperties();
		
		if(isset($properties['_meta']['table_name']))
			return $properties['_meta']['table_name'];
		return strtolower($reflection->getName());
	}
	
	/**
	 * Checks to see if the Field array on a model is valid
	 * 
	 * @param string|array $field
	 */
	public static function get_field_type($field) {
		
		$class_name = null;
		
		if(is_string($field)) {
			$class_name = $field;
		} else if(is_array($field) && (array_key_exists(0, $field) || array_key_exists('type', $field))) {
			if(is_string($field[0])) {
				$class_name = $field[0];
			} else if(isset($field['type'])) {
				$class_name = $field['type'];
			}
		} else
			return null;
		
		try {
			$reflection = self::get_model_reflection($class_name);
			
			if($reflection->isSubclassOf(f\Field))
				return $class_name;
			else
				return null;
			
		} catch(Exception $e) {
			return null;
		}
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
				$field_class_name = 'jenga\\db\\fields\\' . $field_class_name;
				$model->$field_name = new $field_class_name($field);
				$model->$field_name->name = $field_name;
				$model->_meta['fields'][$field_name] = $model->$field_name;
			}
		}
		return $model;
	}
	
	/**
	 * 
	 * Instantiates a field on a model class. Ex: (IntField, ForeignKey, CharField)
	 * @param string $field_name
	 * @param string $field_value
	 */
	public static function instantiate_model_field($field_name, $field_value) {
		$field_class_name = self::get_field_type($field_value);
			
		if($field_class_name !== null) {
			$field_class_name = 'jenga\\db\\fields\\' . $field_class_name;
			$model->$field_name = new $field_class_name($field);
			$model->$field_name->name = $field_name;
			$model->_meta['fields'][$field_name] = $model->$field_name;
		}
	}
}