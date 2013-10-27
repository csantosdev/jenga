<?php
namespace jenga\db\connections;
use jenga\conf\Settings;

const MYSQL_CONNECTION_TYPE = 'jenga\db\connections\MysqlConnection';
const MONGO_CONNECTION_TYPE = 'jenga\db\connections\MongoConnection';

abstract class Connection {

    private static $connections = [];
    protected $query_builder;
    protected $model_builder;

    protected $db;
    protected $options;

    //public function getModelManager();
    abstract public function getQueryBuilder();
    abstract public function getModelBuilder();
    abstract protected function createDatabaseObject();

    public function __construct($options) {
        $this->options = $options;
    }

    /**
     * Returns actual driver objects for that database. ie: MongoClient, MySQLi object
     * @return mixed
     */
    public static function get($config_name) {

        $databases = Settings::get('DATABASES');

        if(!isset($databases[$config_name]))
            throw new \Exception("Database configuration name '$config_name' does not exist. Check your app's DATABASES setting in the settings.php file.");

        if(isset(self::$connections[$config_name]))
            return self::$connections[$config_name];

        $db_config = $databases[$config_name];
        $db_connection_class = $db_config['type'];

        $connection = $db_connection_class($db_config);
        return self::$connections[] = $connection;
    }

    public function getDatabaseObject() {

        if(isset($this->id))
            return $this->db;

        return $this->db = $this->createDatabaseObject();
    }
}

class MysqlConnection extends Connection {

    public function getQueryBuilder() {

        if(isset(self::$query_builder))
            return self::$query_builder;

        return self::$query_builder = new jenga\db\query\builders\SQLQueryBuilder();
    }

    public function getModelBuilder() {

        if(isset(self::$model_builder))
            return self::$model_builder;

        return self::$model_builder = new jenga\db\models\builders\SQLModelBuilder();
    }

    public function createDatabaseObject() {
        throw new \Exception('MySQL DB Object is not implemented yet.');
    }
}

class MongoConnection extends Connection {

    public function getQueryBuilder() {

        if(isset(self::$query_builder))
            return self::$query_builder;

        return self::$query_builder = new jenga\db\query\builders\MongoQueryBuilder();
    }

    public function getModelBuilder() {

        if(isset(self::$model_builder))
            return self::$model_builder;

        return self::$model_builder = new jenga\db\models\builders\MongoModelBuilder();
    }

    public function createDatabaseObject() {

        $conf = $this->options;

        $str = sprintf('mongodb://%s:%d',$conf['host'], $conf['port']);
        $options = [];
        if(isset($conf['user']))
            $options['username'] = $conf['user'];
        if(isset($conf['pass']))
            $options['password'] = $conf['pass'];
        if(isset($conf['name']))
            $options['db'] = $conf['name'];

        $client = new \MongoClient($str, $options);
        return $client->selectDB($conf['name']);
    }
}