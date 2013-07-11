<?php
namespace Jenga\DB\Managers;
use Jenga\DB\Models\IntrospectionModel;
use Jenga\DB\Connections\ConnectionFactory;
use Jenga\DB\Query\QuerySet;
use Jenga\DB\Fields as f;

class BasicModelManager {
	
	private $model; // string
	
	public function __construct($model) {
		$this->model = $model;
	}
	
	public function filter($conditions) {
		return new QuerySet($this->model, $conditions);
	}
	
	public function get($conditions) {
		$qs = new QuerySet($this->model, $conditions);
		return $qs[0];
	}
}

abstract class ModelManager {
	
	private $model;
	private $model_name;
	private $pre_conditions;
	
	public function __construct($model_name, $pre_conditions=null) {
		$this->model_name = $model_name;
		$this->pre_conditions = $pre_conditions;
	}
	
	public function filter($conditions) {
		if($this->pre_conditions != null)
			$conditions = array_merge($this->pre_conditions, $conditions);
		
		return new QuerySet($this->model_name, $conditions);
	}
	
	/**
	 * Throws Exception if query returns 0 rows
	 * @param Array $conditions
	 */
	public function get($conditions) {
		$qs = $this->filter($conditions);
		return $qs[0];
	}
	
	public function set_model($model) {
		$this->model = $model;
	}
	
	abstract function save($model);
	
	protected function validate($model) {
		
		$class_name = get_class($model);
		$reflection_model = IntrospectionModel::get($class_name);
		
		foreach($reflection_model->fields as $field_name => $field) {
			$reflection_field = new \ReflectionClass($field[0]); //TODO: DO THIS A BETTER WAY
			
			if(!$reflection_field->isSubclassOf(f\Field))
				continue;
			
			// NumberFields
			if($reflection_field->isSubclassOf(f\NumberField)) {
				$value = $model->$field_name;
				
				if($reflection_field->getName() == f\IntField) {
					if(!is_int($value))
						throw new Exception($model->$field_name . ' is not of type int');
				} else if($reflection_field->getName() == f\FloatField) {
					if(!is_float($value))
						throw new Exception($value . ' is not of type float');
				}
			} 
		}
	}
}

class MongoModelManager extends ModelManager {
	
	public function save($model) {
		
		$db = ConnectionFactory::get($model->_meta['db_config']);
		$collection = $db->selectCollection($model->get_table_name());
		$this->validate($model); // Throws Exception
		
		$reflection_model = IntrospectionModel::get(get_class($model));
		$doc = [];
		
		foreach($reflection_model->fields as $field_name => $field) {
			
			if($field[0] == f\ForeignKey) {
				$fk_id_field_name = $field_name . '_id';
				
				if(!isset($model->$fk_id_field_name)) {
					$field_name = $fk_id_field_name;
					$value = null;
				}
					
			} else if($field_name == 'id' || $field_name == '_id') {
				if($model->$field_name == null)
					continue;				
			}
			
			else if($field[0] == f\BooleanField) {
				
				if(isset($field['default']))
					$value = $field['default'];
				else
					$value = null;
				
			} else if($field[0] == f\EmbeddedDocumentField) {
				
				if(isset($field['model'])) {
					
					
				} else if(isset($field['models'])) {
					
					
				// Default to treating as Array
				} else {
					$value = $model->$field_name;
				}
				
			} else
				$value = $model->$field_name;
			
			$doc[$field_name] = $value;
		}
		
		$collection->save($doc);
	}
}

class SQLModelManager extends ModelManager {
	
	public function save($model) {
		
		$new = false;
		
		if($model->id == null) {
			$new = true;
			$model->id = 1; // To pass validation. Does not use this value, but creates one an ID.
		}
		
		foreach($model->fields as $field_name => $field) {
				
			$field_class = $field[0];
			$field_class::validate($model->$field_name); // Throws Exceptions
				
		}
			
		$db = \Jenga::get_db();
		
		if($new) {
		}
	}
}