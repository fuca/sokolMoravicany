<?php

namespace SokolMor\Models;

/**
 * FileModel
 * @author fuca
 * @package SokolMor
 */
class File_sectionModel extends BaseModel {

    /**
     * Sql factory
     * @return type
     */
    public function getSections() {
	return $this->getAll()
			->where('[file_section_article_id] IS NULL')
			->getResult()
			->setRowClass('SokolMor\\' . ucfirst($this->tableName));
    }

    /**
     * Returns file section with list of files
     * @param type $id
     * @return type
     */
    public function getSection($id = NULL) {
	$result = array();
	if ($id === NULL) {
	    $sec = $this->getAll()->where('[file_section_article_id] IS NULL')
			    ->execute()->fetchAssoc('file_section_id');
	} else {
	    $sec = $this->getAll()->where('[file_section_article_id] = %i', $id)
			    ->execute()->fetchAssoc('file_section_id');
	}
	$files = $this->getAll('file');
	$counter = 0;
	foreach ($sec as $key => $s) {
	    if ($counter > 0) {
		$files->clause('where')->or('[file_section_id] = %i', $key);
		$counter++;
	    } else {
		$files->where('[file_section_id] = %i', $key);
		$counter++;
	    }
	}
	$files = $files->execute()->fetchAll();
	if (count($files) > 0) {
	    foreach ($files as $f) {

		$secId = $f->file_section_id;
		if (isset($sec[$secId])) {
		    if (!isset($result[$secId])) {
			$result[$secId] = $sec[$secId];
			$result[$secId]['files'] = array();
		    }
		    $result[$secId]['files'] = array_merge($result[$secId]['files'], array($f));
		}
	    }
	} else {
	    if (!$sec)
		return $result;
	    foreach ($sec as $s) {
		$s['files'] = array();
	    }
	    $result = $sec;
	}
	return $result;
    }

    /**
     * Creates file_section and appropriate files
     * @param array $fileUploads
     * @param type $id
     * @param type $title
     * @throws \DataErrorException
     */
    public function createSection(array $fileUploads, $id = NULL, $title = 'Související dokumenty') {
	$label = NULL;
	$data = array(
	    'file_section_title' => $title,
	    'file_section_label' => $label,
	    'file_section_article_id' => $id);
	try {
	    $this->database->insert('file_section', $data)->execute();
	    $secId = $this->database->insertId();
	    $this->addFiles($fileUploads, $secId);
	} catch (Exception $x) {
	    throw new \DataErrorException($x);
	}
    }

    private function addFiles(array $fileUploads, $secId) {
	foreach ($fileUploads as $triplet) {
	    $fTitle = $triplet['title'];
	    $fileUpl = $triplet['file'];
	    if ($fileUpl->isOk()) {
		$fName = \Nette\Utils\Strings::random(10) . '-' . $fileUpl->getSanitizedName();
		$filePath = FILE_DIR . '/sekce/' . $secId . '/' . $fName;
		$fileUpl->move($filePath);
		$typeArr = explode('/', $fileUpl->getContentType());
		$type = $typeArr[1];
		$fileData = array(
		    'file_title' => $fTitle,
		    'file_path' => $fName,
		    'file_type' => $type,
		    'file_section_id' => $secId);
		$this->database->insert('file', $fileData)->execute();
	    }
	}
    }

    /**
     * Delete section and its files
     * @param type $id
     * @throws \DataErrorException
     */
    public function deleteSections($id) {
	$secsArr = $this->getSection($id);
	foreach ($secsArr as $sec) {
	    try {
		$sectionId = $sec->file_section_id;
		foreach ($sec->files as $file) {
		    @unlink(FILE_DIR . '/sekce/' . $sectionId . '/' . $file->file_path); // @ - file may not exist
		    $this->database->delete('file')
			    ->where('file_id = %i', $file->file_id)->execute();
		}
		$this->deleteFileSection($sectionId);
	    } catch (Exception $ex) {
		throw new \DataErrorException($ex);
	    }
	}
    }

    /**
     * Deletes file section from database, and delete its directory from filesystem
     * @param type $sectionId
     * @throws \InvalidArgumentException
     */
    private function deleteFileSection($sectionId) {
	if (!is_numeric($sectionId))
	    throw new \InvalidArgumentException("Argument sectionId has to be type of numeric, '$sectionId' given");
	$this->database->delete('file_section')
		->where('file_section_article_id = %i', $sectionId)
		->execute();
	$sectionDir = FILE_DIR . '/sekce/' . $sectionId . '/';
	rmdir($sectionDir);
    }

    /**
     * Returns SokolMor\File associated with given id
     * @param type $fid
     * @return SokolMor\File
     * @throws \Nette\InvalidArgumentException
     * @throws \DataErrorException
     */
    private function getFile($fid) {
	if (!is_numeric($fid))
	    throw new \Nette\InvalidArgumentException("Argument fid has to be type of numeric, '$fid' given");
	try {
	    $res = $this->database->select('*')->from('file')
			    ->where('file_id = %i', $fid)
			    ->execute()->setRowClass('SokolMor\\File')->fetch();
	    return $res;
	} catch (Exception $ex) {
	    throw new \DataErrorException($ex);
	}
    }

    /**
     * Deletes file from database and filesystem, if parent folder should stay empty, 
     * removes it including file_section within database
     * @param type $fileId
     * @throws \Nette\InvalidArgumentException
     * @throws \DataErrorException
     */
    public function deleteFile($fileId) {
	if (!is_numeric($fileId))
	    throw new \Nette\InvalidArgumentException("Argument fileId has to be type of numeric, '$fileId' given");
	try {
	    $file = $this->getFile($fileId);
	    if ($file) {
		$dir = FILE_DIR . '/sekce/' . $file->file_section_id . '/';
		@unlink($dir . $file->file_path); // @ - file may not exist
		$this->database->delete('file')->where('file_id = %i', $fileId)->execute();

		$iterator = new \FilesystemIterator($dir);
		if (!$iterator->valid()) {
		    $this->deleteFileSection($file->file_section_id);
		}
		unset($file);
	    }
	} catch (Exception $ex) {
	    throw new \DataErrorException($ex);
	}
    }

    /**
     * Updates contenf of article's file section
     * @param array $fileUploads
     * @param type $id
     * @throws \DataErrorException
     */
    public function updateSection(array $fileUploads, $id = NULL) {
	try {
	    $section = $this->getSection($id);
	    if (count($section) == 0) {
		$this->createSection($fileUploads, $id);
	    } else {
		reset($section);
		$secId = $section[key($section)]->file_section_id;
		$this->addFiles($fileUploads, $secId);
	    }
	} catch (Exception $ex) {
	    throw new \DataErrorException($ex);
	}
    }

}
