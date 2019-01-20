<?php

require_once __DIR__ . '/vendor/autoload.php';

use Bookstore\Models\SaleModel;
use Bookstore\Domain\Sale;

$db = new PDO(
	"mysql:host=mariadb;dbname=bookstore",
	"root",
	"qwerty123"
);