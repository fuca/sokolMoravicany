<?php

namespace SokolMor\Components;

/**
 * SponsorControl
 *
 * @author fuca
 * @package SokolMor
 */

class SponsorControl extends \Nette\Application\UI\Control {
	
	/** @var array Sponsors */
	private $__sponsors;
	
	public function getSponsors() {
		return $this->__sponsors;
	}
	
	public function __construct($parent, $name, array $sponsors) {
		
		parent::__construct($parent, $name);
		$this->__sponsors = $sponsors;
	}

	public function render() {
		
		$this->template->setFile(__DIR__ . '/sponsor.latte');
		$this->template->sponsors = $this->getSponsors();
		$this->template->render();
	}
}
