<?php
namespace Jenga\DB\Query;
use Jenga\Helpers;

class QueryBuilder {
	
	private $tables;
	private $inner_joins;
	private $wheres;
	private $groups;
	private $having;
	private $order;
	
	private $select, $update, $delete, $insert;
	
	public function __construct() {
		$this->select = new Select();
		$this->update = new Update();
		$this->delete = new Delete();
		$this->insert = new Insert();
	}
	
	public function __get($name) {
		if($name == 'query')
			return $this->build();
		return null;
	}
	
	/**
	 * Takes models as arguments and gets table names, field names and creates the needed join SQL

	 * @param array $models - List of objects as type ReflectionClass
	 * @param array $inner_joins
	 * @param array $wheres
	 */
	public function create_select_statement($models, $inner_joins, $wheres) {
		
		$alias_num_count = 1;
		$this->clean();
		
		foreach($models as $model_name) {
			$alias = 'T' . $alias_num_count;
			$this->tables[$alias] = Helpers::get_model_table_name($model_name);
		}
		
		foreach($inner_joins as $f1 => $f2) {
			$this->inner_joins[$f1] = $f2;
		}
		
		var_dump($this->tables);
	}
	
	public function create_update_statement() {
		
	}
	
	public function create_delete_statement() {
	
	}
	
	public function create_insert_statement() {
	
	}
	
	private function clean() {
		$this->tables = array();
		$this->inner_joins = array();
		$this->wheres = array();
	}
	
}