<?php
namespace FrontModule\Forms;

use Nette\Application\UI\Form;

/** 
 * Login control's form
 * @author Michal Fucik <michal.fuca.fucik(at)gmail.com>
 */
final class LogInForm extends Form {
	
	public function __construct($parent, $name) {
		
		parent::__construct($parent, $name);
		
		$this->addText('username', 'Login:', 15, 30)
			->setRequired('Prosím zadejte přihlašovací jméno.');

		$this->addPassword('password', 'Heslo:', 15, 40)
			->setRequired('Prosím zadejte heslo.');

		$this->addCheckbox('remember', 'Trvalé přihlášení');

		$this->addSubmit('send', 'Přihlásit');

		$this->onSuccess[] = callback($this, 'logInFormSubmitted');	
	}
	
	public function logInFormSubmitted($form) {
		$pres = $this->getPresenter();
		try {
			$values = $form->getValues();
			if ($values->remember) {
				$pres->getUser()->setExpiration('+ 30 days', FALSE);
			} else {
				$pres->getUser()->setExpiration('+ 30 minutes', TRUE);
			}
			    
			$pres->user->login($values->username, $values->password);
			$pres->redirect(':Admin:Homepage:default');
    
		} catch (\Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}
}


