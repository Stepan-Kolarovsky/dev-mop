<?php

declare(strict_types=1);

namespace App\Module\Admin\Presenters;

use App\Model\ProductFacade;
use App\Model\UserFacade;
use App\Model\OrderFacade;

use Nette;


final class DashboardPresenter extends Nette\Application\UI\Presenter
{
	use RequireLoggedUser;
	private ProductFacade $facade;
	private UserFacade $userFacade;
	private OrderFacade $orderfacade;

	public function __construct(ProductFacade $facade, UserFacade $userFacade, OrderFacade $orderfacade)
	{
		$this->facade = $facade;
		$this->userFacade = $userFacade;
		$this->orderfacade = $orderfacade;
	}
	public function renderproducts(): void
	{
		$this->template->products = $this->facade
			->getPublicProducts()
			->limit(99);
	}
	public function rendercustomers(): void
	{
		$this->template->profiles = $this->userFacade->getAll();
	}



	
	public function renderorders(int $page = 1): void
	{
		$orders = $this->orderfacade->findPublishedOrders();
		$lastPage = 0;
		$this->template->orders = $orders->page($page, 5, $lastPage);
		
		$this->template->lastPage = $lastPage;
		$this->template->page = $page;
	}




	public function renderopenorders(): void
	{
		$this->template->orders = $this->orderfacade->getAll();
	}
	public function renderclosedorders(): void
	{
		$this->template->orders = $this->orderfacade->getAll();
	}
	public function rendercategories(): void
	{
		$this->template->categories = $this->facade->getAllCategories();
	}
	public function handleDeleteProduct(int $productId)
	{
		$this->facade->handleDeleteProduct($productId);
		$this->flashMessage('produkt smazan');
	}
	public function handleDeleteUser(int $userId)
	{
		$this->userFacade->handleDeleteUser($userId);
		$this->flashMessage('user smazan');
	}
	public function handleActivateUser(int $userId)
	{
		$this->userFacade->handleActivateUser($userId);
		$this->flashMessage('user aktivovan');
	}
	public function handleDeleteCategory(int $categoryId)
	{
		$this->facade->DeleteCategory($categoryId);
		$this->flashMessage('kategorie smazana');
	}
	public function startup(): void
	{
		parent::startup();
	
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect(':Front:Homepage:');
		}
	}
}
