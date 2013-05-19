<?php
namespace Jenga\DB\Models\Builders;
use Jenga\DB\Query\SQL\SQLQueryBuilder;

abstract class ModelBuilder {
	
	abstract public function build($model, $grouped_related_models, $wheres);
	
}

class SQLModelBuilder extends ModelBuilder {
	
	private $query_builder;
	
	public function __construct() {
		$this->query_builder = new SQLQueryBuilder();
	}
	
	public function build($model, $grouped_related_models, $wheres) {
		$this->query_builder->add_table($model->table_name);
		foreach($grouped_related_models as $group) {
			foreach($group as $related_models) {
				foreach($related_models as $related) {
					$join_table = $related['model']->table_name;
					$join_column = $this->get_alias($related['model']->table_name) . '".id"';
					$on_table = $related['join_model'];
					$on_column = null;
					$this->add_inner_join($join_table, $join_column, $on_table, $on_column);
				}
			}
		}
	}
}

class MongoModelBuilder extends ModelBuilder {

	public function build($model, $grouped_related_models, $wheres) {

	}
}