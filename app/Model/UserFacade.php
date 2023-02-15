<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use Nette\Security\Passwords;
use Nette\Utils\Validators;

/**
 * Users management.
 */
final class UserFacade implements Nette\Security\Authenticator
{
	use Nette\SmartObject;

	public const PASSWORD_MIN_LENGTH = 7;

	private const
		TABLE_NAME = 'users',
		COLUMN_NAME = 'username',
		COLUMN_ID = 'id',
		COLUMN_SUB = 'sub',
		COLUMN_GIVEN_NAME = 'given_name',
		COLUMN_FAMILY_NAME = 'family_name',
		COLUMN_PICTURE = 'picture',
		COLUMN_PASSWORD_HASH = 'password',
		COLUMN_EMAIL = 'email',
		COLUMN_ADDRESS_ID = 'address_id',
		COLUMN_ROLE = 'role',
		COLUMN_ACTIVE = 'active';


	private Nette\Database\Explorer $database;

	private Passwords $passwords;


	public function __construct(Nette\Database\Explorer $database, Passwords $passwords)
	{
		$this->database = $database;
		$this->passwords = $passwords;
	}


	/**
	 * Performs an authentication.
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(string $username, string $password): Nette\Security\SimpleIdentity
	{
		$row = $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_NAME, $username)
			->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		} elseif (!$this->passwords->verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
			bdump($password);
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		} elseif ($this->passwords->needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
			$row->update([
				self::COLUMN_PASSWORD_HASH => $this->passwords->hash($password),
			]);
		}

		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD_HASH]);
		return new Nette\Security\SimpleIdentity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
	}

	public function getAll()
	{
		return $this->database->table('users')->select("*")->fetchAll();
	}
	public function getUserById(int $id)
	{
		return $this->database->table('users')->select("*")->where("id", $id);
	}


	/**
	 * Adds new user.
	 * @throws DuplicateNameException
	 */
	public function add(string $username, string $email, string $password): void
	{
		Nette\Utils\Validators::assert($email, 'email');
		try {
			$this->database->table(self::TABLE_NAME)->insert([
				self::COLUMN_NAME => $username,
				self::COLUMN_PASSWORD_HASH => $this->passwords->hash($password),
				self::COLUMN_EMAIL => $email,
			]);
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
	}
	public function update(int $userId, \stdClass $data): void
	{
		if ($data->password == null) {
			$this->database->table(self::TABLE_NAME)->get(['id' => $userId])->update([
				self::COLUMN_NAME => $data->username,
				self::COLUMN_EMAIL => $data->email,
			]);
		} else {
			$this->database->table(self::TABLE_NAME)->get(['id' => $userId])->update([
				self::COLUMN_NAME => $data->username,
				self::COLUMN_EMAIL => $data->email,
				self::COLUMN_PASSWORD_HASH => $this->passwords->hash($data->password),
			]);
		}
	}

	public function handleDeleteUser(int $userId)
	{
		$this->database->table('users')->where('id', $userId)->update([self::COLUMN_ACTIVE => "0"]);;
	}
	public function editUser(int $userId, \stdClass $data): void
	{
		$this->database->table('users')->get(['id' => $userId])->update([
			self::COLUMN_PICTURE => $data->picture,
		]);
	}
	public function editUserAddress(int $addressId, \stdClass $data): void
	{
		$this->database->table('address')->get(['id' => $addressId])->update([
			'country' => $data->country,
			'city' => $data->city,
			'street' => $data->street,
			'house_number' => $data->house_number,
			'psc' => $data->psc,
		]);
	}

	public function editCard(int $cardId, \stdClass $data): void
	{
		$this->database->table('cards')->get(['id' => $cardId])->update([
			'number' => $data->number,
			'name' => $data->name,
			'cvc' => $data->cvc,
			'expiration' => $data->expiration,
		]);
	}
	public function addCard(int $userId, \stdClass $data): void
	{
		$this->database->table('cards')->insert([
			'number' => $data->number,
			'name' => $data->name,
			'cvc' => $data->cvc,
			'expiration' => $data->expiration,
			'user_id' => $userId,
		]);
	}
	public function getCardByUserId(int $userId)
	{
		return $this->database->table('cards')->select("*")->where("user_id", $userId);
	}
	public function getAddressByUserId($userId)
	{
		return $this->database->table('address')->where('user_id', $userId);
	}
	public function editUserData(int $userId, \stdClass $data): void
	{
		$this->database->table('users')->get(['id' => $userId])->update([
			'given_name' => $data->given_name,
			'family_name' => $data->family_name,
			'username' => $data->username,
			'email' => $data->email,
		]);
	}
	public function addAddress(int $userId, \stdClass $data): void
	{
		$this->database->table('address')->insert([
			'city' => $data->city,
			'street' => $data->street,
			'house_number' => $data->house_number,
			'psc' => $data->psc,
			'user_id' => $userId,
		]);
	}

	public function handleActivateUser(int $userId)
	{
		$this->database->table('users')->where('id', $userId)->update([self::COLUMN_ACTIVE => "1"]);;
	}

	public function getAddressIdbyUserId(int $userId)
	{
		return $this->database->table('address')->select("*")->where("user_id", $userId);
	}
	public function editUserAddressAdmin(int $addressId, \stdClass $data): void
	{
		$this->database->table('address')->get(['id' => $addressId])->update([
			'country' => $data->country,
			'city' => $data->city,
			'street' => $data->street,
			'house_number' => $data->house_number,
			'psc' => $data->psc,
		]);
	}
	public function editUserLogin(int $userId, \stdClass $data): void
	{
		$this->database->table('users')->get(['id' => $userId])->update([
			'username' => $data->username,
			'password' => $this->passwords->hash($data->password),
		]);
	}








}

class DuplicateNameException extends \Exception
{
}
