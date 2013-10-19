<?php
namespace jenga\files;
use jenga\db\fields as f;

class File {
	public $title = [f\CharField];
	public $filename = [f\CharField];
	public $size = [f\IntField];
}

class Image extends File {

	public $alt = [f\CharField];
	public $width = [f\IntField];
	public $height = [f\IntField];
}