<?php

namespace Bookstore\Utils;

use Bookstore\Exceptions\ExceededMaxAllowedException;
use Bookstore\Exceptions\InvalidIdException;

trait Unique
{
	private static $lastId = 0;
	protected $id;

	public function setId($id) {
		if ($id < 0) {
			throw new InvalidIdException("Id cannot be negative numbers");
		}
		if (empty($id)) {
			$this->id = ++self::$lastId;
		} else {
			$this->id = $id;
			if ($id > self::$lastId) {
				self::$lastId = $id;
			}
		}
	}

	public function getLastId() {
		return $this->$lastId;
	}

	public function getId() {
		return $this->id;
	}
}