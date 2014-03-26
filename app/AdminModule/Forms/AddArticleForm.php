<?php

namespace AdminModule\Forms;

use \Nette\Application\UI\Form,
	\Nette\Http\FileUpload,
	\Nette\Image;

/**
 * Description of AddArticleForm
 *
 * @author Michal Fucik
 */

class AddArticleForm extends Form {

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
		$presenter = $this->getPresenter();
		
		$maxFSize = $presenter->getMaxFileSize();
		$bytes = $maxFSize / 8;
		$cons = $maxFSize / 1024;
		
		
		$day = date('d')+7;
		$month = date('m');
		$year = date('Y');
		$hour = date('G');
		$minute = date('i');
		
		$this->addText('article_title','Název článku',57,100)
				->setRequired('Název článku musí být vyplněn.');
		$this->addRadioList('article_status', 'Komentáře', array('open'=>'povoleny','locked'=>'zakázány'))
				->setRequired('Zámek komentářů musí být nastaven.')
				->setDefaultValue('open');
		$this->addCheckbox('article_visible', 'Publikovat článek')
				->setDefaultValue('true');
		
		$this->addSelect('article_section_id', 'Kategorie', $sections)
				->setDefaultValue(2);
		
		$this->addText('day', '',2,2)
				->setRequired('Den expirace musí být vyplněn.')
				->addRule(Form::NUMERIC, 'Hodnota musí být číslo.')
				->addRule(Form::RANGE, 'Den musí být v rozmezí %d - %d.',array(0,31))
				->setDefaultValue($day);
		$this->addText('month', '',2,2)
				->setRequired('Měsíc expirace musí být vyplněn.')
				->addRule(Form::NUMERIC, 'Hodnota musí být číslo.')
				->addRule(Form::RANGE, 'Měsíc musí být v rozmezí %d - %d.',array(0,12))
				->setDefaultValue($month);
		$this->addText('year', '',4,4)
				->setRequired('Rok expirace musí být vyplněn.')
				->addRule(Form::NUMERIC, 'Hodnota musí být číslo.')
				->addRule(Form::RANGE, 'Rok musí být větší než %d.',array(2012,3000))
				->setDefaultValue($year);
		$this->addText('hour', '',2,2)
				->setRequired('Hodina expirace musí být vyplněna.')
				->addRule(Form::NUMERIC, 'Hodnota musí být číslo.')
				->addRule(Form::RANGE, 'Hodina musí být v rozmezí %d - %d.',array(0,24))
				->setDefaultValue($hour);
		$this->addText('minute', '',1,2)
				->setRequired('Minuta expirace musí být vyplněna.')
				->addRule(Form::NUMERIC, 'Hodnota musí být číslo.')
				->addRule(Form::RANGE, 'Minuta musí být v rozmezí %d - %d.',array(0,60))
				->setDefaultValue($minute);
		
		$this->addTextArea('article_content','Text článku', 65, 20)
				->setRequired('Text článku musí být vyplněn');
		$this->addUpload('article_picture', 'Obrázek (šetřete místem)')
				->addCondition(Form::FILLED)
				->addRule(Form::IMAGE,'Obrázek musí být ve formátu JPG, PNG nebo GIF.')
				->addRule(Form::MAX_FILE_SIZE, "Velikost obrázku musí být maximálně $cons kB.", $maxFSize);
		$this->addTextArea('article_note', 'Poznámka ke článku', 65, 1);
		
		$this->addUpload('1', 'Soubor 1')
				->addCondition(Form::FILLED)
				->addRule(Form::MAX_FILE_SIZE, "Velikost přílohy musí být maximálně $cons kB.", $maxFSize);
		$this->addUpload('2', 'Soubor 2')
				->addCondition(Form::FILLED)
				->addRule(Form::MAX_FILE_SIZE, "Velikost přílohy musí být maximálně $cons kB.", $maxFSize);
		$this->addUpload('3', 'Soubor 3')
				->addCondition(Form::FILLED)
				->addRule(Form::MAX_FILE_SIZE, "Velikost přílohy musí být maximálně $cons kB.", $maxFSize);
			
		$this->addSubmit('addArticleButton', 'Přidat článek');
		$this->onSuccess[] = callback($this,'addArticleSubmitted');
	}
	
	public function addArticleSubmitted($form) {
		
		// sezenu data
		$article = $this->getValues();
		// sehnat presenter
		$presenter = $this->getPresenter();
		
		//$sectionId = $article['section'];
		//	unset($article['section']);
		// prekopat expiraci do nejakyho cas. formatu
		$d = $article['day'];
			unset($article['day']);
		$mo = $article['month'];
			unset($article['month']);
		$y = $article['year'];
			unset($article['year']);
		$h = $article['hour'];
			unset($article['hour']);
		$mi = $article['minute'];
			unset($article['minute']);
		$timestamp = date('Y-m-d G:i', mktime($h, $mi, 0, $mo, $d, $y));
		$article['article_expires']	= $timestamp;
		
		// zpracovat obrazek
		$httpUpFile = $article['article_picture'];
			unset($article['article_picture']);
		if ($httpUpFile->isOk()) {
			$img = Image::FromFile($httpUpFile);
			$imgName = \Nette\Utils\Strings::webalize($httpUpFile->getName(),'.');
			$filePath = IMG_CONTENT_DIR .'/'. $imgName;
			$img->resize(150,150);
			$img->save($filePath);
			$article['article_picture'] = $imgName;
			$article['article_thumbnail'] = $imgName;
		}
		// vymazat file_section
		$fileOne = $article['1'];
			unset($article['1']);
		
		//$tmp = explode('.',$fileOne->getName());
		
		$fileTwo = $article['2'];
		unset($article['2']);
		$fileThree = $article['3'];
		unset($article['3']);
			
		$article['article_type'] = 'article';
		
		// ulozit
		$articleId = $this->__articleModel->createArticle($article);
		//
		// pridat zaznam do article_section
		//$this->__sectionModel->addArticleIntoSection($articleId,$sectionId);
		//
		// pridat file_section a pridat jednotlive soubory	
		// $file_secId = $this->__fileSectionModel->addSection();
		// add file 1, 2, 3
		
		$presenter->flashMessage("Článek byl přidán.",'success');
		// presmerovat
		$presenter->redirect(':Front:Article:show', $articleId);
		
	}
	
	protected function editArticleSubmitted(\Nette\Forms\SubmitButton $button) {
		
	}
	
}
