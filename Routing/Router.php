<?php
namespace Jenga\Routing;
use Jenga\Http\Request;

class Router {

	private static $routes;
	private static $request;

	public static function route($uri) {
		
		if(self::$routes == null)
			self::$routes = include JENGA_APP_PATH . '/urls.php'; //TODO: Do a check here

		if(self::$request == null)
			self::$request = self::getRequest();

		foreach(self::$routes as $regex => $view_func) {
			$regex = str_replace('/', '\/', $regex);
			if(preg_match('/'.$regex.'/', $uri, $matches) !== false && !empty($matches)) {
				$func_reflect = new \ReflectionFunction($view_func);
				$parameters = $func_reflect->getParameters();
				$args = [self::$request];
				unset($parameters[0]);
				foreach($parameters as $param) {
					if(!isset($matches[$param->name]))
						throw new \Exception("View function '$view_func' is expecting argument '$param->name' but was not given. Check your route regex.");
					$args[] = $matches[$param->name];
				}
				call_user_func_array($view_func, $args); //TODO: Collect a $response object and render middleware or whatever you plan to do
			}
		}
	}

	private static function getRequest() {
		$request = new Request();
		return $request;
	}
}