<?php

namespace SokolMor\Compoments;

/**
 * Description of CommentsControl
 *
 * @author fuca
 */
class CommentsControl extends \Nette\Application\UI\Control {

    /** @var article entity id */
    private $articleId;

    /** @var article model */
    private $articleModel;

    /** @var form template */
    private $formTemplate;

    /** @var list template */
    private $listTemplate;
    
//    /** @var author id */
//    private $userId;
//    
//    public function setUserId($userId) {
//	$this->userId = $userId;
//    }
    
    public function setFormTemplate($formTemplate) {
	if (!file_exists(__DIR__ . '/' . $formTemplate))
	    throw new \Nette\InvalidStateException('Form template file must exist');
	$this->formTemplate = $formTemplate;
    }

    public function setListTemplate($listTemplate) {
	if (!file_exists(__DIR__ . '/' . $listTemplate))
	    throw new \Nette\InvalidStateException('List template file must exist');
	$this->listTemplate = $listTemplate;
    }

    public function setArticleId($articleId) {
	if (!is_numeric($articleId))
	    throw new \Nette\InvalidArgumentException("Argument articleId has to be type of numeric, '$articleId' given");
	$this->articleId = $articleId;
    }

    public function setArticleModel(\SokolMor\Models\ArticleModel $articleModel) {
	if ($articleModel === NULL)
	    throw new \Nette\InvalidArgumentException('Argument articleModel cannot be NULL');
	$this->articleModel = $articleModel;
    }

    public function __construct($parent, $name) {
	parent::__construct($parent, $name);
	$this->formTemplate = 'defForm.latte';
	$this->listTemplate = 'defList.latte';
    }

    private function checkState() {
	if ($this->listTemplate == "")
	    throw new \Nette\InvalidStateException('List template file name has to be properly set');
	if ($this->formTemplate == "")
	    throw new \Nette\InvalidStateException('Form template file name has to be properly set');
	if (!isset($this->articleId))
	    throw new \Nette\InvalidStateException('Attribute articleId has to be set');
//	if (!isset($this->userId))
//	    throw new \Nette\InvalidStateException('Attribute userId has to be set');
    }

    public function render() {
	$this->renderList();
	$this->renderForm();
    }

    public function renderList() {
	$this->checkState();
	$this->template->setFile(__DIR__.'/'.$this->listTemplate);
	try {
	    $comments = $this->articleModel->getComments($this->articleId);
	} catch (Exception $ex) {
	    $this->presenter->flashMessage('Nepodařilo se načíst komentáře', 'warning');
	}
	$this->template->comments = $comments;
	$this->template->heading = 'Komentáře';
	$this->template->noData = 'Nebyly nalezeny žádné komentáře';
	$this->template->render();
    }
// nette couldn't find signal receiver when form was submitted ... form moved into presenter
//    public function renderForm() {
//	$this->checkState();
//	$this->template->setFile(__DIR__.'/'.$this->formTemplate);
//	$this->template->render();
//    }
    
//    public function createComment($values) {
//	$values->offsetSet('comment_author_id', $this->userId);
//	$comment = new \SokolMor\Comment($values);
//	try {
//	    $this->articleModel->createComment($comment);
//	} catch (\DataErrorException $x) {
//	    $this->presenter->flashMessage('Komentář se nepodařilo přidat', 'warning');
//	}
//    }

//    public function createComponentAddCommentForm($name) {
//	$form = new \SokolMor\Compoments\CommentsControl\Forms\CommentForm($this, $name);
//	$form->setId($this->articleId);
//	$form->buildUp();
//	return $form;
//    }
}
