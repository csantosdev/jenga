<?php
namespace Jenga\Db\Engines\Mongo\Query;

use Jenga\Configurations\Database;

class QuerySet implements \ArrayAccess, \Iterator, \Countable {

    private $order_by = array();

    private $index = 0;

    private $models = array();

    private $data;

    private $query;

    public function __construct(Query &$query) {

        $this->query = $query;
    }

    public function filter($conditions) {

        $qs = clone $this;
        $qs->query->addFilter($conditions);
        return $qs;
    }

    public function offsetExists($offset) {

        $this->fetch();
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset) {

        return $this->getModelInstance($offset);
    }

    public function offsetSet($offset, $value) {

        throw new \Exception('You are not allowed to set an item of a QuerySet once it has been accessed.');
    }

    public function offsetUnset($offset) {

        throw new \Exception('You are not allowed to unset an item of a QuerySet once it has been accessed.');
    }

    public function offset($num) {

        $this->query->offset($num);
        return clone $this;
    }

    public function limit($num) {

        $this->query->limit($num);
        return clone $this;
    }

    public function rewind() {

        $this->fetch();
        $this->index = 0;
    }

    public function current() {

        return $this->getModelInstance($this->index);
    }

    public function key() {

        return $this->index;
    }

    public function next() {

        ++$this->index;
    }

    public function valid() {

        return isset($this->data[$this->index]);
    }

    public function count() {

        $this->fetch();
        return count($this->data);
    }

    public function contain() {

        $this->query->addForeignKeyModels(func_get_args());
        return clone $this;
    }

    public function order_by() {

        $this->query->order_by(func_get_args());
        return clone $this;
    }

    public function toArray() {

        $this->fetch();

        $arr = array();
        foreach($this->data as &$data)
            $arr[] =& $data[$this->query->getModel()->name];

        return $arr;
    }

    /**
     * Returns the total number of rows for this query. This does not take offset
     * or limit into effect.
     *
     * @returns int
     */
    public function getTotal() {

    }

    private function fetch() {

        if($this->data)
            return;

        $query = $this->query->build();
        $cursor = Database::getDatabaseEngine('mongo')->query($query); // TODO: This conf 'mongo' should not be hardcoded. Should come from json conf.

        foreach($cursor as $doc)
            $this->data[] = $doc;
    }

    private function getModelInstance($index) {

        $this->fetch();

        /*
        if(!isset($this->models[$index]))
            $this->models[$index] = new ProxyModel($this->query->getModel(), $this->query->getModel()->alias, $this->data[$index]);
        */

        if(!isset($this->data[$index]))
            foreach($this->cursor as $doc)
                $this->data[] = $doc;

        return $this->data[$index];
    }
}