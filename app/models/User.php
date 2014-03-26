<?php

namespace SokolMor;

/**
 * @author Michal Fucik
 * @package SokolMor
 */

class User extends \DibiRow {
	
	public function __construct($arr = array()) {

		return parent::__construct($arr);
	}

	public function save() {

		return dibi::update('user', $this->toArray())
				->where('id = %i', $this->id)->execute();
	}

	public function create() {

		return dibi::insert('user', $this->toArray())->execute();
	}

	public function delete() {

		return dibi::delete('user')->where('id = %i', $this->id)->execute();
	}
}