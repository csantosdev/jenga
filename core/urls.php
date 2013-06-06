<?php
namespace Jenga;

class Router {
	public static function route($uri) {
		global $routes;
		foreach($routes as $regex => $view_func) {
			$regex = str_replace('/', '\/', $regex);
			if(preg_match('/'.$regex.'/', $uri, $matches) !== false && !empty($matches)) {
				$func_reflect = new ReflectionFunction($view_func);
				$parameters = $func_reflect->getParameters();
				$args = array();
				foreach($parameters as $param) {
					if(!isset($matches[$param->name]))
						throw new Exception("View function '$view_func' is expecting argument '$param->name' but was not given. Check your route regex.");
					$args[] = $matches[$param->name];
				}
				call_user_func_array($view_func, $args);
			}
		}
	}
}