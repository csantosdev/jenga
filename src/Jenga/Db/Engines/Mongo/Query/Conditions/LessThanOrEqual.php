<?php
namespace Jenga\Db\Engines\Mongo\Query\Conditions;
/**
 * A mongo less than or equal query segment.
 *
 * @author Chris Santos
 */
class LessThanOrEqual extends Base {

    /**
     * @inheritdoc
     */
    public function toQuery() {

        return array($this->field => array('$lte' => $this->value));
    }
}
