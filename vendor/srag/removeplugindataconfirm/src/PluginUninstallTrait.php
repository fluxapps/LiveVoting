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
	 *
	 * @access namespace
	 */
	protected final function beforeUninstall()/*: bool*/ {
		return $this->pluginUninstall();
	}


	/**
	 * @access namespace
	 */
	protected final function afterUninstall()/*: void*/ {

	}
}
