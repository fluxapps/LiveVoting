<?php

namespace srag\RemovePluginDataConfirm;

/**
 * Trait RepositoryObjectPluginUninstallTrait
 *
 * @package srag\RemovePluginDataConfirm
 */
trait RepositoryObjectPluginUninstallTrait {

	use AbstractPluginUninstallTrait;


	/**
	 * @return bool
	 * @throws RemovePluginDataConfirmException
	 *
	 * @access namespace
	 */
	protected final function beforeUninstallCustom()/*: bool*/ {
		return $this->pluginUninstall(false); // Remove plugin data after ilRepUtil::deleteObjectType($this->getId() because may data needs for reading ilObject's!
	}


	/**
	 * @throws RemovePluginDataConfirmException
	 *
	 * @access namespace
	 */
	protected final function uninstallCustom()/*: void*/ {
		$remove_plugin_data_confirm_class = self::getRemovePluginDataConfirmClass();

		$uninstall_removes_data = $remove_plugin_data_confirm_class->getUninstallRemovesData();

		$uninstall_removes_data = boolval($uninstall_removes_data);

		if ($uninstall_removes_data) {
			$this->deleteData();
		}
	}


	/**
	 * @access namespace
	 */
	protected final function afterUninstall()/*: void*/ {

	}
}
