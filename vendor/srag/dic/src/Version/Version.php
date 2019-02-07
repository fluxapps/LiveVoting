<?php

namespace srag\DIC\LiveVoting\Version;

/**
 * Class Version
 *
 * @package srag\DIC\LiveVoting\Version
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Version implements VersionInterface {

	/**
	 * @inheritdoc
	 */
	public function getILIASVersion()/*: string*/ {
		return ILIAS_VERSION_NUMERIC;
	}


	/**
	 * @inheritdoc
	 */
	public function isEqual(/*string*/
		$version)/*: bool*/ {
		return (version_compare($this->getILIASVersion(), $version) === 0);
	}


	/**
	 * @inheritdoc
	 */
	public function isGreater(/*string*/
		$version)/*: bool*/ {
		return (version_compare($this->getILIASVersion(), $version) > 0);
	}


	/**
	 * @inheritdoc
	 */
	public function isLower(/*string*/
		$version)/*: bool*/ {
		return (version_compare($this->getILIASVersion(), $version) < 0);
	}


	/**
	 * @inheritdoc
	 */
	public function isMaxVersion(/*string*/
		$version)/*: bool*/ {
		return (version_compare($this->getILIASVersion(), $version) <= 0);
	}


	/**
	 * @inheritdoc
	 */
	public function isMinVersion(/*string*/
		$version)/*: bool*/ {
		return (version_compare($this->getILIASVersion(), $version) >= 0);
	}


	/**
	 * @inheritdoc
	 */
	public function is52()/*: bool*/ {
		return $this->isMinVersion(self::ILIAS_VERSION_5_2);
	}


	/**
	 * @inheritdoc
	 */
	public function is53()/*: bool*/ {
		return $this->isMinVersion(self::ILIAS_VERSION_5_3);
	}


	/**
	 * @inheritdoc
	 */
	public function is54()/*: bool*/ {
		return $this->isMinVersion(self::ILIAS_VERSION_5_4);
	}


	/**
	 * @inheritdoc
	 */
	public function is60()/*: bool*/ {
		return $this->isMinVersion(self::ILIAS_VERSION_6_0);
	}
}
