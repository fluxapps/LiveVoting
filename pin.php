<?php
/**
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 *         User starts here. Use a RewriteRule to access this page a bit simpler
 */

use LiveVoting\Conf\xlvoConf;
use LiveVoting\Context\cookie\CookieManager;
use LiveVoting\Context\InitialisationManager;
use LiveVoting\Context\xlvoContext;

require_once __DIR__ . '/vendor/autoload.php';
require_once('dir.php');

InitialisationManager::startMinimal();
CookieManager::setContext(xlvoContext::CONTEXT_PIN);
CookieManager::resetCookiePIN();

$existing_pin = trim($_REQUEST['pin'], '/');
if ($existing_pin) {
	CookieManager::setCookiePIN(trim($_REQUEST['pin'], '/'));
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
