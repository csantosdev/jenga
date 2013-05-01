<?php
$JENGA_SETTINGS = array(
	'models' => './models.php',
	'databases' => array(
		'default' => array(
			'host' => 'localhost',
			'user' => 'root',
			'pass' => '',
			'name' => 'test',
			'driver' => JENGA_PDO_BACKEND,
		)
	)		
);

$JENGA_DATABASES = array(
	'default' => array(
		'backend' => JENGA_MYSQL_BACKEND	
	)		
);