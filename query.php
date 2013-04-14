<?php
class QuerySet implements Countable, Iterator, ArrayAccess {
	
	private $model;
	private $models = array();
	private $reflection_models = array();
	private $conditions = array();
	private $objects = null;
	private $position = 0;
	
	private $field_list = array('ForeignKey', 'OneToMany', 'ManyToMany', 'CharField', 'TextField', 'IntField');
	
	public function __construct($model, $conditions) {
		$this->model = new ReflectionClass($model);
		$this->conditions = $conditions;
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
	
	
	private function build_query() {
		
		require_once 'models.php';
		
		$joins = array();
		$fields = array();
		$wheres = array();
		
		$current_model = $this->model;
		$current_eval = null; // how we will evaluate the field (=, IN(), NOT IN(), etc)
				
		foreach($this->conditions as $condition => $value) {
			
			$pieces = explode('__', $condition);
			
			foreach($pieces as $piece) {
				
				$current_model_fields = $current_model->getDefaultProperties();
				
				// Check if it's a property
				if(!isset($current_model_fields[$piece]))
					throw new Exception('model ' . $current_model->getName(). ' has no property ' . $piece);
				
				$field = $current_model_fields[$piece];
				
				if(!is_string($field) && !is_array($field))
					throw new Exception($field . ' must be a string or an array');

				if(is_string($field))
					$field_name = $field;
				else if(isset($field['field']))
					$field_name = $field['field'];
				else
					$field_name = $field[0];
				
				switch($field_name) {
					case 'ForeignKey':
						$joins[] = array('table'=>strtolower($field['model']), 'on_table'=> strtolower($current_model->getName()));
						$current_model = new ReflectionClass($field['model']); // change to get_reflection_model()
						$this->reflection_models[$field['model']] = $current_model;
						break;
						
					case 'IntField':
						$wheres[] = array('table'=> strtolower($current_model->getName()), 'field'=> $piece, 'eval' => $current_eval, 'value'=>$value);
						break;
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
	
	private function get_model($model) {
		if(!isset($this->models[$model]))
			$this->models[$model] = new $model();	
		return $this->models[$model];
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
		
		$db = mysql_connect('localhost', 'root', null);
		mysql_select_db('test', $db);
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
			$default_properties = $this->model->getDefaultProperties();
			
			foreach($row as $col_name => $value) {
				foreach($default_properties as $property_name => $field) {
					if($col_name == $property_name) {
						// Get the field name (ForeignKey, IntField, etc)
						if(is_string($field) && in_array($field, $this->field_list)) {
							$field_name = $field;
						} else if(is_array($field) && (array_key_exists(0, $field) || array_key_exists('field', $field))) {
							if(is_string($field[0]) && in_array($field[0], $this->field_list)) {
								$field_name = $field[0];
							} else if(isset($field['field']) && in_array($field['field'], $this->field_list)) {
								$field_name = $field['field'];
							}
						} else {
							continue;
						}
						
						switch($field_name) {
							case 'ForeignKey':
								$obj->$property_name = new QuerySet($field['model'], array('id'=>$value));
								break;
								
							default:
								$obj->$property_name = $value;
								break;
						}
						
					}
				}
			}
			$objects[] = $obj;
		}
		return $this->objects = $objects;
	}
}