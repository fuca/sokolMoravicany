<?php

namespace SokolMor\Models;

/**
 * @author Michal Fucik
 * @package SokolMor
 */
final class SectionModel extends BaseModel {

    /**
     * Sql factory
     * @return DibiFluent
     * @throws \DataErrorException
     */
    public function getSections() {
	try {
	    return $this->getAll()
			    ->orderBy('section_name')
			    ->execute()
			    ->setRowClass('SokolMor\\' . ucfirst($this->tableName));
	} catch (Exception $x) {
	    throw new \DataErrorException($x);
	}
    }

    /**
     * Returns sections list for select boxes
     * @param type $root
     * @return array of pairs
     * @throws DataErrorException
     */
    public function getSelectSecs($root = FALSE) {
	try {
	    $res = $this->database->select('section_id,section_name')->from('section');
	    if (!$root)
		$res = $res->where('root = 0');
	    $res = $res->execute()
		    ->setRowClass('SokolMor\\' . ucfirst($this->tableName))
		    ->fetchPairs();
	} catch (Exception $x) {
	    throw new DataErrorException($x);
	}
	return $res;
    }

    /**
     * Inserts data into article_section table, and keeps it consistent
     * @param type $id
     * @param array $scs
     */
    public function addSections($id, array $scs) {
	if (!is_numeric($id))
	    throw new \Nette\InvalidArgumentException("Argument id has to be type of numeric, '$id' given");
	try {
	    $this->removeSections($id);
	    foreach ($scs as $s) {
		$this->database->insert('article_section', array('article_id' => $id,
			    'section_id' => $s))
			->execute();
	    }
	} catch (Exception $x) {
	    throw new \DataErrorException($x);
	}
    }

    /**
     * Drops data from article_section table
     * @param \SokolMor\Article $id
     */
    private function removeSections($id) {
	if (!is_numeric($id))
	    throw new \InvalidArgumentException("Argument id has to be type of integer, '$id' given");
	$this->database->delete('article_section')
		->where('article_id = %i', (integer) $id)
		->execute();
    }

    /**
     * Removes article from all sections
     * @param \SokolMor\Article $a
     */
    public function deleteSections($id) {
	$this->addSections($id, array());
    }

    public function addUserSections(array $secs, $id) {
	if (!is_numeric($id))
	    throw new \InvalidArgumentException('Argument id has to be type of numeric');
	try {
	    $this->removeUserSections($id);
	    foreach ($secs as $s) {
		$this->database->insert('user_section', array('user_id' => $id, 'section_id' => $s))
			->execute();
	    }
	} catch (Exception $ex) {
	    throw new \DataErrorException($ex);
	}
    }

    public function deleteUserSections($id) {
	if (!is_numeric($id))
	    throw new \InvalidArgumentException('Argument id has to be type of numeric');
	$this->addUserSections(array(), $id);
    }

    private function removeUserSections($id) {
	if (!is_numeric($id))
	    throw new \InvalidArgumentException('Argument id has to be type of numeric');
	try {
	    $this->database->delete('user_section')
		    ->where('user_id = %i', (integer) $id)
		    ->execute();
	} catch (Exception $ex) {
	    throw new \DataErrorException($ex);
	}
    }
    
    public function getUserSections($id) {
	if (!is_numeric($id))
	    throw new \InvalidArgumentException("Argument id has to be type of numeric, '$id' given");
	try {
	    $res = $this->database->select('section_id')
		    ->from('user_section')->where('user_id = %i', $id)
		    ->execute()->fetchPairs();
	    return $res;
	} catch (Exception $ex) {
	    throw new \DataErrorException($ex);
	}
    }
}
    