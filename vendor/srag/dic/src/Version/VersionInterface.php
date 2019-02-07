<?php

namespace srag\DIC\LiveVoting\Version;

/**
 * Interface VersionInterface
 *
 * @package srag\DIC\LiveVoting\Version
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface VersionInterface {

	const ILIAS_VERSION_5_2 = "5.2.0";
	const ILIAS_VERSION_5_3 = "5.3.0";
	const ILIAS_VERSION_5_4 = "5.4.0";
	const ILIAS_VERSION_6_0 = "6.0.0";


	/**
	 * @return string
	 */
	public function getILIASVersion()/*: string*/
	;


	/**
	 * @return bool
	 */
	public function isEqual(/*string*/
		$version)/*: bool*/
	;


	/**
	 * @return bool
	 */
	public function isGreater(/*string*/
		$version)/*: bool*/
	;


	/**
	 * @return bool
	 */
	public function isLower(/*string*/
		$version)/*: bool*/
	;


	/**
	 * @return bool
	 */
	public function isMaxVersion(/*string*/
		$version)/*: bool*/
	;


	/**
	 * @return bool
	 */
	public function isMinVersion(/*string*/
		$version)/*: bool*/
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


	/**
	 * @return bool
	 */
	public function is60()/*: bool*/
	;
}
