<?php

namespace App\Module\Front\Presenters;


use Nette;
use Nette\Application\UI\Form;
use App\Model\ProductFacade;
use App\Model\UserFacade;
use App\Model\OrderFacade;
use Nette\Utils\Random;


abstract class BasePresenter extends Nette\Application\UI\Presenter
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

	public function handleOrderProduct(int $productId, int $productquantity)
	{
		$product = $this->facade->getProductById($productId);
		$this->template->product = $product;
		$this->template->productquantity = $productquantity;
	}
}
