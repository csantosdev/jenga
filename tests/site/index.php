<?php
define('JENGA_APP_PATH', __DIR__ . '/' . $_SERVER['JENGA_APP']);
define('JENGA_PATH', $_SERVER['JENGA_PATH'] . '/Jenga');
define('JENGA_LIBS_PATH', JENGA_PATH . '/Libs');

if(!file_exists(JENGA_APP_PATH . '/settings.php'))
	exit("Could not find settings file: " . JENGA_APP_PATH . '/settings.php');

spl_autoload_register(function ($class) {
	$path = str_replace("\\", "/", $class);
	$filename = $_SERVER['JENGA_PATH'] . '/' . $path . '.php';
	if(file_exists($filename))
    	require $filename;
    else {
    	$path = explode('/', $path);
        if(count($path) < 2)
            return;
    	array_pop($path);
    	$path[] = $path[count($path)-1];
    	$filename = $_SERVER['JENGA_PATH'] . '/' . implode('/', $path) . '.php';
   	    if(file_exists($filename))
    		require $filename;
    }
});

require JENGA_APP_PATH . '/settings.php';

use Jenga\Conf\Settings;

// Bootstrap
foreach(Settings::get('INSTALLED_APPS') as $app) {
    $path = realpath(JENGA_APP_PATH . '/../' . $app);
    
    $init = $path . '/init.php';
    if(file_exists($init))
        require $init;

    $template_tags = $path . '/template_tags.php';
    if(file_exists($template_tags))
        require $template_tags;
}

require JENGA_APP_PATH . '/controllers.php';

use Jenga\Routing\Router;
Router::route($_SERVER['REQUEST_URI']);