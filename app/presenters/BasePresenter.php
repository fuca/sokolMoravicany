<?php

/**
 * Base class for all application presenters.
 *
 * @author     Michal Fucik
 * @package    SokolMor
 */

use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter {
	
	protected $systemHostAddress;
	protected $maxFileSize;

	protected function getSystemHostAddress() {
		if (!isset($this->systemHostAddress))
			$this->systemHostAddress = 'http://'.$SERVER['SERVER_ADDR'].'/sokolmor/www/';
		return $this->systemHostAddress;
	}
	
	public function getMaxFileSize() {
		
		if (!isset($this->maxFileSize))
			$this->maxFileSize = 1572864;
		return $this->maxFileSize;
	}

	protected function startup(){
		
		parent::startup();
		$this->setLayout('layout');
	}
	
	public function beforeRender(){
		
		$this->template->roles = array_flip($this->user->getRoles());
		
	}
	
	public function getModels() {
		
		return $this->context->modelLoader;
	}
	
	public function createTemplate($class = NULL) {
		// inicializace texy
		$texy = new Texy();

		$texy->encoding = 'utf-8';
		$texy->allowedTags = Texy::NONE;
		$texy->allowedStyles = Texy::NONE;
		$texy->allowedClasses = Texy::NONE;
		$texy->setOutputMode(Texy::XHTML1_TRANSITIONAL);

		$texy->allowed['emoticon'] = TRUE;

		//$texy->imageModule->linkedRoot = WWW_DIR . '/images';
		//$texy->imageModule->fileRoot = WWW_DIR . '/images';
		$texy->imageModule->root = \Nette\Environment::getHttpRequest()->getUrl()->scriptPath . '/images/texy';
		// $texy->emoticonModule->root = WWW_DIR . '/images';
		// $texy->emoticonModule->fileRoot = WWW_DIR . '/images';
		$texy->allowed['heading/surrounded'] = FALSE;
		$texy->headingModule->top = 1;
		$texy->headingModule->balancing = TexyHeadingModule::FIXED;

		// zavolám původní createTemplate
		$template = parent::createTemplate();
		// zaregistruji texy helper
		$template->registerHelper('texy', callback($texy, 'process'));

		return $template;
	}
	
	/* Common controls */
	
	public function createComponentNavigation($name) {
		
		$nav = new SokolMor\Components\NavigationControl($this, $name);
		
		/* TODO: po uvedeni do provozu predelat linky - v db se bude ukladat cela url (kvuli parametrum id u clanku) */
		try {
		    $items = $this->models->navigation->getItems();
		} catch (DataErrorException $x) {
		    $this->flashMessage('Omlouváme se, ale komponenta navigace nedostala potřebná data', 'error');
		}
		
		$hp = array_shift($items);
		$nav->setupHomepage($hp->menu_item_label, $this->link(':'.$hp->menu_item_link));
		$nav->setUseHomepage(TRUE);
		
		foreach ($items as $i) {
		    //dump($i->menu_item_link);
			if ($this->user->isAllowed($i->menu_item_link, 'view')) {
			    //dump($i->menu_item_link.' '.$this->user->roles[0].' '. $this->user->isAllowed($i->menu_item_link, 'view'));
			    $sec = $nav->add($i->menu_item_label, $this->link(':'.$i->menu_item_link), $i->visibility);
			    //dump($this->getName());
			    if ($i->menu_item_link === $this->getName().':'.$this->getAction()) {
				$nav->setCurrentNode ($sec);
			    }
			}
		}
		return $nav;
	}
	
	/* CROSS MODULES CONTROLS FACTORIES */
	
	public function createComponentLoginControl($name) {
		return new SokolMor\Components\LoginControl($this, $name);
	}
	
	public function createComponentArticleInfoControl($name) {
		
		$con = new SokolMor\Components\ArticleInfoControl($this, $name);
		return $con;
	}
	
	public function createComponentSponsorControl($name) {
		$con = new SokolMor\Components\SponsorControl($this, $name, $this->models->sponsor->getSponsors(FALSE));
		return $con;
	}
	
	public function createComponentScheduleControl($name) {
		
		$con = new SokolMor\Components\ScheduleControl($this, $name);
		$con->setModel($this->models->schedule);
		return $con;
	}
	
	public function createComponentUpperStripeControl($name) {
		
		$con = new \SokolMor\Compoments\UpperControl($this, $name);
		$con->setModel($this->models->configure);
		return $con;
	}
	
	protected function createComponentRss() {
	    return new \RssControl();
	}
}
