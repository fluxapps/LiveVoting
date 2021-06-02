<?php

namespace srag\RemovePluginDataConfirm\LiveVoting;

/**
 * Trait PluginUninstallTrait
 *
 * @package srag\RemovePluginDataConfirm\LiveVoting
 */
trait PluginUninstallTrait
{

    use BasePluginUninstallTrait;

    /**
     * @internal
     */
    protected final function afterUninstall()/*: void*/
    {

    }


    /**
     * @return bool
     *
     * @internal
     */
    protected final function beforeUninstall() : bool
    {
        return $this->pluginUninstall();
    }
}
