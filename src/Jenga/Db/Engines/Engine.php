<?php
namespace Jenga\Db\Engines;
/**
 * Backend interface which describes how a database driver should be behave.
 *
 * @author Chris Santos
 */
interface Engine {


    public function __construct($host, $port);

    public function connect();

    public function disconnect();

    public function selectDatabase($name);

    public function ping();

    public function isConnected();

    public function getClient();

    public function query($query);
}
