<?php
namespace Jenga\Db\Query;
/**
 * Interface for the QuerySet object.
 *
 * Responsible for managing the interaction with the model data as an array.
 *
 * @author Chris Santos
 */
interface QuerySet {

    public function filter();

    public function offset();

    public function limit();

    public function order();

    public function fetch();

}