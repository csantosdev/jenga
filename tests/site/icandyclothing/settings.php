<?php
use Jenga\Conf\Settings;

$conf = [
	'DOMAIN' => 'local.icandyclothing.com',
	'DATABASES' => [
		'default' => [
			'host' => '',
			'user' => '',
			'pass' => '',
			'port' => null
		],
		'mongo' => [
			'host' => '',
			'user' => '',
			'pass' => '',
			'port' => null
		]
	]
];
$conf['BASE_URL'] = 'http:/' . $conf['DOMAIN'];
Settings::set($conf);