<?php
namespace Jenga\Db\Engines\Mongo\Query\Conditions;
/**
 * A mongo less than or equal query segment.
 *
 * @author Chris Santos
 */
class LessThanOrEqual extends base {

    /**
     * @inheritdoc
     */
    public function toQuery() {

        return array($this->field => array('$lte' => $this->value));
    }
}
