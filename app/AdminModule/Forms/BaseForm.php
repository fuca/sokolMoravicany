<?php
namespace AdminModule\Forms;

use \Nette\Application\UI\Form;

class BaseForm extends Form {
    
    const MODE_CREATE = TRUE;
    const MODE_UPDATE = FALSE;
    
    /** @var mode */
    private $mode;
    
    public function getMode() {
	return $this->mode;
    }

    public function setMode($__mode) {
	$this->mode = $__mode;
    }
    
    public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, $mode = self::MODE_CREATE) {
	parent::__construct($parent, $name);
	$this->setMode($mode);
    }
}