<?php

namespace SokolMor\Models;

final class ArticleModel extends BaseModel {

    /**
     * SQL Factory
     * @return DibiFluent 
     */
    public function sqlFactory() {

	return $this->database->select('*')->from('article');
    }

    /* READ SECTION */

    /**
     * Sql factory
     * @param $id
     * @return Sokol\Article
     */
    public function getById($id) {
	return $this->getOne($id);
    }

    /**
     * Sql factory
     * @param $name
     * @return SokolMor\Article
     */
    public function getByName($name) {
	return $this->getOne($name, 'name');
    }

    public function getAdminFluent() {
	return $this->getAll();
    }

    /**
     * Returns articles for about homepage screen
     * @return type
     * @throws \DataErrorException
     */
    public function getHomepage() {
	try {
	    return $this->getAll()
			    ->where('article_type = %s AND article_visible = 1', 'abt')
			    ->execute()
			    ->setRowClass('SokolMor\\' . ucfirst($this->tableName));
	} catch (Exception $x) {
	    throw new \DataErrorException($x);
	}
    }

    /**
     * Get recents articles ordered by expiration date.
     * @return array of SokolMor\Article instances
     */
    public function getRecent() {
	try {
	    $articles = $this->getAll()
		    ->where('article_type = %s AND article_visible = 1', \SokolMor\Article::TYPE_ARTICLE)
		    ->orderBy(($this->tableName . '_expires'), 'DESC')
		    ->leftJoin('section')->on('section_id = article_section_id')
		    ->orderBy('article_added', 'DESC')
		    ->execute()
		    ->setRowClass('SokolMor\\' . ucfirst($this->tableName))
		    ->fetchAssoc('article_id');
	} catch (Exception $x) {
	    throw new \DataErrorException($x);
	}
	return $articles;
    }

    /**
     * Returns one article entry
     * @param type $id
     * @return type
     * @throws \DataErrorException
     */
    public function getArticle($id) {
	try {
	    $article = $this->getOne($id)->select(
				    $this->database->select('count(*)')->from('comment')
				    ->where('comment_article_id = article.article_id'))
				    ->as('article_comment_count')
			    ->join('section')->on('section_id = ' . $this->tableName . '_section_id')
			    ->execute()
			    ->setRowClass('SokolMor\\' . ucfirst($this->tableName))->fetch();
	    $sections = $this->database->select('*')
		    ->from('article_section')->where('article_id = %i', $id)
		    ->execute()->setRowClass('SokolMor\\Section')->fetchPairs();
	    $article->offsetSet('article_section_id', $sections);
	} catch (Exception $x) {
	    throw new \DataErrorException($x);
	}
	return $article;
    }

    /**
     * Get all comments for article
     * @param \SokolMor\Models\SokolMor\Article $a
     * @return type
     * @throws \DataErrorException
     */
    public function getComments($id) {

	try {
	    $res = $this->database->select('*, CONCAT(user.user_name,\' \',user.user_surname) AS comment_author')
			    ->from('comment')
			    ->leftJoin('user')->on('comment_author_id = user_id')
			    ->where('comment_article_id = %i', $id)->orderBy('comment_added')->desc()
			    ->execute()->setRowClass('SokolMor\\Comment')->fetchAll();
	} catch (Exception $e) {
	    throw new \DataErrorException($e);
	}
	if (!$res)
	    return array();
	return $res;
    }

    /* /////////////////////////////// WRITE SECTION /////////////////////// */

    /**
     * Increments views counter 
     * @param type $id
     * @param type $oldValue
     * @return type
     * @throws \DataErrorException
     */
    public function incViews($id, $oldValue) {
	try {
	    return $this->database->update('article', array('article_count' => ++$oldValue))
			    ->where(\SokolMor\Article::ID_ID . " = %i", (integer) $id)
			    ->execute();
	} catch (Exception $x) {
	    throw new \DataErrorException($x);
	}
    }

