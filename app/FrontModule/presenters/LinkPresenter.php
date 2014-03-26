<?php

namespace FrontModule;

/**
 * @author Michal Fucik
 * @package SokolMor
 */

final class LinkPresenter extends \BasePresenter {
	
	public function renderDefault() {
		$this->template->references = $this->models->reference->getReferences();
	}
}
