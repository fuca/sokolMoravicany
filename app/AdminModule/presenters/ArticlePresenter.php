<?php

namespace AdminModule;

/**
 * Description of ArticlePresenter
 *
 * @author Michal Fucik
 */

class ArticlePresenter extends SecuredPresenter {

	public function beforeRender() {
		parent::beforeRender();
		
		$this->template->maxFileSize = $this->getMaxFileSize();
	}
	
	public function renderDefault() {
		$this->redirect(':Admin:Homepage:default');
		
	}
	
	public function renderWallAdd() {
		$this->template->title = 'Nový příspěvek na nástěnku';
	}
	
	public function renderArticleAdd() {
		$this->template->title = 'Nový článek';
	}
	
	public function createComponentAddArticle($name) {
		
		$form = new \AdminModule\Forms\AddArticleForm($this, $name);
		$form->setArticleModel($this->models->article);
		$form->setFileSectionModel($this->models->file_section);
		$form->setSectionModel($this->models->section);
		$form->buildUp();
		
		return $form;
	}
	
	public function createComponentWallAdd($name) {
		
		$form = new \AdminModule\Forms\WallAddForm($this, $name);
		$form->setArticleModel($this->models->article);
		$form->setFileSectionModel($this->models->file_section);
		$form->setSectionModel($this->models->section);
		$form->buildUp();
		
		return $form;
	}
	
}

