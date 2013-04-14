<?php
require_once '../Jenga/managers.php';
require_once '../Jenga/models/fields.php';

class Model
{
	public $id = 'IntField';
	protected $_meta = array();
	
	protected static $objects;
	
	/**
	 * Related objects hide in here until they are evaluated.
	 * Then they are attached to it's parent model.
	 * @var Array
	 */
	private $related_objects = array();
	
	public function __get($name) {
		if(!isset($this->related_objects[$name]))
			return null;
		
		
	}
	
	public static function objects() {
		
		if(!isset(self::$objects))
			self::$objects = new BasicModelManager(get_called_class());
		return self::$objects;
	}
	
	public function get_table_name() {
		if(!empty($this->_meta['table_name']))
			return $this->_meta['table_name'];
		return get_class();
	}
}