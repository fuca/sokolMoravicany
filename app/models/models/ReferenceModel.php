<?php

namespace SokolMor\Models;

/**
 * ReferenceModel
 *
 * @author fuca
 * @package SokolMor
 */

class ReferenceModel extends BaseModel {
	
	/**
	 * Get all references 
	 * @param bool $cond
	 */
	public function getReferences($cond = FALSE) {
		
		$tmp = $this->getAll();
		if (!$cond) {
			$tmp->where('reference_public = true');
		}
				
		return $tmp->execute()
				->setRowClass('SokolMor\\'.ucfirst($this->tableName));
	}
}
