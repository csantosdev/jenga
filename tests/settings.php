<?php
require '../jenga.php';

class Settings extends JengaSettings {
	
	public $DATABASES = [
		'default' => [
			'host' => 'localhost',
			'user' => 'root',
			'pass' => '',
			'type' => 'mysql'
		]
	];
}