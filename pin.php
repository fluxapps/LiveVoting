<?php

//chdir('../../../../../../..');
//
//require_once("Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/archive/context/srContext.php");
//require_once("Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/archive/context/srContextLvo.php");
//require_once("Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/archive/context/srInitialisation.php");
//require_once("Services/Init/classes/class.ilInitialisation.php");
//
//srContext::init('srContextLvo');
//srInitialisation::initILIAS();


require_once('classes/class.xlvoInitialisation.php');
//xlvoInitialisation::writeToCookie(xlvoInitialisation::CONTEXT_PIN);
xlvoInitialisation::initILIAS();
//xlvoInitialisation::writeToSession(xlvoInitialisation::CONTEXT_PIN);

global $ilCtrl;
/**
 * @var ilCtrl $ilCtrl
 */
$ilCtrl->initBaseClass('ilUIPluginRouterGUI');
$ilCtrl->setTargetScript('classes/voting/VoterEndpoint.php');
$ilCtrl->redirectByClass(array( 'ilUIPluginRouterGUI', 'xlvoVoterGUI' ));
