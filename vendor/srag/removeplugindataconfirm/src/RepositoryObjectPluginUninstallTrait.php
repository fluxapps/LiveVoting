<?php

namespace srag\RemovePluginDataConfirm\LiveVoting;

use srag\RemovePluginDataConfirm\LiveVoting\Exception\RemovePluginDataConfirmException;

/**
 * Trait RepositoryObjectPluginUninstallTrait
 *
 * @package srag\RemovePluginDataConfirm\LiveVoting
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait RepositoryObjectPluginUninstallTrait {

	use AbstractPluginUninstallTrait;


	/**
	 * @return bool
	 * @throws RemovePluginDataConfirmException
	 *
	 * @internal
	 */
	protected final function beforeUninstallCustom()/*: bool*/ {
		return $this->pluginUninstall(false); // Remove plugin data after ilRepUtil::deleteObjectType($this->getId() because may data needs for reading ilObject's!
	}


	/**
	 * @throws RemovePluginDataConfirmException
	 *
	 * @internal
	 */
	protected final function uninstallCustom()/*: void*/ {
		$remove_plugin_data_confirm_class = self::getRemovePluginDataConfirmClass();

		$uninstall_removes_data = $remove_plugin_data_confirm_class->getUninstallRemovesData();

		$uninstall_removes_data = boolval($uninstall_removes_data);

		if ($uninstall_removes_data) {
			$this->deleteData();
		}

		$remove_plugin_data_confirm_class->removeUninstallRemovesData();
	}


	/**
	 * @internal
	 */
	protected final function afterUninstall()/*: void*/ {

	}
}
