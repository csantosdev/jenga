<?php
namespace jenga\db\models;

use jenga\db\models\reflection\Reflection;

use jenga\db\models\managers\MongoModelManager;
use jenga\db\models\managers\SQLModelManager;

use jenga\db\connections\Connection;
use jenga\db\connections\ConnectionTypeFactory;

use jenga\db\managers\BasicModelManager;
use jenga\db\fields as f;
use jenga\Helpers;

class Model {
	
	public $id = [f\PositiveIntField];
	public $_meta = [];

    public function __construct() {

        $reflection_model = Reflection::getModel(get_class($this));
        $fields = $reflection_model->getFields();

        foreach($fields as $field_name => $reflection_field)
            $this->$field_name = $reflection_field->getDefaultVale();
	}
	
	public static function objects() {
		return new SQLModelManager(get_called_class()); // FIND BETTER WAY TO DO THIS
	}
	

	/**
	 * Validate fields and then pass to the db object to save
	 */
	public function save() {
		$this->manager->save($this);
	}

    private function getManager() {
        if(!isset($this->manager)) {

        }
        return $this->manager;
    }


    public static function get($model_name) {

        if(!isset(self::$models[$model_name])) {
            $reflection = new \ReflectionClass($model_name);
            $properties = $reflection->getDefaultProperties();
            $reflection->fields = array();
            //$reflection->table_name = strtolower($model_name);

            if(isset($properties['_meta']))
                $reflection->_meta = $properties['_meta'];

            foreach($properties as $field_name => $field) {
                $field_class_name = self::get_field_type($field);

                if($field_class_name !== null)
                    $reflection->fields[$field_name] = $field;
            }

            // Setup the table name
            $class = strtolower($model_name);
            if(strpos($class, '\\models\\')) // class is within a namespace
                $class = str_replace(['\\models', '\\'], ['','_'], $class);
            $reflection->table_name = $class;

            self::$models[$model_name] = $reflection;
        }

        return self::$models[$model_name];
    }

    /* Condense this some how. Two copies are in use currently. */
    public static function get_table_name() {
        if(!empty($this->_meta['table_name']))
            return $this->_meta['table_name'];
        $class = strtolower(get_called_class());
        if(strpos($class, '\\models\\')) // class is within a namespace
            $class = str_replace(['\\models', '\\'], ['','_'], $class);
        return $class;
    }


}


class MongoModel extends Model {
	
	public $_id = array(f\TextField);
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
			//$reflection->table_name = strtolower($model_name);
			
			if(isset($properties['_meta']))
				$reflection->_meta = $properties['_meta'];
			
			foreach($properties as $field_name => $field) {
				$field_class_name = self::get_field_type($field);
					
				if($field_class_name !== null)
					$reflection->fields[$field_name] = $field;
			}
			
			// Setup the table name
			$class = strtolower($model_name);
			if(strpos($class, '\\models\\')) // class is within a namespace
				$class = str_replace(['\\models', '\\'], ['','_'], $class);
			$reflection->table_name = $class;
			
			self::$models[$model_name] = $reflection;
		}
			
		return self::$models[$model_name];
	}
	
	/* Condense this some how. Two copies are in use currently. */
	public static function get_table_name() {
		if(!empty($this->_meta['table_name']))
			return $this->_meta['table_name'];
		$class = strtolower(get_called_class());
		if(strpos($class, '\\models\\')) // class is within a namespace
			$class = str_replace(['\\models', '\\'], ['','_'], $class);
		return $class;
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
			$field_class_name = 'jenga\\db\\fields\\' . $field_class_name;
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