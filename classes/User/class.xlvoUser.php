<?php

/**
 * Class xlvoUser
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoUser {

	/**
	 * @var xlvoUser
	 */
	protected static $instance;
	const TYPE_ILIAS = 1;
	const TYPE_PIN = 2;
	/**
	 * @var int
	 */
	protected $type = self::TYPE_PIN;
	/**
	 * @var string
	 */
	protected $identifier = '';


	/**
	 * xlvoUser constructor.
	 */
	protected function __construct() { }


	/**
	 * @return xlvoUser
	 */
	public static function getInstance() {
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * @return int
	 */
	public function getType() {
		return $this->type;
	}


	/**
	 * @return bool
	 */
	public function isILIASUser() {
		return ($this->getType() == self::TYPE_ILIAS);
	}


	/**$
	 * @return bool
	 */
	public function isPINUser() {
		return ($this->getType() == self::TYPE_PIN);
	}


	/**
	 * @param $type
	 * @return $this
	 */
	public function setType($type) {
		$this->type = $type;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getIdentifier() {
		return $this->identifier;
	}


	/**
	 * @param $identifier
	 * @return $this
	 */
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
		return $this;
	}
}
