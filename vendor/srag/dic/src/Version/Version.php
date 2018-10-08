<?php

namespace srag\DIC\Version;

/**
 * Class Version
 *
 * @package srag\DIC\Version
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
	public function is52()/*: bool*/ {
		return ($this->getILIASVersion() >= "5.2");
	}


	/**
	 * @inheritdoc
	 */
	public function is53()/*: bool*/ {
		return ($this->getILIASVersion() >= "5.3");
	}


	/**
	 * @inheritdoc
	 */
	public function is54()/*: bool*/ {
		return ($this->getILIASVersion() >= "5.4");
	}
}
