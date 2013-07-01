<?php
use Jenga\DB\Models\Model;
use Jenga\DB\Models\MongoModel;
use Jenga\DB\Fields as f;

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
	public $body = 'TextField';
	
	public $_meta = array('table_name'=>'comment');
}

class Post extends Model {
	
	public $blog = array(f\ForeignKey, 'model'=>'Blog');
	public $title = array(f\TextField);
	public $categories = array(f\ManyToMany, 'model'=>'Category');
}

class Blog extends Model {
	
	public $name = array(f\TextField);
}

class Category extends Model {
	
	public $name = 'CharField';
}

class Site extends MongoModel {
	
	public $name = array(f\CharField, 'default'=>'iCandy Clothing');
	public $description = array(f\TextField);
	public $active = array(f\BooleanField, 'default' => true);
	public $_meta = [
		'db_config' => 'mongo'];
	
	public $has_rendered = false;
}

class MongoPost extends MongoModel {
	
	public $blog = array(f\ForeignKey, 'model'=>'MongoBlog');
	public $title = array(f\TextField);
	public $categories = array(f\ManyToMany, 'model'=>'Category');
	public $_meta = [
		'db_config' => 'mongo'];
}

class MongoBlog extends MongoModel {
	
	public $name = array(f\CharField);
	public $site = array(f\ForeignKey, 'model' => 'Site');
	public $_meta = [
		'db_config' => 'mongo'];
}