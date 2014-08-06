<?php
namespace Jenga\Db\Engines\Mongo\Query\Conditions;
/**
 * A mongo equals query segment.
 *
 * @author Chris Santos
 */
class Equals implements Condition {

    private $name;

    private $value;

    private $operator;

    /**
     * @inheritdoc
     */
    public function __construct($name, $value, $operator) {

        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function toQuery() {

        return array($this->name => $this->value);
    }
}
