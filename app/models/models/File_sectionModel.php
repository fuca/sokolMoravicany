<?php

namespace SokolMor\Models;

/**
 * FileModel
 * @author fuca
 * @package SokolMor
 */

class File_sectionModel extends BaseModel {
	
	public function getSections() {
		
		return $this->getAll()
				->where('[file_section_article_id] IS NULL')
				->getResult()
				->setRowClass('SokolMor\\'.ucfirst($this->tableName));
	}
	/** @param article id*/
	public function getSection($id = NULL) {
		
		$result = array();
		if ($id === NULL) {
			//throw new \Nette\InvalidArgumentException('Argument $id has to be an integer value, NULL given.');
			$sec = $this->getAll()->where('[file_section_article_id] IS NULL')->execute()->fetchAssoc('file_section_id');
		} else {
			
			$sec = $this->getAll()->where('[file_section_article_id] = %i', $id)->execute()->fetchAssoc('file_section_id');
		}
		
		$files = $this->getAll('file');

		foreach ($sec as $key => $s) {
			$files->where('[file_section_id] = %i', $key);
		}
		
		$files->_change_where_separator("AND", "OR"); // fuj
		
		$files = $files->execute()->fetchAll();
		
		foreach ($files as $f) {
			$k = $f->file_section_id;
			if (!isset($sec[$k]))
				continue;
			// kdyz neni ve vyslednem poli sekce
			if (!isset($result[$k])){
				// tak ji pridam
				$result[$k] = $sec[$k];
				$result[$k]['files'] = array();
			}
			
			$result[$k]['files'] = array_merge($result[$k]['files'], array($f));
			
		}
		return $result;
	}
	
}
