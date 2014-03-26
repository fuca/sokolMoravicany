<?php

namespace SokolMor\Security;

use Nette\Security\Permission;

class Acl extends Permission {

	public function __construct() {
		
		// roles
		$this->addRole('guest');
		$this->addRole('member', 'guest');
		$this->addRole('editor', 'member'); // special type of guest
		$this->addRole('lecture', 'member');
		$this->addRole('admin', 'lecture');
		$this->addRole('supervisor'); 
		
		//resources
		$this->addResource('Admin:Homepage');
		$this->addResource('Admin:Article');
		$this->addResource('Admin:Info');
		$this->addResource('article');
		$this->addResource('comment');
		$this->addResource('file');
		$this->addResource('section');
		
		// privileges
		$this->allow('supervisor');
		$this->allow('admin');
		$this->allow('member');
		$this->allow('editor');
		$this->allow('lecture');
		$this->allow('member', 'Admin:Homepage', array('view','edit','add'));
	}
}
