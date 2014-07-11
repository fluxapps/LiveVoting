<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntryGUI.php');

/**
 * ctrlmmEntryLinkGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntryLinkGUI extends ctrlmmEntryGUI {

	/**
	 * @param string $mode
	 */
	public function initForm($mode = 'create') {
		parent::initForm($mode);
		$te = new ilTextInputGUI($this->pl->txt('link'), 'my_link');
		$te->setRequired(true);
		$this->form->addItem($te);
		$se = new ilSelectInputGUI($this->pl->txt('target'), 'target');
		$opt = array( '_top' => $this->pl->txt('same_page'), '_blank' => $this->pl->txt('new_page') );
		$se->setOptions($opt);
		$this->form->addItem($se);
	}


	public function setFormValuesByArray() {
		$values = parent::setFormValuesByArray();
		$values['my_link'] = $this->entry->getLink();
		$values['target'] = $this->entry->getTarget();

		$this->form->setValuesByArray($values);
	}


	public function createEntry() {
		parent::createEntry();

		$this->entry->setLink($this->form->getInput('my_link'));
		$this->entry->setTarget($this->form->getInput('target'));
		$this->entry->update();
	}
}

?>
