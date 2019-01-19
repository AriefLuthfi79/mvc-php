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

	public function getByUser(int $userId): array {
		$query = 'SELECT b.* FROM borrowed_books bb LEFT JOIN book b ON bb.book_id = b.id WHERE bb.customer_id = :id';

		$table = $this->db->prepare($query);
		$table->execute(['id' => $userId]);

		return $table->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
	}

	public function search(string $title, string $author): array {
		$query = 'SELECT * FROM book WHERE title LIKE :title AND author LIKE :author';

		$table = $this->db->prepare($query);
		$table->bindValue('title', "%$title%");
		$table->bindValue('author', "%$author%");
		$table->execute();

		return $table->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
	}

	public function borrowBooks(Book $book, int $userId) {
		$query = "INSERT INTO borrowed_books (book_id, customer_id, start) VALUES (:book, :user, NOW())";

		$table = $this->db->prepare($query);
		$table->bindValue('book', $book->getId());
		$table->bindValue('user', $userId);
		
		if (!$table->execute()) {
			throw new DbException($table->errorInfo()[2]);
		}

		$this->updateBook($book);
	}

	public function returnBook(Book $book, int $userId) {
		$query = 'UPDATE borrowed_books SET end = NOW()
				  WHERE book_id = :book AND customer_id = :customer
				  AND end IS NULL';
		
		$table = $this->db->prepare($query);
		$table->bindValue('book', $book->getId());
		$table->bindValue('customer', $userId);

		if (!$table->execute()) {
			throw new DbException($table->errorInfo()[2]);
		}

		$this->updateBook($book);
	}

	private function updateBook(Book $book) {
		$query = 'UPDATE book SET stock = :stock WHERE id = :id';
		$table = $this->db->prepare($query);
		
		$table->bindValue('id', $book->getId());
		$table->bindValue('stock', $book->getStock());

		if (!$table->execute()) {
			throw new DbException($table->errorInfo()[2]);
		}
	}
}