<?php
class Model
{
	public $id = 'IntField';
	protected $_meta = array();
	
	protected static $objects;
	
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