<?php

namespace Bookstore\Controllers;

use Bookstore\Models\BookModel;
use Bookstore\Exceptions\DbException;
use Bookstore\Exceptions\NotFoundException;

class BookController extends AbstractController
{
	const PAGE_LENGTH = 10;

	public function getAllWithPage($page): string {
		$page = (int) $page;
		$bookModel = new BookModel($this->db);

		$books = $bookModel->getAll($page, self::PAGE_LENGTH);

		$properties = [
			'books' => $books,
			'currentPage' => $page,
			'lastPage' => counte($books) < self::PAGE_LENGTH;
		];

		return $this->render('books.twig', $properties);
	}

	public function getAll(): string {
		return $this->getAllWithPage(1);
	}

	public function get(int $bookId): string {
		$bookModel = new BookModel($this->db);

		try {
			$book = $bookModel->get($bookId);
		} catch (\Exception $e) {
			$this->log->error(
				'Error getting book' . $e->getMessage()
			);
			$properties = ['errorMessage' => 'Book not found!'];
			return $this->render('error.twig', $properties);
		}

		$properties = ['book' => $book];
		return $this->render('book.twig', $properties);
	}

	public function getByUser(): string {
		$bookModel = new BookModel($this->db);
		$books = $bookModel->getByUser($this->customerId);

		$properties = [
			'books' => $books,
			'currentPage' => 1,
			'lastPage' => true
		]
		return $this->render('books.twig', $properties);
	}

	public function search(): string {
		$title = $this->request->getParams()->getString('title');
		$author = $this->request->getParams()->getString('author');

		$bookModel = new BookModel($this->db);
		$books = $bookModel->search($title, $author);

		$properties = [
			'books' => $books,
			'currentPage' => 1,
			'lastPage' => true
		];
		return $this->render('books.twig', $properties);
	}

	public function borrow(int $bookId): string {
		$bookModel = new BookModel($this->db);

		try {
			$book = $bookModel->get($bookId);			
		} catch (NotFoundException $e) {
			$this->log->warn('Book not found: ' . $bookId);
			$params = ['errorMessage' => 'Book not found.'];
			$this->render('error.twig', $params);
		}

		if (!$book->getCopy()) {
			$params = [
				'errorMessage' => 'There no copies left'
			];
			return $this->render('error.twig', $params);
		}

		try {
			$bookModel->borrow($book, $this->customerId);
		} catch (DbException $e) {
			$this->log->error('Error borrowing book ' . $e->getMessage());
			$params = [
				'errorMessage' => 'Error borrowing book'
			];
			return $this->render('error.twig', $params);
		}

		return $this->getByUser();
	}

	public function returnBook(int $bookId): string {
		$bookModel = new BookModel($this->db);

		try {
			$book = $bookModel->get($bookId);
		} catch (NotFoundException $e) {
			$this->log->warn('Book not found ' . $e->getMessage());
			$params = [
				'errorMessage' => 'Error book not found'
			];
			return $this->render('error.twig', $params);
		}

		$book->addCopy();

		try {
			$bookModel->returnBook($book, $this->customerId);
		} catch (DbException $e) {
			$this->log->error('Error returning book ' . $e->getMessage());
			$params = [
				'errorMessage' => 'Error returning book'
			];
			return $this->render('error.twig', $params);
		}

		return $this->getByUser();
	}
}