<?php

namespace AdminModule;

use Grido\Grid,
    Grido\Components\Columns\Column,
    Grido\Components\Columns\Date,
    Grido\Components\Actions\Action,
    Grido\Components\Filters\Filter,
    Grido\Components\Operation;

/**
 * Description of ArticlePresenter
 *
 * @author Michal Fucik
 */
class ArticlePresenter extends SecuredPresenter {
    /** @var articleId */
    private $articleId;
    
    public function setArticleId($articleId) {
	if (!is_numeric($articleId))
	    throw new \Nette\InvalidArgumentException("Argument articleId has to be type of numeric");
	$this->articleId = $articleId;
    }
    public function beforeRender() {
	parent::beforeRender();
	$this->template->maxFileSize = $this->getMaxFileSize();
    }

    public function actionAddArticle() {
	$this->template->title = 'Přidat novinku';
    }

    public function renderDefault() {
	
    }

    /**
     * @User(loggedIn)
     * @param array $values
     */
    public function createArticleHandle(\Nette\ArrayHash $values) {
	$values[\SokolMor\Article::AUTHOR_ID_ID] = $this->user->id;
	$article = $this->context->createArticle($values);
	$type = $values[\SokolMor\Article::TYPE_ID];
	$articleId = $this->models->article->createArticle($article);
	$article->setId($articleId);
	switch ($type) {
	    case \SokolMor\Article::TYPE_ABOUT:
		break;
	    case \SokolMor\Article::TYPE_NOTICE:
	    case \SokolMor\Article::TYPE_WALLPOST:
	    case \SokolMor\Article::TYPE_ARTICLE:
		$this->models->section->addSections($article->getId(), $article->getFormSections());
		$this->models->file_section->createSection($article->getFormFiles(), $article->getId());
		break;
	}
	$this->redirect(':Admin:Article:default');
    }

    public function actionEditArticle($article_id) {
	if (!is_numeric($article_id)) {
	    $this->flashMessage('Špatný formát argumentu', 'error');
	    $this->redirect("Article:default");
	}
	try {
	    $article = $this->models->article->getArticle($article_id);
	    $article = $this->context->createArticle($article);
	    $sections = $article->getFileSections();
	    reset($sections);
	    $key = key($sections);
	    if (count($sections) > 0) {
		foreach ($sections[$key]['files'] as $i => $f) {
		    $article->offsetSet('l' . ( ++$i), $f->file_title);
		    $article->offsetSet('rid' . ($i), $f->file_id);
		}
	    }
	    $article->offsetSet('article_pic_present', $article[\SokolMor\Article::PICTURE_ID]);

	    $form = $this->getComponent('editArticleForm');
	    $form->setDefaults($article);
	} catch (Exception $ex) {
	    $this->flashMessage('Změny nebylo možné uložit', 'warning');
	}
	$this->template->title = 'Úprava článku';
    }

    public function updateArticleHandle(\Nette\ArrayHash $values) {
	$article = $this->context->createArticle($values);
	$type = $values[\SokolMor\Article::TYPE_ID];
	$this->models->article->updateArticle($article);
	switch ($type) {
	    case \SokolMor\Article::TYPE_ABOUT:
		break;
	    case \SokolMor\Article::TYPE_NOTICE:
	    case \SokolMor\Article::TYPE_WALLPOST:
	    case \SokolMor\Article::TYPE_ARTICLE:
		$this->models->section->addSections($article->getId(), $article->getFormSections());
		$this->models->file_section->updateSection($article->getFormFiles(), $article->getId());
		break;
	}
	$this->redirect(':Admin:Article:default');
    }

    public function createComponentAddArticleForm($name) {

	$form = new \AdminModule\Forms\ArticleForm($this, $name);
	$form->setArticleModel($this->models->article);
	$form->setFileSectionModel($this->models->file_section);
	$form->setSectionModel($this->models->section);
	$form->buildUp();

	return $form;
    }

