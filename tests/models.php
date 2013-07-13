<?php
use Jenga\DB\Fields as f;
use Jenga\DB\Models\MongoModel;

class FeedInstruction extends MongoModel {

	public $attribute = [f\ForeignKey, 'model' => 'Attribute', 'null' => true];
	public $required = [f\BooleanField, 'default' => false];
	public $multiple = [f\BooleanField, 'default' => false];
	public $fields = [f\EmbeddedDocumentField, 'type' => f\ArrayType];

	public $_meta = [
		'db_config' => 'mongo'
	];
}

class Account extends MongoModel {
	
	public $name = [f\CharField];
	public $email = [f\CharField];
	
	public $_meta = [
		'db_config' => 'mongo'
	];
	
}
class Attribute extends MongoModel {

	public $name = [f\CharField];

	public $_meta = [
		'db_config' => 'mongo'
	];

}

class Datafeed extends MongoModel {

	public $account = [f\ForeignKey, 'model' => 'Account'];
	public $merchant = [f\ForeignKey, 'model' => 'Merchant'];
	public $feed_instructions = [
		f\EmbeddedDocumentField,
		'type' => f\ArrayType,
		'models' => ['PullInstruction', 'SearchInstruction', 'CategoryInstruction']];
	
	public $_meta = [
		'db_config' => 'mongo'
	];
}

class Merchant extends MongoModel {
	
	public $name = [f\CharField];
	
	public $_meta = [
		'db_config' => 'mongo'
	];
}

class PullInstruction extends FeedInstruction {
	
	// Has different functions here
} 

class SearchInstruction extends FeedInstruction {
	
	// Again different functionality here
}

class CategoryInstruction extends FeedInstruction {
	
	// ...
}