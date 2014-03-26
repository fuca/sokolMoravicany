<?php

namespace SokolMor;

/**
 * Menu_item class
 *
 * @author fuca
 */

final class Menu_item extends \DibiRow {

	//private $__nodes = array();
	
//	public function setNodes(array $a) {
//		
//		if (count($a) == 0) {
//			throw new \Nette\InvalidArgumentException();
//		}
//		$this->__nodes = $a;
//	}
//	
//	public function getNodes() {
//		
//		return $this->__nodes;
//	}
	
	public function __construct($arr = array()) {
		parent::__construct($arr);
	}
}
