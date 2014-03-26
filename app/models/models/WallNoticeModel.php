<?php

namespace SokolMor\Models;

/**
 * Description of NoticeModel
 *
 * @author Michal Fucik
 */

final class WallNoticeModel extends BaseModel {

	public function getNotices() {
		
		$data = $this->getAll('article')->join('section')->on('section_id = article_section_id')
				->where('[article_type] = %s','notice')->orderBy('article_added', 'DESC')
				->execute()->fetchAll();
		return $data;
	}
}
