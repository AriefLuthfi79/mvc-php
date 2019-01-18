<?php

namespace Bookstore\Models;

use Bookstore\Domain\Customer;
use Bookstore\Domain\Customer\CustomerFactory;
use Bookstore\Exceptions\NotFoundException;

class CustomerModel extends AbstractModel
{
	public function get(int $userId): Customer {
		$query = 'SELECT * FROM customer WHERE id = :id';
		$table = $this->db->prepare($query);
		$table->execute(['id' => $userId]);

		$row = $table->fetch();

		if (empty($row)) {
			throw new NotFoundException();
		}

		return CustomerFactory::factory(
			$row['type'],
			$row['id'],
			$row['firstname'],
			$row['surname'],
			$row['email']
		);
	}

	public function getByEmail(string $userEmail): Customer {
		$query = 'SELECT * FROM customer WHERE email = :user';
		$table = $this->db->prepare($query);
		$table->execute(['user' => $userEmail]);

		$row = $table->fetch();

		if (empty($row)) {
			throw new NotFoundException();
		}

		return CustomerFactory::factory(
			$row['type'],
			$row['id'],
			$row['firstname'],
			$row['surname'],
			$row['email']
		);
	}
}