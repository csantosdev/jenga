<?php
namespace Jenga\Db\Engines\Mongo\Query;
/**
 * MongoDB's implementation of a Query object.
 *
 * @author Chris Santos
 */
class Query {

    private $filters = array();

    private $operators = '__isnull|__gte|__lte|__gt|__lt|__in|__exact|__iexact|__contains|__icontains|__startswith|__istartswith|__endswith|__iendswith';

    public function filter(array $filter) {

        $this->filters[] = $filter;
    }

    public function build() {

        $query = array();

        foreach($this->filters as $filter_conditions) {

            foreach($filter_conditions as $field => $value) {

                /*
                if(is_int($field)) {

                    if(is_array($value)) {
                        $conditions[] = $this->build($value);
                        continue;

                    } else if($value === Query::_OR_) {
                        $conditions[] = 'OR';
                        continue;
                    }
                }
                */

                $operator = $this->findOperator($field);
                $condition = Conditions\ConditionFactory::get($field, $value, $operator);

                /*
                Ticket::filter([
                    'type' => 'Creative',
                    'status.name' => 'Started',
                    EmbeddedMatchCondition([
                        'product.meta.field_id' => 1,
                        'product.meta.value' => 'Red',
                        'product.type' => F('type') // {'product.type': }
                    ])
                ]);
                */

                $query = array_merge($query, $condition->toQuery());
            }
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