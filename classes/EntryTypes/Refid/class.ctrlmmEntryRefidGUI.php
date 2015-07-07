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

		$get_params = new ctrlmmMultiLIneInputGUI($this->pl->txt("get_parameters"), 'get_params');
		$get_params->setInfo($this->pl->txt('get_parameters_description'));
		$get_params->setTemplateDir($this->pl->getDirectory());

		$get_params->addInput(new ilTextInputGUI($this->pl->txt('get_param_name'), ctrlmmEntryRefid::PARAM_NAME));

		$get_params_options = new ilSelectInputGUI($this->pl->txt('get_param_value'), ctrlmmEntryRefid::PARAM_VALUE);
		$get_params_options->setOptions(ctrlmmUserDataReplacer::getDropdownData());
		$get_params->addInput($get_params_options);

		$this->form->addItem($get_params);
	}


	public function setFormValuesByArray() {
		$values = parent::setFormValuesByArray();
		$values['ref_id'] = $this->entry->getRefId();
		$values['recursive'] = $this->entry->getRecursive();
		$values['get_params'] = $this->entry->getGetParams();
		$this->form->setValuesByArray($values);
	}


	public function createEntry() {
		parent::createEntry();
		$this->entry->setRefId($this->form->getInput('ref_id'));
		$this->entry->setRecursive($this->form->getInput('recursive'));

		// remove duplicates
		$get_params =  $this->form->getInput('get_params');
		$this->entry->setGetParams(array_intersect_key($get_params, array_unique(array_map('serialize',$get_params))));
		$this->entry->update();
	}
}

?>
