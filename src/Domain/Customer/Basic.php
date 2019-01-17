<?php

namespace Bookstore\Domain\Customer;

use Bookstore\Domain\Person;
use Bookstore\Domain\Customer;

class Basic extends Person implements Customer
{
	public function getMonthlyFee() {
		return 5.0;
	}

	public function getAmountToBorrow() {
		return 3;
	}

	public function getType() {
		return 'Basic';
	}

	public function pay($amount) {
		echo "Paying $amount";
	}

	public function isExtentOfTaxes(): bool {
		return false;
	}
}