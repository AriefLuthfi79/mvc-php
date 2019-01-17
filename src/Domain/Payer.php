<?php

namespace Bookstore\Domain;

interface Payer
{
	public function pay($amount);
	public function isExtentOfTaxes();
}