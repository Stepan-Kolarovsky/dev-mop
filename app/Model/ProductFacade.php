<?php

namespace App\Model;

use Nette;
use Nette\Database\Table\Selection;

final class ProductFacade
{
	use Nette\SmartObject;

	private Nette\Database\Explorer $database;

	public function __construct(Nette\Database\Explorer $database)
	{
		$this->database = $database;
	}

	public function getPublicProducts(): Selection
	{
		return $this->database
			->table('products')
			->where('created_at < ', new \DateTime)
			->order('created_at DESC');
	}
	public function findPublishedProducts(): Nette\Database\Table\Selection
	{
		return
			$this->database->table('products')
			->where('created_at < ', new \DateTime)
			->order('created_at DESC');
	}

	public function getProductById(int $productId)
	{
		$product = $this->database
			->table('products')
			->get($productId);
		return $product;
	}
	public function editProduct(int $productId, $data)
	{
		$product = $this->database
			->table('products')
			->get($productId);
		$product->update($data);
		return $product;
	}
	public function insertProduct($data)
	{
		$data['created_at'] = new \DateTime;
		return $this->database
			->table('products')
			->insert($data);
	}
	public function handleDeleteProduct(int $productId)
	{
		$this->database->table('products')->where('id', $productId)->delete();
	}
	public function getCategories()
	{
		return $this->database
			->table('categories')
			->fetchPairs('id', 'name');
		$this->template->categories = $this->database;
	}
	public function getAllCategories()
	{
		return $this->database
			->table('categories');
	}
	public function getProductbyCategoryId(int $categoryId, string $orderBy = "default")
	{
		$products = $this->database
			->table('products')
			->where('category_id', $categoryId);

		if ($orderBy == "default") {
			return $products
				->order('created_at DESC');
		} elseif ($orderBy == "price_asc") {
			return $products
				->order('prize ASC');
		} elseif ($orderBy == "price_desc") {
			return $products
				->order('prize DESC');
		} elseif ($orderBy == "name_asc") {
			return $products
				->order('name ASC');
		}
	}
	public function DeleteCategory(int $categoryId)
	{
		$this->database->table('categories')->where('id', $categoryId)->delete();
	}
	public function insertCategory($data)
	{
		return $this->database
			->table('categories')
			->insert($data);
	}
	public function editCategory(int $categoryId, $data)
	{
		$category = $this->database
			->table('categories')
			->get($categoryId);
		$category->update($data);
		return $category;
	}
	public function getCategoryById(int $categoryId)
	{
		$category = $this->database
			->table('categories')
			->get($categoryId);
		return $category;
	}
}
