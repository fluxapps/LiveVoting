<?php

namespace srag\DIC;

use srag\DIC\DIC\DICInterface;
use srag\DIC\Exception\DICException;
use srag\DIC\Plugin\PluginInterface;

/**
 * Interface DICStaticInterface
 *
 * @package srag\DIC
 */
interface DICStaticInterface {

	/**
	 * Get DIC interface
	 *
	 * @return DICInterface DIC interface
	 */
	public static function dic();


	/**
	 * Get plugin interface
	 *
	 * @param string $plugin_class_name
	 *
	 * @return PluginInterface Plugin interface
	 *
	 * @throws DICException Class $plugin_class_name not exists!
	 * @logs   DEBUG Please implement $plugin_class_name::getInstance()!
	 */
	public static function plugin($plugin_class_name);
}
