<?php

namespace LiveVoting\Js;

use ilLiveVotingPlugin;
use LiveVoting\Conf\xlvoConf;
use LiveVoting\Context\Param\ParamManager;
use LiveVoting\GUI\xlvoGUI;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;
use ilSetting;
use ilMathJax;

/**
 * Class xlvoJs
 *
 * @package LiveVoting\Js
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoJs
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    const DEVELOP = false;
    const API_URL = xlvoConf::API_URL;
    const BASE_URL_SETTING = 'base_url';
    const BASE_PATH = './Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/js/';
    /**
     * @var string
     */
    protected $class_name = '';
    /**
     * @var string
     */
    protected $setting_class_name = '';
    /**
     * @var bool
     */
    protected $init = false;
    /**
     * @var string
     */
    protected $lib = '';
    /**
     * @var string
     */
    protected $name = '';
    /**
     * @var string
     */
    protected $category = '';
    /**
     * @var xlvoJsSettings
     */
    protected $settings;


    /**
     * xlvoJs constructor.
     */
    protected function __construct()
    {
        $this->settings = new xlvoJsSettings();
    }


    /**
     * @return xlvoJs
     */
    public static function getInstance()
    {
        return new self();
    }


    /**
     * @param array $settings
     *
     * @return $this
     */
    public function addSettings(array $settings)
    {
        foreach ($settings as $k => $v) {
            $this->settings->addSetting($k, $v);
        }

        return $this;
    }


    /**
     * @param array $translations
     *
     * @return $this
     */
    public function addTranslations(array $translations)
    {
        foreach ($translations as $translation) {
            $this->settings->addTranslation($translation);
        }

        return $this;
    }


    /**
     * @param xlvoGUI $xlvoGUI
     * @param array   $additional_classes
     * @param string  $cmd
     *
     * @return $this
     */
    public function api(xlvoGUI $xlvoGUI, array $additional_classes = array(), $cmd = '')
    {
        $ilCtrl2 = clone(self::dic()->ctrl());
        //self::dic()->ctrl()->initBaseClass(ilUIPluginRouterGUI::class);
        $ilCtrl2->setTargetScript(self::API_URL);
        $additional_classes[] = get_class($xlvoGUI);

        ParamManager::getInstance();

        $this->settings->addSetting(self::BASE_URL_SETTING, self::dic()->ctrl()->getLinkTargetByClass($additional_classes, $cmd, null, true));

        return $this;
    }


    /**
     * @param string $name
     *
     * @return $this
     */
    public function name($name)
    {
        $this->name = $name;

        return $this;
    }


    /**
     * @param string $category
     *
     * @return $this
     */
    public function category($category)
    {
        $this->category = $category;

        return $this;
    }


    /**
     * @param xlvoGUI $xlvoGUI
     * @param string  $cmd
     *
     * @return $this
     */
    public function ilias(xlvoGUI $xlvoGUI, $cmd = '')
    {
        $this->settings->addSetting(self::BASE_URL_SETTING, self::dic()->ctrl()->getLinkTarget($xlvoGUI, $cmd, '', true));

        return $this;
    }


    /**
     *
     */
    protected function resolveLib()
    {
        $base_path = self::BASE_PATH;
        $category = ($this->category ? $this->category . '/' : '') . $this->name . '/';
        $file_name = ilLiveVotingPlugin::PLUGIN_ID . $this->name . '.js';
        $file_name_min = ilLiveVotingPlugin::PLUGIN_ID . $this->name . '.min.js';
        $full_path_min = $base_path . $category . $file_name_min;
        $full_path = $base_path . $category . $file_name;
        if (is_file($full_path_min) && !self::DEVELOP) {
            $this->lib = $full_path_min;
        } else {
            $this->lib = $full_path;
        }
    }


    /**
     * @return string
     */
    public function getLibraryURL()
    {
        $this->resolveLib();

        return $this->lib;
    }


    /**
     * @return $this
     */
    public function init()
    {
        $this->init = true;
        $this->resolveLib();
        $this->addLibToHeader($this->lib, false);
        $this->setInitCode();

        return $this;
    }


    /**
     * @param string $code
     *
     * @return $this
     */
    public function addOnLoadCode($code)
    {
        self::dic()->ui()->mainTemplate()->addOnLoadCode($code);

        return $this;
    }


    /**
     * @param string $method
     * @param string $params
     *
     * @return $this
     */
    public function call($method, $params = '')
    {
        if (!$this->init) {
            return $this;
        }
        $this->addOnLoadCode($this->getCallCode($method, $params));

        return $this;
    }


    /**
     * @return $this
     */
    public function setInitCode()
    {
        return $this->call("init", $this->settings->asJson());
    }


    /**
     * @return string
     */
    public function getRunCode()
    {
        return '<script>' . $this->getCallCode("run") . '</script>';
    }


    /**
     * @return $thiss
     */
    public function setRunCode()
    {
        return $this->call("run");
    }


    /**
     * @param string $method
     * @param string $params
     *
     * @return string
     */
    public function getCallCode($method, $params = '')
    {
        return ilLiveVotingPlugin::PLUGIN_ID . $this->name . '.' . $method . '(' . $params . ');';
    }


    /**
     * @param string $name_of_lib
     * @param bool   $external
     *
     * @return $this
     */
    public function addLibToHeader($name_of_lib, $external = true)
    {
        if ($external) {
            self::dic()->ui()->mainTemplate()->addJavascript(self::plugin()->directory() . '/js/libs/' . $name_of_lib);
        } else {
            self::dic()->ui()->mainTemplate()->addJavaScript($name_of_lib);
        }

        return $this;
    }

    /**
     *
     */
    public function initMathJax()
    {
        $mathJaxSetting = new ilSetting("MathJax");
        if (strpos($mathJaxSetting->get('path_to_mathjax'), 'mathjax@3') !== false) { // not sure if this check will work with >v3
            // mathjax v3 needs to be configured differently
            $this->addLibToHeader('mathjax_config.js');
        }
        ilMathJax::getInstance()->includeMathJax();
    }
}
