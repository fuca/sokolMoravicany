<?php

/**
 * Navigation node
 *
 * @author Jan Marek
 * @license MIT
 */

namespace SokolMor\Components;

use Nette\ComponentModel\Container;

final class NavigationNode extends Container {

	/** @var string */
	public $label;

	/** @var string */
	public $url;

	/** @var bool */
	public $isCurrent = false;
	
	public $visibility = false;

	/**
	 * Add navigation node as a child
	 * @staticvar int $counter
	 * @param string $label
	 * @param string $url
	 * @return NavigationNode
	 */
	public function add($label, $url, $visibility) {
		
		$navigationNode = new self;
		$navigationNode->label = $label;
		$navigationNode->url = $url;
		$navigationNode->visibility = $visibility;

		static $counter = 0;
		$this->addComponent($navigationNode, ++$counter);

		return $navigationNode;
	}

	/**
	 * Set node as current
	 * @param bool $current
	 * @return \Navigation\NavigationNode
	 */
	public function setCurrent($current) {
		
		$this->isCurrent = $current;

		if ($current) {
			$this->lookup('Navigation\Navigation')->setCurrentNode($this);
		}

		return $this;
	}

}
