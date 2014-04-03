<?php

namespace FrontModule;

/**
 * @author Michal Fucik
 * @package SokolMor
 */
final class ArticlePresenter extends \BasePresenter {

    /** @var articleId */
    private $articleId;
    
    public function setArticleId($articleId) {
	if (!is_numeric($articleId))
	    throw new \Nette\InvalidArgumentException("Argument articleId has to be type of numeric");
	$this->articleId = $articleId;
    }

        public function renderDefault() {
	try {
	    $this->template->articles = $this->models->article->getRecent();
	    $this->template->today = date('Y-m-d G:i');
	} catch(DataErrorException $x) {
	    $this->flashMessage('Nepodařilo se nic načíst','warning');
	}
    }

    public function renderShow($id) {

	$tmp = FALSE;
	$fileSec = NULL;

	if (is_numeric($id)) {
	    $tmp = $this->models->article->getArticle($id);
	    $fileSec = $this->models->file_section->getSection($id);
	}
	$this->setArticleId($id);
	if ($tmp === FALSE) {
	    $this->flashMessage('Daný článek nelze zobrazit.', 'warning');
	} else {

	    $this->models->article->incViews($id, $tmp->article_count);
	}
	$comments = FALSE;
	if ($tmp[\SokolMor\Article::STATUS_ID] == \SokolMor\Article::STATUS_OPEN)
	    $comments = TRUE;
	$this->template->article = $tmp;
	$this->template->comments = $comments;
	$this->template->sections = $fileSec;
    }
       public function createComponentComments($name) {
	$c = new \SokolMor\Compoments\CommentsControl($this, $name);
	$c->setArticleModel($this->models->article);
	$c->setArticleId($this->articleId);
	//$c->setUserId($this->user->id == NULL? 1: $this->user->id);
	return $c;
    }
    
    public function createComponentAddCommentForm($name) {
	$form = new \SokolMor\Compoments\CommentsControl\Forms\CommentForm($this, $name);
	$form->setId($this->articleId);
	$form->buildUp();
	return $form;
    }
    
    public function createComment($values) {
	$values->offsetSet('comment_author_id', $this->user->id == NULL? 1: $this->user->id);
	$comment = new \SokolMor\Comment($values);
	try {
	    $this->models->article->createComment($comment);
	} catch (\DataErrorException $x) {
	    $this->presenter->flashMessage('Komentář se nepodařilo přidat', 'warning');
	}
	$this->redirect('this');
    }
}
