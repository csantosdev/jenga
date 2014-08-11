<?php
namespace Jenga\Db\Engines\Mongo\Query\Conditions;
/**
 * A mongo in query segment.
 *
 * @author Chris Santos
 */
class In extends Base {

    /**
     * @inheritdoc
     */
    public function toQuery() {

        if(!is_array($this->value))
            throw new \Exception('When using an $in conditions, you must provide an array value.');

        return array($this->field => array('$in' => $this->value));
    }
}
