<?php
namespace Jenga\DB\Query;
use Jenga\Helpers;
use Jenga\DB\Fields as fields;

class QuerySet implements \Countable, \Iterator, \ArrayAccess {
	
	private $model;
	private $model_properties = array();
	
	private $models = array();
	private $reflection_models = array();
	private $conditions = array();
	private $objects = null;
	private $position = 0;
	
	public function __construct($model, $conditions) {
		$this->model = Helpers::get_model_reflection($model);
		$this->conditions = $conditions;
	}
	
	public function __get($name) {
		$objects = $this->get_objects();
		return $objects[0]->$name;
	}
	
	public function filter($conditions) {
		$this->conditions = array_merge($this->conditions, $condition);
	}
	
	public function query() {
		return $this->build_query();
	}
	
	/**
	 * @see Countable::count()
	 */
	public function count() {
		return count($this->get_objects());
	}
	
	/**
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		$this->position = 0;
	}
	
	/**
	 * @see Iterator::current()
	 */
	public function current() {
		$objects = $this->get_objects();
		return $objects[$this->position];
	}
	
	/**
	 * @see Iterator::key()
	 */
	public function key() {
		return $this->position;
	}
	
	/**
	 * @see Iterator::next()
	 */
	public function next() {
		++$this->position;
	}
	
	/**
	 * @see Iterator::valid()
	 */
	public function valid() {
		$objects = $this->get_objects();
		return isset($objects[$this->position]);
	}
	
	
	/**
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $value) {
		$objects = $this->get_objects();
		
		if (is_null($offset)) {
			$this->objects[] = $value;
		} else {
			$this->objects[$offset] = $value;
		}
	}
	
	/**
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset) {
		$objects = $this->get_objects();
		return isset($this->objects[$offset]);
	}
	
	/**
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset) {
		$objects = $this->get_objects();
		unset($this->objects[$offset]);
	}
	
	/**
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset) {
		$objects = $this->get_objects();
		return isset($this->objects[$offset]) ? $this->objects[$offset] : null;
	}
	
	
	private function build_query() { // Change to parse_query()
		
		$models = array();
		$joins = array();
		$fields = array();
		$wheres = array();
		
		$current_model = $this->model;
		$current_eval = null; // how we will evaluate the field (=, IN(), NOT IN(), etc)
				
		foreach($this->conditions as $condition => $value) {
			
			$pieces = explode('__', $condition);
			
			foreach($pieces as $field_name) {
				
				$model_reflection = Helpers::get_model_reflection($current_model->getName());
				$current_model_fields = $model_reflection->getDefaultProperties();
				
				if(!isset($current_model_fields[$field_name]))
					throw new Exception('model ' . $current_model->getName(). ' has no property ' . $field_name);
				
				$field = $current_model_fields[$field_name]; // ex: array('ForeignKey', 'model' => 'Post')
				
				// Check if the field is a validate Field Class
				if(Helpers::get_field_type($field) === null)
					throw new Exception('Unknown field type on model property: ' . $field_name);
				
				if(!in_array($models, $))
				
				switch($field) {
					case fields\ForeignKey:
						$joins[] = array('table'=>strtolower($field['model']), 'on_table'=> strtolower($current_model->getName()));
						$current_model = new \ReflectionClass($field['model']); // change to get_reflection_model()
						$this->reflection_models[$field['model']] = $current_model;
						break;
						
					case fields\ManyToMany:
						$joins[] = array('table'=>strtolower($field['model']), 'on_table'=> strtolower($current_model->getName()));
						$current_model = new \ReflectionClass($field['model']); // change to get_reflection_model()
						$this->reflection_models[$field['model']] = $current_model;
						break;
						
					case fields\IntField:
						$wheres[] = array('table'=> strtolower($current_model->getName()), 'field'=> $field_name, 'eval' => $current_eval, 'value'=>$value);
						break;
						
					default:
						var_dump($field);
						throw new Exception('Field type: ' . $field['type'] . ' is unknown.');
				}
			}
		}
		
		$model_fields = $this->model->getDefaultProperties();
		$_meta = $model_fields['_meta'];
		
		if(isset($_meta['table_name']))
			$main_table = $_meta['table_name'];
		else
			$main_table = $this->model->getName();
		
		$query = sprintf('SELECT %s.* FROM %s', $main_table, $main_table);
		
		foreach($joins as $join)
			$query .= sprintf(' INNER JOIN %s ON(%s=%s)', $join['table'], $join['table'].'.id', $join['on_table'] . '.'. $join['table'].'_id');
		
		if(count($wheres)) {
			$query .= ' WHERE ';
			
			foreach($wheres as $where) {
				switch($where['eval']) {
					case null:
						$query .= sprintf('%s.%s = %s', $where['table'], $where['field'], $where['value']);
						break;
				}
			}
		}
		
		echo '<br/>Query Built: ' . $query;
		return $query;
	}
	
	
	private function get_field_name($field) {
		return strtolower($field['model'] . '_id');
	}
	
	/**
	 * Called when the QuerySet is read, used or iterated through
	 */
	private function get_objects() {
		
		if($this->objects !== null)
			return $this->objects;
		
		$db = Jenga::get_db();
		$objects = array();
		
		try {
			$query = $this->build_query();
			$result = mysql_query($query);
			
		} catch(Exception $e) {
			throw $e;
		}
		
		if(!$result)
			throw Exception('Invalid SQL: ' . $query);
		
		$rows = mysql_num_rows($result);
		for($i=0; $i < $rows; $i++){
			$row = mysql_fetch_array($result);
			$class_name = $this->model->getName();
			$obj = new $class_name();
			$fields = $this->get_model_fields($this->model->getName());	
					
			foreach($row as $col_name => $value) {
				
				if(isset($fields[$col_name])) {
					$field = $fields[$col_name];
					
					var_dump($field);
					switch($field['type']) {
						case FOREIGN_KEY:
							$obj->$col_name = new QuerySet($field['model'], array('id'=>$value));
							break;
					
						default:
							$obj->$col_name = $value;
							break;
					}
					
				}
			}
			
			$objects[] = $obj;
		}
		return $this->objects = $objects;
	}
	
	private function get_model_fields($model) {
		
		if(isset(\Jenga::$MODEL_FIELDS[$model]))
			return \Jenga::$MODEL_FIELDS[$model];
		
		$fields = array();
		$reflection_obj = new \ReflectionClass($model);
		$default_properties = $reflection_obj->getDefaultProperties();
		
		foreach($default_properties as $property_name => $field) {
			
			if(is_string($field) && in_array($field, \Jenga::$MODEL_FIELD_LIST)) {
				$field = array('type' => $field);
			} else if(is_array($field) && (array_key_exists(0, $field) || array_key_exists('type', $field))) {
				if(is_string($field[0]) && in_array($field[0], \Jenga::$MODEL_FIELD_LIST)) {
					$field['type'] = $field[0];
				} else if(isset($field['type']) && in_array($field['type'], \Jenga::$MODEL_FIELD_LIST)) {
					
				}
			} else
				continue;
		
			
			$fields[$property_name] = $field;
		}
		
		return \Jenga::$MODEL_FIELDS[$model] = $fields;
	}
}