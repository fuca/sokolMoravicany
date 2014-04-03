<?php

namespace SokolMor\Models;

/**
 * Description of NoticeModel
 *
 * @author Michal Fucik
 */
final class WallNoticeModel extends BaseModel {

    public function getNotices() {
	try {
	    $data = $this->getAll('article')->leftJoin('section')->on('section_id = article_section_id')
			    ->where('[article_type] = %s', 'ntc')->orderBy('article_added', 'DESC')
			    ->execute()->fetchAll();
	} catch (Exception $e) {
	    throw new \DataErrorException($e);
	}
	return $data;
    }

}
