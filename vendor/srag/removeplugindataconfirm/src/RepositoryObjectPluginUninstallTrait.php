<?php

namespace srag\RemovePluginDataConfirm\LiveVoting;

/**
 * Trait RepositoryObjectPluginUninstallTrait
 *
 * @package srag\RemovePluginDataConfirm\LiveVoting
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait RepositoryObjectPluginUninstallTrait
{

    use BasePluginUninstallTrait;


    /**
     * @return bool
     *
     * @internal
     */
    protected final function beforeUninstallCustom() : bool
    {
        return $this->pluginUninstall(false); // Remove plugin data after ilRepUtil::deleteObjectType($this->getId() because may data needs for reading ilObject's!
    }


    /**
     * @internal
     */
    protected final function uninstallCustom()/*: void*/
    {
        $uninstall_removes_data = RemovePluginDataConfirmCtrl::getUninstallRemovesData();

        $uninstall_removes_data = boolval($uninstall_removes_data);

        if ($uninstall_removes_data) {
            $this->deleteData();
        }

        RemovePluginDataConfirmCtrl::removeUninstallRemovesData();
    }


    /**
     * @internal
     */
    protected final function afterUninstall()/*: void*/
    {

    }
}
