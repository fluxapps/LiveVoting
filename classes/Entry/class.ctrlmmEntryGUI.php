<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ilCtrlMainMenuPlugin.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Menu/class.ctrlmmMenu.php');
require_once('class.ctrlmmEntry.php');
require_once('./Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php');
require_once('./Services/Form/classes/class.ilMultiSelectInputGUI.php');

/**
 * User interface hook class
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           2.0.02
 * @ingroup           ServicesUIComponent
 *
 * @ilCtrl_IsCalledBy ctrlmmEntryGUI: ilAdministrationGUI, ilPersonalDesktopGUI, ilRepositoryGUI, ilCtrlMainMenuConfigGUI
 */
class ctrlmmEntryGUI {

	/**
	 * @var ilTemplate
	 */
	protected $html;
	/**
	 * @var ctrlmmEntry
	 */
	public $entry;
	/**
	 * @var ilPropertyFormGUI
	 */
	public $form;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;


	/**
	 * @param ctrlmmEntry $entry
	 * @param null        $parent_gui
	 */
	public function __construct(ctrlmmEntry $entry, $parent_gui = NULL) {
		global $ilCtrl, $tpl;
		/**
		 * @var $ilCtrl ilCtrl
		 */
		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->pl = ilCtrlMainMenuPlugin::get();
		$this->entry = $entry;
		$this->parent_gui = $parent_gui;
	}


	public function executeCommand() {
		$cmd = $this->ctrl->getCmd('index');
		$this->$cmd();
	}


	public function edit() {
		$form = ctrlmmEntryInstaceFactory::getInstanceByEntryId($this->entry->getId())->getFormObject($this);
		$form->fillForm();
		$this->tpl->setContent($form->getHTML());
	}


	public function update() {
	}


	public function add() {
	}


	public function create() {
	}


	public function confirmDelete() {
	}


	public function delete() {
	}


	/**
	 * @return string
	 */
	public function renderEntry() {
		$this->entry->replacePlaceholders();
		$this->html = $this->pl->getTemplate('tpl.ctrl_menu_entry.html', true, true);
		$this->html->setVariable('TITLE', $this->entry->getTitle());
		$this->html->setVariable('CSS_ID', 'ctrl_mm_e_' . $this->entry->getId());
		$this->html->setVariable('LINK', $this->entry->getLink());
		$this->html->setVariable('CSS_PREFIX', ctrlmmMenu::getCssPrefix());
		$this->html->setVariable('TARGET', $this->entry->getTarget());
		$cssActive = ilCtrlMainMenuPlugin::getConf()->getCssActive();
		$cssInactive = ilCtrlMainMenuPlugin::getConf()->getCssInactive();
		$this->html->setVariable('STATE', ($this->entry->isActive() ? $cssActive : $cssInactive));

		return $this->html->get();
	}


	/**
	 * @return string
	 */
	public function prepareAndRenderEntry() {
		$this->entry->replacePlaceholders();

		return $this->renderEntry();
	}


	/**
	 * @param string $mode
	 */
	public function initForm($mode = 'create') {
		global $lng;
		/**
		 * @var $lng ilLanguage
		 */
		$lng->loadLanguageModule('meta');
		$this->form = new ilPropertyFormGUI();
		$this->initPermissionSelectionForm();
		$te = new ilFormSectionHeaderGUI();
		$te->setTitle($this->pl->txt('title'));
		$this->form->addItem($te);
		$this->form->setTitle($this->pl->txt('form_title'));
		$this->form->setFormAction($this->ctrl->getFormAction($this->parent_gui));
		foreach (ctrlmmEntry::getAllLanguageIds() as $language) {
			$te = new ilTextInputGUI($lng->txt('meta_l_' . $language), 'title_' . $language);
			$te->setRequired(ctrlmmEntry::isDefaultLanguage($language));
			$this->form->addItem($te);
		}
		$type = new ilHiddenInputGUI('type');
		$type->setValue($this->entry->getType());
		$this->form->addItem($type);
		$link = new ilHiddenInputGUI('link');
		$this->form->addItem($link);
		if (count(ctrlmmEntry::getAdditionalFieldsAsArray($this->entry)) > 0) {
			$te = new ilFormSectionHeaderGUI();
			$te->setTitle($this->pl->txt('settings'));
			$this->form->addItem($te);
		}
		$this->form->addCommandButton($mode . 'Object', $this->pl->txt('create'));
		if ($mode != 'create') {
			$this->form->addCommandButton($mode . 'ObjectAndStay', $this->pl->txt('create_and_stay'));
		}
		$this->form->addCommandButton('configure', $this->pl->txt('cancel'));
	}


	/**
	 * @return array
	 */
	public function setFormValuesByArray() {
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
		$this->form->setValuesByArray($values);

		return $values;
	}


	/**
	 * @param int  $filter
	 * @param bool $with_text
	 *
	 * @deprecated
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
		$this->form->addItem($ro);
	}


	public function createEntry() {
		$lngs = array();
		foreach (ctrlmmEntry::getAllLanguageIds() as $lng) {
			if ($this->form->getInput('title_' . $lng)) {
				$lngs[$lng] = $this->form->getInput('title_' . $lng);
			}
		}
		$perm_type = $this->form->getInput('permission_type');
		$this->entry->setParent($_GET['parent_id']);
		$this->entry->setTranslations($lngs);
		$this->entry->setType($this->form->getInput('type'));
		$this->entry->setPermissionType($perm_type);
		if ($this->form->getInput('permission_locale_' . $perm_type)) {
			$permission = array_merge(explode(',', $this->form->getInput('permission_locale_'
				. $perm_type)), (array)$this->form->getInput('permission_' . $perm_type)); // Variante Textfeld
			/*$permission = array_merge((array)$this->form->getInput('permission_locale_'
				. $perm_type), (array)$this->form->getInput('permission_' . $perm_type));*/
		} elseif ($this->form->getInput('permission_user_' . $perm_type)) {
			$permission = explode(',', $this->form->getInput('permission_user_' . $perm_type));
		} else {
			$permission = (array)$this->form->getInput('permission_' . $perm_type);
		}
		$this->entry->setPermission(json_encode($permission));
		$this->entry->create();
	}


	/**
	 * @return bool
	 * @deprecated
	 */
	public function isActive() {
		return $this->entry->isActive();
	}


	/**
	 * @deprecated
	 */
	public static function includeAllEntryTypeGUIs() {
		foreach (scandir(dirname(__FILE__) . '/EntryTypes/') as $file) {
			if (strpos($file, 'GUI.php')) {
				require_once(dirname(__FILE__) . '/EntryTypes/' . $file);
			}
		}
	}
}

?>