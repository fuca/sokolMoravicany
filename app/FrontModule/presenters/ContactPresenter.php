<?php

namespace FrontModule;

/**
 * @author Michal Fucik
 * @package SokolMor
 */

final class ContactPresenter extends \BasePresenter {
	
	public function actionDefault() {
		
	}
	
	public function renderDefault() {
		$this->template->contacts = $this->models->user->getContacts()->fetchAll();
	}
	
	public function createComponentContactBar($name) {
		
		$con = new \SokolMor\Compoments\ContactControl($this, $name);
		$con->setModel($this->models->configure);
		return $con;
	}
	
}
