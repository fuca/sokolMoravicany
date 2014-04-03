<?php

namespace AdminModule;

use Grido\Grid,
    Grido\Components\Columns\Column,
    Grido\Components\Columns\Date,
    Grido\Components\Actions\Action,
    Grido\Components\Filters\Filter;

final class UserPresenter extends SecuredPresenter {

    /** @var userId */
    private $userId;

    public function setUserId($id) {
	if (!is_numeric($id))
	    throw new \InvalidArgumentException("Argument id has to be type of numeric, '$id' given");
	$this->userId = $id;
    }

    public function actionDefault() {
	
    }

    public function actionDetail() {
	
    }

    public function actionAddUser() {
	$this->template->title = 'Nový uživatel';
    }

    public function actionEditUser($user_id) {
	$this->setUserId($user_id);
	try {
	    $user = $this->models->user->getById($user_id);
	    $form = $this->getComponent('editUserForm');
	    $user->offsetUnset('user_password');
	    $sections = $this->models->section->getUserSections($user_id);
	    $user->offsetSet('user_sections', $sections);
	    $form->setDefaults($user);
	} catch (Exception $ex) {
	    $this->flashMessage('Data o uživateli se nepodařilo načíst', 'error');
	}
	$this->template->user = $user;
	$this->template->title = 'Úprava uživatele';
    }

    protected function createComponentUsersGrid($name) {
	$filterRenderType = Filter::RENDER_OUTER;

	$status = \SokolMor\Article::getCommentsSelect();
	$type = \SokolMor\Article::getTypeSelect();
	$boolean = array('true' => 'Ano', 'false' => 'Ne');

	$statusFilter = array('' => NULL) + $status;
	$typeFilter = array('' => NULL) + $type;
	$booleanFilter = array('' => NULL) + $boolean;

	$grid = new Grid($this, $name);
	$grid->setModel($this->models->user->getAdminFluent());

	$grid->setDefaultPerPage(30);
	$grid->setPrimaryKey('user_id');

	$grid->addColumn('user_id', 'ID')
		->setSortable()
		->setFilter()
		->setSuggestion();
	$grid->addColumn('user_gender', '')
		->setCustomRender(callback($this, 'userGenderRender'))
		->setSortable()
		->setFilter(Filter::TYPE_SELECT, $statusFilter);

	$grid->addColumn('user_name', 'Jméno')
		->setSortable()
		->setFilter()
		->setSuggestion();

	$grid->addColumn('user_surname', 'Příjmení')
		->setSortable()
		->setFilter(Filter::TYPE_SELECT, $booleanFilter);

	$grid->addColumn('user_role_name', 'Typ')
		->setSortable()
		->setFilter(Filter::TYPE_SELECT, $typeFilter);

	$grid->addColumn('user_email', '')
		->setCustomRender(callback($this, 'userEmailRender'))
		->setSortable()
		->setFilter()
		->setSuggestion();

	$grid->addColumn('user_phone', '')
		->setCustomRender(callback($this, 'userPhoneRender'))
		->setSortable()
		->setFilter()
		->setSuggestion();

	$grid->addColumn('user_last_login', 'Posl. přihl.', Column::TYPE_DATE)
		->setSortable()
		->setDateFormat(Date::FORMAT_DATE)
		->setFilter();

	$grid->addAction('edit', 'Upravit', Action::TYPE_HREF, 'editUser');
	$grid->setOperations(array('delete' => 'Smazat'), callback($this, 'usersGridOperationsHandler'));

	$grid->setFilterRenderType($filterRenderType);
	$grid->setExporting();
	return $grid;
    }

    public function userGenderRender($item) {
	$gender = $item->user_gender;
	return "<img src=\"/sokolMoravicany/www/image/design/ico/people/$gender.png\" class=\"user-grid\" />";
    }

    public function userPhoneRender($item) {
	$phone = $item->user_phone == NULL ? 'Nezadáno' : $item->user_phone;

	return "<img src=\"/sokolMoravicany/www/image/design/ico/cell.png\" class=\"user-grid\" title=\"$phone\" />";
    }

    public function userEmailRender($item) {
	$email = $item->user_email == NULL ? 'Nezadáno' : $item->user_email;
	return "<a href=\"mailto:$email\"><img src=\"/sokolMoravicany/www/image/design/ico/email.png\" class=\"user-grid\" title=\"$email\" /></a>";
    }

    public function usersGridOperationsHandler($operation, $ids) {
	switch ($operation) {
	    case 'delete':
		$this->deleteUsers($ids);
		break;
	}
    }

    public function deleteUsers($ids) {
	foreach ($ids as $id) {
	    try {
		$this->models->user->deleteUser($id);
		$this->models->section->deleteUserSections($id);
	    } catch (DataErrorException $x) {
		$this->flashMessage("Uživatel $id nemohl být smazán", 'warning');
	    }
	}
	$this->redirect('this');
    }

    public function createComponentAddUserForm($name) {
	$form = new Forms\UserForm($this, $name);
	$form->setSectionModel($this->models->section);
	$form->setRoleModel($this->models->acl);
	$form->buildUp();
	return $form;
    }

    public function createComponentEditUserForm($name) {
	$form = new Forms\UserForm($this, $name, Forms\UserForm::MODE_UPDATE);
	$form->setSectionModel($this->models->section);
	$form->setRoleModel($this->models->acl);
	$form->setUserId($this->userId);
	$form->buildUp();
	return $form;
    }

    public function createUserHandle(\Nette\ArrayHash $values) {
	$salt = $this->context->parameters['models']['salt'];
	$password = $values['user_password'];
	$values['user_password'] = sha1($password . $salt);
	$user = $this->context->createMember($values);

	try {
	    $uId = $this->models->user->createUser($user);
	    $user->setId($uId);
	    $this->models->section->addUserSections($user->getFormSections(), $user->getId());
	} catch (Exception $ex) {
	    $this->flashMessage('Uživatel nemohl být uložen', 'warning');
	}
    }

    public function updateUserHandle($values) {
	$salt = $this->context->parameters['models']['salt'];
	$password = $values['user_password'];

	if ($password != "" || $password !== NULL) {
	    $values['user_password'] = sha1($password . $salt);
	} else {
	    $values->offsetUnset('user_password');
	}
	$user = $this->context->createMember($values);
	try {
	    $this->models->user->updateUser($user);
	    $this->models->section->deleteUserSections($user->getId());
	    $this->models->section->addUserSections($user->getFormSections(), $user->getId());
	} catch (Exception $ex) {
	    $this->flashMessage('Uživatel nemohl být uložen', 'warning');
	}
    }
}
