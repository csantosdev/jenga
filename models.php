<?php
/**
 * Either require_once or define your models here
 */
require_once 'models/models.php';
require_once '../Jenga/models/fields.php';

class User extends Model {
	
	public $table_name = 'users';
	
	public function __construct() {
		parent::__construct();
		$this->username = new CharField();
		$this->password = new CharField();
	}
}

class Comment extends Model {

	private static $user = array('ForeignKey', 'model'=>'User', 'null'=>false, 'blank'=>false);
	private static $title = array('CharField', 'max_length' => 100);
	private static $body = 'TextField'; // or array('TextField')
	
	private static $table_name = 'comment';
	
}
