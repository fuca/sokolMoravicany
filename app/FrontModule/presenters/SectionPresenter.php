<?php

namespace FrontModule;

/** 
 * Section presenter
 * @author Michal Fucik
 * @package SokolMor
 */

final class SectionPresenter extends \BasePresenter {
	
	public function renderDefault() {
		
		$this->template->sections = $this->models->section->getSections()->fetchAll();
	}
}
