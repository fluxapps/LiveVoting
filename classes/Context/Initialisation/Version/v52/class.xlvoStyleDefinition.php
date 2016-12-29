<?php
namespace LiveVoting\Context\Initialisation\Version\v52;

/**
 * Class xlvoStyleDefinition
 *
 * @package LiveVoting\Context\Initialisation\Version\v52
 */
class xlvoStyleDefinition {

	/**
	 * @var \LiveVoting\Context\Initialisation\Version\v52\xlvoSkin
	 */
	protected $skin;


	/**
	 * xlvoStyleDefinition constructor.
	 */
	public function __construct() {
		$this->skin = new xlvoSkin();
	}


	/**
	 * @return string
	 */
	public function getSkin() {
		return $this->skin;
	}
}

/**
 * Class xlvoSkin
 *
 * @package LiveVoting\Context\Initialisation\Version\v52
 */
class xlvoSkin {

	/**
	 * @return string
	 */
	public function getId() {
		return 'delos';
	}


	/**
	 * @return bool
	 */
	public function hasStyle() {
		return false;
	}


	/**
	 * @return string
	 */
	public function getName() {
		return 'Delos';
	}
}
