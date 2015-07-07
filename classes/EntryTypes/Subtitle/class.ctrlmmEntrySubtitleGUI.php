<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntryGUI.php');

/**
 * ctrlmmEntrySubtitleGUI
 *
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @version 1.0.0
 *
 */
class ctrlmmEntrySubtitleGUI extends ctrlmmEntryGUI {

	/**
	 * @param string $mode
	 */
	public function initForm($mode = 'create') {
		parent::initForm($mode);

		$cb = new ilCheckboxInputGUI($this->pl->txt('show_with_no_children'), 'show_with_no_children');
		$this->form->addItem($cb);

		/**
		 * @var $permission_type ilRadioGroupInputGUI
		 * @var $item            ilTextInputGUI
		 */
		$permission_type = $this->form->getItemByPostVar('permission_type');
		foreach (ctrlmmEntry::getAllLanguageIds() as $language) {
			$item = $this->form->getItemByPostVar('title_' . $language);
			$item->setRequired(false);
		}
	}

	public function setFormValuesByArray() {
		$values = parent::setFormValuesByArray();
		$values['show_with_no_children'] = $this->entry->getShowWithNoChildren();
		$this->form->setValuesByArray($values);
	}


	public function createEntry() {
		parent::createEntry();
		$this->entry->setShowWithNoChildren($this->form->getInput('show_with_no_children'));
		$this->entry->update();
	}


}

?>
