<?php
namespace AdminModule\Forms;

use AdminModule\Forms\BaseForm,
    Nette\Forms\Form;
/**
 * Description of UserForm
 *
 * @author Michal Fucik
 */
class UserForm extends BaseForm {

    /** @var userId */
    private $userId;
    
    /**@var aclModel */
    private $aclModel;
    
    /** @var sectionModel */
    private $__sectionModel;
    
    public function setRoleModel(\SokolMor\Models\AclModel $roleModel) {
	$this->aclModel = $roleModel;
    }

    public function setUserId($userId) {
	if (!is_numeric($userId))
	    throw new \InvalidArgumentException('Argument userId has to be type of numeric');
	$this->userId = $userId;
    }

    public function setSectionModel(\SokolMor\Models\SectionModel $m) {
	$this->__sectionModel = $m;
    }

    public function __construct($parent, $name, $mode = self::MODE_CREATE) {
	parent::__construct($parent, $name, $mode);
    }

    public function buildUp() {

	$sections = $this->__sectionModel->getSelectSecs();
	$presenter = $this->getPresenter();
	$gender = \SokolMor\User::getGenderSelect();
	$roles = $this->aclModel->getSelectRoles();

	$maxFSize = $presenter->getMaxFileSize();
	//$bytes = $maxFSize / 8;
	$cons = $maxFSize / 1024;
	
	$this->addHidden('user_id');

	$this->addText('user_name', 'Jméno', 25)
		->setRequired('Jméno musí být vyplněno.');

	$this->addText('user_surname', 'Příjmení', 25)
		->setRequired('Příjmení musí být vyplněno.');

	$this->addRadioList('user_gender', 'Pohlaví', $gender)
		->setDefaultValue("f");
	
	$this->addText('user_email', 'Email', 40)
		->addRule(Form::EMAIL, 'Zadaný email není validní')
		->setRequired('E-mail musí být vyplněn.');

	$this->addMultiSelect('user_sections', 'Kategorie', $sections, 5);

	$this->addText('user_nick', 'Přezdívka')
		->addRule(Form::FILLED, 'Přezdívka musí být vyplněna');

	$this->addText('user_phone', 'Telefon')
		->addCondition(Form::FILLED)
		->addRule(Form::NUMERIC, 'Telefon musí být samá čísla')
		->addRule(Form::MIN_LENGTH ,'Telefon musí mít minimálně 9 číslic', 9);
	
	$this->addText('user_code', 'Kód')
		->addCondition(Form::FILLED)
		->addRule(Form::NUMERIC, 'Kód musí být samá čísla');
	
	$this->addText('user_job', 'Zaměstnání');
	$this->addText('user_login', 'Přihlašovací jméno')
		->addRule(Form::FILLED, 'Přihlašovací jméno musí být vyplněno');
	
	$this->addSelect('user_role', 'Role na webu', $roles);
	$this->addText('user_function', 'Funkce v jednotě')
		->addRule(Form::FILLED, 'Funkce musí být vyplněna');
	
	$this->addText('user_password', 'Heslo');
	
//	$this->addUpload('user_picture', 'Obrázek (šetřete místem)')
//		->setOption('description',"$maxFSize")
//		->addCondition(Form::FILLED)
//		->addRule(Form::IMAGE, 'Obrázek musí být ve formátu JPG, PNG nebo GIF.')
//		->addRule(Form::MAX_FILE_SIZE, "Velikost obrázku musí být maximálně $cons kB.", $maxFSize);
	
	$this->addSubmit('submitButton', 'Uložit uživatele');
	$this->onSuccess[] = callback($this, 'formSubmitted');
    }

    public function formSubmitted($form) {
	$values = $form->getValues();
	$presenter = $this->getPresenter();
	switch ($this->getMode()) {
	    case self::MODE_CREATE:
		$presenter->createUserHandle($values);
		break;
	    case self::MODE_UPDATE:
		$presenter->updateUserHandle($values);
		break;
	}
    }
}
