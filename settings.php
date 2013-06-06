<?php
$JENGA_SETTINGS = array(
	'models' => './models.php',
	'databases' => array(
		'default' => array(
			'host' => 'localhost',
			'user' => 'root',
			'pass' => '',
			'name' => 'test',
			'driver' => JENGA_MYSQL_BACKEND,
		)
	)		
);

$JENGA_DATABASES = array(
	'default' => array(
		'backend' => JENGA_MYSQL_BACKEND	
	)		
);

$_404_HANDLER = null;
$_500_HANDLER = null;