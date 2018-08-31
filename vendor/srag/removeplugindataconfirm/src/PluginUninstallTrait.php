<?php

namespace srag\RemovePluginDataConfirm;

/**
 * Trait PluginUninstallTrait
 *
 * @package srag\RemovePluginDataConfirm
 */
trait PluginUninstallTrait {

	use AbstractPluginUninstallTrait;


	/**
	 * @return bool
	 * @throws RemovePluginDataConfirmException
	 */
	protected final function beforeUninstall() {
		return $this->pluginUninstall();
	}


	/**
	 *
	 */
	protected final function afterUninstall() {

	}
}
