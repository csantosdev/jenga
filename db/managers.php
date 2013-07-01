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
	
	public function __construct($model_name) {
		$this->model_name = $model_name;
	}
	
	public function filter($conditions) {
		return new QuerySet($this->model_name, $conditions);
	}
	
	public function get($conditions) {
		$qs = new QuerySet($this->model_name, $conditions);
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
				//if(!isset($model->$fk_id_field_name))
					//$model->
			}
			$doc[$field_name] = $model->$field_name;
		}
		
		$collection->save($doc);
		echo "saved";
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