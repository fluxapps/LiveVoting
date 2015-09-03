<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 *
 *         Acts as ILIAS entry-point and calls ILIAS-call-structure
 *         Depending on Context, an ILIAS environment or just the pin context is loaded
 */

chdir(strstr($_SERVER['SCRIPT_FILENAME'], 'Customizing', true));
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoInitialisation.php');
xlvoInitialisation::init();

global $ilCtrl, $ilBench;
/**
 * @var ilCtrl $ilCtrl
 */
$ilCtrl->setTargetScript("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/VoterEndpoint.php");
$ilCtrl->callBaseClass();
$ilBench->save();