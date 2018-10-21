<?php

namespace LiveVoting\GUI;

use ilLinkButton;
use ilLiveVotingPlugin;
use srag\DIC\DICTrait;

/**
 * Class xlvoLinkButton
 *
 * @package LiveVoting\GUI
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoLinkButton extends ilLinkButton {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
	const TYPE_XLVO_LINK = 'xlvo_link';


	public function clearClasses() {
		$this->css = array();
	}


	/**
	 * Prepare render
	 */
	protected function prepareRender() {
		$this->addCSSClass('btn');
	}


	/**
	 * @return xlvoLinkButton
	 */
	public static function getInstance() {
		return new self(self::TYPE_XLVO_LINK);
	}
}
