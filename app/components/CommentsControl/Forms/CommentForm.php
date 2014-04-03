<?php
namespace SokolMor\Compoments\CommentsControl\Forms;

/**
 * Description of CommentForm
 *
 * @author fuca
 */
final class CommentForm extends \AdminModule\Forms\BaseForm {
    
    /** @var id */
    private $id;
    
    public function setId($id) {
	$this->id = $id;
    }
    
    public function __construct($parent, $name, $mode = self::MODE_CREATE) {
	parent::__construct($parent, $name, $mode);
    }

    public function buildUp() {
	
	$this->addHidden('comment_'.\SokolMor\Article::ID_ID)
		->setDefaultValue($this->id);
	$this->addHidden('comment_id');
	$this->addHidden('comment_parent');
	
	$this->addTextArea('comment_content', NULL, 58, 5);
	$this->addSubmit('send', 'Vložit komentář');
	$this->onSuccess[] = callback($this, 'formSubmitted');
    }
    
    public function formSubmitted($form) {
	$values = $form->values;
	switch($this->getMode()) {
	    case self::MODE_CREATE:
		$this->parent->createComment($values);
		break;
	    case self::MODE_UPDATE:
		break;
	}
    }
}