    /**
     * Unset and return value from DibiRow instance
     * @param \DibiRow $obj
     * @param type $string
     * @return type
     * @throws \InvalidArgumentException
     */
    private function ignoreOffset(\DibiRow $obj, $string) {
	if ($obj == NULL)
	    throw new \InvalidArgumentException('Argument obj has to be instance of DibiRow, NULL given');
	if ($string == NULL || $string == '')
	    throw new \InvalidArgumentException('Argument string has to be properly set, \'\' given');
	$tmp = NULL;
	if ($obj->offsetExists($string)) {
	    $tmp = $obj->offsetGet($string);
	    $obj->offsetUnset($string);
	}
	return $tmp;
    }

    /**
     * Store ignoring values into array, which returns
     * @param \DibiRow $a
     * @return type
     */
    private function ignoreValues(\DibiRow $a) {
	$arr = array(
	    \SokolMor\Article::PICTURE_ID => $this->ignoreOffset($a, \SokolMor\Article::PICTURE_ID),
	    \SokolMor\Article::SECTIONS_ID => $this->ignoreOffset($a, \SokolMor\Article::SECTIONS_ID),
	    \SokolMor\Article::ID_ID => $this->ignoreOffset($a, \SokolMor\Article::ID_ID),
		'article_pic_present'=> $this->ignoreOffset($a, 'article_pic_present'),
		'rid1'=>$this->ignoreOffset($a, 'rid1'),
		'rid2'=>$this->ignoreOffset($a, 'rid2'),
		'rid3'=>$this->ignoreOffset($a, 'rid3'),
		'l1'=>$this->ignoreOffset($a, 'l1'),
		'l2'=>$this->ignoreOffset($a, 'l2'),
		'l3'=>$this->ignoreOffset($a, 'l3'));
	for ($i = 1; $i <= \SokolMor\Article::FILE_UPLOAD_COUNT; $i++) {
	    $arr[$i] = $this->ignoreOffset($a, $i);
	}
	return $arr;
    }

    /**
     * Restores values from array into DibiRow object
     * @param \DibiRow $a
     * @param array $arr
     * @throws \InvalidArgumentException
     */
    private function restoreIgnoredValues(\DibiRow $a, array $arr) {
	if ($a == NULL)
	    throw new \InvalidArgumentException('Argument obj has to be instance of DibiRow, NULL given');
	foreach ($arr as $key => $item) {
	    $a->offsetSet($key, $item);
	}
    }

    /**
     * Creates an article
     * @param \SokolMor\Article $a
     * @return type
     * @throws \DataErrorException
     */
    public function createArticle(\SokolMor\Article $a) {
	$articleImgPath = $this->addImage($a[\SokolMor\Article::PICTURE_ID]);
	$arr = $this->ignoreValues($a);
	$a[\SokolMor\Article::PICTURE_ID] = $articleImgPath;
	try {
	    $this->database->insert($this->tableName, $a)
		    ->execute();
	    $id = $this->database->insertId();
	} catch (Exception $x) {
	    throw new \DataErrorException($x);
	}
	$this->restoreIgnoredValues($a, $arr);
	return $id;
    }

    private function addImage(\Nette\Http\FileUpload $fupl) {
	$httpUpFile = $fupl;
	if ($httpUpFile->isOk() && $httpUpFile->isImage()) {
	    $img = $httpUpFile->toImage();
	    //$imgName = \Nette\Utils\Strings::webalize($httpUpFile->getName(), '.');
	    $imgName = \Nette\Utils\Strings::random(10) . '-' . $httpUpFile->getSanitizedName();
	    $filePath = IMG_CONTENT_DIR . '/' . $imgName;
	    $img->resize(150, 150);
	    $img->save($filePath);
	    return $imgName;
	} else {
	    return 'empty.png';
	}
    }
    
