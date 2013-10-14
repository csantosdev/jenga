<?php
namespace jenga\Template;
use jenga\http\Response;
use jenga\conf\Settings;

abstract class BaseTemplate {

	protected static $engine;
	protected static $tag_functions = [];

	abstract protected static function init();
	abstract public static function render($template, $context=[]);

	public static function getEngine() {
		return self::$engine;
	}

	public static function registerTag($name, $function_name) {

		if(isset(self::$tag_functions[$name]))
			throw new \Exception("Tag function '$name' is already registered.");

		self::$tag_functions[$name] = $function_name;

		if(!isset(self::$engine)) 
			static::init();

		self::$engine->registerPlugin('function', $name, $function_name);
	}
}

class BasicTemplate extends BaseTemplate {

	public static function render($template, $context=[]) {
		if(!isset(self::$engine)) 
			self::init();

		foreach($context as $k => $v)
			self::$engine->assign($k, $v);

		return self::$engine->display($template);
	}

	public static function renderToResponse($template, $context) {
		$response = new Response();
		try {
			$response->body = self::render($template, $context);
		
		} catch(Exception $e) {

		}

		return $response;
	}

	protected static function init() {
			require JENGA_LIBS_PATH . '/Smarty/Smarty.class.php';
			self::$engine = new \Smarty();
			self::$engine->debugging = Settings::get('TEMPLATE_DEBUG');
			self::$engine->setTemplateDir(Settings::get('TEMPLATE_DIR'));
			self::$engine->setCompileDir(Settings::get('TEMPLATE_COMPILE_DIR'));
	}
}