<?php

use LiveVoting\Api\xlvoApi;
use LiveVoting\Conf\xlvoConf;

require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');

/**
 * Class xlvoConfFormGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoConfFormGUI extends \ilPropertyFormGUI {

	/**
	 * @var  xlvoConf
	 */
	protected $object;
	/**
	 * @var xlvoConfGUI
	 */
	protected $parent_gui;
	/**
	 * @var  \ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilLiveVotingPlugin
	 */
	protected $pl;


	/**
	 * xlvoConfFormGUI constructor.
	 *
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

		$use_shortlink = new \ilCheckboxInputGUI($this->parent_gui->txt(xlvoConf::F_ALLOW_SHORTLINK), xlvoConf::F_ALLOW_SHORTLINK);
		$use_shortlink->setInfo($this->parent_gui->txt(xlvoConf::F_ALLOW_SHORTLINK . '_info')
		                        . '<br><br><span class="label label-default">'
		                        . xlvoConf::REWRITE_RULE . '</span><br><br>');

		$shortlink = new \ilTextInputGUI($this->parent_gui->txt(xlvoConf::F_ALLOW_SHORTLINK_LINK), xlvoConf::F_ALLOW_SHORTLINK_LINK);
		$shortlink->setInfo($this->parent_gui->txt(xlvoConf::F_ALLOW_SHORTLINK_LINK . '_info'));
		$use_shortlink->addSubItem($shortlink);

		$base_url = new \ilTextInputGUI($this->parent_gui->txt(xlvoConf::F_BASE_URL), xlvoConf::F_BASE_URL);
		$base_url->setInfo($this->parent_gui->txt(xlvoConf::F_BASE_URL . '_info'));
		$use_shortlink->addSubItem($base_url);

		$request_frequency = new \ilNumberInputGUI($this->parent_gui->txt(xlvoConf::F_REQUEST_FREQUENCY), xlvoConf::F_REQUEST_FREQUENCY);
		$request_frequency->setInfo($this->parent_gui->txt(xlvoConf::F_REQUEST_FREQUENCY
		                                                   . '_info'));
		$request_frequency->allowDecimals(true);
		$request_frequency->setMinValue(xlvoConf::MIN_CLIENT_UPDATE_FREQUENCY, false);
		$request_frequency->setMaxValue(xlvoConf::MAX_CLIENT_UPDATE_FREQUENCY, false);

		//global cache setting
        $global_cache_enabled = new \ilCheckboxInputGUI($this->parent_gui->txt(xlvoConf::F_USE_GLOBAL_CACHE), xlvoConf::F_USE_GLOBAL_CACHE);
        $global_cache_enabled->setInfo($this->parent_gui->txt(xlvoConf::F_USE_GLOBAL_CACHE . '_info'));

		// Results API
		$result_api = new \ilCheckboxInputGUI($this->parent_gui->txt(xlvoConf::F_RESULT_API), xlvoConf::F_RESULT_API);
		$result_api->setInfo($this->parent_gui->txt(xlvoConf::F_RESULT_API . '_info'));

		$api_type = new ilSelectInputGUI($this->parent_gui->txt(xlvoConf::F_API_TYPE), xlvoConf::F_API_TYPE);
		$api_type->setOptions(array(
			xlvoApi::TYPE_JSON => 'JSON',
			xlvoApi::TYPE_XML  => 'XML',
		));
		$result_api->addSubItem($api_type);

		$api_token = new ilNonEditableValueGUI();
		$api_token->setTitle($this->parent_gui->txt(xlvoConf::F_API_TOKEN));
		$api_token->setValue(xlvoConf::getApiToken());
		$result_api->addSubItem($api_token);

		//add items to GUI
        $this->addItem($use_shortlink);
        $this->addItem($request_frequency);
        $this->addItem($result_api);
        $this->addItem($global_cache_enabled);
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
		return !$item instanceof \ilFormSectionHeaderGUI AND !$item instanceof
		                                                      \ilMultiSelectInputGUI;
	}


	/**
	 * @param $item
	 *
	 * @return bool
	 */
	public static function checkItem($item) {
		return !$item instanceof \ilFormSectionHeaderGUI && !$item instanceof ilNonEditableValueGUI;
	}
}