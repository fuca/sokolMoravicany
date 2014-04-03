<?php

namespace FrontModule;

use BasePresenter;

/**
 * @author     Michal Fucik
 * @package    SokolMor
 */
final class HomepagePresenter extends BasePresenter {

    public function actionDefault() {
	try {
	    $articles = $this->models->article->getHomepage()->fetchAll();
	    $this->template->articles = $articles;
	} catch (DataErrorException $x) {
	    $this->flashMessage('Bohužel se nic nepodařilo načíst', 'warning');
	}
    }
}
