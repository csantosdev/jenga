<?php
class QuerySet {
	
	private $model;
	private $models = array();
	private $conditions = array();
	
	public function __construct($model, $conditions) {
		$this->model = $model;
		$this->conditions = $conditions;
	}
	
	public function filter($conditions) {
		$this->conditions = array_merge($this->conditions, $condition);
	}
	
	public function query() {
		return $this->build_query();
	}
	
	private function build_query() {
		
		$models = array();
		$current_property = $this->model;
		
		foreach($this->conditions as $condition => $value) {
			
			$pieces = explode('__', $condition);
			require_once 'models.php';
			
			foreach($pieces as $piece) {
				
				// Check if it's a property
				if(!property_exists($current_property, $piece))
					throw new Exception($current_property . ' has no ' . $piece . 'property');
				
				$model = $this->get_model($piece);
				
				if(!isset($models[$class_name])) {
					if(!class_exists($class_name))
						throw new Exception("Model class " . $class_name . " does not exist.");
					
					$models[$class_name] = new $class_name();
					//TODO: research if reflection is faster than instaniation
				} else {
					echo "Could not find: " . $class_name;
				}
			}
		}
	}
	
	private function get_model($model) {
		if(!isset($this->models[$model]))
			$this->models[$model] = new $model();	
		return $this->models[$model];
	}
}