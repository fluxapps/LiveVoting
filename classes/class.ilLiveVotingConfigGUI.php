<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once('./Services/Component/classes/class.ilPluginConfigGUI.php');

/**
 * ilLiveVotingConfigGUI
 *
 * @author             Fabian Schmid <fs@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy  ilLiveVotingConfigGUI: ilObjComponentSettingsGUIs
 */
class ilLiveVotingConfigGUI extends \ilPluginConfigGUI {

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilLanguage
	 */
	protected $lng;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	
	public function __construct() { 
		global $DIC;
		
		$this->ctrl = $DIC->ctrl();
		$this->tabs = $DIC->tabs();
		$this->lng = $DIC->language();
		$this->tpl = $DIC->ui()->mainTemplate();
	}


	public function executeCommand() {
		$this->ctrl->setParameterByClass("ilobjcomponentsettingsgui", "ctype", $_GET["ctype"]);
		$this->ctrl->setParameterByClass("ilobjcomponentsettingsgui", "cname", $_GET["cname"]);
		$this->ctrl->setParameterByClass("ilobjcomponentsettingsgui", "slot_id", $_GET["slot_id"]);
		$this->ctrl->setParameterByClass("ilobjcomponentsettingsgui", "plugin_id", $_GET["plugin_id"]);
		$this->ctrl->setParameterByClass("ilobjcomponentsettingsgui", "pname", $_GET["pname"]);

		$this->tpl->setTitle($this->lng->txt("cmps_plugin") . ": " . $_GET["pname"]);
		$this->tpl->setDescription("");

		$this->tabs->clearTargets();

		if ($_GET["plugin_id"]) {
			$this->tabs->setBackTarget($this->lng->txt("cmps_plugin"), $this->ctrl->getLinkTargetByClass("ilobjcomponentsettingsgui", "showPlugin"));
		} else {
			$this->tabs->setBackTarget($this->lng->txt("cmps_plugins"), $this->ctrl->getLinkTargetByClass("ilobjcomponentsettingsgui", "listPlugins"));
		}

		$nextClass = $this->ctrl->getNextClass();

		if ($nextClass) {
			$a_gui_object = new xlvoMainGUI();
			$this->ctrl->forwardCommand($a_gui_object);
		} else {
			$this->ctrl->redirectByClass(array(
				'xlvoMainGUI',
				'xlvoConfGUI'
			));
		}
	}


	public function performCommand($cmd) {
	}
}
