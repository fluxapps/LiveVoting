<?php

namespace LiveVoting\Js;

use ilLiveVotingPlugin;

/**
 * Class xlvoJsSettings
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoJsSettings {

	/**
	 * @var array
	 */
	protected $settings = array();
	/**
	 * @var array
	 */
	protected $translations = array();
	/**
	 * @var ilLiveVotingPlugin
	 */
	protected $pl;


	/**
	 *
	 */
	public function __construct() {
		$this->pl = ilLiveVotingPlugin::getInstance();
	}


	/**
	 * @param string $name
	 * @param mixed  $value
	 */
	public function addSetting($name, $value) {
		$this->settings[$name] = $value;
	}


	/**
	 * @param string $key
	 */
	public function addTranslation($key) {
		$this->translations[$key] = $this->pl->txt($key);
	}


	/**
	 * @return string
	 */
	public function asJson() {
		$arr = array();
		foreach ($this->settings as $name => $value) {
			$arr[$name] = $value;
		}

		foreach ($this->translations as $key => $string) {
			$arr['lng'][$key] = $string;
		}

		return json_encode($arr);
	}
}
