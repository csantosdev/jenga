<?php
namespace Jenga\DB\Query;

class Select {
	
	private $table;
	private $joins;
	private $where;
	
	public function __construct() {
			
	}
	
	public function set_table($table) {
		$this->table = $table;
	}
	
	public function add_inner_join($joined_table, $joined_table_field, $on_table, $on_table_field) {
		$this->joins[] = sprintf('INNER JOIN %s ON ("%s"."%s" = "%s"."%s")', $joined_table, $joined_table_field, $on_table, $on_table_field);
	}
	
	public function build() {
		
		$query = sprintf('SELECT * FROM %s', $this->table);
		
		if($this->joins != null)
			foreach($this->joins as $join)
				$query .= "\n" . $join;
			
		return $query;
	}
	
}