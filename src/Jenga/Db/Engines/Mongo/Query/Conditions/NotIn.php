<?php
namespace Jenga\Db\Engines\Mongo\Query\Conditions;
/**
 * A mongo not in query segment.
 *
 * @author Chris Santos
 */
class NotIn extends Base {

    /**
     * @inheritdoc
     */
    public function toQuery() {

        if(!is_array($this->value))
            throw new \Exception('When using an $nin conditions, you must provide an array value.');

        return array($this->field => array('$nin' => $this->value));
    }
}
