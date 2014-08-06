<?php
namespace Jenga\Db\Query;
/**
 * Interface for the Query object.
 *
 * Responsibilities include piecing together the query to send to the database.
 *
 * @author Chris Santos
 */
interface Query {

    public function filter();

    public function offset();

    public function limit();

    public function order();

    public function parse();
}