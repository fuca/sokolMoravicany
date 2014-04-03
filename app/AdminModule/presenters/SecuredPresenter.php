<?php

namespace AdminModule; 

/**
 * @author Michal Fucik
 * @package SokolMor\AdminModule
 */

class SecuredPresenter extends BasePresenter {
	
	public function startup() {
		
        parent::startup();

        $user = $this->getUser();

        if (!$user->isLoggedIn()) {
            if ($user->getLogoutReason() === \Nette\Security\User::INACTIVITY) {
                $this->flashMessage('Uplynula maximální doba neaktivity! Systém vás z bezpečnostních důvodů odhlásil.', 'warning');
            }

            $backlink = $this->getApplication()->storeRequest();
            $this->redirect(':Front:Homepage:default', array('backlink' => $backlink));

        } else {
            if (!$user->isAllowed($this->name.':'.$this->action,'view')) {
                $this->flashMessage('Na vstup do této sekce nemáte dostatečné oprávnění!', 'warning');
                $this->redirect(':Admin:Homepage:default');    
            }
        }
    }
}
