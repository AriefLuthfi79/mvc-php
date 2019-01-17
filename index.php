<?php

require_once __DIR__ . '/vendor/autoload.php';

use Bookstore\Domain\Customer\CustomerFactory;

$factory = CustomerFactory::factory('Premium', 12, 'arief', 'arief', 'arief@gmail.com');
print_r($factory->pay(1000));