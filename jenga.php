<?php
/**
 * Bootstrap
 */
require_once 'constants.php';
require_once 'settings.php';

require_once 'db/fields.php';
require_once 'db/models.php';
require_once 'db/query.php';
require_once 'db/managers.php';
require_once 'db/connections.php';

require_once 'helpers.php';

class Jenga {
	
	const IntField = 'IntField';
	const PositiveIntField = 'PositiveIntField';
	const CharField = 'CharField';
	const TextField = 'TextField';
	
	public static $db;
	
	public static $MODEL_FIELD_LIST = array(
		FOREIGN_KEY,
		ONE_TO_MANY,
		MANY_TO_MANY,
		CHAR_FIELD,
		TEXT_FIELD,
		INT_FIELD
	);
	
	public static $MODEL_FIELD_DICT = array(
		FOREIGN_KEY => 1,
		ONE_TO_MANY => 1,
		MANY_TO_MANY => 1,
		CHAR_FIELD => 1,
		TEXT_FIELD => 1,
		INT_FIELD => 1
	);
	
	public static $MODEL_FIELDS = array();
	
	
	public static function get_db() {
		if(static::$db != null)
			return static::$db;
		
		global $JENGA_SETTINGS;
		$db_settings = $JENGA_SETTINGS['databases']['default'];
		static::$db = new $db_settings['driver']();
		static::$db->connect($db_settings['host'], $db_settings['user'], $db_settings['pass'], $db_settings['name']);
		
		return static::$db;
	}
	
}



