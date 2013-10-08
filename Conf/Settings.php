<?php
namespace Jenga\Conf;

class Settings {
	
	private static $conf;

	public static function set($conf) {
		if(self::$conf !== null)
			throw new \Exception('Cannot set settings twice.');

		$defaults = [
			'DATABASES' => [],
			'TEMPLATE_DEBUG' => false,
			'TEMPLATE_DIR' => JENGA_APP_PATH . '/templates/',
			'TEMPLATE_COMPILE_DIR' => JENGA_APP_PATH . '/templates/_compiled/',
			'INSTALLED_APPS' => []
		];

		foreach($conf as $k => $v)
			$defaults[$k] = $v;

		self::$conf = $defaults;
	}

	public static function get($name) {
		if(isset(self::$conf[$name]))
			return self::$conf[$name];
		throw new \Exception("Setting '$name' is not available.");
	}
}