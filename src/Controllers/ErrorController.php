<?php

namespace Bookstore\Controllers;

class ErrorController extends AbstractController
{
	public function notFound(): string {
		$properties = ['errorMessage' => 'Page not found'];
		return $this->render('error.twig', $properties);
	}
}