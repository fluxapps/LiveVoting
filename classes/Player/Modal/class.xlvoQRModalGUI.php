<?php

use LiveVoting\Js\xlvoJs;
use LiveVoting\Player\QR\xlvoQR;

/**
 * Class xlvoQRModalGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoQRModalGUI extends ilModalGUI {

	/**
	 * @param xlvoVotingConfig $xlvoVotingConfig
	 *
	 * @return xlvoQRModalGUI
	 */
	public static function getInstanceFromVotingConfig(xlvoVotingConfig $xlvoVotingConfig) {
		$pl = ilLiveVotingPlugin::getInstance();

		xlvoJs::getInstance()->name('Modal')->addSettings(array( 'id' => 'QRModal' ))->category('Player')->init();
		$ilModalGUI = new self();
		$ilModalGUI->setId('QRModal');
		$ilModalGUI->setHeading(sprintf($pl->txt("player_pin"), $xlvoVotingConfig->getPin()));

		$short_link = $xlvoVotingConfig->getShortLinkURL();

		$modal_body = '<span class="label label-default xlvo-label-url resize">' . $short_link . '</span>';
		$modal_body .= '<img id="xlvo-modal-qr" src="' . xlvoQR::getImageDataString($short_link, 1200) . '">';

		$ilModalGUI->setBody($modal_body);
		$ilModalGUI->setType(ilModalGUI::TYPE_LARGE);

		return $ilModalGUI;
	}
}
