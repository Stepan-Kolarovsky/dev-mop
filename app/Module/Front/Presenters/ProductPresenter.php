<?php

namespace App\Module\Front\Presenters;

use App\Model\ProductFacade;
use App\Model\UserFacade;
use App\Model\OrderFacade;
use Nette;

final class ProductPresenter extends BasePresenter
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
    public function renderShow(int $productId): void
	{

		$product = $this->facade->getproductbyID($productId);


		if (!$product) {
			$this->error('Stránka nebyla nalezena');
		}
		$this->template->product = $product;
		if ($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->is_finished == 1) {
			$this->orderFacade->prepareOrder($this->user->getIdentity()->getId());
		}
		$this->template->order = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch();
		$this->template->orderProducts = $this->orderFacade->getOrderProductByOrderId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
		$this->template->categories = $this->facade->getAllCategories();
	}


	public function handleOrderProduct(int $orderId, int $productId)
	{
		if ($this->orderFacade->getOrderProductByUserId($orderId)->fetch() == null or $this->orderFacade->getOrderProductByProductId($productId)->fetch() == null && $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->is_finished == 0) {
			$this->orderFacade->insertOrderProducts($orderId, $productId);
		}
		else{
			$this->orderFacade->getOrderProductByProductId($productId)->update([
				'product_quantity' => $this->orderFacade->getOrderProductByProductId($productId)->fetch()->product_quantity + 1,
			]);
		}
		$this->flashMessage('produkt přidán do košíku');
		$this->redirect('Product:show', $productId);
	}
	public function handleDeleteOrderProduct(int $productId, int $orderId)
	{
		$orderId = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id;
		$this->template->productId = $productId;
		$this->orderFacade->getDeleteOrderProduct($orderId, $productId);
		$this->flashMessage('produkt odebrán z košíku');
		$this->redirect('Product:show', $productId);
	}

	
    
}