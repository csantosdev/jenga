<?php
require_once '../Pengo/managers.php';
require_once '../Pengo/models/fields.php';

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