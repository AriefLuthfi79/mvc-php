<?php

namespace Bookstore\Models;

use Bookstore\Domain\Sale;
use Bookstore\Exceptions\NotFoundException;
use Bookstore\Exceptions\DbException;
use PDO;

class SaleModel extends AbstractModel
{
	const CLASSNAME = 'Bookstore\Domain\Sale';

	public function getByUser(int $userId): array {
		$query = 'SELECT * FROM sale WHERE customer_id = :id';
		$table = $this->db->prepare($query);
		$table->execute(['id' => $userId]);	

		return $table->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
	}

	public function get(int $saleId): Sale {
		$query = 'SELECT * FROM sale WHERE id = :id';
		$table = $this->db->prepare($query);
		$table->execute(['id' => $saleId]);

		$sales = $table->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);

		if (empty($sales)) {
			throw new NotFoundException('Sale not found');
		}
		
		$sale = array_pop($sales);
		$query = 'SELECT b.id, b.title, b.author, b.price, sb.amount AS stock, b.isbn
					 FROM sale s LEFT JOIN sale_book sb ON s.id = sb.sale_id
					 LEFT JOIN book b ON sb.book_id = b.id
					 WHERE s.id = :id';
		$table = $this->db->prepare($query);
		$table->execute(['id' => $saleId]);

		$books = $table->fetchAll(PDO::FETCH_CLASS, BookModel::CLASSNAME);
		$sale->setBooks($books);

		return $sale;
	}

	public function create(Sale $sale) {
		$this->db->beginTransaction();

		$query = 'INSERT INTO sale (customer_id, date) VALUES(:id, NOW())';

		$table = $this->db->prepare($query);
		if	(!$table->execute(['id' => $sale->getCustomerId()])) {
			$this->db->rollBack();
			throw new DbException($table->errorInfo()[2]);
		}
		$saleId = $this->db->lastInsertId();
		$query = 'INSERT INTO sale_book (sale_id, book_id, amount)
					 VALUES (:sale, :book, :amount)';
		$table = $this->db->prepare($query);
		$table->bindValue('sale', $saleId);

		foreach ($sale->getBooks() as $bookId => $amount) {
			$table->bindValue('book', $bookId);
			$table->bindValue('amount', $amount);

			if (!$table->execute()) {
				$this->db->rollback();
				throw new DbException($table->errorInfo()[2]);
			}
		}

		$this->db->commit();
	}
}