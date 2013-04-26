<?php
use Jenga\DB\Models\Model;

/**
 * Either require_once or define your models here
 */
class User extends Model {
	
	public $table_name = 'users';
	
	public function __construct() {
		parent::__construct();
		$this->username = new CharField();
		$this->password = new CharField();
	}
}

class Comment extends Model {

	public $post = array('ForeignKey', 'model'=>'Post');
	public $user = array('ForeignKey', 'model'=>'User', 'null'=>false, 'blank'=>false);
	public $title = array('CharField', 'max_length'=>100);
	public $body = 'TextField'; // or array('TextField')
	
	public $_meta = array('table_name'=>'comment');
}

class Post extends Model {
	
	public $blog = array('ForeignKey', 'model'=>'Blog');
	public $title = 'TextField';
	public $categories = array('ManyToMany', 'model'=>'Category');
}

class Blog extends Model {
	
	public $name = 'CharField';
}

class Category extends Model {
	
	public $name = 'CharField';
}