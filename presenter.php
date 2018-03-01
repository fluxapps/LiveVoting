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
CookieManager::resetCookiePUK();

$existing_pin = trim($_REQUEST['pin'], '/');
$puk = false;
if ($existing_pin) {
	CookieManager::setCookiePIN($existing_pin);

	$puk = trim($_REQUEST['puk'], '/');
	if ($puk) {
		CookieManager::setCookiePUK($puk);
	}
}
global $DIC;
$ilCtrl = $DIC->ctrl();
$ilCtrl->initBaseClass(ilUIPluginRouterGUI::class);
$ilCtrl->setTargetScript(xlvoConf::getFullApiURL());
$ilCtrl->redirectByClass(array(
	ilUIPluginRouterGUI::class,
	xlvoVoter2GUI::class,
), $puk ? xlvoVoter2GUI::CMD_START_PRESENTER : xlvoVoter2GUI::CMD_STANDARD);
