<?php

require_once __DIR__ . '/../vendor/autoload.php';

use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * ilLiveVotingConfigGUI
 *
 * @author             Fabian Schmid <fs@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy  ilLiveVotingConfigGUI: ilObjComponentSettingsGUIs
 */
class ilLiveVotingConfigGUI extends ilPluginConfigGUI
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


    public function __construct()
    {

    }


    public function executeCommand()
    {
        // TODO: Refactoring
        self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "ctype", $_GET["ctype"]);
        self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "cname", $_GET["cname"]);
        self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "slot_id", $_GET["slot_id"]);
        self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "plugin_id", $_GET["plugin_id"]);
        self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "pname", $_GET["pname"]);

        self::dic()->ui()->mainTemplate()->setTitle(self::dic()->language()->txt("cmps_plugin") . ": " . $_GET["pname"]);
        self::dic()->ui()->mainTemplate()->setDescription("");

        self::dic()->tabs()->clearTargets();

        if ($_GET["plugin_id"]) {
            self::dic()->tabs()->setBackTarget(self::dic()->language()->txt("cmps_plugin"), self::dic()->ctrl()
                ->getLinkTargetByClass(ilObjComponentSettingsGUI::class, "showPlugin"));
        } else {
            self::dic()->tabs()->setBackTarget(self::dic()->language()->txt("cmps_plugins"), self::dic()->ctrl()
                ->getLinkTargetByClass(ilObjComponentSettingsGUI::class, "listPlugins"));
        }

        $nextClass = self::dic()->ctrl()->getNextClass();

        if ($nextClass) {
            $a_gui_object = new xlvoMainGUI();
            self::dic()->ctrl()->forwardCommand($a_gui_object);
        } else {
            self::dic()->ctrl()->redirectByClass(array(
                xlvoMainGUI::class,
                xlvoConfGUI::class
            ));
        }
    }


    public function performCommand($cmd)
    {
    }
}
