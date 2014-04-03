<?php

namespace SokolMor\Compoments;

/**
 * Upper stripe Control
 *
 * @author Michal Fučík
 */

class UpperControl extends \Nette\Application\UI\Control {
	
	/** @var Configuration model instance */
	private $__model;
	
	/** @var Template file name */
	private $__templateFile = NULL;
	
	public function setModel(\SokolMor\Models\ConfigureModel $model) {
		$this->__model = $model;
	}
	
	public function setTemplate($fileName) {
		$this->__templateFile = $fileName;
	}

	public function render() {
		
		if (!isset($this->__model))
			throw new \Nette\InvalidStateException('Model has to be set to ConfigureModel instance.');
		
		if ($this->__templateFile === NULL)
			$this->__templateFile = '/upper.latte';
			
		$this->template->setFile(__DIR__ . $this->__templateFile);
		$dibiRow = $this->__model->getVar('header_stripe_content');
		$this->template->data = $dibiRow['configure_val'];
		$this->template->render();
	}
}
