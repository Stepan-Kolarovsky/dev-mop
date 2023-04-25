<?php

namespace App\Module\Front\Presenters;

use Nette;
use App\Model\UserFacade;
use App\Model\OrderFacade;
use App\Model\ProductFacade;
use Nette\Application\UI\Form;
use Tracy\Dumper\Value;

final class UserPresenter extends Nette\Application\UI\Presenter
{
    private ProductFacade $facade;
    private UserFacade $userFacade;
    private OrderFacade $orderFacade;

    public function __construct(UserFacade $userFacade, OrderFacade $orderFacade, ProductFacade $facade)
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
    public function renderDetail()
    {
        $this->template->order = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch();
        $this->template->orderProducts = $this->orderFacade->getOrderProductByOrderId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
        $this->template->orderProductes = $this->orderFacade->getOrderProductByUserId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
        $this->template->profiles = $this->userFacade->getAll();
        $this->template->categories = $this->facade->getAllCategories();
    }
    public function renderAddress()
    {
        $this->template->order = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch();
        $this->template->orderProducts = $this->orderFacade->getOrderProductByOrderId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
        $this->template->orderProductes = $this->orderFacade->getOrderProductByUserId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
        $this->template->profiles = $this->userFacade->getAll();
        $this->template->categories = $this->facade->getAllCategories();
    }
    public function renderPicture()
    {
        $this->template->order = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch();
        $this->template->orderProducts = $this->orderFacade->getOrderProductByOrderId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
        $this->template->orderProductes = $this->orderFacade->getOrderProductByUserId($this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id)->fetchAll();
        $this->template->profiles = $this->userFacade->getAll();
        $this->template->categories = $this->facade->getAllCategories();
        bdump($this->user->getIdentity()->picture);
    }
    public function handleDeleteOrderProduct(int $productId, int $orderId)
    {
        $orderId = $this->orderFacade->getOrderByUserId($this->user->getIdentity()->getId())->fetch()->id;
        $this->template->productId = $productId;
        $this->orderFacade->getDeleteOrderProduct($orderId, $productId);
        $this->flashMessage('produkt odebrán z košíku');
        $this->redirect('Product:show', $productId);
    }
    protected function createComponentPictureForm(): Form
    {
        $form = new Form;
        $form->addUpload('picture', '')
            ->addRule(Form::IMAGE, 'Thumbnail must be JPEG, PNG or GIF');
        $form->addSubmit('send', 'Nastavit');
        $form->onSuccess[] = [$this, 'pictureFormSucceeded'];

        return $form;
    }
    public function pictureFormSucceeded($form, $data): void
    {
        $userId = $this->user->getIdentity()->getId();
        if (isset($data->picture->size)) {
            if ($data->picture->isOK()) {
                $data->picture->move('upload/' . $data->picture->getSanitizedName());
                $data['picture'] = ('upload/' . $data->picture->getSanitizedName());
            } else {
                $data['picture'] = null;
            }
        }
        $this->userFacade->editUser($userId, $data);
        $this->flashMessage('Obrázek byl publikován.', 'success');
        $this->redirect('User:detail');
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
            $form->addText('psc', 'PSČ:')
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
        $form->addText('psc', 'PSČ:')
            ->setRequired('Zadejte PSČ');
        $form->addSubmit('send', 'Uložit');
        $form->onSuccess[] = [$this, 'AddressFormSucceeded'];

        return $form;
    }
    public function AddressFormSucceeded($form, $data): void
    {
        $userId = $this->user->getIdentity()->getId();
        $this->userFacade->addAddress($userId, $data);
        $this->flashMessage('Adresa byla přidána.', 'success');
        $this->redirect('User:address');
    }
    public function editAddressFormSucceeded($form, $data): void
    {
        $addressId = $this->user->getIdentity()->getId()->fetch()->address_id;
        $this->userFacade->editUser($addressId, $data);
        $this->flashMessage('Adresa byla Změněna.', 'success');
        $this->redirect('User:address');
    }
    protected function createComponentEditUserDataForm(): Form
    {
        $form = new Form;
        $form->addText('given_name', 'Jméno:')
            ->setDefaultValue($this->userFacade->getUserById($this->user->getIdentity()->getId())->fetch()->given_name)
            ->setRequired('Zadejte jméno');
        $form->addText('family_name', 'Příjmení:')
            ->setDefaultValue($this->userFacade->getUserById($this->user->getIdentity()->getId())->fetch()->family_name)
            ->setRequired('Zadejte příjmení');
        $form->addText('username', 'Přezdívka:')
            ->setDefaultValue($this->userFacade->getUserById($this->user->getIdentity()->getId())->fetch()->username)
            ->setRequired('Zadejte Přezdívku');
        $form->addText('email', 'Email:')
            ->setDefaultValue($this->userFacade->getUserById($this->user->getIdentity()->getId())->fetch()->email)
            ->setRequired('Zadejte email')
            ->addRule(Form::EMAIL, 'Zadejte platný email');
        $form->addSubmit('send', 'Uložit');
        $form->onSuccess[] = [$this, 'editUserDataFormSucceeded'];

        return $form;
    }
    public function editUserDataFormSucceeded($form, $data): void
    {
        $userId = $this->user->getIdentity()->getId();
        $this->userFacade->editUserData($userId, $data);
        $this->flashMessage('Osobní údaje byly nastaveny', 'success');
        $this->redirect('User:detail');
    }
}
