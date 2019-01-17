<?php

namespace Bookstore\Domain\Customer;

use Bookstore\Domain\Person;
use Bookstore\Domain\Customer;

class Premium extends Person implements Customer
{
	public function getMonthlyFee() {
		return 10.0;
	}

	public function getAmountToBorrow() {
		return 10;
	}

	public function getType() {
		return 'Premium';
	}

	public function pay($amount) {
		echo "Paying $amount";
	}

	public function isExtentOfTaxes(): bool {
		return true;
	}
}