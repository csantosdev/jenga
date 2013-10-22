<?php
namespace jenga\db\models\builders;
use jenga\db\query\builders\SQLQueryBuilder;
use jenga\db\query\builders\MongoQueryBuilder;

abstract class ModelBuilder {
	
	abstract public function build_select($model, $query_objects);
	
}

class SQLModelBuilder extends ModelBuilder {
	
	private $query_builder;
	
	public function __construct() {
		$this->query_builder = new SQLQueryBuilder();
	}
	
	public function build_select($model, $query_objects) {
		$this->query_builder->add_table($model->table_name);
		$table_count = 2;
		foreach($query_objects as $query_object) {
			$table_aliases = array($model->table_name => 'T1');
			// INNER JOINS
			foreach($query_object->related_models as $related_model) {
				foreach($query_object->related_models as $related) {
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
			// WHERES
			if( !empty($query_object->wheres)) {
				foreach($query_object->wheres as $where) {
					$this->query_builder->add_where($table_aliases[strtolower($where['model_name'])], $where['field_name'], $where['conditional_operator'], $where['value']);
				}
			}
		}
		
	return $this->query_builder->query;
	}
}

class MongoModelBuilder extends ModelBuilder {

	private $query_builder;
	
	public function __construct() {
		$this->query_builder = new MongoQueryBuilder();
	}
	
	public function build_select($model, $query_objects) {
		$this->query_builder->add_table($model->table_name);
		$collection_data = array();
		foreach($query_objects as $query_object) {
			// INNER JOINS, NOT QUITE
			foreach($query_object->related_models as $related_model) {
				foreach($query_object->related_models as $related) {
					$collection_data[] = array(
						'related_collection_name' => strtolower($related['model']->table_name),
						'related_collection_field_name' => $related['on_column'],
					);
					
					$this->query_builder->add_inner_join(
						strtolower($related['model']->table_name),
						null,
						'_id',
						strtolower($related['join_model']->table_name),
						$related['on_column']);
					
					/*
					$join_table = $related['model']->table_name;
					$join_table_alias = 'T'.$table_count;
					$table_count++;
					$join_column = 'id';
					$on_table = $table_aliases[$related['join_model']->table_name];
					$on_column = $related['on_column'];
					$this->query_builder->add_inner_join($join_table,$join_table_alias, $join_column, $on_table, $on_column);
					*/
				}
			}
			// WHERES
			if( !empty($query_object->wheres)) {
				foreach($query_object->wheres as $where) {
					$this->query_builder->add_where($table_aliases[strtolower($where['model_name'])], $where['field_name'], $where['conditional_operator'], $where['value']);
				}
			}
		}
		
		$m = new \MongoClient();
		$db = $m->selectDB('test');
		$collection = $db->{$model->getName()};
		
		foreach($collections as $collection)
		
		/*
		 ['mongoblog' => ['name' => 'iCandy Clothing]];
		 ['mongopost' => 
		*/
		return $this->query_builder->query;
	}
}