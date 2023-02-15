<?php

namespace App\Module\Front\Presenters;

use App\Model\ProductFacade;
use App\Model\UserFacade;
use App\Model\OrderFacade;
use Nette\Application\UI\Form;
use Nette;

final class EditPresenter extends BasePresenter
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

	protected function createComponentProductForm(): Form
	{
		$form = new Form;
		$form->addText('name', 'Jméno:')
			->setRequired();
		$categories = $this->facade->getCategories();
		$form->addSelect('category_id', 'Kategorie:', $categories);
		$form->addTextArea('description', 'Popis:');
		$form->addText('prize', 'Cena:')
			->setRequired();
		$form->addUpload('img', 'Soubor:')
			->addRule(Form::IMAGE, 'Thumbnail must be JPEG, PNG or GIF');
		$form->addSubmit('send', 'Uložit');
		$form->onSuccess[] = [$this, 'ProductFormSucceeded'];

		return $form;
	}
	public function productFormSucceeded($form, $data): void
	{
		$productId = $this->getParameter('productId');
		if (isset($data->img->size))
			if ($data->img->isOK()) {
				$data->img->move('upload/' . $data->img->getSanitizedName());
				$data['img'] = ('upload/' . $data->img->getSanitizedName());
			} else {
				$data['img'] = NULL;
			}

		if ($productId) {
			$product = $this->facade->editProduct($productId, (array) $data);
		} else {
			$product = $this->facade->insertProduct((array) $data);
		}

		$this->flashMessage('Produkt byl publikován.', 'success');
		$this->redirect('Product:show', $product->id);
	}
	public function handleDeleteImage(int $productId)
	{
		$data['img'] = null;
		$Product = $this->facade->editProduct($productId, (array) $data);
		$this->redrawControl('imageSnippet');
	}


	public function rendercreate(): void
	{
		if ($this->getUser()->isLoggedIn()) {
			if ($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch() == null or $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->is_finished == 1) {
				$this->orderFacade->prepareOrder($this->user->getIdentity()->getId());
			}
		}
		$is_finished = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->is_finished;
		$this->template->order = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch();
		$this->template->orderProducts = $this->orderFacade->getOrderProductByOrderId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
		$this->template->orderProductes = $this->orderFacade->getOrderProductByUserId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
	}
	public function handleDeleteOrderProduct(int $productId, int $orderId)
	{
		$orderId = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id;
		$this->template->productId = $productId;
		$this->orderFacade->getDeleteOrderProduct($orderId, $productId);
		$this->flashMessage('produkt odebrán z košíku');
		$this->redirect('Homepage:');
	}
	public function renderEdit(int $productId): void
	{
		$product = $this->facade->getproductById($productId);
		$this->template->product = $product;
		if (!$product) {
			$this->error('Product not found');
		}

		$this->getComponent('productForm')
			->setDefaults($product->toArray());

		$this->template->product = $product;
	}

}
