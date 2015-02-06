<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntryGUI.php');

/**
 * ctrlmmEntryCtrlGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 2.0.02
 */
class ctrlmmEntryCtrlGUI extends ctrlmmEntryGUI {

	/**
	 * @var ctrlmmEntryCtrl
	 */
	public $entry;


	/**
	 * @param string $mode
	 */
	public function initForm($mode = 'create') {
		$this->tpl->addJavaScript('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/templates/js/check.js');
		$this->tpl->addCss('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/templates/css/check.css');
		parent::initForm($mode);
		$te = new ilTextInputGUI($this->pl->txt('gui_class'), 'gui_class');
		$te->setRequired(true);
		$this->form->addItem($te);
		$te = new ilTextInputGUI($this->pl->txt('cmd'), 'my_cmd');
		$te->setRequired(false);
		$this->form->addItem($te);

		$te = new ilTextInputGUI($this->pl->txt('ref_id'), 'ref_id');
		$this->form->addItem($te);

		$te = new ilHiddenInputGUI('type_id');
		$te->setValue($this->entry->getType());
		$this->form->addItem($te);

		$se = new ilSelectInputGUI($this->pl->txt('target'), 'target');
		$opt = array( '_top' => $this->pl->txt('same_page'), '_blank' => $this->pl->txt('new_page') );
		$se->setOptions($opt);
		$this->form->addItem($se);
	}


	public function setFormValuesByArray() {
		$values = parent::setFormValuesByArray();
		$values['gui_class'] = $this->entry->getGuiClass();
		$values['my_cmd'] = $this->entry->getCmd();
		$values['additions'] = $this->entry->getAdditions();
		$values['ref_id'] = $this->entry->getRefId();
		$values['target'] = $this->entry->getTarget();
		$this->form->setValuesByArray($values);
	}


	public function createEntry() {
		parent::createEntry();
		$this->entry->setGuiClass($this->form->getInput('gui_class'));
		$this->entry->setCmd($this->form->getInput('my_cmd'));
		$this->entry->setAdditions($this->form->getInput('additions'));
		$this->entry->setRefId($this->form->getInput('ref_id'));
		$this->entry->setTarget($this->form->getInput('target'));
		$this->entry->update();
	}
}

?>
