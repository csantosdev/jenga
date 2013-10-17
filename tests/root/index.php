<?php
define('JENGA_APP_PATH', __DIR__ . '/' . $_SERVER['JENGA_APP']);
define('JENGA_PATH', $_SERVER['JENGA_LIBRARY_PATH'] . '/jenga');
define('JENGA_BASE_PATH', __DIR__);

if(!file_exists(JENGA_APP_PATH . '/settings.php'))
	exit("Could not find settings file: " . JENGA_APP_PATH . '/settings.php');

spl_autoload_register(function ($class) {
    echo "<br>Class: " . $class;
	$path = str_replace("\\", "/", $class);
	$filename = $_SERVER['JENGA_LIBRARY_PATH'] . '/' . $path . '.php';
    // Direct
	if(file_exists($filename)) {
    	require $filename;
        echo '<br>' . $filename;
        return;
    }

    // File as Namespace Jenga\Template.php
    $path = explode('/', $path);
    if(count($path) > 1) {
        array_pop($path);
        $file = array_pop($path);
        $path[] = $file;
        $filename = $_SERVER['JENGA_LIBRARY_PATH'] . '/' . implode('/', $path) . '.php';
        if(file_exists($filename)) {
            require $filename;
            echo '<br>' . $filename;
            return;
        }
    }
    // Folder as namespace Jenga\Template\Template.php
	$path = explode('/', str_replace("\\", "/", $class));
    if(count($path) < 2) {
        echo "NOTHING";
        return;
    }
	array_pop($path);
	$path[] = $path[count($path)-1];
	$filename = $_SERVER['JENGA_LIBRARY_PATH'] . '/' . implode('/', $path) . '.php';
	    if(file_exists($filename))
		  require $filename;

    echo '<br>' . $filename;
});

// Load in Fields.php to allow aliasing an entire namespace. ie: use jenga\db\fields as f;
require $_SERVER['JENGA_LIBRARY_PATH'] . '/jenga/db/fields/fields.php';
require JENGA_APP_PATH . '/settings.php';

use jenga\conf\Settings;

// Bootstrap
foreach(Settings::get('INSTALLED_APPS') as $app) {
    $path = $_SERVER['JENGA_LIBRARY_PATH'] . '/' . $app;
    
    $init = $path . '/init.php';
    if(file_exists($init))
        require $init;

    $template_tags = $path . '/template_tags.php';
    echo $template_tags;
    if(file_exists($template_tags)) { echo "yes";
        require $template_tags; }
}

require JENGA_APP_PATH . '/controllers.php';

use jenga\routing\Router;
Router::route($_SERVER['REQUEST_URI']);