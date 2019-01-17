<?php

namespace Bookstore\Domain;

use Bookstore\Domain\Payer;

interface Customer extends Payer
{
	public function getMonthlyFee();
	public function getAmountToBorrow();
	public function getType();
}