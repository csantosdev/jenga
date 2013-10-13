<?php
use Jenga\Conf\Settings;

$conf = [
	'DOMAIN' => 'local.icandyclothing.com',
	'DATABASES' => [
		'default' => [
			'host' => 'localhost',
			'user' => '',
			'pass' => '',
			'port' => ''
		],
		'mongo' => [
			'host' => '',
			'user' => '',
			'pass' => '',
			'port' => null
		]
	],
	'INSTALLED_APPS' => [
		'Thumbnails'
	]
];
$conf['BASE_URL'] = 'http://' . $conf['DOMAIN'];
Settings::set($conf);