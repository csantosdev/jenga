<?php
/**
 * Bootstrap
 */
require_once 'constants.php';
require_once 'db/fields.php';
require_once 'db/models.php';
require_once 'db/query.php';
require_once 'db/managers.php';

class Jenga {
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
}

