<?php

namespace Bookstore\Controllers;

use Bookstore\Core\Config;
use Bookstore\Core\Database;
use Bookstore\Core\Request;
use Monolog\Logger;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Monolog\Handler\StreamHandler;

abstract class AbstractController
{
	protected $request;
	protected $db;
	protected $config;
	protected $view;
	protected $log;
	protected $customerId;

	public function __construct(Request $request) {
		$this->request = $request;
		$this->db = Database::getInstance();
		$this->config = Config::getInstance();

		$loader = new Twig_Loader_Filesystem(__DIR__ . 'views');
		$this->view = new Twig_Environment($loader);

		$this->log = new Logger('bookstore');
		$logFile = $this->config->get('log');
		$this->log->pushHandler(
			new StreamHandler($logFile, Logger::DEBUG)
		);
	}

	public function setCustomerId(int $customerId) {
		$this->customerId = $customerId;
	}

	protected function render(string $template, array $params): string {
		return $this->view->loadTemplate($template)->render($params);
	}
}