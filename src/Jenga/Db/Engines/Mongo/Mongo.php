<?php
namespace Jenga\Db\Engines\Mongo;
/**
 * MongoDB database engine.
 *
 * @author Chris Santos
 */
class Mongo implements \Jenga\Db\Engines\Engine {

    /**
     * MongoClient object.
     *
     * @var MongoClient
     */
    private $client;

    /**
     * The selected database to make queries to.
     *
     * @var MongoDB
     */
    private $database;

    private $host, $port;

    public function __construct($host, $port) {

        $this->host = $host;
        $this->port = $port;
    }

    public function connect() {

        if($this->isConnected())
            return true;

        $this->getMongoClient()->connect();
    }

    public function disconnect() {

        return $this->getMongoClient()->close(false); // TODO: This may not work
    }

    public function selectDatabase($name) {

        $this->database = $this->getMongoClient()->selectDB($name);
    }

    public function ping() {

    }

    public function isConnected() {

        if($this->client === null)
            return false;

        return $this->getMongoClient()->connected;
    }

    public function getClient() {

        return $this->client;
    }

    public function query($query) {

        if(!$this->isConnected())
            $this->connect();

        return $this->client->test->posts->find($query); // TODO FIX THIS !!!
    }

    private function getMongoClient() {

        if($this->client)
            return $this->client;

        $params = sprintf('mongodb://%s:%s', $this->host, $this->port);
        return $this->client = new \MongoClient($params);
    }
}