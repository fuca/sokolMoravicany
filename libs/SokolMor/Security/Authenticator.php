<?php

namespace SokolMor\Security;

use Nette\Object,
    Nette\Database\Connection,
    Nette\Security\Identity,
    Nette\Security\IAuthenticator,
    Nette\Security\AuthenticationException;

/**
 * @author Michal Fucik
 * @package SokolMor
 */

final class Authenticator extends Object implements IAuthenticator {
	
	/** @var SokolMor\Models\UserModel */
	private $model;
	
	/** @var Salt */
	private $salt;
	
	public function __construct(\SokolMor\Models\UserModel $userModel, $salt){
		
		$this->model = $userModel;
		$this->salt = $salt;
	}
	
	public function authenticate(array $credentials) {
		
		list($username, $password) = $credentials;
		$saltedHash = sha1($password . $this->salt);
		
		$user = $this->model->getByLogin($username);
		
		if (!$user) {
            throw new AuthenticationException("User with login '$username' not found", self::IDENTITY_NOT_FOUND);
        }
		
        if ($user->user_password != $saltedHash) {
            throw new AuthenticationException('Wrong password', self::INVALID_CREDENTIAL);
        }
		
		$this->model->setLastLogin($user->user_id);
		
		$identity = new Identity($user->user_id, $user->role);
		$identity->name = $user->user_name;
		$identity->surname = $user->user_surname;
		$identity->email = $user->user_email;
		$identity->gender = $user->user_gender;
		$identity->last_login = $user->user_last_login;
		$identity->picture = $user->user_picture;
		
		return $identity;	
	}
	
}

