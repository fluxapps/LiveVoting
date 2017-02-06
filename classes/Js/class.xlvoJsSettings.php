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
	 * @param $name
	 * @param $value
	 */
	public function addSetting($name, $value) {
		$this->settings[$name] = $value;
	}


	/**
	 * @param $key
	 */
	public function addTranslation($key) {
		$pl = ilLiveVotingPlugin::getInstance();
		$this->translations[$key] = $pl->txt($key);
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