<?php
namespace SokolMor\Models;

/**
 * Access control list model
 * @author Michal Fucik <michal.fuca.fucik(at)gmail.com>
 */
final class AclModel extends \SokolMor\Models\BaseModel {
    
    const ROLES_TABLE_NAME	= 'role';
    const ROLE_IDENTIFIER	= 'role_id';
    const RESOURCES_TABLE_NAME	= 'resources';
    const RESOURCE_IDENTIFIER	= 'resource_id';
    const PRIVILEGES_TABLE_NAME = 'role_resource';
    
    /**
     * Returns array of roles available from table role
     * Method is implemented within BaseModel due to backward compatibility
     * @return array
     */
    public function getRoles() {
	return parent::getRoles();
    }
    
    /**
     * Returns assoc array of roles for select box
     * @return type
     * @throws \DataErrorException
     */
    public function getSelectRoles() {
	try {
	    return $this->database->select('role_id,role_name')
		    ->from('role')
		    ->execute()->fetchPairs();
	} catch (Exception $e) {
	    throw new \DataErrorException($e);
	}
    }
    
    /**
     * Returns array of all resources contained within resources table
     * @return array
     * @throws \DataErrorException
     */
    public function getResources() {
	try {
	    $res = $this->database->select('*')
		    ->from(self::RESOURCES_TABLE_NAME)
		    ->execute()->fetchAssoc(self::RESOURCE_IDENTIFIER);
	} catch (Exception $ex) {
	    throw new \DataErrorException($ex);
	}
	if (!$res) 
	    $res = array();
	return $res;
    }
    
    /**
     * Returns array of all privileges contained within role_resource table
     * @return array
     * @throws \DataErrorException
     */
    public function getPrivileges() {
	try {
	   $res = $this->database->select('*')
		    ->from(self::PRIVILEGES_TABLE_NAME)
		    ->execute()->fetchAll();
	} catch (Exception $ex) {
	    throw new \DataErrorException($ex);
	}
	if (!$res) 
	    $res = array();
	return $res;
    }
}