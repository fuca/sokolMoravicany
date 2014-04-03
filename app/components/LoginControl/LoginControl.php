<?php
namespace SokolMor\Components;

/**
 * Login control
 * @author Michal Fucik <michal.fuca.fucik(at)gmail.com>
 */
final class LoginControl extends \Nette\Application\UI\Control {

    public function render() {
	$presenter = $this->presenter;
	$user = $presenter->user;
	$identity = $user->identity;
	$this->template->setFile(__DIR__ . '/login.latte');
	$this->template->title = 'Přihlášení';

	if ($user->isLoggedIn()) {
	    $this->template->title = 'Uživatel';
	    $this->template->data = array(
		'login'		=> $identity->login,
		'name'		=> $identity->name,
		'surname'	=> $identity->surname,
		'gender'	=> $identity->gender,
		'last_login'	=> $identity->last_login,
		'picture'	=> $identity->picture
	    );
	}
	$this->template->render();
    }

    public function createComponentLogInForm($name) {
	return new \FrontModule\Forms\LoginForm($this, $name);
    }
}
