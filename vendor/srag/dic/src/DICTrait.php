<?php

namespace srag\DIC\LiveVoting;

use srag\DIC\LiveVoting\DIC\DICInterface;
use srag\DIC\LiveVoting\Exception\DICException;
use srag\DIC\LiveVoting\Output\OutputInterface;
use srag\DIC\LiveVoting\Plugin\PluginInterface;
use srag\DIC\LiveVoting\Version\VersionInterface;

/**
 * Trait DICTrait
 *
 * @package srag\DIC\LiveVoting
 */
trait DICTrait
{

    /* *
     * @var string
     *
     * @abstract
     *
     * TODO: Implement Constants in Traits in PHP Core
     * /
    const PLUGIN_CLASS_NAME = "";*/

    /**
     * Get DIC interface
     *
     * @return DICInterface DIC interface
     */
    protected static final function dic() : DICInterface
    {
        return DICStatic::dic();
    }


    /**
     * Get output interface
     *
     * @return OutputInterface Output interface
     */
    protected static final function output() : OutputInterface
    {
        return DICStatic::output();
    }


    /**
     * Get plugin interface
     *
     * @return PluginInterface Plugin interface
     *
     * @throws DICException Class $plugin_class_name not exists!
     * @throws DICException Class $plugin_class_name not extends ilPlugin!
     * @logs   DEBUG Please implement $plugin_class_name::getInstance()!
     */
    protected static final function plugin() : PluginInterface
    {
        self::checkPluginClassNameConst();

        return DICStatic::plugin(static::PLUGIN_CLASS_NAME);
    }


    /**
     * Get version interface
     *
     * @return VersionInterface Version interface
     */
    protected static final function version() : VersionInterface
    {
        return DICStatic::version();
    }


    /**
     * @throws DICException Your class needs to implement the PLUGIN_CLASS_NAME constant!
     */
    private static final function checkPluginClassNameConst()/*: void*/
    {
        if (!defined("static::PLUGIN_CLASS_NAME") || empty(static::PLUGIN_CLASS_NAME)) {
            throw new DICException("Your class needs to implement the PLUGIN_CLASS_NAME constant!", DICException::CODE_MISSING_CONST_PLUGIN_CLASS_NAME);
        }
    }
}
