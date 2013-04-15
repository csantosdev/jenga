<?php
class Field {
	
	protected $name;
	protected $null = true;
	protected $blank = true;
}

class RelatedField extends Field {
	
	protected $model;
}

class ForeignKey extends RelatedField {

	protected $model;
	
	public function __toString() {
		return '<ForeignKey object>';
	}

	public function __construct($args) {
		$this->model = $args['model'];
		if(isset($args['null']))
			$this->null = $args['null'];
		if(isset($args['blank']))
			$this->blank = $args['blank'];
	}
}

class ManyToMany extends RelatedField {
	
}

class OneToMany extends RelatedField {
	
}

class CharField extends Field {
	
	private $max_length = 255;
	
	public function __construct($args) {
		if(isset($args['max_length'])) {
			if( is_object($args['max_length']) || is_array($args['max_length']) )
				throw new Exception("max_length cannot be of type Object or Array.");
			$this->max_length = (string)$args['max_length'];
		}
	}
}

class TextField extends Field {

	public function __construct($args) {

	}
}

class BooleanField extends Field {
	
}

class IntField extends Field {
	
}

class FloatField extends Field {
	
}

class PositiveIntField extends Field {
	
}