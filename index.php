<?php

use Bookstore\Core\Request;
use Bookstore\Core\Router;
use Bookstore\Core\Database;
use Bookstore\Core\Config;
use Bookstore\Models\BookModel;
use Bookstore\Models\SaleModel;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once __DIR__ . '/vendor/autoload.php';

$config = new Config;
$loader = new Twig_Loader_Filesystem(__DIR__ . '/views');
$twig = new Twig_Environment($loader);

$dbConfig = $config->get('db');
$db = new PDO(
	'mysql:host=mariadb;dbname=bookstore',
	$dbConfig['user'],
	$dbConfig['password']
);

$db = Database::getInstance();
