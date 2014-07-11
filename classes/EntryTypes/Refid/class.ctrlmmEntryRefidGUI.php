<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntryGUI.php');

/**
 * ctrlmmEntryRefidGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntryRefidGUI extends ctrlmmEntryGUI {

	/**
	 * @param string $mode
	 */
	public function initForm($mode = 'create') {
		parent::initForm($mode);
		$te = new ilTextInputGUI($this->pl->txt('ref_id'), 'ref_id');
		$te->setRequired(true);
		$this->form->addItem($te);
		$cb = new ilCheckboxInputGUI($this->pl->txt('recursive'), 'recursive');
		$cb->setValue(1);
		$this->form->addItem($cb);
	}


	public function setFormValuesByArray() {
		$values = parent::setFormValuesByArray();
		$values['ref_id'] = $this->entry->getRefId();
		$values['recursive'] = $this->entry->getRecursive();
		$this->form->setValuesByArray($values);
	}


	public function createEntry() {
		parent::createEntry();
		$this->entry->setRefId($this->form->getInput('ref_id'));
		$this->entry->setRecursive($this->form->getInput('recursive'));
		$this->entry->update();
	}
}

?>
