<?php
namespace Jenga\DB\Models;
use Jenga\DB\Managers\BasicModelManager;
use Jenga\DB\Fields as f;
use Jenga\Helpers;

const SQL_BACKEND_TYPE = 'sql';
const MONGO_BACKEND_TYPE = 'mongo';

class Model
{
	public $id = array(f\PositiveIntField);
	public $backend_type = SQL_BACKEND_TYPE;
	
	public $_meta = array();
	
	private $child_class;
	private $fields = array();
	
	protected static $objects;
	private static $reflection_classes = array();
	
	/**
	 * Set any Jenga fields to null.
	 */
	public function __construct() {
		
		$class_name = get_class($this);
		if(isset(self::$reflection_classes[$class_name]))
			$reflection = self::$reflection_classes[$class_name];
		else {
			$reflection = new \ReflectionClass($this);
			self::$reflection_classes[$class_name] = $reflection;
		}
		
		$properties = $reflection->getDefaultProperties();
		
		foreach($properties as $field_name => $field) {
			$field_class_name = Helpers::get_field_type($field);
				
			if($field_class_name !== null) {
				$this->$field_name = null;
				$this->fields[$field_name] = $field;
				$this->_meta['fields'][$field_name] = &$this->$field_name;
			}
		}
		
		$this->child_class = $class_name;
	}
	
	public static function objects() {
		
		if(!isset(self::$objects))
			self::$objects = new BasicModelManager(get_called_class());
		return self::$objects;
	}
	
	public function get_table_name() {
		if(!empty($this->_meta['table_name']))
			return $this->_meta['table_name'];
		return strtolower(get_called_class());
	}
	
	/**
	 * Validate fields and then pass to the db object to save
	 */
	public function save() {
		
		$new = false;
		if($this->id == null) {
			$new = true;
			$this->id = 1; // To pass validation
		}
		
		foreach($this->fields as $field_name => $field) {
			
			$field_class = $field[0];
			$field_class::validate($this->$field_name); // Throws Exceptions
			
		}	
			
		$db = \Jenga::get_db();
		
		if($new) {
		}
	}
}


class IntrospectionModel {
	
	private static $models = array();
	private static $fields = array();
	
	public static function get($model_name) {
		if(!isset(self::$models[$model_name])) {
			$reflection = new \ReflectionClass($model_name);
			$properties = $reflection->getDefaultProperties();
			$reflection->fields = array();
			$reflection->table_name = strtolower($model_name);
		
			foreach($properties as $field_name => $field) {
				$field_class_name = self::get_field_type($field);
					
				if($field_class_name !== null)
					$reflection->fields[$field_name] = $field;
				
			self::$models[$model_name] = $reflection;
			}
		}
			
		return self::$models[$model_name];
	}
	
	
	/**
	 *
	 * Instantiates a field on a model class. Ex: (IntField, ForeignKey, CharField)
	 * @param string $field_name
	 * @param string $field_value
	 */
	private static function instantiate_model_field($field_name, $field_value) {
		$field_class_name = self::get_field_type($field_value);
			
		if($field_class_name !== null) {
			$field_class_name = 'Jenga\\DB\\Fields\\' . $field_class_name;
			$model->$field_name = new $field_class_name($field);
			$model->$field_name->name = $field_name;
			$model->_meta['fields'][$field_name] = $model->$field_name;
		}
	}
	
	
	/**
	 * Checks to see if the Field array on a model is valid
	 *
	 * @param string|array $field
	 */
	private static function get_field_type($field) {
	
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
			
			if(!isset(self::$fields[$class_name]))
				self::$fields[$class_name] = new \ReflectionClass($class_name);

			$reflection = self::$fields[$class_name];
						
			if($reflection->isSubclassOf(f\Field))
				return $class_name;
			else
				return null;
				
		} catch(\Exception $e) {
			return null;
		}
	}
}