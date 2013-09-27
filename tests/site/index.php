<?php
define('JENGA_APP_PATH', __DIR__ . '/' . $_SERVER['JENGA_APP']);

if(!file_exists(JENGA_APP_PATH . '/settings.php'))
	exit("Could not find settings file: " . JENGA_APP_PATH . '/settings.php');

echo "Load bootstrap...</br>";

spl_autoload_register(function ($class) {
    require $_SERVER['JENGA_PATH'] . '/' . str_replace("\\", "/", $class) . '.php';
});

require JENGA_APP_PATH . '/settings.php';
require JENGA_APP_PATH . '/controllers.php';

use Jenga\Routing\Router;
Router::route($_SERVER['REQUEST_URI']);