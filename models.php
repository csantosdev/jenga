<?php
use Jenga\DB\Models as m;

/**
 * Either require_once or define your models here
 */
class User extends m\Model {
	
	public $table_name = 'users';
	
	public function __construct() {
		parent::__construct();
		$this->username = new CharField();
		$this->password = new CharField();
	}
}

class Comment extends m\Model {

	public $post = array('ForeignKey', 'model'=>'Post');
	public $user = array('ForeignKey', 'model'=>'User', 'null'=>false, 'blank'=>false);
	public $title = array('CharField', 'max_length'=>100);
	public $body = 'TextField';
	
	public $_meta = array('table_name'=>'comment');
}

class Post extends m\Model {
	
	public $blog = array('ForeignKey', 'model'=>'Blog');
	public $title = 'TextField';
	public $categories = array('ManyToMany', 'model'=>'Category');
}

class Blog extends m\Model {
	
	public $name = 'CharField';
}

class Category extends m\Model {
	
	public $name = 'CharField';
}

class Site extends m\Model {
	
	public $name = array(m\CharField, 'default'=>'iCandy Clothing');
	public $description = array(m\TextField);
	
	public $has_rendered = false;
}