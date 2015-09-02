<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * ilias.php. main script.
 *
 * If you want to use this script your base class must be declared
 * within modules.xml.
 *
 * @author  Alex Killing <alex.killing@gmx.de>
 * @version $Id$
 *
 */

chdir('../../../../../../../../..');

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoInitialisation.php');
xlvoInitialisation::initILIAS();

global $ilCtrl, $ilBench;
/**
 * @var ilCtrl $ilCtrl
 */
$ilCtrl->setTargetScript("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/VoterEndpoint.php");
$ilCtrl->callBaseClass();
$ilBench->save();