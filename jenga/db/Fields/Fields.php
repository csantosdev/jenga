<?php
namespace Jenga\DB\Fields;

/* Will work when running on PHP5.5+
const Field = Field::class;
const RelatedField = RelatedField::class;
const NumberField = NumberField::class;
const IntField = IntField::class;
const PositiveIntField = PositiveIntField::class;
const CharField = CharField::class;
const TextField = TextField::class;
const BooleanField = BooleanField::class;
const ForeignKey = ForeignKey::class;
const ManyToMany = ManyToMany::class;

const EmbeddedDocumentField = EmbeddedDocumentField::class;
*/

// Done this way got PHP5.4 (for now)
const Field = 'Jenga\\DB\\Fields\\Field';
const RelatedField = 'Jenga\\DB\\Fields\\RelatedField';
const NumberField = 'Jenga\\DB\\Fields\\NumberField';
const IntField = 'Jenga\\DB\\Fields\\IntField';
const PositiveIntField = 'Jenga\\DB\\Fields\\PositiveIntField';
const CharField = 'Jenga\\DB\\Fields\\CharField';
const TextField = 'Jenga\\DB\\Fields\\TextField';
const BooleanField = 'Jenga\\DB\\Fields\\BooleanField';
const ForeignKey = 'Jenga\\DB\\Fields\\ForeignKey';
const ManyToMany = 'Jenga\\DB\\Fields\\ManyToMany';

const EmbeddedDocumentField = 'Jenga\\DB\\Fields\\EmbeddedDocumentField';

const ArrayType = 'Array';
const ObjectType = 'Object';

class Field {
	
	private $properties = array(
		'null' => true,
		'blank' => true
	);
	
	public function __get($name) {
		if(isset($this->properties[$name]))
			return $this->properties[$name];
		return null;
	}
	
	public function has_default() {
		if(isset($this->default))
			return true;
		return false;
	}
	
	public function has_max_length() {
		if(isset($this->max_length))
			return $this->max_length;
		return false;
	}
	
	public function is_null() {
		if(isset($this->null) && $this->null === true)
			return true;
		return false;
	}
	
	public function is_blank() {
		if(isset($this->blank) && $this->blank === true)
			return true;
		return false;
	}
}

class NumberField extends Field {
	public $max_length = 11;
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
	
	public static function validate($value) {
		
	}
}

class TextField extends CharField {

	public function __construct($args) {
		$this->properties = $args;
		
		if($this->has_default())
			echo "I have a default: " . $this->default;
	}
	
	public static function validate($value) {
	
	}
}

class BooleanField extends Field {
	
}

class IntField extends NumberField {
	
	public static function validate($value) {
		if(!is_numeric($value) || !is_int($value))
			throw new \Exception($value . ' is not of type Int');
		return true;
	}
	
}

class FloatField extends NumberField {
	
}

class PositiveIntField extends IntField {

}

class EmbeddedDocumentField extends Field {
	
}