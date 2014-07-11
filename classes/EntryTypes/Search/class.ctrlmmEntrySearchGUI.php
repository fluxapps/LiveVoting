<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntryGUI.php');
require_once('./Services/Search/classes/class.ilMainMenuSearchGUI.php');
require_once('./Services/Search/classes/class.ilSearchSettings.php');
/**
 * ctrlmmEntrySearchGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntrySearchGUI extends ctrlmmEntryGUI {

	/**
	 * @return string
	 */
	public function renderEntry() {
		$main_search = new ilMainMenuSearchGUI();
		$this->html = $this->pl->getTemplate('tpl.admin_entry.html', false, false);
		$this->html->setVariable('DROPDOWN', str_ireplace('ilMainMenuSearch',
			ctrlmmMenu::getCssPrefix() . 'MainMenuSearch', $main_search->getHTML()));
		$this->html->setVariable('CSS_PREFIX', ctrlmmMenu::getCssPrefix());

		return $this->html->get();
	}
}

?>
