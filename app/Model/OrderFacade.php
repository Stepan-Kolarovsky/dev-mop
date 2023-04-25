<?php

namespace App\Model;

use Nette;
use stdClass;

final class OrderFacade
{
	use Nette\SmartObject;

	private Nette\Database\Explorer $database;

	public function __construct(Nette\Database\Explorer $database)
	{
		$this->database = $database;
	}
	public function getAll()
	{
		return $this->database
			->table('orders')
			->order('created_at DESC');
	}
	public function prepareOrder(int $userId)
	{
		return $this->database
			->table('orders')
			->insert([
				'user_id' => $userId,
				'created_at' => new \DateTime,
				'is_finished' => 0,
			]);
	}
	public function getOrderByUserId(int $userId)
	{
		return $this->database
			->table('orders')
			->where('user_id', $userId)
			->order('created_at DESC');
	}
	public function getOrderProductByProductId(int $productId)
	{
		return $this->database
			->table('order_products')
			->where('product_id', $productId)
			->order('created_at DESC');
	}
	public function insertOrderProducts(int $orderId, int $productId)
	{
		return $this->database
			->table('order_products')
			->insert([
				'order_id' => $orderId,
				'product_id' => $productId,
				'created_at' => new \DateTime,
			]);
	}
	public function getOrderProductByUserId(int $userId)
	{
		return $this->database
			->table('order_products')
			->where('order_id', $userId)
			->order('created_at DESC');
	}

	public function getOrderProductByUserIdandProductId(int $userId, int $productId)
	{
		return $this->database
			->table('order_products')
			->where('order_id', $userId)
			->where('product_id', $productId)
			->order('created_at DESC');
	}
	public function getOrderProductByOrderId(int $orderId)
	{
		return $this->database
			->table('order_products')
			->where('order_id', $orderId)
			->order('created_at DESC');
	}
	public function getOrderProductByOrderIdandProductId(int $orderId, int $productId)
	{
		return $this->database
			->table('order_products')
			->where('order_id', $orderId)
			->where('product_id', $productId)
			->order('created_at DESC');
	}
	public function getOrderIdByUserId(int $userId)
	{
		$orderId = $this->database
			->table('orders')
			->where('user_id', $userId)
			->order('created_at DESC');
		return $orderId;
	}
	public function getOrderIdByUserIdandIsFinished(int $userId)
	{
		$orderId = $this->database
			->table('orders')
			->where('user_id', $userId)
			->where('is_finished', 0)
			->order('created_at DESC');
		return $orderId;
	}
	public function getDeleteOrderProduct(int $orderId, int $productId)
	{
		$this->database->table('order_products')->where('order_id', $orderId)->where('product_id', $productId)->delete();
	}
	public function editOrder(int $orderId, $data)
	{
		$this->database->table('orders')->where('id', $orderId)->update($data);
	}
	public function finishOrder(int $orderId)
	{
		$this->database->table('orders')->where('id', $orderId)->update(['is_finished' => 1]);
	}
	public function getOrderById(int $orderId)
	{
		$order = $this->database
			->table('orders')
			->get($orderId);
		return $order;
	}
	public function getAddressByorderUserId(int $userId)
	{
		$address = $this->database
			->table('address')
			->select('*')
			->where('user_id', $userId)->fetch();

		return $address;
	}
	public function getOrderProductByyOrderId(int $orderId)
	{
		$orderProducts = $this->database
			->table('order_products')
			->select('*')
			->where('order_id', $orderId)->fetchAll();

		return $orderProducts;
	}
	public function getOrderUser(int $userId)
	{
		$orderUser = $this->database
			->table('users')
			->select('*')
			->where('id', $userId)->fetch();

		return $orderUser;
	}
	public function completeOrder(int $orderId)
	{
		$this->database->table('orders')->where('id', $orderId)->update(['completed' => 1]);
	}
	public function deleteOrderProducts(int $orderId)
	{
		$this->database->table('order_products')->where('order_id', $orderId)->delete();
	}
	public function deleteOrder(int $orderId)
	{
		$this->database->table('orders')->where('id', $orderId)->delete();
	}
	public function getUserOpenOrderId(int $userId)
	{
		$orderId = $this->database
			->table('orders')
			->where('user_id', $userId)
			->where('is_finished', 0)
			->order('created_at DESC');
		return $orderId;
	}






	public function findPublishedOrders(): Nette\Database\Table\Selection
	{
		return
			$this->database->table('orders')
			->where('created_at < ', new \DateTime)
			->order('created_at DESC');
	}
	public function findPublishedOpenOrders(): Nette\Database\Table\Selection
	{
		return
			$this->database->table('orders')
			->where('created_at < ', new \DateTime)
			->where('is_finished', 0)
			->order('created_at DESC');
	}
	public function findPublishedClosedOrders(): Nette\Database\Table\Selection
	{
		return
			$this->database->table('orders')
			->where('created_at < ', new \DateTime)
			->where('is_finished', 1)
			->order('created_at DESC');
	}
}
