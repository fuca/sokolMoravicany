<?php 

namespace SokolMor;

class Article extends \DibiRow {
	
	/**
	 * @var Comments
	 */
	private $__comments = array();
	private $__sections = array();
	
	public function getComments(){
		
		return $this->__comments;
	}
	
	public function setComments(array $cs) {
		
		if (!isset($cs) || !is_array($cs)) {
			throw new \Nette\InvalidArgumentException('Argument has to be unempty array.');
		}
		return $this->__comments;
	}
	
	public function setSections(array $secs) {
		
		$this->__sections = $secs;
	}
	
	public function __construct($arr = array()) {
		return parent::__construct($arr);
	}

	public function save() {
		
		return \dibi::update('article', $this->toArray())
				->where('id = %i', $this->id)->execute();
	}
	
	public function create() {
		
		return \dibi::insert('article', $this->toArray())->execute();
	}
	
	public function delete() {
		return \dibi::delete('article')->where('id = %i', $this->id)->execute();
	}
	
	
}