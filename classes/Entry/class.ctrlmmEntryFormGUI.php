<?php
require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');

/**
 * Class ctrlmmEntryFormGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 *         not yet implemented
 * @version 2.0.02
 */
abstract class ctrlmmEntryFormGUI extends ilPropertyFormGUI {

	/**
	 * @var ilHubConfigGUI
	 */
	protected $parent_gui;
	/**
	 * @var  ilCtrl
	 */
	protected $ctrl;


	/**
	 * @param             $parent_gui
	 * @param ctrlmmEntry $entry
	 */
	public function __construct($parent_gui, ctrlmmEntry $entry) {
		global $ilCtrl;
		$this->parent_gui = $parent_gui;
		$this->ctrl = $ilCtrl;
		$this->entry = $entry;
		$this->pl = ilCtrlMainMenuPlugin::getInstance();
		$this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
		$this->initPermissionSelectionForm();
		$this->initForm();
	}


	/**
	 * @param int  $filter
	 * @param bool $with_text
	 *
	 * @return array
	 */
	public static function getRoles($filter, $with_text = true) {
		global $rbacreview;
		$opt = array();
		$role_ids = array();
		foreach ($rbacreview->getRolesByFilter($filter) as $role) {
			$opt[$role['obj_id']] = $role['title'] . ' (' . $role['obj_id'] . ')';
			$role_ids[] = $role['obj_id'];
		}
		if ($with_text) {
			return $opt;
		} else {
			return $role_ids;
		}
	}


	private function initPermissionSelectionForm() {
		$global_roles = self::getRoles(ilRbacReview::FILTER_ALL_GLOBAL);
		$locale_roles = self::getRoles(ilRbacReview::FILTER_ALL_LOCAL);
		$ro = new ilRadioGroupInputGUI($this->pl->txt('permission_type'), 'permission_type');
		$ro->setRequired(true);
		foreach (ctrlmmMenu::getAllPermissionsAsArray() as $k => $v) {
			$option = new ilRadioOption($v, $k);
			switch ($k) {
				case ctrlmmMenu::PERM_NONE :
					break;
				case ctrlmmMenu::PERM_ROLE :
				case ctrlmmMenu::PERM_ROLE_EXEPTION :
					$se = new ilMultiSelectInputGUI($this->pl->txt('perm_input'), 'permission_' . $k);
					$se->setWidth(400);
					$se->setOptions($global_roles);
					$option->addSubItem($se);
					// Variante mit MultiSelection
					$se = new ilMultiSelectInputGUI($this->pl->txt('perm_input_locale'), 'permission_locale_' . $k);
					$se->setWidth(400);
					$se->setOptions($locale_roles);
					// $option->addSubItem($se);
					// Variante mit TextInputGUI
					$te = new ilTextInputGUI($this->pl->txt('perm_input_locale'), 'permission_locale_' . $k);
					$te->setInfo($this->pl->txt('perm_input_locale_info'));
					$option->addSubItem($te);
					break;
				case ctrlmmMenu::PERM_REF_WRITE :
				case ctrlmmMenu::PERM_REF_READ :
					$te = new ilTextInputGUI($this->pl->txt('perm_input'), 'permission_' . $k);
					$option->addSubItem($te);
					break;
				case ctrlmmMenu::PERM_USERID :
					$te = new ilTextInputGUI($this->pl->txt('perm_input_user'), 'permission_user_' . $k);
					$te->setInfo($this->pl->txt('perm_input_user_info'));
					$option->addSubItem($te);
					break;
			}
			$ro->addOption($option);
		}
		$this->addItem($ro);
	}


