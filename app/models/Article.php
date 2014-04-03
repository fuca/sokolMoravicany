<?php
namespace SokolMor;

class Article extends \DibiRow {

    const TYPE_ARTICLE	= 'art';
    const TYPE_ABOUT	= 'abt';
    const TYPE_WALLPOST = 'wpo';
    const TYPE_NOTICE	= 'ntc';
    const STATUS_OPEN	= 'open';
    const STATUS_LOCKED = 'locked';
    
    const ID_ID		    = 'article_id';
    const TITLE_ID	    = 'article_title';
    const STATUS_ID	    = 'article_status';
    const VISIBILITY_ID	    = 'article_visible';
    const SECTIONS_ID	    = 'article_section_id';
    const EXPIRE_ID	    = 'article_expires';
    const CONTENT_ID	    = 'article_content';
    const PICTURE_ID	    = 'article_picture';
    const THUMBNAIL_ID	    = 'article_thumbnail';
    const NOTE_ID	    = 'article_note';
    const TYPE_ID	    = 'article_type';
    const AUTHOR_ID_ID	    = 'article_author_id';
    const ADDED_ID	    = 'article_added';
    const EDIT_ID	    = 'article_edit';
    const FILE_UPLOAD_COUNT = 3;

    /** @var comments */
    private $__comments;

    /** @var sections */
    private $__sections;
    
    /** @var fileSections */
    private $__fileSections;
    
    /** @var article model */
    private $articleModel;
    
    /** @var fileSection model */
    private $fSectionModel;
    
    /** @var sectionMpdel */
    private $sectionModel;
    
    public function setFileSectionModel(Models\File_sectionModel $fSectionModel) {
	$this->fSectionModel = $fSectionModel;
    }

    public function setSectionModel(Models\SectionModel $sectionModel) {
	$this->sectionModel = $sectionModel;
    }
        
    public static function getCommentsSelect() {
	return array(self::STATUS_OPEN=>"Odemčeny", 
		     self::STATUS_LOCKED=>'Uzamčeny');
    }
    
    public static function getVisibilitySelect() {
	return array("true"=>'Ano',"false"=>'Ne');
    }
    
    public static function getTypeSelect() {
	return array(self::TYPE_ABOUT=>'O nás',
		     self::TYPE_ARTICLE=>'Článek',
		     self::TYPE_WALLPOST=>'Nástěnka',
		     self::TYPE_NOTICE=>'Upozornění');
    }
    
    public function getFileSections() {
	if (!isset($this->__fileSections))
	    $this->__fileSections = $this->fSectionModel->getSection($this->getId());
	return $this->__fileSections;
    }

    public function setId($id) {
	$this->offsetSet(self::ID_ID, $id);
    }
    
    public function getId() {
	return $this->offsetGet(self::ID_ID);
    }
    
    public function getFormSections() {
	return $this->offsetGet(self::SECTIONS_ID);
    }
    
    public function getFormFiles() {
	$res = array();
	for($i=1;$i<=self::FILE_UPLOAD_COUNT;$i++) {
	    $res[$i]['file'] = $this->offsetGet($i);
	    $res[$i]['title'] = $this->offsetGet('l'.$i);
	    $res[$i]['rid'] = $this->offsetGet('rid'.$i);
	}
	return $res;
    }

    public function setArticleModel($model) {
	$this->articleModel = $model;
    }

    public function getComments() {
	if (!isset($this->__comments))
	    $this->__comments = $this->aricleModel->getComments($this);
	return $this->__comments;
    }

    public function getSections() {
	if (!isset($this->__sections))
		$this->__sections = $this->sectionModel->getSections($this);
	return $this->__sections;
    }
    
    public function __construct($arr = array()) {
	return parent::__construct($arr);
    }
}
