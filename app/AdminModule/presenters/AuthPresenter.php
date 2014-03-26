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
}
