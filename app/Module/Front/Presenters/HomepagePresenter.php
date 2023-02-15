<?php

declare(strict_types=1);

namespace App\Module\Front\Presenters;

use App\Model\ProductFacade;
use App\Model\UserFacade;
use App\Model\OrderFacade;
use Nette\Application\UI\Form;
use Nette;

final class HomepagePresenter extends BasePresenter
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


	public function renderDefault(): void
	{
		
		
		$this->template->refreshNumber = rand(1, 55);
		$this->template->products = $this->facade
			->getPublicProducts()
			->limit(5);
		$this->template->profiles = $this->userFacade->getAll();
		if ($this->getUser()->isLoggedIn()) {
			if ($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch() == null or $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->is_finished == 1) {
				$this->orderFacade->prepareOrder($this->user->getIdentity()->getId());
			}
		}
		if ($this->getUser()->isLoggedIn()) {
			$is_finished = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->is_finished;
			$this->template->order = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch();
			$this->template->orderProducts = $this->orderFacade->getOrderProductByOrderId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
			$this->template->orderProductes = $this->orderFacade->getOrderProductByUserId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
		}
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
	protected function createComponentSignInForm(): Form
	{
		$form = new Form;
		$form->addText('username', 'Uživatelské jméno:')
			->setRequired('Prosím vyplňte své uživatelské jméno.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Prosím vyplňte své heslo.');

		$form->addSubmit('send', 'Přihlásit');

		$form->onSuccess[] = [$this, 'signInFormSucceeded'];
		return $form;
	}

	public function signInFormSucceeded(Form $form, \stdClass $data): void
	{
		try {
			$this->getUser()->login($data->username, $data->password);
			$this->redirect('Homepage:');
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError('Nesprávné přihlašovací jméno nebo heslo.');
		}
	}
}
