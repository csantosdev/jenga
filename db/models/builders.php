<?php
namespace Jenga\DB\Models;
use Jenga\DB\Query\Builders\SQLQueryBuilder;
use Jenga\DB\Query\Builders\MongoQueryBuilder;
use Jenga\DB\Fields as f;
use Jenga\DB\Connections\ConnectionFactory;

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
		
		$query = $query_objects[0];
		var_dump($query);
		$main_query = array();
		
		foreach($query->wheres as $where) {
			$ids = $this->build_where($where, $is_dependent=true);
			echo 'MAIN/'.$where->model->getName().': ' ;
			$field_name = $where->field . '_id';
			$main_query[$field_name] = array('$in' => $ids);
		}
		
		/**
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
				}
			}
			// WHERES
			if( !empty($query_object->wheres)) {
				foreach($query_object->wheres as $where) {
					$this->query_builder->add_where($table_aliases[strtolower($where['model_name'])], $where['field_name'], $where['conditional_operator'], $where['value']);
				}
			}
			
		}*/
		
		$db = ConnectionFactory::get($model->_meta['db_config']);
		$collection = new \MongoCollection($db, $model->table_name);
		
		echo 'QUERYING MAIN QUERY: ';
		var_dump($main_query);
		$cursor = $collection->find($main_query);
		
		$models = [];
		
		// Put data into object model
		foreach($cursor as $doc) {
			echo '<br/>Pulled:';
			var_dump($doc);
			
			$m = $model->newInstance();
			
			foreach($model->fields as $column_name => $field) {
				var_dump($field);
				$f = $field;
				
				if($column_name == 'id') {
					$m->id = $doc['_id'];
					continue;
				}
					
				$field = new \ReflectionClass($field[0]);
				
				if($field->getNamespaceName() == f\CharField || $field->isSubclassOf(f\CharField)) {
					$m->$column_name = (string)$doc[$column_name];
					
				} else if($field->getNamespaceName() == f\NumberField || $field->isSubclassOf(f\NumberField)) {
					$m->$column_name = $doc[$column_name];
					
				} else if($field->getName() == f\ForeignKey) {
					$fk_id_field_name = $column_name . '_id';
					if($doc[$fk_id_field_name] != null) {
						$m->$column_name = $doc[$fk_id_field_name];
						$m->$fk_id_field_name = $doc[$fk_id_field_name];
					}
				}
			}
			
			$models[] = $m;
		}
		
		return $models;
	}
	
	private function build_where($where, $is_dependent=false) {
		
		$query = array();
		echo '<br/>WHERE:';
		var_dump($where);
		
		foreach($where->dependencies as $dependency) {
			$ids = $this->build_where($dependency, true);
			
			if(empty($ids))
				return [];
			
			$field_name = $dependency->field . '_id';
			
			if(count($ids) > 1)
				$query[$field_name] = array('$in' => $ids);
			else
				$query[$field_name] = $ids[0];
		}
		
		foreach($where->wheres as $field => $value) {
			// In here we would do checks for operation conditional checks and
			// add in $in or $nin, etc.
		}
		
		if(!empty($where->wheres)) {
			$query = array_merge($query, $where->wheres);
			echo 'Sub-Query on '.$where->model->getName().':';
			var_dump($query);	
			
			$db = ConnectionFactory::get($where->model->_meta['db_config']);
			$collection = new \MongoCollection($db, $where->model->table_name);
		
			$cursor = $collection->find($query, array('_id'));
			$ids = array();
			foreach($cursor as $doc)
				$ids[] = $doc['_id'];
			
			echo "<br/>Returning IDs:";
			var_dump($ids);
			return $ids;
		}
	}
}