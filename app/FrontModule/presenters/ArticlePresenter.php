<?php

namespace FrontModule;

/**
 * @author Michal Fucik
 * @package SokolMor
 */

final class ArticlePresenter extends \BasePresenter {
	
	public function renderDefault() {
		
		$this->template->articles = $this->models->article->getRecent();
		$this->template->today = date('Y-m-d G:i');
	}
	
	public function renderShow($id) {
		
		$tmp = FALSE;
		$fileSec = NULL;
		
		if (is_numeric($id)) {
			$tmp = $this->models->article->getArticle($id);
			$fileSec = $this->models->file_section->getSection($id);
		}
		
		if ($tmp === FALSE) {
			$this->flashMessage('Daný článek nelze zobrazit.', 'warning');
		} else {
			
			/* Increments article's view counter */
			$this->models->article->incViews($id, $tmp->article_count);
		}
		
		$this->template->article = $tmp;
		$this->template->sections = $fileSec;
	}
}
