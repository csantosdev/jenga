<?php
namespace Jenga\Db\Engines\Mongo\Query\Conditions;
/**
 * A mongo less than query segment.
 *
 * @author Chris Santos
 */
class LessThan extends base {

    /**
     * @inheritdoc
     */
    public function toQuery() {

        return array($this->field => array('$lt' => $this->value));
    }
}
