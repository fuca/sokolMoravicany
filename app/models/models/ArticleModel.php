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
	 * @param $id
	 * @return Sokol\Article
	 */
	public function getById($id) {
		
		return $this->getOne($id);
	}
	/**
	 * @param $name
	 * @return SokolMor\Article
	 */
	public function getByName($name) {
		
		return $this->getOne($name, 'name');
	}
	
	public function getHomepage() {
		
		return $this->getAll()
				->where('article_type = %s', 'about')
				->execute()
				->setRowClass('SokolMor\\'.ucfirst($this->tableName));
	}
	
	/**
	 * Get recents articles ordered by expiration date.
	 * @return array of SokolMor\Article instances
	 */
	public function getRecent() {
		
		$articles = $this->getAll()
				->where('article_type = %s', 'article')
				->orderBy(($this->tableName . '_expires'),'DESC')
				->join('section')->on('section_id = article_section_id')
				->orderBy('article_added','DESC')
				->execute()
				->setRowClass('SokolMor\\'.ucfirst($this->tableName))
				->fetchAssoc('article_id');
		
/* tohle bude potreba az kdyz budu chtit pracovat s komentari na Article:default */
//		foreach ($articles as $art) {
//			$art->comments = $this->getAll('comment')
//					->where('comment_article_id = %i', $art->article_id)
//					->getResult()
//					->setRowClass('SokolMor\\'.ucfirst('comment'))
//					->fetchAll();
//		}
		return $articles;
	}
	
	public function getArticle($id) {
		
		$article = $this->getOne($id)->select(
					$this->database->select('count(*)')->from('comment')
						->where('comment_article_id = article.article_id'))->as('article_comment_count')
					->join('section')->on('section_id = '.$this->tableName . '_section_id')
					->execute()
					->setRowClass('SokolMor\\'.ucfirst($this->tableName))->fetch();
		//$article->setSections($this->getArticleSections($id));
		return $article;
	}
	
	//public function getArticleSections($id) {
		
	//	return $this->getAll('article_section')->where('article_id = %i', $id)->execute()->fetchAll();
	//}
	
	/* WRITE SECTION */
	
	public function incViews($id, $oldValue) {
		
		return $this->database->update('article', array('article_count' => ++$oldValue))
					->where('article_id = %i', (int) $id)
					->execute();
	}
	
	public function createArticle($data) {
		
		$this->database->insert('article', $data)->execute();
		return (int) $this->database->insertId();
	}
}
