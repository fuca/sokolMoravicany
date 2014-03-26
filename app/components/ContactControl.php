<?php

namespace SokolMor\Compoments;

/**
 * ContactControl bar
 *
 * @author Michal Fucik
 */

class ContactControl extends \Nette\Application\UI\Control {

	private $__model;
	private $__templateContactFile;
	private $__templateBillingFile;
	
	public function setModel(\SokolMor\Models\ConfigureModel $model) {	
		$this->__model = $model;
	}
	
	public function setContactTemplate($fileName) {
		if(!file_exists($fileName))
			throw new \Nette\FatalErrorException("Given template file '$fileName' doesn't exist.");
		
		$this->__templateContactFile = $fileName;
	}
	
	public function setBillingTemplate($fileName) {
		if(!file_exists($fileName))
			throw new \Nette\FatalErrorException("Given template file '$fileName' doesn't exist.");
		
		$this->__templateBillingFile = $fileName;
	}
	
	public function renderContact() {
		
		if (!isset($this->__templateContactFile))
			$this->__templateContactFile = 'contactBar.latte';
		
		$this->template->setFile(__DIR__ . '/' . $this->__templateContactFile);
		$this->template->data = $this->__model->getContactBarData();
		$this->template->render();
	}
	
	public function renderBilling() {
		
		if (!isset($this->__templateContactFile))
			$this->__templateBillingFile = 'contactBillingBar.latte';
		$this->template->setFile(__DIR__ . '/' . $this->__templateBillingFile);
		$this->template->data = $this->__model->getContactBarData('bill');
		$this->template->render();
	}
	
	public function beforeRender() {
		
		if (!isset($this->__model))
			throw new \Nette\InvalidStateException("Property 'model' has to be set to \SokolMor\Models\ConfigureModel instance. Use 'setModel()' method.");
	}
		
}
