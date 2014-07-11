<?php
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntryFormGUI.php');

/**
 * Class ctrlmmEntryCtrlFormGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 2.0.02
 */
class ctrlmmEntryCtrlFormGUI extends ctrlmmEntryFormGUI {

	const F_REF_ID = 'ref_id';
	const F_CMD = 'cmd';
	const F_GUI_CLASS = 'gui_class';
	const F_ADDITIONS = 'additions';
	/**
	 * @var ctrlmmEntryCtrl
	 */
	protected $entry;


	public function addFields() {
		global $tpl;

		$tpl->addJavaScript('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/templates/js/check.js');
		$tpl->addCss('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/templates/css/check.css');

		$te = new ilTextInputGUI($this->pl->txt(self::F_GUI_CLASS), self::F_GUI_CLASS);
		$te->setRequired(true);
		$this->addItem($te);

		$te = new ilTextInputGUI($this->pl->txt(self::F_CMD), 'my_cmd');
		$te->setRequired(false);
		$this->addItem($te);

		$te = new ilTextInputGUI($this->pl->txt(self::F_REF_ID), self::F_REF_ID);
		$this->addItem($te);
	}


	/**
	 * @return array
	 */
	public function returnValuesAsArray() {
		$values = array(
			self::F_CMD => $this->entry->getCmd(),
			self::F_REF_ID => $this->entry->getRefId(),
			self::F_GUI_CLASS => $this->entry->getGuiClass(),
			self::F_ADDITIONS => $this->entry->getAdditions(),
		);

		return $values;
	}
}

?>
