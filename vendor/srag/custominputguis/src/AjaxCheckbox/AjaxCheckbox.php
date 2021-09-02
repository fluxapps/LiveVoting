<?php

namespace srag\CustomInputGUIs\LiveVoting\AjaxCheckbox;

use srag\CustomInputGUIs\LiveVoting\Template\Template;
use srag\CustomInputGUIs\LiveVoting\Waiter\Waiter;
use srag\DIC\LiveVoting\DICTrait;
use srag\DIC\LiveVoting\Plugin\PluginInterface;
use srag\DIC\LiveVoting\Version\PluginVersionParameter;

/**
 * Class AjaxCheckbox
 *
 * @package srag\CustomInputGUIs\LiveVoting\AjaxCheckbox
 */
class AjaxCheckbox
{

    use DICTrait;

    const GET_PARAM_CHECKED = "checked";
    /**
     * @var bool
     */
    protected static $init = false;
    /**
     * @var string
     */
    protected $ajax_change_link = "";
    /**
     * @var bool
     */
    protected $checked = false;


    /**
     * AjaxCheckbox constructor
     *
     * @param PluginInterface|null $plugin
     */
    public function __construct(/*?*/ PluginInterface $plugin = null)
    {
        self::init($plugin);
    }


    /**
     * @param PluginInterface|null $plugin
     */
    public static function init(/*?*/ PluginInterface $plugin = null) : void
    {
        if (self::$init === false) {
            self::$init = true;

            $version_parameter = PluginVersionParameter::getInstance();
            if ($plugin !== null) {
                $version_parameter = $version_parameter->withPlugin($plugin);
            }

            Waiter::init(Waiter::TYPE_WAITER, null, $plugin);

            $dir = __DIR__;
            $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

            self::dic()->ui()->mainTemplate()->addJavaScript($version_parameter->appendToUrl($dir . "/js/ajax_checkbox.min.js", $dir . "/js/ajax_checkbox.js"));
        }
    }


    /**
     * @return string
     */
    public function getAjaxChangeLink() : string
    {
        return $this->ajax_change_link;
    }


    /**
     * @return bool
     */
    public function isChecked() : bool
    {
        return $this->checked;
    }


    /**
     * @return string
     */
    public function render() : string
    {
        $tpl = new Template(__DIR__ . "/templates/ajax_checkbox.html");

        if ($this->checked) {
            $tpl->setVariableEscaped("CHECKED", " checked");
        }

        $config = [
            "ajax_change_link" => $this->ajax_change_link
        ];

        $tpl->setVariableEscaped("CONFIG", base64_encode(json_encode($config)));

        return self::output()->getHTML($tpl);
    }


    /**
     * @param string $ajax_change_link
     *
     * @return self
     */
    public function withAjaxChangeLink(string $ajax_change_link) : self
    {
        $this->ajax_change_link = $ajax_change_link;

        return $this;
    }


    /**
     * @param bool $checked
     *
     * @return self
     */
    public function withChecked(bool $checked) : self
    {
        $this->checked = $checked;

        return $this;
    }
}
