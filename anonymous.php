<?php

// define ILIAS_MODULE, so ILIAS_HTTP_PATH is correct
define('ILIAS_MODULE', 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting');

require_once('include.php');
require_once('classes/class.ilObjLiveVotingGUI.php');
require_once('classes/class.ilObjLiveVoting.php');
require_once('classes/class.ilLiveVotingContentGUI.php');
global $tpl, $ilTabs;
if (ilObjLiveVoting::_isGlobalAnonymForPin($_GET['pin'])) {
	$xlvObj = ilObjLiveVoting::_getObjectByPin($_GET['pin']);
	if ($xlvObj->getOnline()) {
		$xlvObjGUI = new ilObjLiveVotingGUI($xlvObj->getId(), ilObjLiveVotingGUI::OBJECT_ID);
		$ctrl = new AlternativeCtrl($ilias_root_relative);
		if ($_GET['cmd']) {
			$router = new LiveVotingRouter($xlvObj, $xlvObjGUI);
			$router->executeCommand($_GET['cmdClass'], $_GET['cmd']);
		}
		$ctrl->setParameterByClass('ilLiveVotingContentGUI', 'pin', $_GET['pin']);
		$ctrl->setParameterByClass('ilObjLiveVotingGUI', 'pin', $_GET['pin']);
		$xlvObjContentGUI = new ilLiveVotingContentGUI($xlvObj, $xlvObjGUI, $ctrl);
		$pin_url = ILIAS_HTTP_PATH . '/Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/pin.php';
		$pl = new ilLiveVotingPlugin();
		$tpl->addCss($pl->getStyleSheetLocation('pin_hider.css'));
		$tpl->getStandardTemplate();
		$tpl->setCurrentBlock('HeadBaseTag');
		$tpl->setVariable('BASE', ILIAS_HTTP_PATH . '/');
		$tpl->parseCurrentBlock();
		$back = $pl->getTemplate('tpl.backtopin.html', true, false);
		$back->setVariable('BACKTOPIN', $pl->txt('back_to_pin'));
		$back->setVariable('HREF', $pin_url);
		$tpl->setContent($back->get() . $xlvObjContentGUI->getHTML());
		$tpl->show();
	} else {
		ilUtil::sendFailure('LiveVoting ist nicht anonym zugänglich.', true);
		ilUtil::redirect(ILIAS_HTTP_PATH . '/Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/pin.php');
	}
} else {
	ilUtil::sendFailure('LiveVoting ist nicht anonym zugänglich.', true);
	ilUtil::redirect(ILIAS_HTTP_PATH . '/Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/pin.php');
}

/**
 * LiveVotingRouter
 */
class LiveVotingRouter {

	/**
	 * @var ilObjLiveVoting
	 */
	private $obj;
	/**
	 * @var ilObjLiveVotingGUI
	 */
	protected $gui;


	/**
	 * @param $obj
	 * @param $gui
	 */
	public function __construct($obj, $gui) {
		$this->obj = $obj;
		$this->gui = $gui;
	}


	/**
	 * @param $cmd_class
	 * @param $cmd
	 */
	public function executeCommand($cmd_class, $cmd) {
		$this->$cmd();
	}


	private function unvote() {
		$this->obj->unvote($_GET['option_id'], NULL, session_id());
	}


	private function vote() {
		$this->obj->vote($_GET['option_id'], NULL, session_id());
	}


	protected function asyncIsActive() {
		$this->gui->asyncIsActive();
	}


	protected function asyncShowContent() {
		$this->gui->asyncShowContent();
	}
}

/**
 * AlternativeCtrl
 * an ugly alternative ilCtrl for out of ILIAS ctrling.
 */
class AlternativeCtrl {

	protected $parameters = array();
	protected $client;


	/**
	 * @param null $client
	 */
	public function __construct($client = NULL) {
		$this->client = $client;
	}


	/**
	 * @param $class_name
	 * @param $cmd
	 *
	 * @return string
	 */
	public function getLinkTargetByClass($class_name, $cmd) {
		$link = str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']) . '?cmdClass=' . strtolower($class_name)
			. '&cmd=' . strtolower($cmd);;
		foreach ($this->parameters[$class_name] as $parameter => $value) {
			$link .= '&' . $parameter . '=' . $value;
		}

		return $link;
	}


	/**
	 * @param $class_name
	 * @param $parameter
	 * @param $value
	 */
	public function setParameterByClass($class_name, $parameter, $value) {
		if (is_array($this->parameters[$class_name])) {
			$this->parameters[$class_name][$parameter] = $value;
		} else {
			$this->parameters[$class_name] = array( $parameter => $value );
		}
	}


	/**
	 * @param $class_name
	 * @param $parameter
	 * @param $value
	 */
	public function saveParameterByClass($class_name, $parameter, $value) {
		$this->setParameterByClass($class_name, $parameter, $value);
	}
}

?>
