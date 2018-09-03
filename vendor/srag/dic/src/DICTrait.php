<?php

namespace srag\DIC;

use srag\DIC\DIC\DICInterface;
use srag\DIC\Exception\DICException;
use srag\DIC\Plugin\PluginInterface;

/**
 * Trait DICTrait
 *
 * @package srag\DIC
 */
trait DICTrait {

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
	protected static final function dic() {
		return DICStatic::dic();
	}


	/**
	 * Get plugin interface
	 *
	 * @return PluginInterface Plugin interface
	 *
	 * @throws DICException Class $plugin_class_name not exists!
	 * @logs   DEBUG Please implement $plugin_class_name::getInstance()!
	 */
	protected static final function plugin() {
		self::checkPluginClassNameConst();

		return DICStatic::plugin(static::PLUGIN_CLASS_NAME);
	}


	/**
	 * @throws DICException Your class needs to implement the PLUGIN_CLASS_NAME constant!
	 */
	private static final function checkPluginClassNameConst() {
		if (!defined("static::PLUGIN_CLASS_NAME") || empty(static::PLUGIN_CLASS_NAME)) {
			throw new DICException("Your class needs to implement the PLUGIN_CLASS_NAME constant!");
		}
	}
}
