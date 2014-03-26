<?php

namespace SokolMor\Compoments;

/**
 * WallNotice
 *
 * @author Michal Fucik
 */

class WallNotice extends \Nette\Application\UI\Control {

	private $__model;
	private $__templateFile;
	
	public function setModel(\SokolMor\Models\WallNoticeModel $m) {
		$this->__model = $m;
	}
	
	public function setTemplateFile($fileName) {
		if (!file_exists($fileName))
			throw new \Nette\FileNotFoundException("Template file $fileName does not exist.");
		$this->__templateFile = $fileName;
	}
	
	public function render () {
		if (!isset($this->__model))
			throw new \Nette\InvalidStateException("Private property model is not set. Use WallNotice::setModel(\SokolMor\Models\NoticeModel \$m).");
		
		if (!isset($this->__templateFile))
			$this->__templateFile = "/wallNotice.latte";
		
		$this->template->setFile(__DIR__.$this->__templateFile);
		$this->template->data = $this->__model->getNotices();
		$this->template->title = "Upozornění";
		$this->template->render();
	}
}
