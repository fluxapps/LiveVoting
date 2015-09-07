<?php
/**
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 *         User starts here. Use a RewriteRule to access this page a bit simpler
 */
require_once('classes/class.xlvoInitialisation.php');
xlvoInitialisation::init(xlvoInitialisation::CONTEXT_PIN);
xlvoInitialisation::resetCookiePIN();
if (trim($_REQUEST['pin'], '/')) {
	xlvoInitialisation::setCookiePIN(trim($_REQUEST['pin'], '/'));
}
global $ilCtrl;
/**
 * @var ilCtrl $ilCtrl
 */
$ilCtrl->initBaseClass('ilUIPluginRouterGUI');
$ilCtrl->setTargetScript(dirname($_SERVER['SCRIPT_NAME']) . '/classes/voting/VoterEndpoint.php');
$ilCtrl->redirectByClass(array(
	'ilUIPluginRouterGUI',
	'xlvoVoterGUI'
));
