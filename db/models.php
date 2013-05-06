<?php
namespace Jenga\DB\Models;
use Jenga\DB\Managers\BasicModelManager;
use Jenga\DB\Fields as Fields;
use Jenga\Helpers;

const Field = 'Jenga\\DB\\Fields\\Field';
const IntField = 'Jenga\\DB\\Fields\\IntField';
const PositiveIntField = 'Jenga\\DB\\Fields\\PositiveIntField';
const CharField = 'Jenga\\DB\\Fields\\CharField';
const TextField = 'Jenga\\DB\\Fields\\TextField';

class Model
{
	public $id = array(PositiveIntField);
	public $_meta = array();
	
	private $child_class;
	private $fields = array();
	
	protected static $objects;
	private static $reflection_classes = array();
	
	/**
	 * Set any Jenga fields to null.
	 */
	public function __construct() {
		
		$class_name = get_class($this);
		if(isset(self::$reflection_classes[$class_name]))
			$reflection = self::$reflection_classes[$class_name];
		else {
			$reflection = new \ReflectionClass($this);
			self::$reflection_classes[$class_name] = $reflection;
		}
		
		$properties = $reflection->getDefaultProperties();
		
		foreach($properties as $field_name => $field) {
			$field_class_name = Helpers::get_field_type($field);
				
			if($field_class_name !== null) {
				$this->$field_name = null;
				$this->fields[$field_name] = $field;
				$this->_meta['fields'][$field_name] = &$this->$field_name;
			}
		}
		
		$this->child_class = $class_name;
	}
	
	public static function objects() {
		
		if(!isset(self::$objects))
			self::$objects = new BasicModelManager(get_called_class());
		return self::$objects;
	}
	
	public function get_table_name() {
		if(!empty($this->_meta['table_name']))
			return $this->_meta['table_name'];
		return strtolower(get_called_class());
	}
	
	/**
	 * Validate fields and then pass to the db object to save
	 */
	public function save() {
		
		$new = false;
		if($this->id == null) {
			$new = true;
			$this->id = 1; // To pass validation
		}
		
		foreach($this->fields as $field_name => $field) {
			
			$field_class = $field[0];
			$field_class::validate($this->$field_name); // Throws Exceptions
			
		}	
			
		$db = \Jenga::get_db();
		
		if($new) {
		}
	}
}