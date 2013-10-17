<?php
use jenga\http\Request;
use jenga\template\BasicTemplate;

function index(Request $request) {
	BasicTemplate::render('index.html', ['name'=>'Chris Santos']);
}

function image($request) {
	$image = new thumbnails\models\Image('/var/www/jenga/tests/root/static/icandyclothing/images/php-elephant.jpg');
	$image->title = 'My Image';
	$image->save();
	var_dump($image);
}

function contact(Request $request) {
	
}