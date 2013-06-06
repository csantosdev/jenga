<?php
namespace Jenga\DB\Models;
use Jenga\DB\Query\Builders\SQLQueryBuilder;

abstract class ModelBuilder {
	
	abstract public function build_select($model, $grouped_related_models, $wheres);
	
}

class SQLModelBuilder extends ModelBuilder {
	
	private $query_builder;
	
	public function __construct() {
		$this->query_builder = new SQLQueryBuilder();
	}
	
	public function build_select($model, $grouped_related_models, $wheres=null) {
		$this->query_builder->add_table($model->table_name);
		$table_count = 2;
		foreach($grouped_related_models as $related_models) {
			$table_aliases = array($model->table_name => 'T1');
			foreach($related_models as $related) {
				if(isset($table_aliases[$related['model']->table_name]))
					continue;
				$join_table = $related['model']->table_name;
				$join_table_alias = 'T'.$table_count;
				$table_count++;
				$join_column = 'id';
				$on_table = $table_aliases[$related['join_model']->table_name];
				$on_column = $related['on_column'];
				$this->query_builder->add_inner_join($join_table,$join_table_alias, $join_column, $on_table, $on_column);
			}
			
		}
		
		foreach($wheres as $where) {
			$this->query_builder->	
		}
		
	return $this->query_builder->query;
	}
}

class MongoModelBuilder extends ModelBuilder {

	public function build_select($model, $grouped_related_models, $wheres) {

	}
}