<?php

namespace SokolMor\Components;

/**
 * ArticleInfoControl
 *
 * @author Michal Fucik
 * @package SokolMor
 */
class ArticleInfoControl extends \Nette\Application\UI\Control {
	
	/** @var Article */
	private $__article = NULL;
	
	public function getArticle() {
		if ($this->__article === NULL) 
			throw new \Nette\InvalidStateException('Slot Article is not set yet. It\'s value is NULL.');
		return $this->__article;
	}
	
	public function setArticle(\SokolMor\Article $art) {
		$this->__article = $art;
	}
	
	public function render($article) {
		
		$this->setArticle($article);
		$this->template->setFile(__DIR__ . '/articleInfo.latte');
		$this->template->article = $this->getArticle();
		$this->template->render();
	}
}
