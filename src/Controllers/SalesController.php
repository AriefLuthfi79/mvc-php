<?php

namespace Bookstore\Controllers;

use Bookstore\Domain\Sale;
use Bookstore\Models\SaleModel;

class SalesController extends AbstractController
{
	public function add(int $id): string {
		$bookId = (int) $id;
		$saleModel = new SaleModel($this->db);

		$sale = new Sale;
		$sale->setCustomerId($this->customerId);
		$sale->addBooks($bookId);

		try {
			$saleModel->create($sale);
		} catch (\Exception $e) {
			$properties = [
				'errorMessage' => 'Error buying the book'
			];
			$this->log->error(
				'Error buying book ' . $e->getMessage()
			);
			return $this->render('error.twig', $properties);
		}

		return $this->getByUser();
	}
	public function getByUser(): string {
		$saleModel = new SaleModel($this->db);
		$sales = $saleModel->getByUser($this->customerId);

		$properties = ['sales' => $sales];
		return $this->render('sales.twig', $properties);
	}

	public function get($saleId): string {
		$saleModel = new SaleModel($this->db);
		$sale = $saleModel->get($saleId);

		$properties = ['sale' => $sale];
		return $this->render('sale.twig', $properties);
	}
}