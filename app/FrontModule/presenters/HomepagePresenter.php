<?php

namespace FrontModule;
use BasePresenter;

/**
 * @author     Michal Fucik
 * @package    SokolMor
 */

final class HomepagePresenter extends BasePresenter {
	
	public function actionDefault () {
			
	}
	
	public function renderDefault() {	
		
		$this->template->articles = $this->models->article->getHomepage()->fetchAll();
	}

}
