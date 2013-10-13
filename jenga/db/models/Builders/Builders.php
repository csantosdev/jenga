<?php
namespace Jenga\Db\Models\Builders;

//use Jenga\Db\Query\Builders\SQLQueryBuilder;
//use Jenga\Db\Query\Builders\MongoQueryBuilder;
use Jenga\Db\Fields as f;
use Jenga\Db\Connections\ConnectionFactory;
use Jenga\Db\Managers\MongoModelManager;
use Jenga\Db\Query\QuerySet;
use Jenga\Db\Query\Query;

abstract class ModelBuilder {
	
	abstract public function build_select($model, $query_objects);
	
}

/*
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
}*/

class MongoModelBuilder extends ModelBuilder {

	private $query_builder;
	
	public function __construct() {
		$this->query_builder = new MongoQueryBuilder();
	}
	
	public function build_select($model, $query_objects) {
		$this->query_builder->add_table($model->table_name);
		$collection_data = array();
		
		$query = $query_objects[0];
		$main_query = array();
		
		foreach($query->dependencies as $dependency) {
			$ids = $this->build_dependency($dependency);
			$field_name = $dependency->field . '_id';
			$main_query[$field_name] = array('$in' => $ids);
		}
		
		if(count($query->wheres) > 0) {
			$q = $this->build_where($query->wheres);
			$main_query = array_merge($main_query, $q);
		}
		
		/*
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
		$cursor = $collection->find($main_query);
		
		$models = [];
		
		// Put data into object model
		foreach($cursor as $doc) {
			$models[] = $this->build_model($model, $doc);
		}
		
		return $models;
	}
	
	/**
	 * Recursive function that builds and queries all dependency collections (Table Joins)
	 * @param Query $dependency
	 * @throws \Exception
	 */
	private function build_dependency(Query $query) {
		
		$mongo_query = array();
		
		foreach($query->dependencies as $dependency) {
			$ids = $this->build_dependency($dependency);
			
			if(empty($ids))
				return [];
			
			$field_name = $dependency->field . '_id';
			
			if(count($ids) > 1)
				$mongo_query[$field_name] = array('$in' => $ids);
			else
				$mongo_query[$field_name] = $ids[0];
		}
		
		if(count($query->wheres) > 0) {
			$q = $this->build_where($query->wheres);
			$mongo_query = array_merge($mongo_query, $q);
		}
		
		if(!empty($mongo_query)) {
			$db = ConnectionFactory::get($query->model->_meta['db_config']);
			$collection = new \MongoCollection($db, $query->model->table_name);
		
			$cursor = $collection->find($mongo_query, array('_id'));
			$ids = array();
			foreach($cursor as $doc)
				$ids[] = $doc['_id'];
			
			return $ids;
			
		} else
			throw new \Exception('No criteria for Mongo Query.');
	}
	
	private function build_where($wheres) {
		$query = [];
		
		foreach($wheres as $field => $value) {
			// In here we would do checks for operation conditional checks and
			// add in $in or $nin, etc.
				
			if(!is_array($value)) {
				$query[$field] = $value;
				continue;
			}
			
			foreach($value as $operator => $value) {
				switch($value) {
					case Query::IN || Query::_IN:
						$conditional_operator = '$in';
						break;
			
					default:
						throw new \Exception('Unknown conditional operator: ' . $value);
				}
			}
				
			$query[$field] = [$conditional_operator => $value];
		}
		return $query;
	}
	
	/**
	 * Puts data pulled from Mongo into the PHP model class.
	 * 
	 * @param ReflectionClass $model
	 * @param Array $doc
	 */
	private function build_model(\ReflectionClass $model, $doc) {
		
		$m = $model->newInstance();
		
		foreach($model->fields as $column_name => $field) {
			$f = $field;
		
			if($column_name == 'id') {
				$m->id = $doc['_id'];
				continue;
			}
				
			//TODO: DO THIS BETTER
			$field = new \ReflectionClass($field[0]);
			$field_class = $field->getName();
		
			// CharField
			if($field_class == f\CharField || $field->isSubclassOf(f\CharField)) {
				$m->$column_name = (string)$doc[$column_name];
					
			// NumberField
			} else if($field_class == f\NumberField || $field->isSubclassOf(f\NumberField)) {
				$m->$column_name = $doc[$column_name];
					
			// BooleanField
			} else if($field_class == f\BooleanField) {
				$m->$column_name = (bool)$doc[$column_name];
					
			// ForeignKey
			} else if($field_class == f\ForeignKey) {
				$fk_id_field_name = $column_name . '_id';
				/**
				 *  Set the FK field with a pre-conditioned manager object
				 *  to pull a QuerySet for Lazy Loading.
				 **/
				if($doc[$fk_id_field_name] != null) {
					$m->$column_name = $doc[$fk_id_field_name];
					$m->$fk_id_field_name = $doc[$fk_id_field_name];
				
					$query = new Query();
					$query->wheres['_id'] = $doc[$fk_id_field_name];
					$qs = new QuerySet($f['model'], null, false, $query);
					$m->$column_name = $qs;
				}
					
			// ManyToMany
			} else if($field_class == f\ManyToMany) {
				
				$m->$column_name = new QuerySet($f['model'], [$m->get_table_name() . '._id' => $doc['_id']]);
					
			// EmbeddedDocument
			} else if($field_class == f\EmbeddedDocumentField) {
				
				if(isset($f['type']))
					$type = $f['type'];
				else
					$type = f\ObjectType;
				
				if($type == f\ObjectType)
					$items = [$doc[$column_name]];
				else
					$items = $doc[$column_name];
					
				$docs = [];
				
				foreach($items as $item) {
				
					if(isset($f['model'])) {
						
						$reflection_model = IntrospectionModel::get($f['model']);
						$docs[] = $this->build_model($reflection_model, $item);
						
					} else if(isset($f['models'])) {
						
						if(!isset($item['_class'])) // Class name of model
							throw new \Exception('No _class assigned to Mongo object ' . $model->getName());
						
						if(!in_array($item['_class'], $f['models']))
							throw new \Exception($item['_class'] . ' class is not allowed to be used with ' . $model->getName());
						
						$reflection_model = IntrospectionModel::get($item['_class']);
						$docs[] = $this->build_model($reflection_model, $item);
						
					// Set properties as-is
					} else
						$docs[] = $item;
				}
				
				//TODO: DO THIS BETTER
				if($type == f\ObjectType)
					$m->$column_name = $docs[0];
				else
					$m->$column_name = $docs;
		
			} else
				throw new \Exception('No logic for: ' . $field_class);
		}
		
		return $m;
	}
}