<?php
use jenga\http\Request;
use jenga\template\BasicTemplate;
use icandyclothing\models\Image;
use icandyclothing\models\Product;

function index(Request $request) {
	/**
	*$product = new Product();
	$product->name = 'Ecko T-Shirt';
	$product->price = 10.00;
	$product->slug = 'my-product';
	*/
	$product = Product::objects()->filter(['_id' => new \MongoId('52640aa7bd3a03b5048b4567')]);
	count($product);
	var_dump($product);
	BasicTemplate::render('index.html', ['name'=>'Chris Santos']);
}

function image($request) {
	$image = new Image('/var/www/jenga/tests/root/static/icandyclothing/images/php-elephant.jpg');
	$image->title = 'My Image';
	$image->save();
	var_dump($image);
}

function contact(Request $request) {
	
}