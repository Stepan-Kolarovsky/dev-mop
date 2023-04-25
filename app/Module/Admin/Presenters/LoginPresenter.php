<?php

namespace App\Module\admin\Presenters;

use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\GoogleUser;
use Nette\Application\UI\Presenter;
use App\Model\GoogleUserFacade;

class LoginPresenter extends Presenter
{
	/** @var Google */
	private $google;

	private $userRepository;

	public function __construct(Google $google, GoogleUserFacade $userRepository)
	{
		$this->userRepository = $userRepository;
		$this->google = $google;
	}

	public function handleGoogleLogin(): void
	{
		$authorizationUrl = $this->google->getAuthorizationUrl([
			'redirect_uri' => $this->link('//google'),
		]);

		$this->getSession(Google::class)->state = $this->google->getState();
		$this->redirectUrl($authorizationUrl);
	}


	public function actionGoogle(): void
	{
		$error = $this->getParameter('error');
		if ($error !== null) {
			$this->flashMessage('... google login error ...', 'error');
			$this->redirect('Sign:in');
		}

		$state = $this->getParameter('state');
		$stateInSession = $this->getSession(Google::class)->state;
		if ($state === null || $stateInSession === null || !\hash_equals($stateInSession, $state)) {
			$this->flashMessage('... invalid CSRF token ...', 'error');
			$this->redirect('Sign:in');
		}

		// reset CSRF protection, it has done its job
		unset($this->getSession(Google::class)->state);

		$accessToken = $this->google->getAccessToken('authorization_code', [
			'code' => $this->getParameter('code'),
			'redirect_uri' => $this->link('//google'),
		]);

		try {
			/** @var GoogleUser $googleUser */
			$googleUser = $this->google->getResourceOwner($accessToken);
		} catch (\Throwable $e) {
			$this->flashMessage('... cannot retrieve user profile ...', 'error');
			$this->redirect('Sign:in');
		}

		$googleId = $googleUser->getId();
		if ($user = $this->userRepository->findByGoogleId($googleId)) {
			// found existing user by googleId, login and redirect
			$this->user->login($user["username"], $user["password"]);
			$this->redirect('Homepage:');
		}

		$googleEmail = $googleUser->getEmail();
		if ($user = $this->userRepository->findByEmail($googleEmail)) {
			// found existing user with the same email, error and force them to login using password
			$this->flashMessage('... somebody already signed up with given email ...', 'error');
			$this->redirect('Sign:in');
		}

		// new user, register them, login and redirect
		$user = $this->userRepository->registerFromGoogle($googleUser);
		$this->user->login($user["username"], $user["password"]);
		$this->redirect('Homepage:');
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
