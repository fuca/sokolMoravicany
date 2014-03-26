<?php

namespace SokolMor\Models;

/**
 * ConfigurationModel
 *
 * @author Michal Fučík
 */

class ConfigureModel extends BaseModel {
	
	/**
	 * @param $string name of variable
	 */
	public function getVar($string) {
		return $this->getOne($string, 'configure_var_name')->execute()->fetch();
	}
	
	public function getContactBarData($mode = NULL) {
		
		$res = array();
		
		if ($mode = NULL)
			$res = 	$this->getVar('billing_data');
		else
			$res = $this->getVar('contact_data');
		
		return $res;
			
	}
}
