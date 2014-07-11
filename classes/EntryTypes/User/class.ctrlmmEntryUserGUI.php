<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/GroupedListDropdown/class.ctrlmmEntryGroupedListDropdownGUI.php');


/**
 * ctrlmmEntryCtrlGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntryUserGUI extends ctrlmmEntryGroupedListDropdownGUI {

	/**
	 * @var ctrlmmEntryUser
	 */
	public $entry;


	/**
	 * @return bool
	 */
	public function isActive() {
		return false;
	}


	/**
	 * Render main menu entry
	 *
	 * @param
	 *
	 * @return html
	 */
	protected function setGroupedListContent() {
		$pg = new ilPersonalSettingsGUI();
		$pg->initGeneralSettingsForm();
	}


	/**
	 * @param string $mode
	 */
	public function initForm($mode = 'create') {
		parent::initForm($mode);
		$te = new ilCheckboxInputGUI($this->pl->txt('logout'), 'logout');
		$this->form->addItem($te);

		$te = new ilCheckboxInputGUI($this->pl->txt('personal_desktop'), 'personal_desktop');
		$this->form->addItem($te);

		$te = new ilCheckboxInputGUI($this->pl->txt('user_image'), 'user_image');
		$this->form->addItem($te);
	}


	public function setFormValuesByArray() {
		$values = parent::setFormValuesByArray();
		$values['logout'] = $this->entry->getLogout();
		$values['personal_desktop'] = $this->entry->getPersonalDesktop();
		$values['user_image'] = $this->entry->getUserImage();
		$this->form->setValuesByArray($values);
	}


	public function createEntry() {
		parent::createEntry();
		$this->entry->setLogout($this->form->getInput('logout'));
		$this->entry->setPersonalDesktop($this->form->getInput('personal_desktop'));
		$this->entry->setUserImage($this->form->getInput('user_image'));
		$this->entry->update();
	}
}

?>
