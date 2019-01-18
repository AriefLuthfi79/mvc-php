<?php

namespace Bookstore\Core;

use Bookstore\Controllers\ErrorController;
use Bookstore\Controllers\CustomerController;

class Router {
	private $routeMap;
	private static $regexPatterns = [
		'number' => '\d+',
		'string' => '\w'
	];

	public function __construct() {
		$json = file_get_contents(__DIR__ . '/config/routes.json');
		$this->routeMap = json_decode($json, true);
	}

	public function route(Request $request): string {
		$path = $request->getPath();

		foreach ($this->routeMap as $route => $infoDataType) {
			$regexRoute = $this->getRegexRoute($route, $infoDataType);
			if (preg_match("@^/$regexRoute$@", $path)) {
				return $this->executeController($route, $path, $infoDataType, $request);
			}
		}

		$errorController = new ErrorController($request);
		return $errorController->notFound();
	}

	private function getRegexRoute(string $route, array $info): string {
		if (isset($info['params'])) {
			foreach ($info['params'] as $name => $type) {
				// Replace the route
				// Example books/:id/borrow to books/\d+/borrow
				$route = str_replace(':' . $name, self::$regexPatterns[$type], $route);
			}
		}

		return $route;
	}

	private function executeController(string $route, string $path, array $info, Request $request): string {
		$controllerName = '\Bookstore\Controllers\\' . $info['controller'] . 'Controller';
		$controller = new $controllerName($request);

		if (isset($info['login'])) {
			if ($request->getCookies()->has('user')) {
				$customerId = $request->getCookies()->get('user');
				$controller->setCustomerId($customerId);
			} else {
				$errorController = new CustomerController($request);
				return $errorController->login();
			}
		}

		$params = $this->extractParams($route, $path);
		return call_user_func_array([$controller, $info['method']], $params);		
	}

	private function extractParams(string $route, string $path): array {
		$params = [];

		$pathParts = explode('/', $path);
		$routeParts = explode('/', $route);

		foreach ($routeParts as $key => $routePart) {
			if (strpos($routePart, ':')) {
				$name = substr($routePart, 1);
				$params[$name] = $pathParts[$key];
			}
		}

		return $params;
	}
}