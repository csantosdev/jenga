<?php
namespace Jenga\DB\Query;
use Jenga\DB\Models\IntrospectionModel;
use Jenga\DB\Fields as f;

class Query {
	
	const _IN = '_in';
	const _GT = '_gt';
	const _GTE = '_gte';
	const _LT = '_lt';
	const _LTE = '_lte';
	const _NE = '_ne';
	
	const IN = ' in';
	const LT = ' <';
	const LTE = ' <=';
	const GT = ' >';
	const GTE = ' >=';
	const NE = ' !=';
	
	const DOT_OPERATOR = '.'; // django uses __
	
	public $related_models = array();
	public $wheres = array();
	
	public function parse($main_model, $conditions) {
		
		foreach($conditions as $condition => $value) {
				
			$pieces = explode(self::DOT_OPERATOR, $condition);
			$conditional_operator = null; // default is equals
			$current_model = $main_model;
			$current_where = null;
			
			if(strstr($condition, ' ') !== false) {
				if($operator = $this->find_operator($condition, array(self::LT, self::LTE, self::GT, self::GTE, self::NE)))
					$conditional_operator = $operator;
				
			} else if(strrpos($condition, '_') !== false) {
				if($operator = $this->find_operator($condition, array(self::_LT, self::_LTE, self::_GT, self::_GTE, self::_NE)))
					$conditional_operator = $operator;
			}
				
			foreach($pieces as $field_name) {
				
				if(!isset($current_model->fields[$field_name]))
					throw new \Exception('model ' . $current_model->getName(). ' has no property ' . $field_name);
		
				$field = $current_model->fields[$field_name]; // ex: array('ForeignKey', 'model' => 'Post')
				$field_class = IntrospectionModel::get($field[0]);
		
				// Related Field? Setup the join
				if($field_class->isSubclassOf(f\RelatedField)) {
						
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
					
					$where_statement = $this->get_where($model);
					
					if($where_statement == null)
						$where_statement = new Where($model);
					
					if($current_where != null)
						$current_where->dependencies[] = $where_statement;
					else {
						/** For now to prevent duplicate $models, we store by key **/
						$key = $where_statement->model->getName();
						if(!array_key_exists($key, $this->wheres))
							$this->wheres[$key] = $where_statement;
					}

					$current_model = $model;
					$current_where = $where_statement;
		
				} else if($field_class->getName() == f\CharField || $field_class->isSubclassOf(f\CharField)) {
				
					/** SQL SHIT
					$this->wheres[] = array(
						'model_name' => $current_model->getName(),
						'field_name' => $field_name,
						'conditional_operator' => $conditional_operator,
						'value' => $value
					);
					*/
					$current_where->wheres[$field_name] = $value;
						
				} else {
					echo "NOTHING";
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
	
	private function get_where($model) {
		foreach($this->wheres as $where) {
			if($where->model == $model)
				return $where;
		}
		return null;
	}
}

class Where {
	public $model = null;
	public $dependencies = array();
	public $wheres = array();
	
	public function __construct($model) {
		$this->model = $model;
	}
}