<?php
namespace Jenga\Db\Engines\Mongo\Query\Conditions;
/**
 * A mongo base query segment.
 *
 * @author Chris Santos
 */
abstract class Base implements Condition {

    protected $field;

    protected $value;

    protected $operator;

    /**
     * @inheritdoc
     */
    public function __construct($field, $value, $operator) {

        $this->field = $field;
        $this->value = $value;
        $this->operator = $operator;
    }

    /**
     * @inheritdoc
     */
    abstract public function toQuery();
}
