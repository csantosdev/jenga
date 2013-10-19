<?php
namespace jenga\db\connections;
use jenga\Helpers;
use jenga\conf\Settings;

abstract class Connection {
	
	const SQL_BACKEND_TYPE = 'sql';
	const MONGO_BACKEND_TYPE = 'mongo';
	
	const SQL_ADD_TABLE = 'CREATE TABLE %s (%s int(%d) %s %s, PRIMARY KEY (%s)) ENGINE=%s DEFAULT CHARSET=%s AUTO_INCREMENT=1 ;';
	const SQL_ALTER_TABLE = 'ALTER TABLE %s ';
	const SQL_ALTER_COLUMN_SET_NULL = 'MODIFY COLUMN %s %s SET NULL';
	const SQL_ALTER_COLUMN_SET_NOT_NULL = 'ALERT COLUMN %s SET NOT NULL';
	
	const SQL_PRIMARY_KEY = 'PRIMARY KEY (%s)';
	const SQL_ENGINE = 'ENGINE=%s';
	const SQL_DEFAULT_CHARSET = 'DEFAULT CHARSET=%s';
	const SQL_AUTOINCREMENT = 'AUTO_INCREMENT=%d';
	
	/**
	 * Object resource that connects to the actual database.
	 * @var resource
	 */
	protected $resource;
	protected $connected = false;
	
	public abstract function connect($host, $user, $pass, $database);
	
	public abstract function disconnect();
	
	
	public function add_table($args) {
		$model = Helpers::instantiate_skeleton_model($args['model']);
		$values = array(
			'table_name' => $model->get_table_name(),
			'column_name' => 'id',
			'max_length' => 11,
			'null' => 'NOT NULL',
			'auto_increment' => 'AUTO_INCREMENT',
			'primary_key' => 'id',
			'engine' => 'MyISAM',
			'default_charset' => 'utf8'
		);
		$sql = vsprintf(self::SQL_ADD_TABLE, $values);
		
		echo $sql;
		
		try {
			$statement = $this->resource->prepare($sql);
			$success = $statement->execute();
				
			if(!$success) {
				var_dump($statement->errorInfo());
				exit();
			}
				
		} catch(Exception $e) {
			exit('Could not add table. ' . $e->getMessage());
		}
	}
	
	public abstract function add_field($name, $type);
	
	public abstract function add_index();
	
	public abstract function remove_table();
	
	public abstract function remove_field();
	
	public abstract function remove_index();
	
	protected abstract function get_resource();
	
}

class PDO extends Connection {
	
	public function connect($host, $user, $pass, $database) {
		
		if($this->resource == null) {
			$config = sprintf('mysql:host=%s;dbname=%s', $host, $database);
			try {
				$this->resource = new \PDO($config, $user, $pass);
				$this->connected = true;
				
			} catch(PDOException $e) {
				$this->connected = false;
				exit('Could not connect to database via PDO. ' . $e->getMessage());
			}
		}
	}
	
	public function disconnect() {
		$this->resource = null;
	}
	
	public function add_table($model) {
		
		parent::add_table($model);
		return;
		try {
			$statement = $this->resource->prepare('CREATE TABLE :name (');
			$success = $statement->execute(array(':name'=>$name));
			
			if(!$success) {
				var_dump($statement->errorInfo());
				exit();
			}
			
		} catch(Exception $e) {
			exit('Could not add table. ' . $e->getMessage());
		}
	}
	
	public function add_field($name, $type) {
		
	}
	
	public function add_index() {
		
	}
	
	public function remove_table() {
		
	}
	
	public function remove_field() {
		
	}
	
	public function remove_index() {
		
	}
	
	public function save_model($model) {
		
		// Loop fields
		// if FK -> save in separate table
		// if M2M -> save in separate table in forloop
		
	}
	
	protected function get_resource() {}
}

class ConnectionTypeFactory {
	
	public static function get($db_config) {
		
		$DATABASES = Settings::get('DATABASES');

		if(!isset($DATABASES[$db_config]))
			throw new \Exception('There no database configuration named ' . $db_config);
			
		return $DATABASES[$db_config];
	}
}

class ConnectionFactory {
	
	private static $connections = [];
	
	public static function get($db_backend_config_name) {
		
		$DATABASES = Settings::get('DATABASES');

		if(isset(self::$connections[$db_backend_config_name]))
			return self::$connections[$db_backend_config_name];
			
		if(!isset($DATABASES[$db_backend_config_name]))
			throw new \Exception('There no database configuration named ' . $db_backend_config_name . ' in your settings file.');
		
		$config = $DATABASES[$db_backend_config_name];
		
		switch($config['type']) {
			case Connection::SQL_BACKEND_TYPE:
				$connection = new PDO();
				break;
				
			case Connection::MONGO_BACKEND_TYPE:
				$str = sprintf('mongodb://%s:%d',$config['host'], $config['port']);
				$options = [];
				if(isset($config['user']))
					$options['username'] = $config['user'];
				if(isset($config['pass']))
					$options['password'] = $config['pass'];
				if(isset($config['name']))
					$options['db'] = $config['name'];
				
				$client = new \MongoClient($str, $options);
				$connection = $client->selectDB($config['name']);
				break;
				
			default:
				throw new \Exception('There is no database connection type: ' . $config['type']);
				break;
		}
		
		return self::$connections[$db_backend_config_name] = $connection;
	}
}
