<?php
require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');
require_once('class.xlvoConf.php');

/**
 * Class xlvoConfFormGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoConfFormGUI extends ilPropertyFormGUI {

	/**
	 * @var  xlvoConf
	 */
	protected $object;
	/**
	 * @var xlvoConfGUI
	 */
	protected $parent_gui;
	/**
	 * @var  ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilLiveVotingPlugin
	 */
	protected $pl;


	/**
	 * xlvoConfFormGUI constructor.
	 * @param xlvoConfGUI $parent_gui
	 */
	public function __construct(xlvoConfGUI $parent_gui) {
		global $ilCtrl, $lng;
		$this->parent_gui = $parent_gui;
		$this->ctrl = $ilCtrl;
		$this->pl = ilLiveVotingPlugin::getInstance();
		$this->lng = $lng;
		$this->initForm();
	}


	protected function initForm() {
		$this->setTarget('_top');
		$this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
		$this->initButtons();

		$use_shortlink = new ilCheckboxInputGUI($this->parent_gui->txt(xlvoConf::F_ALLOW_SHORTLINK), xlvoConf::F_ALLOW_SHORTLINK);
		$use_shortlink->setInfo($this->parent_gui->txt(xlvoConf::F_ALLOW_SHORTLINK . '_info') . '<br><br><span class="label label-default">' . xlvoConf::REWRITE_RULE . '</span><br><br>');

		$shortlink = new ilTextInputGUI($this->parent_gui->txt(xlvoConf::F_ALLOW_SHORTLINK_LINK), xlvoConf::F_ALLOW_SHORTLINK_LINK);
		$shortlink->setInfo($this->parent_gui->txt(xlvoConf::F_ALLOW_SHORTLINK_LINK . '_info'));
		$use_shortlink->addSubItem($shortlink);

		$base_url = new ilTextInputGUI($this->parent_gui->txt(xlvoConf::F_BASE_URL), xlvoConf::F_BASE_URL);
		$base_url->setInfo($this->parent_gui->txt(xlvoConf::F_BASE_URL . '_info'));
		$use_shortlink->addSubItem($base_url);

		$this->addItem($use_shortlink);
	}


	protected function initButtons() {
		$this->addCommandButton(xlvoConfGUI::CMD_UPDATE, $this->parent_gui->txt(xlvoConfGUI::CMD_UPDATE));
		$this->addCommandButton(xlvoConfGUI::CMD_CANCEL, $this->parent_gui->txt(xlvoConfGUI::CMD_CANCEL));
	}


	public function fillForm() {
		$array = array();
		foreach ($this->getItems() as $item) {
			$this->getValuesForItem($item, $array);
		}
		$this->setValuesByArray($array);
	}


	/**
	 * @param $item
	 * @param $array
	 *
	 * @internal param $key
	 */
	private function getValuesForItem($item, &$array) {
		if (self::checkItem($item)) {
			$key = $item->getPostVar();
			$array[$key] = xlvoConf::getConfig($key);
			if (self::checkForSubItem($item)) {
				foreach ($item->getSubItems() as $subitem) {
					$this->getValuesForItem($subitem, $array);
				}
			}
		}
	}


	/**
	 * @return bool
	 */
	public function saveObject() {
		if (!$this->checkInput()) {
			return false;
		}
		foreach ($this->getItems() as $item) {
			$this->saveValueForItem($item);
		}
		xlvoConf::set(xlvoConf::F_CONFIG_VERSION, xlvoConf::CONFIG_VERSION);

		return true;
	}


	/**
	 * @param $item
	 */
	private function saveValueForItem($item) {
		if (self::checkItem($item)) {
			$key = $item->getPostVar();
			xlvoConf::set($key, $this->getInput($key));
			if (self::checkForSubItem($item)) {
				foreach ($item->getSubItems() as $subitem) {
					$this->saveValueForItem($subitem);
				}
			}
		}
	}


	/**
	 * @param $item
	 *
	 * @return bool
	 */
	public static function checkForSubItem($item) {
		return !$item instanceof ilFormSectionHeaderGUI AND !$item instanceof ilMultiSelectInputGUI;
	}


	/**
	 * @param $item
	 *
	 * @return bool
	 */
	public static function checkItem($item) {
		return !$item instanceof ilFormSectionHeaderGUI;
	}
}

?>
