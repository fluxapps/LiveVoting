<?php
/**
 * AJAX ilCtrlMainMenuChecker
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
require_once('./classes/class.ctrlmmChecker.php');
ctrlmmChecker::check($_REQUEST['classes']);
?>