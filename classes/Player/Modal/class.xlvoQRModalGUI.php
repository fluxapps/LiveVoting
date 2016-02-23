<?php
require_once('./Services/UIComponent/Modal/classes/class.ilModalGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Player/QR/class.xlvoQR.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Conf/class.xlvoConf.php');

/**
 * Class xlvoQRModalGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoQRModalGUI extends ilModalGUI {

	/**
	 * @param \xlvoVotingConfig $xlvoVotingConfig
	 * @return \xlvoQRModalGUI
	 */
	public static function getInstanceFromVotingConfig(xlvoVotingConfig $xlvoVotingConfig) {
		xlvoJs::getInstance()->name('Modal')->addSettings(array( 'id' => 'QRModal' ))->category('Player')->init();
		$ilModalGUI = new self();
		$ilModalGUI->setId('QRModal');
		$ilModalGUI->setHeading('PIN: ' . $xlvoVotingConfig->getPin());

		$short_link = xlvoConf::getShortLinkURL() . $xlvoVotingConfig->getPin();

		$modal_body = '<span class="label label-default xlvo-label-url resize">' . $short_link . '</span>';
		$modal_body .= '<img id="xlvo-modal-qr" src="' . xlvoQR::getImageDataString($short_link, 1200) . '">';

		$ilModalGUI->setBody($modal_body);
		$ilModalGUI->setType(ilModalGUI::TYPE_LARGE);

		return $ilModalGUI;
	}
}
