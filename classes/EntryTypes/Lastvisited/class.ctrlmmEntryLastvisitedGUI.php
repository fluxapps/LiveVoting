<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntryGUI.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/EntryTypes/Dropdown/class.ctrlmmEntryDropdownGUI.php');

/**
 * ctrlmmEntryLastvisitedGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntryLastvisitedGUI extends ctrlmmEntryDropdownGUI {

	/**
	 * @var ctrlmmEntryLastvisited
	 */
	public $entry;


	/**
	 * Render main menu entry
	 *
	 * @param
	 *
	 * @return html
	 */
	protected function setGroupedListContent() {
		foreach ($this->entry->getEntries() as $entry) {
			if ($entry->checkPermission()) {
				$this->gl->addEntry($entry->getTitle(), $entry->getLink(), $entry->getTarget(), '', '',
					'mm_pd_sel_items' . $entry->getId(), '', 'left center', 'right center', false);
			}
		}
	}


	protected function accessKey() {
		if (ilAccessKey::getKey(ilAccessKey::LAST_VISITED) != '') {
			$this->html->setVariable('ACC_KEY_REPOSITORY', 'accesskey=\' . ilAccessKey::getKey(ilAccessKey::LAST_VISITED) . \'');
		}
	}


	/**
	 * @param string $mode
	 */
	public function initForm($mode = 'create') {
		parent::initForm($mode);
		$use_image = new ilCheckboxInputGUI($this->pl->txt('show_icons'), 'show_icons');
		$this->form->addItem($use_image);
	}


	public function setFormValuesByArray() {
		$values = parent::setFormValuesByArray();
		$values['show_icons'] = $this->entry->getShowIcons();
		$this->form->setValuesByArray($values);
	}


	public function createEntry() {
		parent::createEntry();
		$this->entry->setShowIcons($this->form->getInput('show_icons'));
		$this->entry->update();
	}
}

?>
