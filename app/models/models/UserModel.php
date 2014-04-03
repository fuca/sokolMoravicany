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
	try {
	    $res = $this->database
			->select('*, [role_name] as [role]')
			->from('[user]')
			->innerJoin('[role]')->on('[user_role] = [role_id]')
			->where('[user_id] = %i', $id)->execute()
			->setRowClass('SokolMor\\' . ucfirst($this->tableName))
			->fetch();
	    return $res;
	} catch (Exception $x) {
	    throw new \DataErrorException($x);
	}
    }

    /**
     * @param $login
     * @return \SokolMor\User
     */
    public function getByLogin($login) {

	return $this->database
			->select('*, [role_name] as [role]')
			->from('[user]')
			->innerJoin('[role]')->on('[user_role] = [role_id]')
			->where('[user_login] = %s', $login)->execute()
			->setRowClass('SokolMor\\' . ucfirst($this->tableName))
			->fetch();
    }

    public function getContacts() {
	return $this->getAll()
			->where('user_function IS NOT NULL')
			->where('user_function <> \'informacni_system\'')
			->execute()
			->setRowClass('SokolMor\\' . ucfirst($this->tableName));
    }

    public function setLastLogin($id) {
	return $this->database->update('user', array('user_last_login' => date('Y-m-d G:i')))->where('user_id = %i', $id)->execute();
    }

    public function getAdminFluent() {
	return $this->database->select('*, role_name AS user_role_name')->from('user')->leftJoin('role')->on('user_role = role_id');
    }

    public function deleteUser($id) {
	if (!is_numeric($id))
	    throw new \InvalidArgumentException('Argument id has to be type of numeric');
	if ($id > 2) {
	    try {
		$this->database->delete('user')
			->where('user_id = %i', (integer) $id)
			->execute();
	    } catch (Exception $ex) {
		throw new \DataErrorException($ex);
	    }
	}
    }

    public function updateUser(\SokolMor\User $u) {
	$uId = $u->offsetGet('user_id');
	$tmp = $this->ignoreValues($u);
	try {
	    $this->database->update('user', $u)->where('user_id = %i', (integer) $uId)
		    ->execute();
	    $this->restoreIgnoredValues($u, $tmp);
	} catch (Exception $ex) {
	    throw new \DataErrorException($ex);
	}
    }

    public function createUser(\SokolMor\User $u) {
	$tmp = $this->ignoreValues($u);
	try {
	    $this->database->insert('user', $u)
		    ->execute();
	    $this->restoreIgnoredValues($u, $tmp);
	    return $this->database->insertId();
	} catch (Exception $ex) {
	    throw new \DataErrorException($ex);
	}
    }

    /**
     * Unset and return value from DibiRow instance
     * @param \DibiRow $obj
     * @param type $string
     * @return type
     * @throws \InvalidArgumentException
     */
    private function ignoreOffset(\DibiRow $obj, $string) {
	if ($obj == NULL)
	    throw new \InvalidArgumentException('Argument obj has to be instance of DibiRow, NULL given');
	if ($string == NULL || $string == '')
	    throw new \InvalidArgumentException('Argument string has to be properly set, \'\' given');
	$tmp = NULL;
	if ($obj->offsetExists($string)) {
	    $tmp = $obj->offsetGet($string);
	    $obj->offsetUnset($string);
	}
	return $tmp;
    }

    /**
     * Store ignoring values into array, which returns
     * @param \DibiRow $a
     * @return type
     */
    private function ignoreValues(\DibiRow $a) {
	$arr = array(
	    'user_sections' => $this->ignoreOffset($a, 'user_sections'),
	    'user_id' => $this->ignoreOffset($a, 'user_id'));
	return $arr;
    }

    /**
     * Restores values from array into DibiRow object
     * @param \DibiRow $a
     * @param array $arr
     * @throws \InvalidArgumentException
     */
    private function restoreIgnoredValues(\DibiRow $a, array $arr) {
	if ($a == NULL)
	    throw new \InvalidArgumentException('Argument obj has to be instance of DibiRow, NULL given');
	foreach ($arr as $key => $item) {
	    $a->offsetSet($key, $item);
	}
    }
}
