<?php

namespace SokolMor\Models;

/**
 * RssModel
 *
 * @author Michal Fucik
 */

class RssModel extends BaseModel {
	
	public function getNews() {
		
		$res = $this->getAll('article')
					->where('article_type = %s', 'article')
					->and('article_visible = %s', 'true')
					->orderBy('article_added','DESC')->execute()->fetchAll();
		return $res;
	}
}
