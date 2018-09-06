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
	public static function dic()/*: DICInterface*/
	;


	/**
	 * Get plugin interface
	 *
	 * @param string $plugin_class_name
	 *
	 * @return PluginInterface Plugin interface
	 *
	 * @throws DICException Class $plugin_class_name not exists!
	 * @throws DICException Class $plugin_class_name not extends ilPlugin!
	 * @logs   DEBUG Please implement $plugin_class_name::getInstance()!
	 */
	public static function plugin(/*string*/
		$plugin_class_name)/*: PluginInterface*/
	;
}
