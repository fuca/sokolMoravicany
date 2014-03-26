<?php

namespace SokolMor\Models;

/**
 * WallModel
 *
 * @author Michal Fucik
 */

class WallModel extends BaseModel {
	
	public function getPosts(){
		
		$this->getAll('article')->join('section')->on('article_section_id = section_id')
				->where('article_type = %s', 'wall_post')
				->orderBy('article_added','DESC')->execute()->fetchAll();
	}
}
