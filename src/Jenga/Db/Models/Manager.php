<?php
namespace Jenga\Models;
/**
 * Responsible for managing model relationships and allowing the developer to query them.
 *
 * @package Jenga\Models
 * @author Chris Santos
 */
class Manager {

    private $model;
    private $conditions = array();
    private $select_related_models = array();

    public function __construct(&$model, $conditions = null) {

        $this->model = $model;

        if($conditions)
            $this->conditions = $conditions;

    }

    /**
     * Returns 1 object.
     *
     * @param $conditions
     * @return QuerySet
     */
    public function get($conditions) {

        $conditions = array_merge($this->conditions, $conditions);

        $qs = new QuerySet(new Query($this->model, $conditions, $this->select_related_models));
        $count = count($qs);

        if($count == 0)
            throw new \Exception("Could not find " . $this->model->name . ' object in database.');

        else if($count > 1)
            throw new \Exception('More than 1 object was returned for query.');

        return $qs[0];
    }

    /**
     * Returns a QuerySet that is filtered by the conditions passed.
     *
     * @param array $conditions
     * @return QuerySet
     */
    public function filter($conditions) {

        $conditions = array_merge($this->conditions, $conditions);
        return $qs = new QuerySet(new Query($this->model, $conditions, $this->select_related_models));
    }

    /**
     * Returns a QuerySet.
     *
     * @return QuerySet
     */
    public function all() {
        return $qs = new QuerySet(new Query($this->model, $this->conditions, $this->select_related_models));
    }

    public function contain() {
        $args = func_get_args();
        $this->select_related_models = array_merge($this->select_related_models, $args[0]);
        return clone $this;
    }
}