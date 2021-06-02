<?php

namespace srag\DIC\LiveVoting;

use ilLogLevel;
use ilPlugin;
use srag\DIC\LiveVoting\DIC\DICInterface;
use srag\DIC\LiveVoting\DIC\Implementation\ILIAS54DIC;
use srag\DIC\LiveVoting\DIC\Implementation\ILIAS60DIC;
use srag\DIC\LiveVoting\Exception\DICException;
use srag\DIC\LiveVoting\Output\Output;
use srag\DIC\LiveVoting\Output\OutputInterface;
use srag\DIC\LiveVoting\Plugin\Plugin;
use srag\DIC\LiveVoting\Plugin\PluginInterface;
use srag\DIC\LiveVoting\Version\Version;
use srag\DIC\LiveVoting\Version\VersionInterface;

/**
 * Class DICStatic
 *
 * @package srag\DIC\LiveVoting
 */
final class DICStatic implements DICStaticInterface
{

    /**
     * @var DICInterface|null
     */
    private static $dic = null;
    /**
     * @var OutputInterface|null
     */
    private static $output = null;
    /**
     * @var PluginInterface[]
     */
    private static $plugins = [];
    /**
     * @var VersionInterface|null
     */
    private static $version = null;


    /**
     * DICStatic constructor
     */
    private function __construct()
    {

    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public static function clearCache()/*: void*/
    {
        self::$dic = null;
        self::$output = null;
        self::$plugins = [];
        self::$version = null;
    }


    /**
     * @inheritDoc
     */
    public static function dic() : DICInterface
    {
        if (self::$dic === null) {
            switch (true) {
                case (self::version()->isLower(VersionInterface::ILIAS_VERSION_5_4)):
                    throw new DICException("DIC not supports ILIAS " . self::version()->getILIASVersion() . " anymore!");
                    break;

                case (self::version()->isLower(VersionInterface::ILIAS_VERSION_6)):
                    global $DIC;
                    self::$dic = new ILIAS54DIC($DIC);
                    break;

                default:
                    global $DIC;
                    self::$dic = new ILIAS60DIC($DIC);
                    break;
            }
        }

        return self::$dic;
    }


    /**
     * @inheritDoc
     */
    public static function output() : OutputInterface
    {
        if (self::$output === null) {
            self::$output = new Output();
        }

        return self::$output;
    }


    /**
     * @inheritDoc
     */
    public static function plugin(string $plugin_class_name) : PluginInterface
    {
        if (!isset(self::$plugins[$plugin_class_name])) {
            if (!class_exists($plugin_class_name)) {
                throw new DICException("Class $plugin_class_name not exists!", DICException::CODE_INVALID_PLUGIN_CLASS);
            }

            if (method_exists($plugin_class_name, "getInstance")) {
                $plugin_object = $plugin_class_name::getInstance();
            } else {
                $plugin_object = new $plugin_class_name();

                self::dic()->log()->write("DICLog: Please implement $plugin_class_name::getInstance()!", ilLogLevel::DEBUG);
            }

            if (!$plugin_object instanceof ilPlugin) {
                throw new DICException("Class $plugin_class_name not extends ilPlugin!", DICException::CODE_INVALID_PLUGIN_CLASS);
            }

            self::$plugins[$plugin_class_name] = new Plugin($plugin_object);
        }

        return self::$plugins[$plugin_class_name];
    }


    /**
     * @inheritDoc
     */
    public static function version() : VersionInterface
    {
        if (self::$version === null) {
            self::$version = new Version();
        }

        return self::$version;
    }
}
