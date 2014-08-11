<?php
namespace Jenga\Db\Engines\Mongo\Query;
/**
 * MongoDB's implementation of a Query object.
 *
 * @author Chris Santos
 */
class Query {

    const _OR_ = 'OR';

    private $model;

    /**
     * @var string
     */
    private $model_class;

    private $filters = array();

    private $operators = '__isnull|__gte|__lte|__gt|__lt|__in|__nin|__exact|__iexact|__contains|__icontains|__startswith|__istartswith|__endswith|__iendswith';

    /**
     * Used to keep track of the current query scope when building the query.
     *
     * @var array
     */
    private $scope;

    public function __construct($model_class = null, $filters = array(), $select_related = array()) {

        $this->filters = array($filters);

    }

    public function filter(array $filter) {

        $this->filters[] = $filter;
    }

    public function build() {

        $query = array();

        foreach($this->filters as $filter)
            $query = array_merge($query, $this->_build($filter));

        return $query;
    }

    private function _build($filter) {

        $query = array('$and' => array());
        $scope = &$query['$and'];

        foreach($filter as $field => $value) {

            if(is_int($field)) {

                if(is_array($value)) {
                    $scope[] =$this->_build($value);
                    continue;

                } else if($value === Query::_OR_) {

                    if(isset($query['$and'])) {
                        $or = array('$or' => array(
                            array('$and' => $scope)
                        ));
                        $query = $or;
                        $scope = &$query['$or'];

                    } else if(isset($query['$or'])) {

                    } else {
                        throw new \Exception('When query building the main key should be using either $and or $or.');
                    }

                    //$condition = new Conditions\_OR_($field, $value, null);
                   // $query = array_merge($query, $condition->toQuery());
                    continue;
                }
            }

            $operator = $this->findOperator($field);
            $condition = Conditions\ConditionFactory::get($field, $value, $operator);
            $scope[] = $condition->toQuery();
        }

        return $query;
    }

    private function findOperator(&$condition) {

        $matches_found = preg_match('/' . $this->operators . '/', $condition, $matches);

        if($matches_found === 0)
            return null;

        else if($matches_found > 1)
            throw new \Exception('Multiple operators found in condition: ' . $condition);

        $condition = str_replace($matches[0], '', $condition);

        return $matches[0];
    }
}