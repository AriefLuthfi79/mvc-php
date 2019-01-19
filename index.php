<?php

require_once __DIR__ . '/vendor/autoload.php';

use Bookstore\Models\BookModel;

$db = new PDO(
	"mysql:host=mariadb;dbname=bookstore",
	"root",
	"qwerty123"
);
$book = new BookModel($db);
$book->borrowBooks($book->get(2), 1);