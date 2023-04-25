<?php

namespace App\Module\Admin\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\ProductFacade;
use App\Model\UserFacade;
use Nette\Utils\Random;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	public function handleOrderProduct(int $productId, int $productquantity)
	{
		$product = $this->facade->getProductById($productId);
		$this->template->product = $product;
		$this->template->productquantity = $productquantity;
	}
}
