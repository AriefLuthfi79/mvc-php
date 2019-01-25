<?php

namespace Bookstore\Controllers;

use Bookstore\Exceptions\NotFoundException;
use Bookstore\Models\CustomerModel;

class CustomerController extends AbstractController
{
	public function login(string $email): string {
		if (!$this->request->isPost()) {
			return $this->render('login.twig', []);
		}

		$params = $this->request->getParams();

		if (!$params->has('email')) {
			$flash = ['errorMessage' => 'No info Provided.'];
			return $this->render('login.twig', $flash);
		}

		$email = $params->getString('email');
		$customerModel = new CustomerModel($this->db);

		try {
			$customer = $customerModel->getByEmail($email);
		} catch (NotFoundException $e) {
			$this->log->warn('Customer email not found: ' . $email);
			$params = ['errorMessage' => 'Email not found'];
			return $this->render('login.twig', $params);
		}

		setcookie('user', $customer->getId());
		$newController = new BookController($this->request);
		return $newController->getAll();
	}
}