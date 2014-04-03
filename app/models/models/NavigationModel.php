<?php

namespace SokolMor\Models;

/**
 * 
 *
 * @author fuca
 */

final class NavigationModel extends BaseModel {

	public function getItems ($mode = NULL) {
		
		//$items = $this->getAll('menu_item')
				//->join('user')->on('menu_item_author_id = user_id')
		//		->getResult()
		//		->setRowClass('SokolMor\\Menu_item')->fetchAssoc('menu_item_id','menu_item_parent_id');
		
		if ($mode === NULL) {
			
			$mode = '';
		} else {
			
			$mode .= '_';
		}
		
		$items = $this->database->select('*, resources.link AS menu_item_link')->from($mode.'menu_item')
			->innerJoin('resources')->using('(resource_id)')
			->execute()->setRowClass('SokolMor\\Menu_item')
			->fetchAssoc($mode.'menu_item_id');
		
		//$tmp = array();
		
//		foreach($items as $i) {
//			$tmp = array_merge($tmp, array($i->menu_item_id => $i));
//		}
		//$items = $tmp;
		//$result = array(1 => $items[1]);
		//$parent = 1;
		//array_shift($items);
		///$root = $result[1];
		
		/* pouze dve urovne zanoreni vcetne te prvni korenove, pro vice nutno implementovat stromovou strukturu */
		
		//foreach ($items as $it) {
		//	$parent_id = $it->menu_item_parent_id;
		//	$parent = NULL;
			
//			if ($parent_id == 1)
//				$root->setNodes(array_merge ($root->getNodes(), array($it->menu_item_id=>$it)));
//			
//			if (!isset($tmp)) {
//				$resultSet = array_merge($resultSet, array("$parent" => $items[$parent]));
//			}
//			//var_dump($parent);
//			//var_dump($resultSet);
//			//var_dump($resultSet[$parent]);
//			$resultSet[$parent]->setNodes(array_merge($resultSet[$parent]->getNodes(), array($it->menu_item_id => $it)));
//						
//		}
		return $items;
		//return $resultSet;
	}
}
