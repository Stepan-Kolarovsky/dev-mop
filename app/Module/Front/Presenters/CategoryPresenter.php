<?php

declare(strict_types=1);

namespace App\Module\Front\Presenters;

use App\Model\ProductFacade;
use App\Model\UserFacade;
use App\Model\OrderFacade;
use Nette;

final class CategoryPresenter extends BasePresenter
{
	private ProductFacade $facade;
	private UserFacade $userFacade;
	private OrderFacade $orderFacade;

	public function __construct(ProductFacade $facade, UserFacade $userFacade,  OrderFacade $orderFacade)
	{
		$this->facade = $facade;
		$this->userFacade = $userFacade;
		$this->orderFacade = $orderFacade;
	}
	public function startup(): void
	{
		parent::startup();

		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect(':Front:Homepage:');
		}
	}
	public function renderCategory(int $categoryId, string $orderBy = "default"): void
	{
		$this->template->refreshNumber = rand(1, 55);
		$this->template->categoryId = $categoryId;
		$this->template->orderBy = $orderBy;
		$products = $this->facade->getProductbyCategoryId($categoryId, $orderBy);
		$this->template->products = $products;
		$this->template->profiles = $this->userFacade->getAll();
		if ($this->getUser()->isLoggedIn()) {
			if ($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch() == null or $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->is_finished == 1) {
				$this->orderFacade->prepareOrder($this->user->getIdentity()->getId());
			}
		}
		$is_finished = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->is_finished;
		$this->template->order = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch();
		$this->template->orderProducts = $this->orderFacade->getOrderProductByOrderId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
		$this->template->orderProductes = $this->orderFacade->getOrderProductByUserId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
		$this->template->categories = $this->facade->getAllCategories();
	}
	public function handleOrderProduct(int $orderId, int $productId)
	{
		if ($this->orderFacade->getOrderProductByOrderIdandProductId($orderId, $productId)->fetch() == null && $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->is_finished == 0) {
			$this->orderFacade->insertOrderProducts($orderId, $productId);
		} else {
			$this->orderFacade->getOrderProductByOrderIdandProductId($orderId, $productId)->update([
				'product_quantity' => $this->orderFacade->getOrderProductByOrderIdandProductId($orderId, $productId)->fetch()->product_quantity + 1,
			]);
		}
		$this->flashMessage('produkt přidán do košíku');
		$this->redirect('Homepage:');
	}
	public function handleDeleteOrderProduct(int $productId, int $orderId)
	{
		$orderId = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id;
		$this->template->productId = $productId;
		$this->orderFacade->getDeleteOrderProduct($orderId, $productId);
		$this->flashMessage('produkt odebrán z košíku');
		$this->redirect('Homepage:');
	}
}
