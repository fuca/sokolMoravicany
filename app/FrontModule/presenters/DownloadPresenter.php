<?php

namespace FrontModule;

/**
 * @author Michal Fucik
 * @package SokolMor
 */

final class DownloadPresenter extends \BasePresenter {
	
	public function renderDefault() {
		
		$sections = $this->models->file_section->getSection();
		foreach ($sections as $s) {
			if (isset($s->files) && count($s->files) > 0) {
				foreach ($s->files as $f) {
					$f->file_path = '/file/verejne/' . $f->file_path . '.' .  $f->file_type;
				}
			}
		}
		$this->template->sections = $sections;
	}
}
