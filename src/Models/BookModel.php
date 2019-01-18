<?php

namespace Bookstore\Models;

use Bookstore\Exceptions\NotFoundException;
use Bookstore\Exceptions\DbException;
use Bookstore\Domain\Book;
use PDO;

class BookModel extends AbstractModel
{
	const CLASSNAME = 'Bookstore\Domain\Book';

	public function get(int $bookId): Book {
		$query = 'SELECT * FROM book where id = :book_id';
		$table = $this->db->prepare($query);

		$table->execute(['book_id' => $bookId]);

		$books = $table->fetchAll(
			PDO::FETCH_CLASS, self::CLASSNAME
		);

		if(empty($books)) {
			throw new NotFoundException;
		}

		return $books[0];
	}

	public function getAll(int $page, int $pageLength): array {
		$start = $pageLength * ($page - 1);
		$query = 'SELECT * FROM book LIMIT :page, :length';
		$table = $this->db->prepare($query);

		$table->bindParam('page', $start, PDO::PARAM_INT);
		$table->bindParam('length', $pageLength, PDO::PARAM_INT);
		$table->execute();

		return $table->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
	}
}