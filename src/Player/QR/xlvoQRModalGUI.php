<?php

namespace LiveVoting\Player\QR;

use ilLiveVotingPlugin;
use ilModalGUI;
use LiveVoting\Js\xlvoJs;
use LiveVoting\Pin\xlvoPin;
use LiveVoting\Voting\xlvoVotingConfig;
use srag\DIC\DICTrait;

/**
 * Class xlvoQRModalGUI
 *
 * @package LiveVoting\Player\QR
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoQRModalGUI extends ilModalGUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


	/**
	 * @param xlvoVotingConfig $xlvoVotingConfig
	 *
	 * @return xlvoQRModalGUI
	 */
	public static function getInstanceFromVotingConfig(xlvoVotingConfig $xlvoVotingConfig) {
		xlvoJs::getInstance()->name('Modal')->addSettings(array( 'id' => 'QRModal' ))->category('Player')->init()->setRunCode();
		$ilModalGUI = new self();
		$ilModalGUI->setId('QRModal');
		$ilModalGUI->setHeading(self::translate("player_pin", "", [ xlvoPin::formatPin($xlvoVotingConfig->getPin()) ]));

		$modal_body = '<span class="label label-default xlvo-label-url resize">' . $xlvoVotingConfig->getShortLinkURL()
			. '</span>'; // TODO: Fix link label shrinks after opem modal animation
		$modal_body .= '<img id="xlvo-modal-qr" src="' . xlvoQR::getImageDataString($xlvoVotingConfig->getShortLinkURL(true), 1200) . '">';

		$ilModalGUI->setBody($modal_body);
		$ilModalGUI->setType(ilModalGUI::TYPE_LARGE);

		return $ilModalGUI;
	}
}