    private function deleteImage($id) {
	$a = $this->getArticle($id);
	$iName = $a[\SokolMor\Article::PICTURE_ID];
	if ($iName != "empty.png")
	    @unlink(IMG_CONTENT_DIR . '/' . $iName); // @ - img may not exist
    }

    /**
     * Updates article
     * @param \SokolMor\Article $a
     */
    public function updateArticle(\SokolMor\Article $a) {
	if ($a[\SokolMor\Article::PICTURE_ID]->isOk()) {
	    $this->deleteImage($a->getId());
	    $articleImgPath = $this->addImage($a[\SokolMor\Article::PICTURE_ID]);
	}
	$arr = $this->ignoreValues($a);
	if (isset($articleImgPath))
	    $a[\SokolMor\Article::PICTURE_ID] = $articleImgPath;
	try {
	    $this->database->update($this->tableName, $a)
		    ->where(\SokolMor\Article::ID_ID . " = %i", (integer) $arr[\SokolMor\Article::ID_ID])
		    ->execute();
	} catch (Exception $x) {
	    throw new \DataErrorException($x);
	}
	$this->restoreIgnoredValues($a, $arr);
    }

    /**
     * Removes article from entire World
     * @param \SokolMor\Article $a
     * @throws \DataErrorException
     */
    public function deleteArticle($id) {
	try {
	    $this->deleteImage($id);
	    $this->database->delete('article')
		    ->where(\SokolMor\Article::ID_ID . " = %i", (integer) $id)
		    ->execute();
	} catch (Exception $e) {
	    throw new \DataErrorException($e);
	}
    }

    /**
     * Removes one certain article comment from database
     * @param \SokolMor\Comment $c
     * @throws \DataErrorException
     */
    public function deteleComment(\SokolMor\Comment $c) {
	try {
	    $this->database->delete('comment')
		    ->where('id_comment = %i', $c->getId());
	} catch (Exception $e) {
	    throw new \DataErrorException($e);
	}
    }

    /**
     * Delete article's comments from database
     * @param \SokolMor\Article $a
     */
    public function deleteComments($aId) {
	try {
	    $this->database->delete('comment')
		    ->where('comment_article_id = %i', $aId)
		    ->execute();
	} catch (Exception $x) {
	    throw new \DataErrorException($x);
	}
    }
    
    public function createComment(\SokolMor\Comment $c) {
	
	try {
	    $this->database->insert('comment', $c)
		    ->execute();
	} catch(Exception $x) {
	    throw new \DataErrorException($x);
	}
    }
    
    
    public function getPosts() {
	try {
	    $res = $this->getAll('article')->leftJoin('section')
			    ->on('article_section_id = section_id')
			    ->where('article_type = %s', 'wpo')
			    ->orderBy('article_added', 'DESC')
			    ->execute()->fetchAll();
	} catch (Exception $e) {
	    throw new \DataErrorException($e);
	}
	return $res;
    }
    
    public function getPost($id, $keyType = 'wpo') {
	try {
	    $article = $this->database->select('*')->from("$this->tableName")
					->join('user')->on('user_id = '.$this->tableName . '_author_id')
					->where('article_id = %i AND article_type = %s', $id, $keyType)
				    ->select(
				    $this->database->select('count(*)')->from('comment')
				    ->where('comment_article_id = article.article_id'))
				    ->as('article_comment_count')
			    ->join('section')->on('section_id = ' . $this->tableName . '_section_id')
			    ->execute()
			    ->setRowClass('SokolMor\\' . ucfirst($this->tableName))->fetch();
	    $sections = $this->database->select('*')
		    ->from('article_section')->where('article_id = %i', $id)
		    ->execute()->setRowClass('SokolMor\\Section')->fetchPairs();
	    if ($article)
	    $article->offsetSet('article_section_id', $sections);
	} catch (Exception $x) {
	    throw new \DataErrorException($x);
	}
	return $article;
    }

}
