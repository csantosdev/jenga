<?php
namespace Jenga\DB\Query\SQL;
use Jenga\DB\Query\QueryBuilder;

class SQLQueryBuilder extends QueryBuilder {
	
	public function __get($name) {
		
		if($name == 'query')
			return $this->build();
	}
	
	public function add_table($table, $type=null) {
		$this->table = $table;
	}
	
	public function add_inner_join($join_table, $join_column, $on_table, $on_column) {
		$this->inner_joins[] = sprintf('INNER JOIN %s ON ("%s"."%s" = "%s"."%s")', $join_table, $join_column, $on_table, $on_column);
	}
	
	public function add_order($table, $column) {
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Jenga\DB\Query.Query::add_select_column()
	 */
	public function add_select_column($column) {
		$this->select_columns[] = $column;
	}
	
	/**
	 * 
	 * @param array $columns
	 */
	public function set_select_column($columns) {
		$this->select_columns = $columns;
	}
	
	
	public function build_select() {
		
		$query = sprintf('SELECT * FROM %s', $this->table);
			
		if(!empty($this->inner_joins))
			foreach($this->inner_joins as $join)
				$query .= " " . $join;
				
		return $query;
				
	}
	
}