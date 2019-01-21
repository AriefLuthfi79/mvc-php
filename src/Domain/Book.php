<?php

namespace Bookstore\Domain;

class Book
{
	private $id;
	private $isbn;
	private $title;
	private $author;
	private $stock;
	private $price;

	public function getId(): int {
		return $this->id;
	}

	public function getIsbn(): string {
		return $this->isbn;
	}

	public function getTitle(): string {
		return $this->title;
	}

	public function getAuthor(): string {
		return $this->author;
	}

	public function getStock(): int {
		return $this->stock;
	}

	public function getCopy(): bool {
		if ($this->stock > 1) {
			$this->stock--;
			return true;
		}
		return false;
	}

	public function addCopy() {
		return $this->stock++;
	}

	public function getPrice(): float {
		return $this->price;
	}
}