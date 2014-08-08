<?php
namespace Jenga\Db\Engines\Mongo\Query\Conditions;
/**
 * A mongo greater than or equals query segment.
 *
 * @author Chris Santos
 */
class GreaterThanOrEqual extends base {

    /**
     * @inheritdoc
     */
    public function toQuery() {

        return array($this->field => array('$gte' => $this->value));
    }
}
