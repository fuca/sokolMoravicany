<?php

namespace AdminModule;

/**
 * @author Michal Fucik
 * @package SokolMor
 */

abstract class BasePresenter extends \BasePresenter {
	
	protected function startup() {
		
		parent::startup();
		$this->setLayout('AdminLayout');
	}
	
	
}
