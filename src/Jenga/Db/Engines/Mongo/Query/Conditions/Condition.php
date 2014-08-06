<?php
namespace Jenga\Db\Engines\Mongo\Query\Conditions;
/**
 * Interface for a Mongo query condition.
 *
 * @author Chris Santos
 */
interface Condition {

    /**
     * Build a representation of a mongo condition.
     *
     * @param $field
     * @param $value
     * @param $operator
     */
    public function __construct($field, $value, $operator);

    /**
     * Returns a representation of a query segment to be used in the final query.
     */
    public function toQuery();
}