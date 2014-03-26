<?php

namespace AdminModule\Forms;

use \Nette\Application\UI\Form; 
/**
 * Description of WallAddForm
 *
 * @author Michal Fucik
 */
class WallAddForm extends \Nette\Application\UI\Form {

	private $__sectionModel;
	private $__fileSectionModel;
	private $__articleModel;
	
	public function setSectionModel(\SokolMor\Models\SectionModel $m) {
		$this->__sectionModel = $m;
	}
	
	public function setFileSectionModel(\SokolMor\Models\File_sectionModel $m) {
		$this->__fileSectionModel = $m;
	}
	
	public function setArticleModel(\SokolMor\Models\ArticleModel $m) {
		$this->__articleModel = $m;
	}
	
	public function __construct ($parent, $name) {
		parent::__construct($parent, $name);
		
	}
	
	public function buildUp() {
		
		$sections = $this->__sectionModel->getSelectSecs();
		
		$this->addText('article_title','Název příspěvku',57,100)
				->setRequired('Název příspěvku musí být vyplněn.');
		$this->addRadioList('article_status', 'Komentáře', array('open'=>'povoleny','locked'=>'zakázány'))
				->setRequired('Zámek komentářů musí být nastaven.');
		
		$this->addSelect('section', 'Kategorie', $sections);
		
		$this->addText('day', '',2,2)
				->setRequired('Den expirace musí být vyplněn.')
				->addRule(Form::NUMERIC, 'Hodnota musí být číslo.');
		$this->addText('month', '',2,2)
				->setRequired('Měsíc expirace musí být vyplněn.')
				->addRule(Form::NUMERIC, 'Hodnota musí být číslo.');
		$this->addText('year', '',4,4)
				->setRequired('Rok expirace musí být vyplněn.')
				->addRule(Form::NUMERIC, 'Hodnota musí být číslo.');
		$this->addText('hour', '',2,2)
				->setRequired('Hodina expirace musí být vyplněna.')
				->addRule(Form::NUMERIC, 'Hodnota musí být číslo.');
		$this->addText('minute', '',1,2)
				->setRequired('Minuta expirace musí být vyplněna.')
				->addRule(Form::NUMERIC, 'Hodnota musí být číslo.');
		
		$this->addTextArea('article_content','Text příspěvku', 65, 20)
				->setRequired('Text příspěvku musí být vyplněn');
		$this->addTextArea('article_note', 'Poznámka ke článku', 65, 1);				
	
			
		$this->addSubmit('addArticleButton', 'Připnout na nástěnku')->onClick[] = callback($this,'addArticleSubmitted');
	}
	
	protected function addArticleSubmitted() {
		$presenter = $this->getPresenter();
		$parent->flashMessage("Příspěvek byl přidán.","success");
		$presenter->redirect(':Front:Rss:default');
		
	}
	
	protected function editArticleSubmitted(\Nette\Forms\SubmitButton $button) {
	
	}
	
}

