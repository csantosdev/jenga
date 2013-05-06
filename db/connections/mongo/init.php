<?php
namespace Jenga\DB\Connections;

class Mongo extends Connection {

	private $host, $user, $pass, $database;
	
	public function connect($host, $user, $pass, $database) {

		if($this->resource == null) {
			$this->resource = new mysqli($host, $user, $pass, $database);
			
			if($this->resource->connect_error) {
				$this->connected = false;
				throw new \Exception('Could not connect to database via MySQLi');
			}
			
			$this->connected = true;
			
			$this->host = $host;
			$this->user = $user;
			$this->pass = $pass;
			$this->database = $database;
		}
	}

	public function disconnect() {
		$this->resource->close();
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
	
	protected function get_resource() {
		if($this->resource == null || $this->resource->ping() === false) {
			$this->connect($this->host, $this->user, $this->pass, $this->database);
			return $this->resource;
		}
	}
}