    public function createComponentEditArticleForm($name) {
	$form = new \AdminModule\Forms\ArticleForm($this, $name, Forms\ArticleForm::MODE_UPDATE);
	$form->setArticleModel($this->models->article);
	$form->setFileSectionModel($this->models->file_section);
	$form->setSectionModel($this->models->section);
	$form->buildUp();

	return $form;
    }

    protected function createComponentArticlesGrid($name) {
	$filterRenderType = Filter::RENDER_OUTER;

	$status = \SokolMor\Article::getCommentsSelect();
	$type = \SokolMor\Article::getTypeSelect();
	$boolean = array('true' => 'Ano', 'false' => 'Ne');

	$statusFilter = array('' => NULL) + $status;
	$typeFilter = array('' => NULL) + $type;
	$booleanFilter = array('' => NULL) + $boolean;

	$grid = new Grid($this, $name);
	$grid->setModel($this->models->article->getAdminFluent());

	$grid->setDefaultPerPage(30);
	$grid->setPrimaryKey(\SokolMor\Article::ID_ID);

	$grid->addColumn(\SokolMor\Article::ID_ID, 'ID')
		->setSortable()
		->setFilter()
		->setSuggestion();

	$grid->addColumn(\SokolMor\Article::TITLE_ID, 'Titulek')
		->setCustomRender(callback($this, 'articleTitleRender'))
		->setSortable()
		->setFilter()
		->setSuggestion();

	$grid->addColumn(\SokolMor\Article::VISIBILITY_ID, 'Veřejn')
		->setReplacement($boolean)
		->setSortable()
		->setFilter(Filter::TYPE_SELECT, $booleanFilter);

	$grid->addColumn(\SokolMor\Article::TYPE_ID, 'Typ')
		->setReplacement($type)
		->setSortable()
		->setFilter(Filter::TYPE_SELECT, $typeFilter);

	$grid->addColumn(\SokolMor\Article::EXPIRE_ID, 'Zobrazit do', Column::TYPE_DATE)
		->setSortable()
		->setDateFormat(Date::FORMAT_DATE)
		->setFilter();

	$grid->addColumn(\SokolMor\Article::STATUS_ID, 'Komentáře')
		->setReplacement($status)
		->setSortable()
		->setFilter(Filter::TYPE_SELECT, $statusFilter);

	$grid->addAction('edit', 'Upravit', Action::TYPE_HREF, 'editArticle');
	$grid->setOperations(array('delete' => 'Smazat'), callback($this, 'articlesGridOperationsHandler'));

	$grid->setFilterRenderType($filterRenderType);
	$grid->setExporting();
	return $grid;
    }
    
    public function articleTitleRender($item) {
	$link = $this->link(':Front:Article:show',$item[\SokolMor\Article::ID_ID]);
	$title = $item[\SokolMor\Article::TITLE_ID];
	return "<a href=\"$link\">$title</a>";
    }

    public function articlesGridOperationsHandler($operation, $ids) {
	switch ($operation) {
	    case 'delete':
		$this->deleteArticles($ids);
		break;
	}
    }

    public function deleteArticles($ids) {
	foreach ($ids as $id) {
	    try {
		$this->models->article->deleteArticle($id);
		$this->models->article->deleteComments($id);
		$this->models->section->deleteSections($id);
		$this->models->file_section->deleteSections($id);
	    } catch (DataErrorException $x) {
		$this->flashMessage("Článek $id nemohl být smazán", 'warning');
	    }
	}
	$this->redirect('this');
    }

    public function deleteFileHandle(\Nette\Forms\Controls\SubmitButton $button) {
	$explode = explode('remove', $button->name);
	$fileId = $button->form['rid' . $explode[1]]->value;
	try {
	    $this->models->file_section->deleteFile($fileId);
	} catch (Exception $e) {
	    $this->flashMessage('Soubor se nepodařilo smazat', 'warning');
	}
	$this->redirect('this');
    }
    
    public function actionShowWallPost($id) {
	$tmp = FALSE;
	$fileSec = NULL;

	if (is_numeric($id)) {
	    $tmp = $this->models->article->getPost($id,'ntc');
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
