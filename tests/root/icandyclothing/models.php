<?php
namespace icandyclothing\models;

use jenga\db\models\MongoModel;
use jenga\db\fields as f;

class Image extends MongoModel {
	
	public $file = [f\ImageField, 'model'=>'jenga\\files\\ImageFile'];
	
	public $title = [f\CharField];
	public $alt = [f\CharField];
	
}

class Category extends MongoModel {
	public $name = [f\CharField];
	public $parent = [f\ForeignKey, 'model'=>'Category'];
}

class Product extends MongoModel {
	
	public $name = [f\CharField];
	public $price = [f\FloatField];
	public $slug = [f\CharField];
	public $categories = [f\ManyToMany, 'model'=>'Category'];
	
	public $_meta = ['db_config' => 'mongo'];
	
	//public $image = [f\ImageField];
}