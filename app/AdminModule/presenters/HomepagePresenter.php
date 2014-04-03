<?php
namespace AdminModule;

/**
 * @author Michal Fucik
 * @package SokolMor\AdminModule
 */
final class HomepagePresenter extends SecuredPresenter {

    public function renderDefault() {
//	try {
//	    $posts = $this->models->wall->getPosts();
//	} catch(DataErrorException $e) {
//	    $this->flashMessage('Obsah nástěnky se nepodařilo načíst', 'warning');
//	}
//	$this->template->wallPosts = $posts;
	$this->template->title = 'Nástěnka';
    }

    public function createComponentWallNotice($name) {

	$con = new \SokolMor\Compoments\WallNotice($this, $name);
	$con->setModel($this->models->wallNotice);
	return $con;
    }

    public function createComponentWall($name) {

	$con = new \SokolMor\Compoments\WallControl($this, $name);
	$con->setModel($this->models->article);
	return $con;
    }
}
