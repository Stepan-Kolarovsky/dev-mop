<?php

namespace App\Module\Admin\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Application\UI\DateInput;
use App\Model\ProductFacade;
use App\Model\UserFacade;
use App\Model\OrderFacade;
use Nette\ComponentModel\IComponent;
use Nette\Utils\Random;

final class OrderPresenter extends BasePresenter
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

	public function renderCart(): void
	{
		$this->template->order = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch();
		//$this->template->orderProducts = $this->orderFacade->getOrderProductByOrderId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
		$this->template->orderProducts = $this->orderFacade->getOrderProductByUserId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
		bdump($this->template->orderProducts);
		$this->template->categories = $this->facade->getAllCategories();
	}

	public function renderAddress(): void
	{
		$this->template->order = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch();
		//$this->template->orderProducts = $this->orderFacade->getOrderProductByOrderId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
		$this->template->orderProducts = $this->orderFacade->getOrderProductByUserId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
		bdump($this->template->orderProducts);
		$this->template->categories = $this->facade->getAllCategories();
	}
	public function renderPayment(): void
	{
		$this->template->order = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch();
		//$this->template->orderProducts = $this->orderFacade->getOrderProductByOrderId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
		$this->template->orderProducts = $this->orderFacade->getOrderProductByUserId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
		bdump($this->template->orderProducts);
		$this->template->categories = $this->facade->getAllCategories();
		$this->template->card = $this->userFacade->getCardByUserId($this->user->getIdentity()->getId())->fetch();
	}
	public function renderCheckout(): void
	{
		$this->template->order = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch();
		//$this->template->orderProducts = $this->orderFacade->getOrderProductByOrderId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
		$this->template->orderProducts = $this->orderFacade->getOrderProductByUserId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
		bdump($this->template->orderProducts);
		$this->template->categories = $this->facade->getAllCategories();
		$this->template->card = $this->userFacade->getCardByUserId($this->user->getIdentity()->getId())->fetch();
		$this->template->address = $this->userFacade->getAddressByUserId($this->user->getIdentity()->getId())->fetch();
	}
	public function renderDetail(int $orderId): void
	{
		$order = $this->orderFacade->getOrderbyID($orderId);
		$this->template->order = $order;
		$userId = $order->user_id;
		$address = $this->orderFacade->getAddressByorderUserId($userId);
		$this->template->address = $address;
		$orderId = $order->id;
		$orderProducts = $this->orderFacade->getOrderProductByyOrderId($orderId);
		$this->template->orderProducts = $orderProducts;
		$orderUser = $this->orderFacade->getOrderUser($userId);
		$this->template->orderUser = $orderUser;
		bdump($orderUser);
	}

	public function handleOrderProduct(int $productId, int $productquantity)
	{
		$product = $this->facade->getProductById($productId);
		$this->template->product = $product;
		$this->template->productquantity = $productquantity;
	}
	public function handleOrder(int $productId, int $productquantity)
	{
		$product = $this->facade->getProductById($productId);
		$this->template->product = $product;
		$this->template->productquantity = $productquantity;
		$this->template->totalprice = $product->price * $productquantity;
	}
	public function handleDeleteOrderProduct(int $productId, int $orderId)
	{
		$orderId = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id;
		$this->template->productId = $productId;
		$this->orderFacade->getDeleteOrderProduct($orderId, $productId);
		$this->flashMessage('produkt odebrán z košíku');
		$this->redirect('Order:cart');
	}
	protected function createComponentAddressForm(): Form
	{
		if ($this->userFacade->getAddressByUserId($this->user->getIdentity()->getId())->fetch() != null) {
			$form = new Form;
			$form->addText('street', 'Ulice:')
				->setDefaultValue($this->userFacade->getAddressByUserId($this->user->getIdentity()->getId())->fetch()->street)
				->setRequired('Zadejte ulici');
			$form->addText('city', 'Město:')
				->setDefaultValue($this->userFacade->getAddressByUserId($this->user->getIdentity()->getId())->fetch()->city)
				->setRequired('Zadejte město');
			$form->addText('house_number', 'Číslo domu:')
				->setDefaultValue($this->userFacade->getAddressByUserId($this->user->getIdentity()->getId())->fetch()->house_number)
				->setRequired('Zadejte číslo domu');
			$form->addText('zip', 'PSČ:')
				->setDefaultValue($this->userFacade->getAddressByUserId($this->user->getIdentity()->getId())->fetch()->psc)
				->setRequired('Zadejte PSČ');
			$form->addSubmit('send', 'Uložit');
			$form->onSuccess[] = [$this, 'editAddressFormSucceeded'];

			return $form;
		}
		$form = new Form;
		$form->addText('street', 'Ulice:')

			->setRequired('Zadejte ulici');
		$form->addText('city', 'Město:')
			->setRequired('Zadejte město');
		$form->addText('house_number', 'Číslo domu:')
			->setRequired('Zadejte číslo domu');
		$form->addText('zip', 'PSČ:')
			->setRequired('Zadejte PSČ');
		$form->addSubmit('send', 'Uložit');
		$form->onSuccess[] = [$this, 'editAddressFormSucceeded'];

		return $form;
	}
	public function editAddressFormSucceeded($form, $data): void
	{
		$addressId = $this->user->getIdentity()->getId()->fetch()->address_id;
		$this->userFacade->editUser($addressId, $data);
		$this->flashMessage('Adresa byla Změněna.', 'success');
		$this->redirect('Order:address');
	}
	protected function createComponentOrderForm(): Form
	{
		$form = new Form;
		$form->addText('name', 'Jméno:')
			->setDefaultValue($this->userFacade->getUserById($this->user->getIdentity()->getId())->fetch()->given_name)
			->setRequired('Zadejte jméno');
		$form->addText('surname', 'Příjmení:')
			->setDefaultValue($this->userFacade->getUserById($this->user->getIdentity()->getId())->fetch()->family_name)
			->setRequired('Zadejte příjmení');
		$form->addText('email', 'Email:')
			->setDefaultValue($this->userFacade->getUserById($this->user->getIdentity()->getId())->fetch()->email)
			->setRequired('Zadejte email')
			->addRule(Form::EMAIL, 'Zadejte platný email');
		$form->addSubmit('send', 'Uložit');
		$form->onSuccess[] = [$this, 'orderFormSucceeded'];

		return $form;
	}
	public function orderFormSucceeded($form, $data): void
	{
		$orderId = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id;
		$this->orderFacade->editOrder($orderId, $data);
		$this->flashMessage('Objednávka byla Změněna.', 'success');
		$this->redirect('Order:address');
	}

	protected function createComponentPaymentForm(): Form
	{
		$form = new Form;
		$form->addRadioList('payment_metod', 'Způsob platby:', [
			'1' => 'Dobírka',
			'2' => 'Kartou',
		])
			->setRequired('Vyberte způsob platby');
		$form->addSubmit('send', 'Uložit');
		$form->setMethod('POST');
		$form->onSuccess[] = [$this, 'paymentFormSucceeded'];

		return $form;
	}
	public function paymentFormSucceeded($form, $data): void
	{
		$orderId = $this->orderFacade->getOrderIdByUserIdandIsFinished($this->user->getIdentity()->getId())->fetch()->id;
		$this->orderFacade->editOrder($orderId, $data);
		$this->flashMessage('Způsob platby byl Změněn.', 'success');
		$this->redirect('Order:payment');
	}
	protected function createComponentEditCardForm(): Form
	{
		$form = new Form;
		$form->addText('number', 'Číslo karty:')
			->setDefaultValue($this->userFacade->getCardByUserId($this->user->getIdentity()->getId())->fetch()->number)
			->setRequired('Zadejte číslo karty');
		$form->addText('name', 'Jméno na kartě:')
			->setDefaultValue($this->userFacade->getCardByUserId($this->user->getIdentity()->getId())->fetch()->name)
			->setRequired('Zadejte jméno na kartě');
		$form->addText('cvc', 'CVC:')
			->setDefaultValue($this->userFacade->getCardByUserId($this->user->getIdentity()->getId())->fetch()->cvc)
			->setRequired('Zadejte CVC');
		$form->addText('expiration', 'Datum expirace:')
			->setDefaultValue($this->userFacade->getCardByUserId($this->user->getIdentity()->getId())->fetch()->expiration)
			->setRequired('Zadejte datum expirace');
		$form->addSubmit('send', 'Uložit');
		$form->onSuccess[] = [$this, 'EditCardFormSucceeded'];

		return $form;
	}
	public function EditCardFormSucceeded($form, $data): void
	{
		$cardId = $this->userFacade->getCardByUserId($this->user->getIdentity()->getId())->fetch()->id;
		$this->userFacade->editCard($cardId, $data);
		$this->flashMessage('Platební údaje byly nastaveny.', 'success');
		$this->redirect('Order:payment');
	}
	protected function createComponentAddCardForm(): Form
	{
		$form = new Form;
		$form->addText('number', 'Číslo karty:')
			->setRequired('Zadejte číslo karty');
		$form->addText('name', 'Jméno na kartě:')
			->setRequired('Zadejte jméno na kartě');
		$form->addText('cvc', 'CVC:')
			->setRequired('Zadejte CVC');
		$form->addText('expiration', 'Datum expirace:');
		$form->addSubmit('send', 'Uložit');
		$form->onSuccess[] = [$this, 'addCardFormSucceeded'];

		return $form;
	}
	public function addCardFormSucceeded($form, $data): void
	{
		$userId = $this->user->getIdentity()->getId();
		$this->userFacade->addCard($userId, $data);
		$this->flashMessage('Platební údaje byly nastaveny.', 'success');
		$this->redirect('Order:payment');
	}
	public function handlefinishOrder(): void
	{
		$orderId = $this->orderFacade->getOrderIdByUserIdandIsFinished($this->user->getIdentity()->getId())->fetch()->id;
		$this->orderFacade->finishOrder($orderId);
		$this->flashMessage('Objednávka byla dokončena.', 'success');
		$this->redirect('Homepage:default');
	}

	public function handleCompleteOrder(int $orderId): void
	{
		$this->orderFacade->completeOrder($orderId);
		$this->flashMessage('Objednávka byla Potvrzena.', 'success');
		$this->redirect('Dashboard:orders');
	}

	public function handleDeleteOrder(int $orderId): void
	{
		$this->orderFacade->deleteOrderProducts($orderId);
		$this->orderFacade->deleteOrder($orderId);
		$this->flashMessage('Objednávka byla smazána.', 'success');
		$this->redirect('Dashboard:orders');
	}
	public function startup(): void
	{
		parent::startup();

		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect(':Front:Homepage:');
		}
	}
}