	private function initForm() {
		global $lng;
		/**
		 * @var $lng ilLanguage
		 */
		$lng->loadLanguageModule('meta');

		$mode = $this->entry->getId() == 0 ? 'create' : 'edit';

		$te = new ilFormSectionHeaderGUI();
		$te->setTitle($this->pl->txt('title'));
		$this->addItem($te);
		$this->setTitle($this->pl->txt('form_title'));
		$this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
		foreach (ctrlmmEntry::getAllLanguageIds() as $language) {
			$te = new ilTextInputGUI($lng->txt('meta_l_' . $language), 'title_' . $language);
			$te->setRequired(ctrlmmEntry::isDefaultLanguage($language));
			$this->addItem($te);
		}
		$type = new ilHiddenInputGUI('type');
		$this->addItem($type);
		$link = new ilHiddenInputGUI('link');
		$this->addItem($link);
		if (count(ctrlmmEntry::getAdditionalFieldsAsArray($this->entry)) > 0) {
			$te = new ilFormSectionHeaderGUI();
			$te->setTitle($this->pl->txt('settings'));
			$this->addItem($te);
		}
		$this->addCommandButton($mode . 'Object', $this->pl->txt('create'));
		if ($mode != 'create') {
			$this->addCommandButton($mode . 'ObjectAndStay', $this->pl->txt('create_and_stay'));
		}
		$this->addCommandButton('configure', $this->pl->txt('cancel'));

		$this->addFields();
	}


	public function addFields() {
	}


	/**
	 * @return array
	 */
	public function returnValuesAsArray() {
		return array();
	}


	public function fillForm() {
		$values = array();

		foreach ($this->entry->getTranslations() as $k => $v) {
			$values['title_' . $k] = $v;
		}
		$perm_type = $this->entry->getPermissionType();
		$values['permission_type'] = $perm_type;
		$role_ids = json_decode($this->entry->getPermission());
		$roles_global = @array_intersect($role_ids, self::getRoles(ilRbacReview::FILTER_ALL_GLOBAL, false));
		$roles_local = @array_intersect($role_ids, self::getRoles(ilRbacReview::FILTER_ALL_LOCAL, false));
		//$roles_local = @array_diff($role_ids, $roles_global); // Bessere Variante, da auch falsche vorhanden
		$values['permission_' . $perm_type] = $roles_global;
		$values['permission_locale_' . $perm_type] = @implode(',', $roles_local); // Variante Textfeld
		// $values['permission_locale_' . $perm_type] = $roles_local; // Variante MultiSelect
		$role_ids_as_string = '';
		if (is_array($role_ids) AND count($role_ids) > 0) {
			$role_ids_as_string = implode(',', $role_ids);
		}
		$values['permission_user_' . $perm_type] = $role_ids_as_string;
		$values['type'] = $this->entry->getType();

		$values = array_merge($values, $this->returnValuesAsArray());

		$this->setValuesByArray($values);
	}


	/**
	 * returns whether checkinput was successful or not.
	 *
	 * @return bool
	 */
	public function fillObject() {
		if (!$this->checkInput()) {
			return false;
		}
		$lngs = array();
		foreach (ctrlmmEntry::getAllLanguageIds() as $lng) {
			if ($this->getInput('title_' . $lng)) {
				$lngs[$lng] = $this->getInput('title_' . $lng);
			}
		}
		$pl = 'permission_locale_';
		$pu = 'permission_user_';
		$p = 'permission_';
		$perm_type = $this->getInput('permission_type');
		$this->entry->setParent($_GET['parent_id']);
		$this->entry->setTranslations($lngs);
		$this->entry->setType($this->getInput('type'));
		$this->entry->setPermissionType($perm_type);
		if ($this->getInput($pl . $perm_type)) {
			$permission = array_merge(explode(',', $this->getInput($pl . $perm_type)), (array)$this->getInput($p . $perm_type));
		} elseif ($this->getInput($pu . $perm_type)) {
			$permission = explode(',', $this->getInput($pu . $perm_type));
		} elseif  ($this->getInput('permission_type') == ctrlmmMenu::PERM_SCRIPT ) {
				$permission = array(0 => $this->getInput('perm_input_script_path'),
				1 => $this->getInput('perm_input_script_class'),
				2 => $this->getInput('perm_input_script_method'));
		} else {
			$permission = (array)$this->getInput($p . $perm_type);
		}
		$this->entry->setPermission(json_encode($permission));

		return true;
	}


	/**
	 * @return bool
	 */
	public function saveObject() {
		if (!$this->fillObject()) {
			return false;
		}
		$this->entry->create();

		return true;
	}


	protected function addCommandButtons() {
		$this->addCommandButton('save', $this->pl->txt('admin_form_button_save'));
		$this->addCommandButton('cancel', $this->pl->txt('admin_form_button_cancel'));
	}


	//
	// Helper
	//

}

?>
