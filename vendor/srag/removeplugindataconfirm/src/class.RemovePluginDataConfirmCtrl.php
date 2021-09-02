<?php

namespace srag\RemovePluginDataConfirm\LiveVoting;

require_once __DIR__ . "./../../../autoload.php";

use ilAdministrationGUI;
use ilConfirmationGUI;
use ilObjComponentSettingsGUI;
use ilSession;
use ilUtil;
use srag\DIC\LiveVoting\DICStatic;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class RemovePluginDataConfirmCtrl
 *
 * @package           srag\RemovePluginDataConfirm\LiveVoting
 *
 * @ilCtrl_isCalledBy srag\RemovePluginDataConfirm\LiveVoting\RemovePluginDataConfirmCtrl: ilUIPluginRouterGUI
 */
class RemovePluginDataConfirmCtrl
{

    use DICTrait;

    const CMD_CANCEL = "cancel";
    const CMD_CONFIRM_REMOVE_DATA = "confirmRemoveData";
    const CMD_DEACTIVATE = "deactivate";
    const CMD_SET_KEEP_DATA = "setKeepData";
    const CMD_SET_REMOVE_DATA = "setRemoveData";
    const KEY_UNINSTALL_REMOVES_DATA = "uninstall_removes_data";
    const LANG_MODULE = "removeplugindataconfirm";


    /**
     * RemovePluginDataConfirmCtrl constructor
     */
    public function __construct()
    {

    }


    /**
     * @return bool|null
     */
    public static function getUninstallRemovesData() : ?bool
    {
        return json_decode(ilSession::get(self::KEY_UNINSTALL_REMOVES_DATA));
    }


    /**
     *
     */
    public static function removeUninstallRemovesData() : void
    {
        ilSession::clear(self::KEY_UNINSTALL_REMOVES_DATA);
    }


    /**
     * @param bool $plugin
     */
    public static function saveParameterByClass(bool $plugin = true) : void
    {
        $ref_id = filter_input(INPUT_GET, "ref_id");
        self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "ref_id", $ref_id);
        self::dic()->ctrl()->setParameterByClass(static::class, "ref_id", $ref_id);

        if ($plugin) {
            $ctype = filter_input(INPUT_GET, "ctype");
            self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "ctype", $ctype);
            self::dic()->ctrl()->setParameterByClass(static::class, "ctype", $ctype);

            $cname = filter_input(INPUT_GET, "cname");
            self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "cname", $cname);
            self::dic()->ctrl()->setParameterByClass(static::class, "cname", $cname);

            $slot_id = filter_input(INPUT_GET, "slot_id");
            self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "slot_id", $slot_id);
            self::dic()->ctrl()->setParameterByClass(static::class, "slot_id", $slot_id);

            $plugin_id = filter_input(INPUT_GET, "plugin_id");
            self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "plugin_id", $plugin_id);
            self::dic()->ctrl()->setParameterByClass(static::class, "plugin_id", $plugin_id);

            $pname = filter_input(INPUT_GET, "pname");
            self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "pname", $pname);
            self::dic()->ctrl()->setParameterByClass(static::class, "pname", $pname);
        }
    }


    /**
     * @param bool $uninstall_removes_data
     */
    public static function setUninstallRemovesData(bool $uninstall_removes_data) : void
    {
        ilSession::set(self::KEY_UNINSTALL_REMOVES_DATA, json_encode($uninstall_removes_data));
    }


    /**
     *
     */
    public function executeCommand() : void
    {
        $ref_id = filter_input(INPUT_GET, "ref_id");
        if (!self::dic()->access()->checkAccess("write", "", $ref_id)) {
            die();
        }

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch ($next_class) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_CANCEL:
                    case self::CMD_CONFIRM_REMOVE_DATA:
                    case self::CMD_DEACTIVATE:
                    case self::CMD_SET_KEEP_DATA:
                    case self::CMD_SET_REMOVE_DATA:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     *
     */
    protected function cancel() : void
    {
        $this->redirectToPlugins("listPlugins");
    }


    /**
     *
     */
    protected function confirmRemoveData() : void
    {
        self::saveParameterByClass();

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText($this->txt("confirm_remove_data"));

        $confirmation->addItem("_", "_", $this->txt("data"));

        $confirmation->addButton($this->txt("remove_data"), self::CMD_SET_REMOVE_DATA);
        $confirmation->addButton($this->txt("keep_data"), self::CMD_SET_KEEP_DATA);
        $confirmation->addButton($this->txt("deactivate"), self::CMD_DEACTIVATE);
        $confirmation->setCancel($this->txt("cancel"), self::CMD_CANCEL);

        self::output()->output($confirmation, true);
    }


    /**
     *
     */
    protected function deactivate() : void
    {
        $this->redirectToPlugins("deactivatePlugin");
    }


    /**
     * @param string $cmd
     */
    protected function redirectToPlugins(string $cmd) : void
    {
        self::saveParameterByClass($cmd !== "listPlugins");

        self::dic()->ctrl()->redirectByClass([
            ilAdministrationGUI::class,
            ilObjComponentSettingsGUI::class
        ], $cmd);
    }


    /**
     *
     */
    protected function setKeepData() : void
    {
        self::setUninstallRemovesData(false);

        ilUtil::sendInfo($this->txt("msg_kept_data"), true);

        $this->redirectToPlugins("uninstallPlugin");
    }


    /**
     *
     */
    protected function setRemoveData() : void
    {
        self::setUninstallRemovesData(true);

        ilUtil::sendInfo($this->txt("msg_removed_data"), true);

        $this->redirectToPlugins("uninstallPlugin");
    }


    /**
     *
     */
    protected function setTabs() : void
    {

    }


    /**
     * @param string $key
     *
     * @return string
     */
    protected function txt(string $key) : string
    {
        $pname = filter_input(INPUT_GET, "pname");

        return DICStatic::plugin("il" . $pname . "Plugin")->translate($key, self::LANG_MODULE, [$pname]);
    }
}
