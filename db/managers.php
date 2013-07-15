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
	
	/**
	 * Validates the model with their respective types. Creates a simple object
	 * from the model and inserts or saves into Mongo.
	 * 
	 * @see Jenga\DB\Managers.ModelManager::save()
	 */
	public function save($model) {
		
		$this->validate($model); // Throws Exception
		$reflection_model = IntrospectionModel::get(get_class($model));
		$doc = $this->create_document($model);
		$db = ConnectionFactory::get($model->_meta['db_config']);
		$collection = $db->selectCollection($model->get_table_name());
		var_dump($doc);
		$collection->save($doc);
		echo 'DOC:';
		var_dump($doc);
		$model->_id = $doc['_id'];
	}
	
	/**
	 * Recursive function that build the simple object to insert/save in Mongo.
	 */
	private function create_document($model, $is_embedded=false) {
		
		$this->validate($model); // Throws Exception
		
		$reflection_model = IntrospectionModel::get(get_class($model));
		$doc = [];
		
		if($is_embedded)
			$doc['_class'] = $reflection_model->getName();
		
		foreach($reflection_model->fields as $field_name => $field) {
				
			if($field[0] == f\ForeignKey) {
				/*
				 * Move most of these checks to the validation section.
				 */
				$can_be_null = isset($field['null']) ? $field['null'] : false;
				$can_be_blank = isset($field['blank']) ? $field['blank'] : false;
				
				if(!$can_be_null) {
					if(!isset($model->$field_name))
						throw new \Exception ('ForeignKey object constraint: "$' . $field_name . '" cannot be null.');
					else if(!isset($model->$field_name->_id))
						throw new \Exception('ForeignKey object ' . $field_name . ' does not have an ID (_id). Must be saved first.');
					
					$value = $model->$field_name->_id;
					
				} else {
					if(!isset($model->$field_name) || !isset($model->$field_name->_id))
						$value = null;
					else
						$value = $model->$field_name->_id;
				}
					
				$fk_id_field_name = $field_name . '_id';
				$field_name = $fk_id_field_name;
					
			} else if($field_name == 'id')
				continue;
				
			else if($field_name == '_id') {
				if($model->$field_name == null) {
					if($is_embedded)
						$value = $model->$field_name = new \MongoId();
					else
						continue;
					
				} else
					$value = $model->$field_name;
			}
				
			else if($field[0] == f\BooleanField) {
		
				if(isset($field['default']))
					$value = $field['default'];
				else
					$value = null;
		
			} else if($field[0] == f\EmbeddedDocumentField) {
		
				$type = f\ObjectType;
				$models = [];
				
				if(isset($field['model']))
					$models[] = $field['model'] ;
				else if(isset($field['models']))
					$models = $field['models'];
				
				if(isset($field['type']) && $field['type'] == f\ArrayType)
					$type = f\ArrayType;
				
				switch($type) {
					
					case f\ObjectType:
						$value = $this->create_document($embedded_model);
						break;
						
					case f\ArrayType:
						if(!is_array($model->$field_name))
							throw new \Exception($field_name . ' must be an Array.');
						
						$value = [];
						foreach($model->$field_name as $embedded_model) {
							if(!empty($models))
								$value[] = $this->create_document($embedded_model, true);
							else
								$value[] = $embedded_model; // Non-model i.e: int, string, boolean.
						}
						
						break;
						
					default:
						throw new \Exception('Unknown EmbeddedDocumentField type: ' . $type);
				}
				
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
		
		return $doc;
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