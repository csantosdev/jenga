<?php
namespace jenga\conf;

class Settings {
	
	private static $conf;

	public static function set($conf) {
		if(self::$conf !== null)
			throw new \Exception('Cannot set settings twice.');
		self::$conf = $conf;
	}

	public static function get($name) {
		if(isset(self::$conf[$name]))
			return self::$conf[$name];
		throw new \Exception("Setting '$name' is not available.");
	}
}

class Test {
	public static function go(){
		echo 'GO';
	}
}