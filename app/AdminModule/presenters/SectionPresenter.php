<?php

namespace AdminModule;

/**
 * 
 * 
 */

final class SectionPresenter extends SecuredPresenter {
	
	// startup test na to, zda uzivatel patri do dane sekce, aby mohl byt poslan nekam do haje kdyztak
	
	public function renderDefault() {
		$this->template->message = $this->name;
		
	}
}
