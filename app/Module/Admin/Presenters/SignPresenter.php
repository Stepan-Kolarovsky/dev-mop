<?php

declare(strict_types=1);

namespace App\Module\Admin\Presenters;

use App\Forms;
use Nette;
use Nette\Application\UI\Form;
use App\Model\GoogleUserFacade;
use League\OAuth2\Client\Provider\Google;


final class SignPresenter extends Nette\Application\UI\Presenter
{
	/** @persistent */
	public string $backlink = '';

	private Forms\SignInFormFactory $signInFactory;

	private Forms\SignUpFormFactory $signUpFactory;

	private Google $google;
	
    private GoogleUserFacade $googleUserFacade;


	public function __construct(Forms\SignInFormFactory $signInFactory, Forms\SignUpFormFactory $signUpFactory)
	{
		$this->signInFactory = $signInFactory;
		$this->signUpFactory = $signUpFactory;
	}


	/**
	 * Sign-in form factory.
	 */
	protected function createComponentSignInForm(): Form
	{
		return $this->signInFactory->create(function (): void {
			$this->restoreRequest($this->backlink);
			$this->redirect('Dashboard:');
		});
	}


	/**
	 * Sign-up form factory.
	 */
	protected function createComponentSignUpForm(): Form
	{
		return $this->signUpFactory->create(function (): void {
			$this->redirect('Dashboard:');
		});
	}


	public function actionOut(): void
	{
		$this->getUser()->logout();
	}
	public function startup(): void
	{
		parent::startup();
	
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect(':Front:Homepage:');
		}
	}
}
