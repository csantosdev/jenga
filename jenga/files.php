<?php
namespace jenga\files;
use jenga\db\fields as f;
use jenga\db\models\MongoModel;

class File extends MongoModel {
	public $title = [f\CharField];
	public $filename = [f\CharField];
	public $size = [f\IntField];
}

class Image extends Field {
	
	public $title = [f\CharField];
	public $alt = [f\CharField];
	public $width = [f\IntField];
	public $height = [f\IntField];
}