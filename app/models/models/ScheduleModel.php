<?php

namespace SokolMor\Models;

/**
 * ScheduleModel
 *
 * @author Michal Fucik
 * @package SokolMor
 */

class ScheduleModel extends BaseModel implements IScheduleModel {
	
	public function getOccupation () {
		
		$quarters = $this->getAll()
			->join('section')->on('schedule_section_id = section_id')
			//->orderBy('schedule_hour')
			//->execute()->fetchAll()
				;
		//dump($quarters);
		return $quarters;
	}
}

