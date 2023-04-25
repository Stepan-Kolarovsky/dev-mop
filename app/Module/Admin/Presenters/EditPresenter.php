<?php

namespace App\Module\Admin\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\ProductFacade;
use Nette\ComponentModel\IComponent;
use Nette\Utils\Random;

final class EditPresenter extends Nette\Application\UI\Presenter
{
	private ProductFacade $facade;

	public function __construct(ProductFacade $facade)
	{
		$this->facade = $facade;
	}
	protected function createComponentProductForm(): Form
	{
		$form = new Form;
		$form->addText('name', 'Jméno:')
			->setRequired();
		$form->addTextArea('description', 'Popis:');
		$categories = $this->facade->getCategories();
		$form->addSelect('category_id', 'Kategorie:', $categories);
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
		if ($data->img->isOK()) {
			$data->img->move('upload/' . $data->img->getSanitizedName());
			$data['img'] = ('upload/' . $data->img->getSanitizedName());
		} else {
			unset($data->image);
			$this->flashMessage('Sobor nebyl přidán', 'failed');
		}
		if ($productId) {
			$product = $this->facade->editProduct($productId, (array) $data);
		} else {
			$product = $this->facade->insertProduct((array) $data);
		}

		$this->flashMessage('Produkt byl publikován.', 'success');
		$this->redirect(':admin:Edit:edit', $product->id);
	}
	public function handleDeleteImage(int $productId)
	{
		$data['img'] = null;
		$Product = $this->facade->editProduct($productId, (array) $data);
		$this->redrawControl('imageSnippet');
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

	public function handleDeleteProduct(int $productId)
	{
		$this->facade->handleDelete($productId);
		$this->flashMessage('produkt smazan');
	}
	public function rendercreateCategory(): void
	{
		$this->template->categories = $this->facade->getAllCategories();
	}

	protected function createComponentCategoryForm(): Form
	{
		$form = new Form;
		$form->addText('name', 'Jméno:')
			->setRequired();
		$form->addSubmit('send', 'Uložit');
		$form->onSuccess[] = [$this, 'CategoryFormSucceeded'];

		return $form;
	}
	public function CategoryFormSucceeded($form, $data): void
	{
		$this->facade->insertCategory((array) $data);
		$this->flashMessage('Kategorie byla vytvořena.', 'success');
		$this->redirect('Dashboard:categories');
	}
	public function startup(): void
	{
		parent::startup();

		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect(':Front:Homepage:');
		}
	}
	public function renderEditCategory(int $categoryId): void
	{
		$category = $this->facade->getCategoryById($categoryId);
		$this->template->category = $category;
		if (!$category) {
			$this->error('Category not found');
		}

		$this->getComponent('editCategoryForm')
			->setDefaults($category->toArray());

		$this->template->category = $category;
	}

	protected function createComponentEditCategoryForm(): Form
	{
		$form = new Form;
		$form->addText('name', 'Název kategorie:')
			->setRequired();
		$form->addSubmit('send', 'Nastavit');
		$form->onSuccess[] = [$this, 'EditCategoryFormSucceeded'];

		return $form;
	}
	public function editCategoryFormSucceeded($form, $data): void
	{
		$categoryId = $this->getParameter('categoryId');
		$category = $this->facade->editcategory($categoryId, (array) $data);
		$this->flashMessage('Kategorie byla upravena.', 'success');
		$this->redirect('Dashboard:categories');
	}
}
