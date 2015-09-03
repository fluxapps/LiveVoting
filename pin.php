<?php
/**
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 *         User starts here. Use a RewriteRule to access this page a bit simpler
 */
require_once('classes/class.xlvoInitialisation.php');
xlvoInitialisation::init(xlvoInitialisation::CONTEXT_PIN);
xlvoInitialisation::resetCookiePIN();

global $ilCtrl;
/**
 * @var ilCtrl $ilCtrl
 */
$ilCtrl->initBaseClass('ilUIPluginRouterGUI');
$ilCtrl->setTargetScript('classes/voting/VoterEndpoint.php');
$ilCtrl->redirectByClass(array( 'ilUIPluginRouterGUI', 'xlvoVoterGUI' ));
