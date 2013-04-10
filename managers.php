<?php
require_once 'query.php';

class BasicModelManager {
	
	private $model; // string
	
	public function __construct($model) {
		$this->model = $model;
	}
	
	public function filter($conditions) {
		var_dump($conditions);
		var_dump($this->model);
		var_dump(new $this->model());
		
		return new QuerySet($this->model, $conditions);
	}
	
}