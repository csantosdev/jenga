<?php
namespace Jenga\Db\Engines\Mongo\Query\Conditions;
/**
 * A mongo greater than query segment.
 *
 * @author Chris Santos
 */
class GreaterThan extends Base {

    /**
     * @inheritdoc
     */
    public function toQuery() {

        return array($this->field => array('$gt' => $this->value));
    }
}
