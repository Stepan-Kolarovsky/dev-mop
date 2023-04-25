<?php

namespace App\Module\Admin\Presenters;

use Nette;
use App\Model\UserFacade;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;

final class UserPresenter extends Nette\Application\UI\Presenter
{
    private UserFacade $userFacade;

    public function __construct(UserFacade $userFacade)
    {
        $this->userFacade = $userFacade;
    }

    public function renderDetail(int $id)
    {
        $this->template->profiles = $this->userFacade->getUserById($id)[$id];
    }
    public function startup(): void
    {
        parent::startup();

        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect(':Front:Homepage:');
        }
    }
    public function renderEdit(int $profileid)
    {
        $this->template->profile = $this->userFacade->getUserById($profileid)[$profileid];
    }
    public function renderEditpd(int $profileid)
    {
        $this->template->profile = $this->userFacade->getUserById($profileid)[$profileid];
    }
    public function renderEditlogin(int $profileid)
    {
        $this->template->profile = $this->userFacade->getUserById($profileid)[$profileid];
        $profile = $this->template->profile;
        $this->getComponent('editUserLoginForm')
            ->setDefaults($profile->toArray());
    }
    public function renderEditaddress(int $profileid)
    {
        $this->template->profile = $this->userFacade->getUserById($profileid)[$profileid];
        $this->template->address = $this->userFacade->getAddressIdbyUserId($profileid)->fetch();
    }
    protected function createComponentEditUserPersonalDataForm(): Form
    {
        $form = new Form;
        $form->addText('given_name', 'Jméno');
        $form->addText('family_name', 'Příjmení');
        $form->addText('email', 'Email');
        $form->addText('username', 'Přezdívka');
        $form->addSubmit('send', 'Uložit');
        $form->onSuccess[] = [$this, 'editUserPersonalDataFormSucceeded'];
        return $form;
    }
    public function editUserPersonalDataFormSucceeded(Form $form, \stdClass $values): void
    {
        $this->userFacade->editUserPersonalData($values);
        $this->flashMessage('Uživatelská data byla úspěšně změněna.');
        $this->redirect('User:detail', $values->profileid);
    }

    protected function createComponentEditUserAddressForm(): Form
    {
        $form = new Form;
        $form->addText('street', 'Ulice');
        $form->addText('city', 'Město');
        $form->addText('psc', 'PSČ');
        $form->addText('country', 'Země');
        $form->addSubmit('send', 'Nastavit');
        $form->onSuccess[] = [$this, 'editUserAddressFormSucceeded'];
        return $form;
    }
    public function editUserAddressFormSucceeded(Form $form, \stdClass $values): void
    {

        if ($this->userFacade->getAddressIdbyUserId($this->template->profile)->fetch() == null) {
            $addressId = $this->userFacade->getAddressIdbyUserId($this->template->profile)->fetch()->id;
            $this->userFacade->addAddress($addressId, $values);
        }
        $addressId = $this->userFacade->getAddressIdbyUserId($values->profileid)->fetch()->id;
        $this->userFacade->editUserAddressAdmin($addressId, $values);
        $this->flashMessage('Uživatelská data byla úspěšně změněna.');
        $this->redirect('User:detail', $values->profileid);
    }
    protected function createComponentEditUserLoginForm(): Form
    {
        $form = new Form;
        $form->addText('username', 'Uživatelské jméno');
        $form->addPassword('password', 'Heslo');
        $form->addSubmit('send', 'Nastavit');
        $form->onSuccess[] = [$this, 'EditUserLoginSucceeded'];
        return $form;
    }
    public function EditUserLoginSucceeded(Form $form, \stdClass $values): void
    {
        $this->userFacade->editUserLogin($values);
        $this->flashMessage('Uživatelská data byla úspěšně změněna.');
        $this->redirect('Dashbord:Customers');
    }
}
