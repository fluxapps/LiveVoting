<?php
/**
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 *         User starts here. Use a RewriteRule to access this page a bit simpler
 */
require_once('classes/Context/class.xlvoInitialisation.php');
xlvoInitialisation::init(xlvoInitialisation::CONTEXT_PIN);
xlvoInitialisation::resetCookiePIN();
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Conf/class.xlvoConf.php');

if (trim($_REQUEST['pin'], '/')) {
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
	'xlvoVoter2GUI'
));
