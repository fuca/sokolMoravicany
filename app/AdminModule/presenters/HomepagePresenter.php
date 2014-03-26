<?php

namespace AdminModule;

/**
 * @author Michal Fucik
 * @package SokolMor\AdminModule
 */

final class HomepagePresenter extends SecuredPresenter {
	
	public function startup() {
		
        parent::startup();
		
		$user = $this->getUser();
		
        if (!$user->isLoggedIn()) {
            if ($user->getLogoutReason() === \Nette\Security\User::INACTIVITY) {
                $this->flashMessage('Časový limit sezení vypršel, byl/a jste odhlášen/a.', 'warning');
            }
			
            $backlink = $this->getApplication()->storeRequest();
            $this->redirect(':Front:Homepage:default', array('backlink' => $backlink));
        } else {
            if (!$user->isAllowed($this->name, $this->action)) {
                $this->flashMessage('Přístup zamítnut. Nemáte dostatečné oprávnění pro prohlížení této stránky.', 'warning');
                $this->redirect(':Front:Homepage:default');
            }
        }
    }

    public function actionLogout() {
		
        $this->user->logOut();
        $this->redirect(':Front:Homepage:default');
    }
	
	public function renderDefault() {
		
		
	}
	
	public function renderShow($id) {
		$this->template->article = $this->models->article->getArticle($id);
		
	}
	
	public function createComponentWallNotice($name) {
		
		$con = new \SokolMor\Compoments\WallNotice($this, $name);
		$con->setModel($this->models->wallNotice);
		return $con;
	}
	
	public function createComponentWall($name) {
		
		$con = new \SokolMor\Compoments\WallControl($this, $name);
		$con->setModel($this->models->wall);
		return $con;
	}
}
