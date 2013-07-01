<?php
namespace Jenga\DB\Models;
use Jenga\DB\Managers\MongoModelManager;

use Jenga\DB\Connections\Connection;
use Jenga\DB\Connections\ConnectionTypeFactory;

use Jenga\DB\Managers\SQLModelManager;

use Jenga\DB\Managers\BasicModelManager;
use Jenga\DB\Fields as f;
use Jenga\Helpers;

class Model {
	
	public $id = array(f\PositiveIntField);
	public $_meta = array(
		'db_config' => 'default');
	
	protected $manager;
	private $backend_config;
	
	/**
	 * Set any Jenga fields to null.
	 */
	public function __construct() {
		
		$class_name = get_class($this);
		$reflection = IntrospectionModel::get($class_name);

		foreach($reflection->fields as $field_name => $field) {
			
			// Eventually have this automatically set when you create a reflection class
			$field_config = $field;
			$field = new \ReflectionClass($field[0]);
			
			if($field->getName() == f\CharField || $field->isSubclassOf(f\CharField)) {
				if(isset($field_config['default']))
					$this->$field_name = $field_config['default'];
				else
					$this->$field_name = null;
			}
					
			else if($field->getName() == f\NumberField || $field->isSubclassOf(f\NumberField))
				$this->$field_name = null;
			
		}
		
		$backend_config = ConnectionTypeFactory::get($this->_meta['db_config']);
		$this->backend_config = &$backend_config;
		$this->manager = new SQLModelManager($class_name);
	}
	
	public static function objects() {
		return new SQLModelManager(get_called_class()); // FIND BETTER WAY TO DO THIS
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
		$this->manager->save($this);
	}
}


class MongoModel extends Model {
	
	public $_id = null;
	public $id = array(f\TextField);
	
	public function __construct() {
		parent::__construct();
		$this->_id = &$this->id;
		$this->manager = new MongoModelManager(get_class($this));
	}

	public static function objects() {
		return new MongoModelManager(get_called_class());
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
			
			if(isset($properties['_meta']))
				$reflection->_meta = $properties['_meta'];
			
			foreach($properties as $field_name => $field) {
				$field_class_name = self::get_field_type($field);
					
				if($field_class_name !== null)
					$reflection->fields[$field_name] = $field;
			}
			
			self::$models[$model_name] = $reflection;
		}
			
		return self::$models[$model_name];
	}
	
	public static function instantiate($model_name) {
		$reflection = self::get($model_name);
		$model = $reflection->newInstance(true);
		foreach($reflection->fields as $col_name => $field) {
			$field = new \ReflectionClass($field[0]);
			
			echo "<br/>Field: " . $col_name;
			
			if($field->getNamespaceName() == f\CharField || $field->isSubclassOf(f\CharField)) {
				echo 'setting ' . $col_name;
				$model->$col_name = null;
				var_dump($model->$col_name);
					
			} else if($field->getNamespaceName() == f\NumberField || $field->isSubclassOf(f\NumberField)) {
				$model->$col_name = null;
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

class ModelManagerFactory {
	
	public static function get($backend_config, $model_name) {
		switch($backend_config['type']) {
			case Connection::SQL_BACKEND_TYPE:
				return new SQLModelManager($model_name);
			case Connection::MONGO_BACKEND_TYPE:
				return new MongoModelManager($model_name);
			default:
				throw new \Exception('Could not find a model manager for: ' . $backend_config['type']);
		}
	}
}