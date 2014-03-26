<?php

namespace SokolMor\Models;

abstract class BaseModel extends \Nette\Object {
	
	/** @var \Nette\DI\Container */
	private $context;
	protected $tableName;

	public function __construct(\Nette\DI\Container $container) {
		
		$this->context = $container;
		$classNameParts = explode('\\', get_class($this));
		$pr = explode('Model',array_pop($classNameParts));
		$this->tableName = strtolower($pr[0]);
	}
	
	/**
	* @return \Nette\DI\Container
	*/
	final public function getContext() {
	
		return $this->context;
	}

	/**
	* @return \DibiConnection
	*/
	final public function getDatabase() {
	
		return $this->context->database;
	}
	
	/**
	 * @return \Nette\Http\Session
	 */
	final public function getSession() {
	
		return $this->context->session;
	}
	/**
	 * @return assoc array
	 */
	public function getRoles() {
		
		return $this->database->select('role_name')->execute()->fetchPairs('id');
	}
	
	/**
	 * @return \Dibi\Fluent
	 */
	public function getAll($tableName = NULL) {
		
		$tmp = NULL;
		$tableName = ($tableName === NULL ? $this->tableName : $tableName);
		
		if ($tableName !== 'user') {
			$tmp = $this->database->select('*')->from($tableName)
				->leftJoin('[user]')->on('[user_id] = ' . $tableName . '_author_id')
					->join('[address]')->on('[address_id] = user_address_id');
		} else {
			
			$tmp = $this->database->select('*')->from($tableName)
				->leftJoin('[address]')->on('[address_id] = ' . $tableName . '_address_id');
		}
		
		return $tmp;
	}
	
	/**
	 * @param Key value.
	 * @param Table column name. If not set, find by 'id' is performed.
	 * 
	 * @return entity instance - depends on model type
	 */
	public function getOne($val, $col = NULL) {
		
		$tmp = $this->database->select('*')->from("$this->tableName")
					->join('user')->on('user_id = '.$this->tableName . '_author_id');
		
		$col = ($col === NULL ? ($this->tableName . '_id'):$col);
			
		$tmp = $tmp->where("$col = '$val'");
		return $tmp;
					
	}
}

