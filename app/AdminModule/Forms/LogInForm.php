<?php

namespace AdminModule\Forms;

use Nette\Application\UI\Form;

/** 
 * 
 * 
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
				$pres->getUser()->setExpiration('+ 14 days', FALSE);
			} else {
				$pres->getUser()->setExpiration('+ 20 minutes', TRUE);
			}
			//dump($pres);
			$pres->getUser()->login($values->username, $values->password);
			
			$pres->redirect(':Admin:Homepage:default');

		} catch (\Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
			$pres->redirect('this');
		}
	}
}


