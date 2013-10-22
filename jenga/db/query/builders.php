<?php
namespace jenga\db\query\builders;
use jenga\db\query\Query;

abstract class QueryBuilder {

	const SELECT = 'select';
	const UPDATE = 'update';
	const INSERT = 'insert';
	const DELETE = 'delete';

	protected $table = null;
	protected $select_columns = array();
	protected $inner_joins = array();
	protected $wheres = array();
	protected $orders = array();
	
	public abstract function create_select_statement($model, $grouped_related_models, $wheres=null);
	//public abstract function create_update_statement($model);
	//public abstract function create_insert_statement($model);
	//public abstract function create_delete_statement($model);

	public abstract function add_table($table, $type);
	public abstract function add_inner_join($join_table, $join_table_alias, $join_column, $on_table, $on_column);
	public abstract function add_order($table, $column);
	public abstract function add_select_column($table, $column);
	public abstract function add_where($table, $column, $conditional_operator, $value);
}


class SQLQueryBuilder extends QueryBuilder {

	private $table_aliases = array();
	private $conditional_operators = array(
			null => '='
	);

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

	public function add_where($table, $column, $conditional_operator, $value) {
		$this->wheres[] = array(
				'table' => $table,
				'column' => $column,
				'conditional_operator' => $conditional_operator,
				'value' => $value
		);
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
			$query .= ' ' . $join;

		if(!empty($this->wheres)) {
			$wheres = array();
			foreach($this->wheres as $where) {
				$operator = $this->conditional_operators[$where['conditional_operator']];
				$string_or_digit = "'%s'";
				if(is_numeric($where['value']))
					$string_or_digit = '%d';
				$wheres[] = sprintf(' %s.%s %s ' . $string_or_digit, $where['table'], $where['column'], $operator, $where['value']);
			}
			$query .= ' WHERE' . implode(' AND', $wheres);
		}

		return $query;

	}

	private function get_table_alias($table_name) {

	}
}


class MongoQueryBuilder extends QueryBuilder {

	private $conditional_operators = array(
			null => '='
	);

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
		// No implementation
	}

	public function add_inner_join($join_table, $join_table_alias, $join_column, $on_table, $on_column) {
		//$this->inner_joins[$join_table] = array($join_column => $);
	}

	public function add_select_column($table, $column) {
		if($table_alias != '')
			$this->select_columns[] = $table . '.' . $column;
		else
			$this->select_column[] = $column;
	}

	public function add_where($table, $column, $conditional_operator, $value) {
		$this->wheres[] = array(
				'table' => $table,
				'column' => $column,
				'conditional_operator' => $conditional_operator,
				'value' => $value
		);
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
			$query .= ' ' . $join;

		if(!empty($this->wheres)) {
			$wheres = array();
			foreach($this->wheres as $where) {
				$operator = $this->conditional_operators[$where['conditional_operator']];
				$string_or_digit = "'%s'";
				if(is_numeric($where['value']))
					$string_or_digit = '%d';
				$wheres[] = sprintf(' %s.%s %s ' . $string_or_digit, $where['table'], $where['column'], $operator, $where['value']);
			}
			$query .= ' WHERE' . implode(' AND', $wheres);
		}

		return $query;

	}

	private function get_table_alias($table_name) {

	}

}