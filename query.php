<?php
class QuerySet {
	
	private $model;
	private $models = array();
	private $reflection_models = array();
	private $conditions = array();
	
	private $field_list = array('ForeignKey', 'OneToMany', 'ManyToMany');
	
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
		
		require_once 'models.php';
		
		$models = array();
		$current_model = new ReflectionClass($this->model);
		$current_model_properties = $current_model->getDefaultProperties();
		
		foreach($this->conditions as $condition => $value) {
			
			$pieces = explode('__', $condition);
			
			foreach($pieces as $piece) {
				
				/**
				if(!isset($this->reflection_models[$current_property])){
					$model = new ReflectionClass($current_property);
				}*/
				
				// Check if it's a property
				if(!isset($current_model_properties[$piece]))
					throw new Exception($current_model->getName(). ' has no ' . $piece . 'property');
				
				$property = $current_model_properties[$piece];
				
				if(!is_string($property) || !is_array($property))
					throw new Exception($property . ' must be a string or an array');

				if(is_string($property))
					$field = $property;
				else if(isset($property['field']))
					$field = $property['field'];
				else
					$field = $property[0];
				
				switch($field) {
					case 'ForeignKey':
						$models[] = $property['model'];
						break;
				}
					
				
				
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