<?php
require_once '../Jenga/managers.php';
require_once '../Jenga/models/fields.php';

class Model
{
	protected static $objects;
	
	public function __construct() {
		$this->id = new PositiveIntField();
	}
	
	public static function objects() {
		
		if(!isset(self::$objects))
			self::$objects = new BasicModelManager(get_called_class());
		return self::$objects;
	}
}