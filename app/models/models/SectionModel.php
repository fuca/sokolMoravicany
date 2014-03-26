<?php

namespace SokolMor\Models;

/**
 * @author Michal Fucik
 * @package SokolMor
 */

final class SectionModel extends BaseModel {
	
	public function getSections() {
		
		return $this->getAll()
				->orderBy('section_name')
				->execute()
				->setRowClass('SokolMor\\'.ucfirst($this->tableName));
	}
	
	public function getSelectSecs() {
		
		return $this->database->select('section_id,section_name')->from('section')
				->where('section_id > 1')->execute()
				->setRowClass('SokolMor\\'.ucfirst($this->tableName))
				->fetchPairs();
	}	
	
	public function addArticleIntoSection($aId, $sId) {
		
		$this->database->insert('article_section',array('article_id'=>$aId,'section_id'=>$sId));
		return (int) $this->database->insertId();
	}
	
}