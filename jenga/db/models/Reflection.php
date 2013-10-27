<?php
namespace jenga\db\models\reflection;
use jenga\conf\Settings;
use jenga\db\connections\Connection;

class Reflection {

    private static $models, $fields;

    public static function getModel($class) {

        if(isset(self::$models[$class]))
            return self::$models[$class];

        try {
            $model = new Model($class);
        } catch(\Exception $e) {
            throw new \Exception("Model '$class' does not exist. Error: " . $e->getMessage());
        }

        return self::$fields[$class] = $model;
    }

    public static function getField($class) {

        if(isset(self::$fields[$class]))
            return self::$fields[$class];

        try {
            $reflection = new \ReflectionClass($class);
        } catch(\Exception $e) {
            throw new \Exception("Field '$class' does not exist. Error: " . $e->getMessage());
        }

        self::$fields[$class] = $reflection;
        return self::$fields[$class] = $field;
    }
}

class Model extends \ReflectionClass {

    private $fields = [];
    private $meta;

    private $db_config_name = 'default';
    private $table_name;

    public function __construct($arg) {

        parent::__construct($arg);

        $properties = $this->getDefaultProperties();
        $fields = [];

        if(isset($properties['_meta'])) {
            $this->meta = $properties['_meta'];
            if(isset($this->meta['db_config_name']))
                $this->db_config_name = $this->meta['db_config_name'];
            if(isset($this->meta['table_name']))
                $this->table_name = $this->meta['table_name'];
        }

        foreach($properties as $field_name => $property) {

            if(!is_array($property) || !is_string($property[0]))
                continue;

            if(class_exists($property[0])) {

                try {
                    $field =  Reflection::getField($property[0]);
                } catch(Exception $e) {
                    continue;
                }
            }

            $this->fields[$field_name] = $field;
        }

        if($this->table_name == null) {
            if($this->inNamespace())
                $this->table_name = strtolower(str_replace(['\\models', '\\'], ['','_'], $this->table_name));
            else
                $this->table_name = strtolower($this->getName());
        }
    }

    public function getFields() {
        return $this->fields;
    }

    public function getTableName() {
        return $this->table_name;
    }

    public function getDbConfig() {
        return Settngs::get('DATABASES')[$this->db_config_name];
    }

    public function getDbConfigName() {
        return $this->db_config_name;
    }

    public function getDbConnection() {
        return Connection::get($this->db_config_name);
    }
}

class Field extends \ReflectionClass {

    private $instance;

    public function __construct($arg) {

        parent::__construct($arg);

        if(!$this->isSubclassOf(jenga\db\fields\Field))
            throw new \Exception("'" . $this->getName() . "' must be a subclass of Field.");

        $this->instance = $this->newInstance($options);
    }

    public function validate($value) {
        return $this->instance->validate($value);
    }

    public function sanitize($value) {
        return $this->instance->sanitize($value);
    }

    public function getDefaultValue() {
        return $this->instance->getDefaultValue();
    }
}