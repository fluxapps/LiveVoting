<?php

namespace srag\ActiveRecordConfig\LiveVoting;

use ilPluginConfigGUI;
use ilUtil;
use srag\ActiveRecordConfig\LiveVoting\Exception\ActiveRecordConfigException;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class ActiveRecordConfigGUI
 *
 * @package    srag\ActiveRecordConfig\LiveVoting
 *
 * @deprecated Please use ilPluginConfigGUI from ILIAS core instead
 */
abstract class ActiveRecordConfigGUI extends ilPluginConfigGUI
{

    use DICTrait;

    /**
     * @var string
     *
     * @internal
     *
     * @deprecated
     */
    const CMD_APPLY_FILTER = "applyFilter";
    /**
     * @var string
     *
     * @internal
     *
     * @deprecated
     */
    const CMD_CONFIGURE = "configure";
    /**
     * @var string
     *
     * @internal
     *
     * @deprecated
     */
    const CMD_RESET_FILTER = "resetFilter";
    /**
     * @var string
     *
     * @internal
     *
     * @deprecated
     */
    const CMD_UPDATE_CONFIGURE = "updateConfigure";
    /**
     * @var string
     *
     * @deprecated
     */
    const LANG_MODULE_CONFIG = "config";
    /**
     * @var string
     *
     * @deprecated
     */
    const TAB_CONFIGURATION = "configuration";
    /**
     * @var array
     *
     * @deprecated
     */
    protected static $custom_commands = [];
    /**
     * @var array
     *
     * @abstract
     *
     * @deprecated
     */
    protected static $tabs = [self::TAB_CONFIGURATION => ActiveRecordConfigFormGUI::class];


    /**
     * ActiveRecordConfigGUI constructor
     *
     * @deprecated
     */
    public function __construct()
    {

    }


    /**
     * @internal
     *
     * @deprecated
     */
    public final function executeCommand()/*: void*/
    {
        parent::executeCommand();
    }


    /**
     * @param string $tab_id
     *
     * @return string
     *
     * @deprecated
     */
    public final function getCmdForTab(string $tab_id) : string
    {
        return self::CMD_CONFIGURE . "_" . $tab_id;
    }


    /**
     * @param string $cmd
     *
     * @throws ActiveRecordConfigException Unknown command $cmd!
     * @throws ActiveRecordConfigException Class $config_gui_class_name not extends ActiveRecordConfigFormGUI, ActiveRecordObjectFormGUI or ActiveRecordConfigTableGUI!
     *
     * @internal
     *
     * @deprecated
     */
    public /*final*/ function performCommand(/*string*/ $cmd)/*: void*/
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case "":
                switch (true) {
                    case (in_array($cmd, static::$custom_commands)):
                        /*foreach (static::$tabs as $tab_id => $config_gui_class_name) {
                            if ($config_gui_class_name === $cmd) {
                                self::dic()->tabs()->activateTab($tab_id);
                                break;
                            }
                        }*/

                        $this->{$cmd}();
                        break;

                    case ($cmd === self::CMD_CONFIGURE):
                        reset(static::$tabs);

                        $tab_id = key(static::$tabs);

                        if (in_array(static::$tabs[$tab_id], static::$custom_commands)) {
                            self::dic()->tabs()->activateTab($tab_id);

                            $this->{$tab_id};
                        } else {
                            $this->configure($tab_id);
                        }
                        break;

                    case (strpos($cmd, $this->getCmdForTab("")) === 0):
                        $tab_id = substr($cmd, strlen($this->getCmdForTab("")));

                        if (in_array(static::$tabs[$tab_id], static::$custom_commands)) {
                            self::dic()->tabs()->activateTab($tab_id);

                            $this->{$tab_id};
                        } else {
                            $this->configure($tab_id);
                        }
                        break;

                    case (strpos($cmd, self::CMD_UPDATE_CONFIGURE . "_") === 0):
                        $tab_id = substr($cmd, strlen(self::CMD_UPDATE_CONFIGURE . "_"));

                        $this->updateConfigure($tab_id);
                        break;

                    case (strpos($cmd, self::CMD_APPLY_FILTER . "_") === 0):
                        $tab_id = substr($cmd, strlen(self::CMD_APPLY_FILTER . "_"));

                        $this->applyFilter($tab_id);
                        break;

                    case (strpos($cmd, self::CMD_RESET_FILTER . "_") === 0):
                        $tab_id = substr($cmd, strlen(self::CMD_RESET_FILTER . "_"));

                        $this->resetFilter($tab_id);
                        break;

                    default:
                        throw new ActiveRecordConfigException("Unknown command $cmd!", ActiveRecordConfigException::CODE_UNKOWN_COMMAND);
                        break;
                }
                break;

