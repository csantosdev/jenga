<?php
namespace Jenga\DB\Query;
use Jenga\DB\Models\IntrospectionModel;
use Jenga\DB\Fields as f;

class Query {
	
	const _IN = 	'__in';
	const _GT = 	'__gt';
	const _GTE = 	'__gte';
	const _LT = 	'__lt';
	const _LTE = 	'__lte';
	const _NE = 	'__ne';
	
	const IN = 		' in';
	const LT = 		' <';
	const LTE = 	' <=';
	const GT = 		' >';
	const GTE = 	' >=';
	const NE = 		' !=';
	
	const DOT_OPERATOR = '.'; // django uses __
	
	public $related_models = array();
	
	/**
	 * The main reflection model for the query. Not always needed.
	 * @var ReflectionClass
	 */
	public $model;
	
	/**
	 * The WHERE criteria for the this object's $model.
	 * @var Array
	 */
	public $wheres = [];
	
	/**
	 * Tables/Collections to JOIN onto the object's $model.
	 * @var Array - Array of Query objects.
	 */
	public $dependencies = [];
	
	/**
	 * Name of the field to do the JOIN on from the object's $model.
	 * @var string
	 */
	public $field;
	
	public function __construct($model=null, $field=null) {
		$this->model = $model;
		$this->field = $field;
	}
	
	public function parse($conditions) {
		
		if(!isset($this->model))
			throw new \Exception('Must have a $model reflection class set before parsing.');
		
		foreach($conditions as $condition => $value) {
				
			$conditional_operator = null; // default is equals
			$current_model = $this->model;
			$current_query = $this;
			
			if(strstr($condition, ' ') !== false)
				$conditional_operator = $this->find_operator($condition, array(self::IN, self::LT, self::LTE, self::GT, self::GTE, self::NE));
			
			else if(strrpos($condition, '__') !== false)
				$conditional_operator = $this->find_operator($condition, array(self::_IN, self::_LT, self::_LTE, self::_GT, self::_GTE, self::_NE));
			
			else
				$conditional_operator = null;
			
			if($conditional_operator !== null)
				$condition = str_replace($conditional_operator, '', $condition);
				
			$pieces = explode(self::DOT_OPERATOR, $condition);
			
			foreach($pieces as $field_name) {
				
				if(!isset($current_model->fields[$field_name]))
					throw new \Exception('model ' . $current_model->getName(). ' has no property ' . $field_name);
		
				$field = $current_model->fields[$field_name]; // ex: array('ForeignKey', 'model' => 'Post')
				$field_class = IntrospectionModel::get($field[0]);
				$field_class_name = $field_class->getName();
		
				echo 'Piece: ' . $field_name . ' | ' . $field_class_name;
				// Related Field? Setup the join
				if($field_class_name == f\ForeignKey) {
						
					$model = IntrospectionModel::get($field['model']);
						
					/** SQL SHIT 
					if(!isset($this->related_models[$model->getName()])) {
						$this->related_models[$model->getName()] = array(
							'model' => $model,
							'join_model' => $current_model,
							'on_column' => $field_name.'_id'
						);
					}
					*/
					
					$dependency = $this->get_dependency($model);
					
					if($dependency == null)
						$dependency = new Query($model, $field_name);
					
					$key = $dependency->model->getName();
					
					if(!isset($current_query->dependencies[$key]))
						$current_query->dependencies[$key] = $dependency;

					$current_model = $model;
					$current_query = $dependency;
		
				} else if($field_class_name == f\ManyToMany) {
					
					// NOT IMPLEMENTED
					exit("ManyToMany querying is not yet implemented!");
					
					if($conditional_operator == null) // MIGHT NOT NEED THIS??
						$current_query->wheres[$field_name] = $value;
					else
						$current_query->wheres[$field_name] = [$conditional_operator => $value];
					
				} else if($field_class->getName() == f\CharField || $field_class->isSubclassOf(f\CharField)) {
				
					/** SQL SHIT
					$this->wheres[] = array(
						'model_name' => $current_model->getName(),
						'field_name' => $field_name,
						'conditional_operator' => $conditional_operator,
						'value' => $value
					);
					*/
					
					if($conditional_operator == null)
						$current_query->wheres[$field_name] = $value;
					else
						$current_query->wheres[$field_name] = [$conditional_operator => $value];
					
				} else if($field_class->getName() == f\BooleanField) {
					$current_query->wheres[$field_name] = $value;
					
				} else {
					echo "FIELD TYPE NOTHING";
					continue;
				}
			}
		}
	}
	
	private function find_operator($condition, $conditional_operators) {
		foreach($conditional_operators as $operator)
			if(strstr($condition, $operator) !== false)
				return $operator;
		return null;
	}
	
	private function get_dependency($reflection_model) {
		foreach($this->dependencies as $dependency)
			if($dependency->model == $reflection_model)
				return $dependency;
		return null;
	}
}