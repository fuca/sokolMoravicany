<?php

namespace SokolMor\Models;

/**
 * User model
 * @author Michal Fucik
 * @package SokolMor
 */

final class UserModel extends BaseModel {

	/**
	 * SQL Factory
	 * @return DibiFluent 
	 */
	public function sqlFactory() {
		
	    return $this->database->select('*')->from('user');
	}
	
	/**
	 * @param $id
	 * @return \SokolMor\User 
	 */
	public function getById($id) {
		
		return $this->database
				->select('*, [role_name] as [role]')
				->from('[user]')
				->innerJoin('[role]')->on('[user_role] = [role_id]')
				->where('[user_id] = %i', $id)->execute()
				->setRowClass('SokolMor\\'.ucfirst($this->tableName))
				->fetch();
	}
	
	/**
	 * @param $login
	 * @return \SokolMor\User
	 */
	public function getByLogin($login){
		
		return $this->database
				->select('*, [role_name] as [role]')
				->from('[user]')
				->innerJoin('[role]')->on('[user_role] = [role_id]')
				->where('[user_login] = %s', $login)->execute()
				->setRowClass('SokolMor\\'.ucfirst($this->tableName))
				->fetch();
	}
	
	public function getContacts() {
		return $this->getAll()
				->where('user_function IS NOT NULL')
				->where('user_function <> \'informacni_system\'')
				->execute()
				->setRowClass('SokolMor\\'.ucfirst($this->tableName));
	}
	
	public function setLastLogin($id) {
		
		return $this->database->update('user', array('user_last_login'=>date('Y-m-d G:i')))->where('user_id = %i', $id)->execute();
	}
}