            default:
                /*foreach (static::$tabs as $tab_id => $config_gui_class_name) {
                    if ($config_gui_class_name[1] === $cmd) {
                        self::dic()->tabs()->activateTab($tab_id);
                        break;
                    }
                }*/

                self::dic()->ctrl()->forwardCommand(new $next_class());
                break;
        }
    }


    /**
     * @param string $tab_id
     *
     * @deprecated
     */
    public final function redirectToTab(string $tab_id)/*: void*/
    {
        self::dic()->ctrl()->redirect($this, $this->getCmdForTab($tab_id));
    }


    /**
     * @param string $tab_id
     * @param string $cmd
     *
     * @deprecated
     */
    protected function addTab(string $tab_id, string $cmd)/*: void*/
    {
        self::dic()->tabs()->addTab($tab_id, $this->txt($tab_id), self::dic()->ctrl()->getLinkTarget($this, $cmd));
    }


    /**
     * @param string $key
     *
     * @return string
     *
     * @deprecated
     */
    protected function txt(string $key) : string
    {
        return self::plugin()->translate($key, self::LANG_MODULE_CONFIG);
    }


    /**
     * @param string $tab_id
     *
     * @throws ActiveRecordConfigException Class $config_form_gui_class_name not extends ActiveRecordConfigTableGUI!
     *
     * @internal
     *
     * @deprecated
     */
    private final function applyFilter(string $tab_id)/*: void*/
    {
        $table = $this->getConfigurationTable(static::$tabs[$tab_id], self::CMD_APPLY_FILTER . "_" . $tab_id, $tab_id);

        $table->writeFilterToSession();

        $table->resetOffset();

        //$this->redirectToTab($tab_id);
        $this->configure($tab_id); // Fix reset offset
    }


    /**
     * @param string $tab_id
     *
     * @throws ActiveRecordConfigException Class $config_gui_class_name not extends ActiveRecordConfigFormGUI, ActiveRecordObjectFormGUI or ActiveRecordConfigTableGUI!
     *
     * @internal
     *
     * @deprecated
     */
    private final function configure(string $tab_id)/*: void*/
    {
        self::dic()->tabs()->activateTab($tab_id);

        $gui = $this->getConfigurationGUI($tab_id);

        self::output()->output($gui);
    }


    /**
     * @param string $config_form_gui_class_name
     * @param string $tab_id
     *
     * @return ActiveRecordConfigFormGUI|ActiveRecordObjectFormGUI
     *
     * @throws ActiveRecordConfigException Class $config_form_gui_class_name not exists!
     * @throws ActiveRecordConfigException Class $config_form_gui_class_name not extends ActiveRecordConfigFormGUI or ActiveRecordObjectFormGUI!
     *
     * @internal
     *
     * @deprecated
     */
    private final function getConfigurationFormGUI(string $config_form_gui_class_name, string $tab_id)
    {
        if (!class_exists($config_form_gui_class_name)) {
            throw new ActiveRecordConfigException("Class $config_form_gui_class_name not exists!", ActiveRecordConfigException::CODE_INVALID_CONFIG_GUI_CLASS);
        }

        $config_form_gui = new $config_form_gui_class_name($this, $tab_id);

        if (!($config_form_gui instanceof ActiveRecordConfigFormGUI || $config_form_gui instanceof ActiveRecordObjectFormGUI)) {
            throw new ActiveRecordConfigException("Class $config_form_gui_class_name not extends ActiveRecordConfigFormGUI or ActiveRecordObjectFormGUI!",
                ActiveRecordConfigException::CODE_INVALID_CONFIG_GUI_CLASS);
        }

        return $config_form_gui;
    }


    /**
     * @param string $tab_id
     *
     * @return ActiveRecordConfigFormGUI|ActiveRecordObjectFormGUI|ActiveRecordConfigTableGUI
     *
     * @throws ActiveRecordConfigException Class $config_gui_class_name not extends ActiveRecordConfigFormGUI, ActiveRecordObjectFormGUI or ActiveRecordConfigTableGUI!
     *
     * @internal
     *
     * @deprecated
     */
    private final function getConfigurationGUI(string $tab_id)
    {
        $config_gui_class_name = static::$tabs[$tab_id];

        switch (true) {
            case is_array($config_gui_class_name):
                $this->addTab($tab_id, $config_gui_class_name[1]);
                self::dic()->ctrl()->redirect(new $config_gui_class_name[0](), $config_gui_class_name[1]);
                break;

            case (substr($config_gui_class_name, -strlen("FormGUI")) === "FormGUI"):
                $config_gui = $this->getConfigurationFormGUI($config_gui_class_name, $tab_id);
                break;

            case (substr($config_gui_class_name, -strlen("TableGUI")) === "TableGUI"):
                $config_gui = $this->getConfigurationTable($config_gui_class_name, $this->getCmdForTab($tab_id), $tab_id);
                break;

            default:
                throw new ActiveRecordConfigException("Class $config_gui_class_name not extends ActiveRecordConfigFormGUI, ActiveRecordObjectFormGUI or ActiveRecordConfigTableGUI!",
                    ActiveRecordConfigException::CODE_INVALID_CONFIG_GUI_CLASS);
                break;
        }

        return $config_gui;
    }


    /**
     * @param string $config_table_gui_class_name
     * @param string $parent_cmd
     * @param string $tab_id
     *
     * @return ActiveRecordConfigTableGUI
     *
     * @throws ActiveRecordConfigException Class $config_form_gui_class_name not exists!
     * @throws ActiveRecordConfigException Class $config_form_gui_class_name not extends ActiveRecordConfigTableGUI!
     *
     * @internal
     *
     * @deprecated
     */
    private final function getConfigurationTable(string $config_table_gui_class_name, string $parent_cmd, string $tab_id)
    {
        if (!class_exists($config_table_gui_class_name)) {
            throw new ActiveRecordConfigException("Class $config_table_gui_class_name not exists!", ActiveRecordConfigException::CODE_INVALID_CONFIG_GUI_CLASS);
        }

        $config_table_gui = new $config_table_gui_class_name($this, $parent_cmd, $tab_id);

        if (!$config_table_gui instanceof ActiveRecordConfigTableGUI) {
            throw new ActiveRecordConfigException("Class $config_table_gui_class_name not extends ActiveRecordConfigTableGUI!", ActiveRecordConfigException::CODE_INVALID_CONFIG_GUI_CLASS);
        }

        return $config_table_gui;
    }


    /**
     * @param string $tab_id
     *
     * @throws ActiveRecordConfigException Class $config_form_gui_class_name not extends ActiveRecordConfigTableGUI!
     *
     * @internal
     *
     * @deprecated
     */
    private final function resetFilter(string $tab_id)/*: void*/
    {
        $table = $this->getConfigurationTable(static::$tabs[$tab_id], self::CMD_RESET_FILTER . "_" . $tab_id, $tab_id);

        $table->resetFilter();

        $table->resetOffset();

        //$this->redirectToTab($tab_id);
        $this->configure($tab_id); // Fix reset offset
    }


    /**
     * @internal
     *
     * @deprecated
     */
    private final function setTabs()/*: void*/
    {
        foreach (static::$tabs as $tab_id => $config_gui_class_name) {
            if (in_array($config_gui_class_name, static::$custom_commands)) {
                $this->addTab($tab_id, $config_gui_class_name);
            } else {
                $this->addTab($tab_id, $this->getCmdForTab($tab_id));
            }
        }

        self::dic()->locator()->addItem(self::plugin()->getPluginObject()->getPluginName(), self::dic()->ctrl()->getLinkTarget($this));
    }


    /**
     * @param string $tab_id
     *
     * @throws ActiveRecordConfigException Class $config_gui_class_name not extends ActiveRecordConfigFormGUI or ActiveRecordObjectFormGUI!
     *
     * @internal
     *
     * @deprecated
     */
    private final function updateConfigure(string $tab_id)/*: void*/
    {
        self::dic()->tabs()->activateTab($tab_id);

        $form = $this->getConfigurationFormGUI(static::$tabs[$tab_id], $tab_id);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        ilUtil::sendSuccess($this->txt($tab_id . "_saved"), true);

        $this->redirectToTab($tab_id);
    }
}
