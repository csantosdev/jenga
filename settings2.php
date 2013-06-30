<?php
namespace Jenga;
use Jenga\DB\Connections\Connection;

class Settings {
	
	public static $DATABASES = [
		'mongo' => [
			'type' => Connection::MONGO_BACKEND_TYPE,
			'host' => 'localhost',
			'port' => 27017,
			'name' => 'test',
			'user' => 'admin',
			'pass' => 'admin',
		],
		'default' => [
			'type' => Connection::SQL_BACKEND_TYPE,
			'name' => 'test',
			'host' => 'localhost',
			'user' => 'root',
			'pass' => ''
		]
	];
}