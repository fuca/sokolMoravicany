<?php

/**
 * Navigation control
 *
 * @author Jan Marek
 * @license MIT
 */

namespace SokolMor\Components;

use Nette\Application\UI\Control;

class NavigationControl extends Control {

	/** @var NavigationNode */
	private $homepage;

	/** @var NavigationNode */
	private $current;

	/** @var bool */
	private $useHomepage = false;

	/** @var string */
	private $menuTemplate;

	/** @var string */
	private $breadcrumbsTemplate;

	/**
	 * Set node as current
	 * @param NavigationNode $node
	 */
	public function setCurrentNode(NavigationNode $node) {
		
		if (isset($this->current)) {
			$this->current->isCurrent = false;
		}
		$node->isCurrent = true; // $node->setCurrent(true);
		$this->current = $node; // a tohle smazat
	}

	/**
	 * Add navigation node as a child
	 * @param string $label
	 * @param string $url
	 * @return NavigationNode
	 */
	public function add($label, $url, $visibility) {
		
		return $this->getComponent('homepage')->add($label, $url, $visibility);
	}

	/**
	 * Setup homepage
	 * @param string $label
	 * @param string $url
	 * @return NavigationNode
	 */
	public function setupHomepage($label, $url) {
		
		$homepage = $this->getComponent('homepage');
		$homepage->label = $label;
		$homepage->url = $url;
		$this->useHomepage = true;
		return $homepage;
	}

	/**
	 * Homepage factory
	 * @param string $name
	 */
	protected function createComponentHomepage($name) {
		
		new NavigationNode($this, $name);
	}

	/**
	 * Render menu
	 * @param bool $renderChildren
	 * @param NavigationNode $base
	 * @param bool $renderHomepage
	 */
	public function renderMenu($renderChildren = TRUE, $base = NULL, $renderHomepage = TRUE) {
		
		//$template = $this->createTemplate()
		$this->template->setFile($this->menuTemplate ?: __DIR__ . '/navigation.latte');
		$this->template->homepage = $base ? $base : $this->getComponent('homepage');
		$this->template->useHomepage = $this->useHomepage && $renderHomepage;
		$this->template->renderChildren = $renderChildren;
		$this->template->children = $this->getComponent('homepage')->getComponents();
		$this->template->render();
	}

	/**
	 * Render full menu
	 */
	public function render() {
		
		$this->renderMenu();
	}

	/**
	 * Render main menu
	 */
	public function renderMainMenu() {
		
		$this->renderMenu(FALSE);
	}

	/**
	 * Render breadcrumbs
	 */
	public function renderBreadcrumbs() {
		
		if (empty($this->current)) {
			return;
		}

		$items = array();
		$node = $this->current;

		while ($node instanceof NavigationNode) {
			$parent = $node->getParent();
			if (!$this->useHomepage && !($parent instanceof NavigationNode)) {
				break;
			}

			array_unshift($items, $node);
			$node = $parent;
		}

		//$template = $this->createTemplate()
		$this->template->setFile($this->breadcrumbsTemplate ?: __DIR__ . '/breadcrumbs.latte');

		$this->template->items = $items;
		$this->template->render();
	}

	/**
	 * @param string $breadcrumbsTemplate
	 */
	public function setBreadcrumbsTemplate($breadcrumbsTemplate) {
		
		$this->breadcrumbsTemplate = $breadcrumbsTemplate;
	}

	/**
	 * @param string $menuTemplate
	 */
	public function setMenuTemplate($menuTemplate) {
		
		$this->menuTemplate = $menuTemplate;
	}

	/**
	 * @return \Navigation\NavigationNode
	 */
	public function getCurrentNode() {
		
		return $this->current;
	}
	
	/**
	 * @param boolean
	 * @return void
	 */
	public function setUseHomepage($use) {
		$this->useHomepage = $use;
	}

}
