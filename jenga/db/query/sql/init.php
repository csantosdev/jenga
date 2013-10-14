<?php
namespace jenga\db\query\SQL;
use jenga\db\query\QueryBuilder;

class SQLQueryBuilder extends QueryBuilder {
	
	private $table_aliases = array();
	
	public function __get($name) {
		
		if($name == 'query')
			return $this->build();
	}
	
	public  function create_select_statement($model, $grouped_related_models, $wheres=null) {
		$this->add_table($model->table_name);
		// INNER JOINS
		foreach($grouped_related_models as $group) {
			foreach($group as $related_models) {
				foreach($related_models as $related) {
					
					//$this->add_inner_join($join_table, $join_column, $on_table, $on_column);
				}
			}
		}
	}
	
	public function add_table($table, $type=null) {
		$this->table = $table;
	}
	
	public function add_inner_join($join_table, $join_table_alias, $join_column, $on_table, $on_column) {
		$this->inner_joins[] = sprintf('INNER JOIN %s as %s ON (%s.%s = %s.%s)', $join_table, $join_table_alias, $join_table_alias, $join_column, $on_table, $on_column);
	}
	
	public function add_select_column($table, $column) {
		if($table_alias != '')
			$this->select_columns[] = $table . '.' . $column;
		else
			$this->select_column[] = $column;
	}
	
	public function add_order($table, $column) {
		
	}
	

	
	/**
	 * 
	 * @param array $columns
	 */
	public function set_select_column($columns) {
		$this->select_columns = $columns;
	}
	
	
	private function build() {
		
		$query = sprintf('SELECT * FROM %s as T1', $this->table);
			
		if(!empty($this->inner_joins))
			foreach($this->inner_joins as $join)
				$query .= " " . $join;
				
		return $query;
				
	}
	
	private function get_table_alias($table_name) {
		
	}
	
}