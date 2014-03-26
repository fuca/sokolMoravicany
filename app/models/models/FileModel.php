<?php

namespace SokolMor\Models;

/**
 * Description of FileModel
 *
 * @author fuca
 * @package SokolMor
 */

class FileModel extends BaseModel {

	public function getFiles() {
		
		return $this->getAll('file')
				->execute()
				->setRowClass('SokolMor\\'.ucfirst($this->tableName));
	}
}

