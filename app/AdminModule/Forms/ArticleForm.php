<?php
namespace AdminModule\Forms;

use AdminModule\Forms\BaseForm,
    Nette\Forms\Form,
    Vodacek\Forms\Controls\DateInput;
/**
 * Description of AddArticleForm
 *
 * @author Michal Fucik
 */
class ArticleForm extends BaseForm {

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

    public function __construct($parent, $name, $mode = self::MODE_CREATE) {
	parent::__construct($parent, $name, $mode);
    }

    public function buildUp() {

	$sections = $this->__sectionModel->getSelectSecs();
	$artStatus = \SokolMor\Article::getCommentsSelect();
	$presenter = $this->getPresenter();
	
	$types = \SokolMor\Article::getTypeSelect();
	$visibility = \SokolMor\Article::getVisibilitySelect();
	$maxFSize = $presenter->getMaxFileSize();
	//$bytes = $maxFSize / 8;
	$cons = $maxFSize / 1024;
	
	$this->addHidden(\SokolMor\Article::ID_ID);
	$this->addHidden('article_pic_present', 'empty.png');

	$this->addText(\SokolMor\Article::TITLE_ID, 'Titulek', 57, 100)
		->setRequired('Název článku musí být vyplněn.');

	$this->addRadioList(\SokolMor\Article::STATUS_ID, 'Komentáře', $artStatus)
		->setRequired('Zámek komentářů musí být nastaven.')
		->setDefaultValue(\SokolMor\Article::STATUS_OPEN);

	$this->addRadioList(\SokolMor\Article::VISIBILITY_ID, 'Zveřejnit', $visibility)
		->setDefaultValue("true");
	
	$this->addSelect(\SokolMor\Article::TYPE_ID, 'Typ', $types)
		->setDefaultValue(\SokolMor\Article::TYPE_ARTICLE);

	$this->addMultiSelect(\SokolMor\Article::SECTIONS_ID, 'Kategorie', $sections, 5)
		->setDefaultValue(2);

	$this->addDate(\SokolMor\Article::EXPIRE_ID, 'Zobrazit do', DateInput::TYPE_DATE)
		->addRule(Form::FILLED, 'Datum expirace musí být vyplněno')
		->setDefaultValue(new \Nette\DateTime('+2 hours'));			 

	$this->addTextArea(\SokolMor\Article::CONTENT_ID, 'Text článku', 55, 20)
		->setRequired('Text článku musí být vyplněn');

	$this->addUpload(\SokolMor\Article::PICTURE_ID, 'Obrázek (šetřete místem)')
		->setOption('description',"$maxFSize")
		->addCondition(Form::FILLED)
		->addRule(Form::IMAGE, 'Obrázek musí být ve formátu JPG, PNG nebo GIF.')
		->addRule(Form::MAX_FILE_SIZE, "Velikost obrázku musí být maximálně $cons kB.", $maxFSize);

	$this->addTextArea(\SokolMor\Article::NOTE_ID, 'Poznámka ke článku', 55, 2);
	
// -- FILE UPLOAD SECTION -- //
	
	$this->addUpload('1', 'Soubor 1')
		->addCondition(Form::FILLED)
		->addRule(Form::MAX_FILE_SIZE, "Velikost přílohy musí být maximálně $cons kB.", $maxFSize);
	
	$this->addText('l1','Popisek',40)
		->addConditionOn($this[1], Form::FILLED,'Popisek musí být zadán');
	
	$this->addHidden('rid1');
	$sbmt1 = $this->addSubmit('remove1', 'smazat')->setValidationScope(FALSE);
	$sbmt1->onClick[] = callback($this->presenter, 'deleteFileHandle');

	
	$this->addUpload('2', 'Soubor 2')
		->addCondition(Form::FILLED)
		->addRule(Form::MAX_FILE_SIZE, "Velikost přílohy musí být maximálně $cons kB.", $maxFSize);

	$this->addText('l2','Popisek', 40)
		->addConditionOn($this[2], Form::FILLED,'Popisek musí být zadán');
	$this->addHidden('rid2');
	$sbmt2 = $this->addSubmit('remove2', 'smazat')->setValidationScope(FALSE);
	$sbmt2->onClick[] = callback($this->presenter, 'deleteFileHandle');

	
	$this->addUpload('3', 'Soubor 3')
		->addCondition(Form::FILLED)
		->addRule(Form::MAX_FILE_SIZE, "Velikost přílohy musí být maximálně $cons kB.", $maxFSize);
	$this->addText('l3','Popisek', 40)
		->addConditionOn($this[3], Form::FILLED,'Popisek musí být zadán');
	$this->addHidden('rid3');
	$sbmt3 = $this->addSubmit('remove3', 'smazat')->setValidationScope(FALSE);
	$sbmt3->onClick[] = callback($this->presenter, 'deleteFileHandle');
	
	$this->addSubmit('addArticleButton', 'Přidat článek');

	$this->onSuccess[] = callback($this, 'addArticleSubmitted');
    }

    public function addArticleSubmitted($form) {
	$values = $form->getValues();
	$presenter = $this->getPresenter();
	switch ($this->getMode()) {
	    case self::MODE_CREATE:
		$presenter->createArticleHandle($values);
		break;
	    case self::MODE_UPDATE:
		$values[\SokolMor\Article::EDIT_ID] = new \Nette\DateTime();
		$presenter->updateArticleHandle($values);
		break;
	}
    }
}
