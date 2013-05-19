<?php
namespace Jenga\DB\Query;

abstract class QueryBuilder {

	const SELECT = 'select';
	const UPDATE = 'update';
	const INSERT = 'insert';
	const DELETE = 'delete';

	protected $table = null;
	protected $inner_joins = array();
	protected $select_columns = array();
	protected $orders = array();
	
	public abstract function create_select_statement($model, $grouped_related_models, $wheres=null);
	//public abstract function create_update_statement($model);
	//public abstract function create_insert_statement($model);
	//public abstract function create_delete_statement($model);

	public abstract function add_table($table, $type);
	public abstract function add_inner_join($join_table, $join_column, $on_table, $on_column);
	public abstract function add_order($table, $column);
	public abstract function add_select_column($column);
}