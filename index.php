<?php

use Bookstore\Core\Request;
use Bookstore\Core\Router;
use Bookstore\Core\Config;
use Bookstore\Utils\DependencyInjector;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once __DIR__ . '/vendor/autoload.php';

$config = new Config;
$dbConfig = $config->get('db');
$db = new PDO(
	'mysql:host=mariadb;dbname=bookstore',
	$dbConfig['user'],
	$dbConfig['password']
);

$loader = new Twig_Loader_Filesystem(__DIR__ . '/views');
$twig = new Twig_Environment($loader);

$log = new Logger('bookstore');
$logFile = $config->get('log');
$log->pushHandler(new StreamHandler($logFile, Logger::DEBUG));

$di = new DependencyInjector;
$di->set('PDO', $db);
$di->set('Utils\Config', $config);
$di->set('Twig_Environment', $twig);
$di->set('Logger', $log);

$router = new Router($di);
$response = $router->route(new Request);
echo $response;