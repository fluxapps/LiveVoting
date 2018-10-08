<?php

namespace srag\DIC\Version;

/**
 * Interface VersionInterface
 *
 * @package srag\DIC\Version
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface VersionInterface {

	/**
	 * @return string
	 */
	public function getILIASVersion()/*: string*/
	;


	/**
	 * @return bool
	 */
	public function is52()/*: bool*/
	;


	/**
	 * @return bool
	 */
	public function is53()/*: bool*/
	;


	/**
	 * @return bool
	 */
	public function is54()/*: bool*/
	;
}
