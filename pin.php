<?php
/**
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 *         User starts here. Use a RewriteRule to access this page a bit simpler
 */

use LiveVoting\Conf\xlvoConf;
use LiveVoting\Context\xlvoInitialisation;

require_once __DIR__ . '/vendor/autoload.php';
require_once('dir.php');
xlvoInitialisation::init(xlvoInitialisation::CONTEXT_PIN);
xlvoInitialisation::resetCookiePIN();
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
