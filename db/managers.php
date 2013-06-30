<?php
namespace Jenga\DB\Managers;
use Jenga\DB\Connections\ConnectionFactory;

use Jenga\DB\Query\QuerySet;

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
}

class MongoModelManager extends ModelManager {
	
	public function save($model) {
		
		$db = ConnectionFactory::get($model->_meta['db_config']);
		echo "<br/>[Got Connection]";
		var_dump($db);
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