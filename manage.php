<?php
require_once 'jenga.php';

$options = array(
	'schemamigration',
	'--auto',
	'--settings'	
);

$input = array();
foreach($argv as $arg) {
	
	$cleaned = clean_arg($arg);
	if(is_array($cleaned))
		$input[$cleaned[0]] = $cleaned[1];
	else
		$input[$cleaned] = true;
}

$command = $argv[1];

switch($command) {
	case 'schemamigration':
		if(isset($input['--auto']))
			echo '....setting auto';
		$classes = file_get_php_classes($JENGA_SETTINGS['models']);
		update_database_schema($classes);
		break;
	
	default:
		exit('Unknown command: ' . $command);
}

function file_get_php_classes($filepath) {
	$php_code = file_get_contents($filepath);
	$classes = get_php_classes($php_code);
	return $classes;
}

function get_php_classes($php_code) {
	$classes = array();
	$tokens = token_get_all($php_code);
	$count = count($tokens);
	for ($i = 2; $i < $count; $i++) {
		if (   $tokens[$i - 2][0] == T_CLASS
				&& $tokens[$i - 1][0] == T_WHITESPACE
				&& $tokens[$i][0] == T_STRING) {

			$class_name = $tokens[$i][1];
			$classes[] = $class_name;
		}
	}
	return $classes;
}

function clean_arg($key) {
	if(strstr($key, '=')) {
		$a = explode('=', $key);
		$a[1] = str_replace(array('"', "'"), '', $a[1]);
		return $a;
	}
	return $key;
}

function update_database_schema($classes) {
	global $JENGA_SETTINGS;
	require_once $JENGA_SETTINGS['models'];
	$tables = array();
	
	foreach($classes as $class) {
		$reflection_class = new ReflectionClass($class);
		$fields = $reflection_class->getDefaultProperties();

		if($reflection_class->getParentClass()->getName() !== 'Jenga\DB\Models\Model')
			continue;
		
		$tables[$class] = array('fields' => array());
		
		foreach($fields as $field => $value) {
			$field_type = Jenga\Helpers::get_field_type($value);
			
			if(isset(Jenga::$MODEL_FIELD_DICT[$field_type])) {
				$tables[$class]['fields'][$field] = $value;
			}
		}
	}
	
	var_dump($tables);
	
	$db = Jenga::get_db();
}
?>
