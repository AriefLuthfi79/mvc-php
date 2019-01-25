<?php

namespace Bookstore\Core;

use Bookstore\Core\Config;
use PDO;

class Database
{
	private static $instance;

	public function connect(): PDO {
		$dbConfig = Config::getInstance()->get('db');
		return new PDO(
			'mysql:host=mariadb;dbname=bookstore',
			$dbConfig['user'],
			$dbConfig['password']
		);
	}

	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = self::connect();
		}
		return self::$instance;
	}
}