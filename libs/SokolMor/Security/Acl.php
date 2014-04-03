<?php
namespace SokolMor\Security;

use Nette\Security\Permission;
/**
 * Access control list
 * @author Michal Fucik <michal.fuca.fucik(at)gmail.com>
 */
class Acl extends Permission {
    
    const ROLE_ID_ID		= 'role_id';
    const ROLE_NAME_ID		= 'role_name';
    const ROLE_PARENT_ID	= 'parent';
    
    const RESOURCE_ID_ID	= 'resource_id';
    const RESOURCE_LINK_ID	= 'link';
    const RESOURCE_PARENT_ID	= 'parent';
    
    const PRIVILEGE_ROLE_ID	= 'role_id';
    const PRIVILEGE_RESOURCE_ID = 'resource_id';
    const PRIVILEGE_PRIV_ID	= 'privilege';
    const PRIVILEGE_MODE_ID	= 'mode';
    
    /* @var access control list model */
    private $aclModel;
    
    public function __construct($model) {
	if ($model === NULL) {
	    throw new \InvalidArgumentException('Acl model can\'t be null');
	}
	$this->aclModel = $model;
	$roles		= $model->getRoles();
	$resources	= $model->getResources();
	$privileges	= $model->getPrivileges();

	foreach($roles as $role) {
	    if ($role[self::ROLE_PARENT_ID] !== NULL) {
		    $this->addRole($role[self::ROLE_NAME_ID], 
			    $roles[$role[self::ROLE_PARENT_ID]][self::ROLE_NAME_ID]);
		    //dump("addRole(".$role[self::ROLE_NAME_ID].', '.$roles[$role[self::ROLE_PARENT_ID]][self::ROLE_NAME_ID].')');
	    } else {
		$this->addRole($role[self::ROLE_NAME_ID]);
		//dump("addRole(".$role[self::ROLE_NAME_ID].')');
	    }
	}
	
	foreach ($resources as $res) {
	    if ($res[self::RESOURCE_PARENT_ID] !== NULL) {
		$this->addResource($res[self::RESOURCE_LINK_ID], 
			$resources[$res[self::RESOURCE_PARENT_ID]][self::RESOURCE_LINK_ID]);
	    } else {
		$this->addResource($res[self::RESOURCE_LINK_ID]);
	    }
	}

	foreach ($privileges as $pv) {
	    if ($pv[self::PRIVILEGE_MODE_ID] == 1) {
		if ($pv[self::PRIVILEGE_RESOURCE_ID] == NULL) {
		    $this->allow($roles[$pv[self::PRIVILEGE_ROLE_ID]]);
		//dump($roles[$pv[self::PRIVILEGE_ROLE_ID]]);
		}
		else
		    $this->allow($roles[$pv[self::PRIVILEGE_ROLE_ID]][self::ROLE_NAME_ID], 
			    $resources[$pv[self::PRIVILEGE_RESOURCE_ID]][self::RESOURCE_LINK_ID], 
			    $pv[self::PRIVILEGE_PRIV_ID]);
		//dump($roles[$pv[self::PRIVILEGE_ROLE_ID]][self::ROLE_NAME_ID].', '.$resources[$pv[self::PRIVILEGE_RESOURCE_ID]][self::RESOURCE_LINK_ID]);
	    } else {
		if ($pv[self::PRIVILEGE_RESOURCE_ID] == NULL) {
		    $this->deny($roles[$pv[self::PRIVILEGE_ROLE_ID]]);
		//dump('deny '.$roles[$pv[self::PRIVILEGE_ROLE_ID]]);
		}
		else
		    $this->deny($roles[$pv[self::PRIVILEGE_ROLE_ID]][self::ROLE_NAME_ID], 
			    $resources[$pv[self::PRIVILEGE_RESOURCE_ID]][self::RESOURCE_LINK_ID], 
			    $pv[self::PRIVILEGE_PRIV_ID]);
		//dump('deny '.$roles[$pv[self::PRIVILEGE_ROLE_ID]][self::ROLE_NAME_ID].', '. $resources[$pv[self::PRIVILEGE_RESOURCE_ID]][self::RESOURCE_LINK_ID]);
	    }
	}
    }
}
