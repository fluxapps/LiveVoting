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

		$te = new ilTextInputGUI($this->pl->txt('link'), 'url');
		$te->setRequired(true);
		$this->form->addItem($te);

		$se = new ilSelectInputGUI($this->pl->txt('target'), 'target');
		$opt = array( '_top' => $this->pl->txt('same_page'), '_blank' => $this->pl->txt('new_page') );
		$se->setOptions($opt);
		$this->form->addItem($se);

		$get_params = new ctrlmmMultiLIneInputGUI($this->pl->txt("get_parameters"), 'get_params');
		$get_params->setInfo($this->pl->txt('get_parameters_description'));
		$get_params->setTemplateDir($this->pl->getDirectory());

		$get_params->addInput(new ilTextInputGUI($this->pl->txt('get_param_name'), ctrlmmEntryLink::PARAM_NAME));

		$get_params_options = new ilSelectInputGUI($this->pl->txt('get_param_value'), ctrlmmEntryLink::PARAM_VALUE);
		$get_params_options->setOptions(ctrlmmUserDataReplacer::getDropdownData());
		$get_params->addInput($get_params_options);

		$this->form->addItem($get_params);
	}


	public function setFormValuesByArray() {
		$values = parent::setFormValuesByArray();
		$values['url'] = $this->entry->getUrl();
		$values['target'] = $this->entry->getTarget();
		$values['get_params'] = $this->entry->getGetParams();

		$this->form->setValuesByArray($values);
	}


	public function createEntry() {
		parent::createEntry();

		$this->entry->setUrl($this->form->getInput('url'));
		$this->entry->setTarget($this->form->getInput('target'));

		// remove duplicates
		$get_params =  $this->form->getInput('get_params');
		$this->entry->setGetParams(array_intersect_key($get_params, array_unique(array_map('serialize',$get_params))));
		$this->entry->update();
	}
}

?>
