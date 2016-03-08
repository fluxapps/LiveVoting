<?php
/**
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 *         User starts here. Use a RewriteRule to access this page a bit simpler
 */
require_once('dir.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Context/class.xlvoInitialisation.php');
xlvoInitialisation::init(xlvoInitialisation::CONTEXT_PIN);
xlvoInitialisation::resetCookiePIN();
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Conf/class.xlvoConf.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voter/class.xlvoVoter2GUI.php');
$existing_pin = trim($_REQUEST['pin'], '/');
if ($existing_pin) {
	xlvoInitialisation::setCookiePIN(trim($_REQUEST['pin'], '/'));
}
global $ilCtrl;
/**
 * @var ilCtrl $ilCtrl
 */
$ilCtrl->initBaseClass('ilUIPluginRouterGUI');
$ilCtrl->setTargetScript(xlvoConf::getFullApiURL());
$ilCtrl->redirectByClass(array(
	'ilUIPluginRouterGUI',
	'xlvoVoter2GUI',
), $existing_pin ? xlvoVoter2GUI::CMD_START_VOTER_PLAYER : xlvoVoter2GUI::CMD_STANDARD);
