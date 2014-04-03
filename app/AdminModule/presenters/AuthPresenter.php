<?php
namespace AdminModule;

/**
 * @author Michal Fucik
 * @package SokolMor\AdminModule
 */
final class AuthPresenter extends BasePresenter {

    /** @persistent */
    public $backlink = '';

    public function renderDefault() {
	$this->redirect(':Admin:Homepage:default');
    }
    
    public function actionLogOut() {
	$this->user->logout(TRUE);
	$this->redirect(':Front:Homepage:default');
    }
    
    /* loginFunction is implemented within login form */
}
