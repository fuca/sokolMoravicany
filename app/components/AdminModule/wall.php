<?php

namespace SokolMor\Compoments;

/**
 * Wall
 *
 * @author Michal Fucik
 */

class WallControl extends \Nette\Application\UI\Control {
	
	private $__model;
	private $__templateFile;
	
	public function setModel(\SokolMor\Models\WallModel $m) {
		$this->__model = $m;
	}
	
	public function setTemplateFile($fileName) {
		if (!file_exists($fileName))
			throw new \Nette\FileNotFoundException("Template file $fileName does not exist.");
		$this->__templateFile = $fileName;
	}
	
	public function render () {
		if (!isset($this->__model))
			throw new \Nette\InvalidStateException("Private property model is not set. Use Wall::setModel(\SokolMor\Models\WallModel \$m).");
		
		if (!isset($this->__templateFile))
			$this->__templateFile = "/wall.latte";
		
		$this->template->setFile(__DIR__.$this->__templateFile);
		$this->template->data = $this->__model->getPosts();
		$this->template->title = "Nástěnka jednoty";
		$this->template->render();
	}
}
