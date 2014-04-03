<?php
namespace SokolMor\Models;

/**
 * SponsorModel
 *
 * @author Michal Fucik <michal.fuca.fucik(at)gmail.com>
 * @package SokolMor
 */
class SponsorModel extends BaseModel {

    /**
     * @param bool $all TRUE/FALSE - ALL/VISIBLE ONLY
     */
    public function getSponsors($all = TRUE) {

	$res = $this->getAll();
	if (!$all) {
	    $res->where('sponsor_visible = %s', 't');
	}
	try {
	    return $res->orderBy('sponsor_id')
		    ->execute()
		    ->setRowClass('SokolMor\\' . ucfirst($this->tableName))
		    ->fetchAll();
	} catch (Exception $x) {
	    throw new \DataErrorException($x);
	}
    }

}
