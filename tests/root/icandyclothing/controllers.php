<?php
use jenga\http\Request;
use jenga\template\BasicTemplate;
use icandyclothing\models\Image;
use icandyclothing\models\Product;
use icandyclothing\models\Category;

function index(Request $request) {
	/*$product = new Product();
	$product->name = 'Ecko T-Shirt';
	$product->price = 10.00;
	$product->slug = 'my-product';
	$product->save();
	*/
	$product = Product::objects()->get(['_id' => new MongoId('52640aa7bd3a03b5048b4567')]);
	$category = new Category();
	$category->name = 'Shoes';
	$category->save();
	
	$product->categories = [$category];
	$product->save();
	
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