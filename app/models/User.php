<?php

namespace SokolMor;

/**
 * @author Michal Fucik
 * @package SokolMor
 */
class User extends \DibiRow {

    private $sectionModel;

    public function setSectionModel(Models\SectionModel $sectionModel) {
	$this->sectionModel = $sectionModel;
    }

    public static function getGenderSelect() {
	return array('m' => 'Muž', 'f' => 'Žena');
    }

    public function __construct($arr = array()) {

	return parent::__construct($arr);
    }
    
    public function getFormSections() {
	if ($this->offsetExists('user_sections'))
	    return $this->offsetGet('user_sections');
	return array();
    }
    
    public function getId() {
	if ($this->offsetExists('user_id'))
	    return $this->offsetGet ('user_id');
	return NULL;
    }
    
    public function setId($id) {
	if (!is_numeric($id))
	    throw new \Nette\InvalidStateException("Argument id has to be type of numeric, '$id' given");
	$this->offsetSet('user_id', $id);
    }

}
