<?php
/**
 * Either require_once or define your models here
 */
require_once 'models/models.php';
require_once '../Pengo/models/fields.php';

class User extends Model {
	
	public $table_name = 'users';
	
	public function __construct() {
		parent::__construct();
		$this->username = new CharField();
		$this->password = new CharField();
	}
}

class Comment extends Model {

	private $user = array('field'=>'ForeignKey', 'model'=>'User', 'null'=>false, 'blank'=>false);
	
	private $table_name = 'comment';
	
	public function __construct() {
		parent::__construct();
		$this->user = new ForeignKey(array('model' => 'User', 'null' => false, 'blank' => false));
		$this->title = new CharField(array('max_length' => 100));
		$this->body = new TextField(null);
	}

}
