<?php
namespace Jenga\DB\Query\Builders;

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