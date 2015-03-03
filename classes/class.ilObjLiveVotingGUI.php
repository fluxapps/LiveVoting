<?php
/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2009 ILIAS open source, University of Cologne            |
	|                                                                             |
	| This program is free software; you can redistribute it and/or               |
	| modify it under the terms of the GNU General Public License                 |
	| as published by the Free Software Foundation; either version 2              |
	| of the License, or (at your option) any later version.                      |
	|                                                                             |
	| This program is distributed in the hope that it will be useful,             |
	| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
	| GNU General Public License for more details.                                |
	|                                                                             |
	| You should have received a copy of the GNU General Public License           |
	| along with this program; if not, write to the Free Software                 |
	| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
	+-----------------------------------------------------------------------------+
*/

require_once("./Services/Repository/classes/class.ilObjectPluginGUI.php");
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVoting.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilMultipleTextInputGUI.php');
require_once('./Services/Form/classes/class.ilSubEnabledFormPropertyGUI.php');
//@include_once('./classes/class.ilLink.php');
//@include_once('./Services/Link/classes/class.ilLink.php');
require_once('class.ilLiveVotingContentGUI.php');
require_once('./Services/Calendar/classes/class.ilDateTime.php');

if (is_file('./Services/Object/classes/class.ilDummyAccessHandler.php')) {
	include_once('./Services/Object/classes/class.ilDummyAccessHandler.php');
}


/**
 * User Interface class for example repository object.
 *
 * User interface classes process GET and POST parameter and call
 * application classes to fulfill certain tasks.
 *
 * @author            Oskar Truffer <ot@studer-raimann.ch>
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 *
 * $Id$
 *
 * Integration into control structure:
 * - The GUI class is called by ilRepositoryGUI
 * - GUI classes used by this class are ilPermissionGUI (provides the rbac
 *   screens) and ilInfoScreenGUI (handles the info screen).
 *
 * @ilCtrl_isCalledBy ilObjLiveVotingGUI: ilRepositoryGUI, ilAdministrationGUI, ilObjPluginDispatchGUI
 * @ilCtrl_Calls      ilObjLiveVotingGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonactionDispatcherGUI
 *
 */
class ilObjLiveVotingGUI extends ilObjectPluginGUI {

	const CMD_DEFAULT_BS = 'showContentBootStrap';
	const CMD_SHOW_CONTENT = "showContent";
	const CMD_EDIT_PROPERTIES = "editProperties";
	/**
	 * @var ilObjLiveVoting
	 */
	protected $live_voting;
	/**
	 * @var ilObjLiveVoting
	 */
	protected $plugin;


	protected function afterConstructor() {
		global $tpl;
		$this->live_voting = $this->object;
		$tpl->addCSS("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/content.css");
		$tpl->addCSS("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/mobile.css");
		if (ilLiveVotingConfigGUI::_getValue('use_responsive')) {
			$tpl->addCSS("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/responsive.css");
		}
	}


	/**
	 * @return bool|void
	 */
	public function &executeCommand() {
		switch ($this->ctrl->getNextClass()) {
			case 'ilcommonactiondispatchergui':
				include_once 'Services/Object/classes/class.ilCommonActionDispatcherGUI.php';
				$gui = ilCommonActionDispatcherGUI::getInstanceFromAjaxCall();
				$this->ctrl->forwardCommand($gui);
				break;
			default:
				parent::executeCommand();
				break;
		}
	}


	/**
	 * @param $a_target
	 */
	public static function _goto($a_target) {
		global $ilCtrl, $ilAccess, $lng;
		$t = explode("_", $a_target[0]);
		$ref_id = (int)$t[0];
		$class_name = $a_target[1];
		if ($ilAccess->checkAccess("read", "", $ref_id)) {
			$ilCtrl->initBaseClass("ilObjPluginDispatchGUI");
			$ilCtrl->setTargetScript("ilias.php");
			$ilCtrl->getCallStructure(strtolower("ilObjPluginDispatchGUI"));
			$ilCtrl->setParameterByClass($class_name, "ref_id", $ref_id);
			$ilCtrl->setParameterByClass($class_name, "full", "1");
			$ilCtrl->redirectByClass(array( "ilobjplugindispatchgui", $class_name ), "");
		} else {
			if ($ilAccess->checkAccess("visible", "", $ref_id)) {
				$ilCtrl->initBaseClass("ilObjPluginDispatchGUI");
				$ilCtrl->setTargetScript("ilias.php");
				$ilCtrl->getCallStructure(strtolower("ilObjPluginDispatchGUI"));
				$ilCtrl->setParameterByClass($class_name, "ref_id", $ref_id);
				$ilCtrl->setParameterByClass($class_name, "full", "1");
				$ilCtrl->redirectByClass(array( "ilobjplugindispatchgui", $class_name ), "infoScreen");
			} else {
				if ($ilAccess->checkAccess("read", "", ROOT_FOLDER_ID)) {
					$_GET["cmd"] = "frameset";
					$_GET["target"] = "";
					$_GET["ref_id"] = ROOT_FOLDER_ID;
					ilUtil::sendFailure(sprintf($lng->txt("msg_no_perm_read_item"), ilObject::_lookupTitle(ilObject::_lookupObjId($ref_id))));
					include("repository.php");
					exit;
				}
			}
		}
	}


	/**
	 * @return string
	 */
	final function getType() {
		return "xlvo";
	}


	/**
	 * @param $cmd
	 */
	public function performCommand($cmd) {
		switch ($cmd) {
			case self::CMD_EDIT_PROPERTIES: // list all commands that need write permission here
			case "updateProperties":
			case "resetVotes":
			case "confirmReset":
			case "cancelReset":
			case "freeze":
			case "unfreeze":
				$this->checkPermission("write");
				$this->$cmd();
				break;
			case self::CMD_SHOW_CONTENT: // list all commands that need read permission here
			case "vote":
			case "unvote":
			case "asyncShowContent":
			case "asyncVote":
			case "asyncUnvote":
			case "asyncIsActive":
			case self::CMD_DEFAULT_BS:
				$this->checkPermission("read");
				$this->$cmd();
				break;
		}
	}


	/**
	 * @return string
	 */
	public function getAfterCreationCmd() {
		return self::CMD_EDIT_PROPERTIES;
	}


	/**
	 * @return string
	 */
	public function getStandardCmd() {
		return self::CMD_SHOW_CONTENT;
	}


	/**
	 * @param $perm
	 *
	 * @return bool
	 */
	public function hasPermission($perm) {
		return $this->checkPermissionBool($perm);
	}


	public function setTabs() {
		global $ilTabs, $ilCtrl, $ilAccess;
		// tab for the "show content" command
		if ($ilAccess->checkAccess("read", "", $this->object->getRefId())) {
			$ilTabs->addTab("content", $this->txt("content"), $ilCtrl->getLinkTarget($this, self::CMD_SHOW_CONTENT));
//			$ilTabs->addTab("content", $this->txt("content"), $ilCtrl->getLinkTarget($this, self::CMD_DEFAULT_BS));
		}
		// standard info screen tab
		$this->addInfoTab();
		// a "properties" tab
		if ($ilAccess->checkAccess("write", "", $this->object->getRefId())) {
			$ilTabs->addTab("properties", $this->txt("properties"), $ilCtrl->getLinkTarget($this, self::CMD_EDIT_PROPERTIES));
		}
		// standard epermission tab
		$this->addPermissionTab();
	}


	public function editProperties() {
		global $tpl, $ilTabs;
		$ilTabs->activateTab("properties");
		$this->initPropertiesForm();
		$this->getPropertiesValues();
		$tpl->setContent($this->form->getHTML());
	}


	public function initPropertiesForm() {
		global $ilCtrl, $ilUser;
		$pl = ilLiveVotingPlugin::getInstance();
		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$this->form = new ilPropertyFormGUI();
		// title
		$ti = new ilTextInputGUI($this->txt("title"), "title");
		$ti->setRequired(true);
		$this->form->addItem($ti);
		// description
		$ta = new ilTextAreaInputGUI($this->txt("description"), "desc");
		$this->form->addItem($ta);
		// question
		$qu = new ilTextAreaInputGUI($pl->txt("question"), "question");
		$qu->setUseRte(true);
		$qu->usePurifier(true);
		$qu->setRTESupport($this->object->getId(), "xlvo", "xlvo_question", NULL, false, "3.4.7");
		$this->form->addItem($qu);
		// online
		$cb = new ilCheckboxInputGUI($this->lng->txt("online"), "online");
		$this->form->addItem($cb);
		// terminated
		$cb = new ilCheckboxInputGUI($pl->txt("terminated"), "terminated");
		$cb->setValue(1);
		include_once('./Services/Form/classes/class.ilDateDurationInputGUI.php');
		$te = new ilDateDurationInputGUI($pl->txt("terminated_select"), "terminated_select");
		$te->setShowTime(true);
		$te->setStartText($pl->txt("terminated_select_start_time"));
		$te->setEndText($pl->txt("terminated_select_end_time"));
		$te->setMinuteStepSize(1);
		if ($this->live_voting->getTerminated()) {
			$te->setStart(new ilDateTime($this->live_voting->getStart(), IL_CAL_UNIX, $ilUser->getTimeZone()));
			$te->setEnd(new ilDateTime($this->live_voting->getEnd(), IL_CAL_UNIX, $ilUser->getTimeZone()));
		}
		$cb->addSubItem($te);
		$this->form->addItem($cb);
		// anonym
		$cb = new ilCheckboxInputGUI($pl->txt("anonym"), "anonym");
		//$cb->setChecked(true);
		$this->form->addItem($cb);
		$cb = new ilCheckboxInputGUI($pl->txt("colorful"), "colorful");
		$this->form->addItem($cb);
		$cb = new ilCheckboxInputGUI($pl->txt("multiple_selection"), "multiple_selection");
		$cb->setInfo($pl->txt("multiple_selection_info"));
		$this->form->addItem($cb);
		$options = new ilMultipleTextInputGUI($pl->txt("choices"), "choices", $pl->txt("new_option_placeholder"));
		$options->setDisableOldFields(true);
		$options->setInfo($pl->txt("choices_info"));
		$this->form->addItem($options);
		$this->form->addCommandButton("updateProperties", $this->txt("save"));
		$this->form->setTitle($this->txt("edit_properties"));
		$this->form->setFormAction($ilCtrl->getFormAction($this));
	}


	public function getPropertiesValues() {
		$values["title"] = $this->object->getTitle();
		$values["desc"] = $this->object->getDescription();
		$values["online"] = $this->object->getOnline();
		$values["anonym"] = $this->object->getAnonym();
		$values["colorful"] = $this->object->getColorful();
		$values["question"] = $this->live_voting->getQuestion();
		$values["multiple_selection"] = $this->live_voting->getOptionsType();
		$values["terminated"] = $this->live_voting->getTerminated();
		/*$values['terminated_select'] =  array(
			'start' => $this->getArrayFromTimestamp($this->live_voting->getStart()),
			'end' => $this->getArrayFromTimestamp($this->live_voting->getStart())
		);*/
		$options = $this->live_voting->getOptions();
		if (is_array($options) && count($options) > 0) {
			foreach ($options as $option) {
				$values["choices"][$option->getId()] = $option->getTitle();
			}
		}
		$this->form->setValuesByArray($values);
	}


	/**
	 * Update properties
	 */
	public function updateProperties() {
		global $tpl, $lng, $ilCtrl;
		$this->initPropertiesForm();
		if ($this->form->checkInput()) {
			$this->live_voting->setTitle($this->form->getInput("title"));
			$this->live_voting->setDescription($this->form->getInput("desc"));
			$this->live_voting->setAnonym($this->form->getInput("anonym"));
			$this->live_voting->setColorful($this->form->getInput("colorful"));
			$this->live_voting->setOnline($this->form->getInput("online"));
			$this->live_voting->setOptionsType($this->form->getInput("multiple_selection"));
			$this->live_voting->setQuestion($this->form->getInput("question"));
			$this->live_voting->setTerminated($this->form->getInput("terminated"));
			$terminate_select = $this->form->getInput("terminated_select");
			$this->live_voting->setStart($this->getTimestampFromArray($terminate_select['start']));
			$this->live_voting->setEnd($this->getTimestampFromArray($terminate_select['end']));
			//forced to use $_REQUEST... doesn't work with $_POST... no idea why.
			$this->live_voting->setOptionsByArray($_REQUEST['choices']);
			$this->live_voting->update();
			ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
			$ilCtrl->redirect($this, self::CMD_EDIT_PROPERTIES);
		}
		$this->form->setValuesByPost();
		$tpl->setContent($this->form->getHtml());
	}


	/**
	 * @return string
	 */
	public static function getHttpPath() {
		$httpPath = ilUtil::_getHttpPath();
		preg_match('/(\/Customizing|\/vote)/', $httpPath, $matches, PREG_OFFSET_CAPTURE);
		$position = $matches[0][1];
		if ($position) {
			$httpPath = substr($httpPath, 0, $position);
		}

		return $httpPath;
	}


	/**
	 * @param $pin
	 *
	 * @return string
	 */
	public static function getLinkByPin($pin) {
		$obj = ilObjLiveVoting::_getObjectByPin($pin);
		$link = self::getHttpPath();
		if ($obj) {
			$anonym = $obj->getAnonym();
			$global_anonym = ilLiveVotingConfigGUI::_getValue('global_anonymous');
		} else {
			return false;
		}

		if ($anonym AND $global_anonym) {
			$link .= '/Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting';
			$link .= '/anonymous.php?pin=' . $pin . '&full=1';
		} else {
			// if the livevoting isn't anonym or global_anonym is disabled
			$ref = ilObject::_getAllReferences($obj->getId());
			$link .= '/goto.php?target=xlvo_' . array_pop($ref) . '&full=1';
		}

		return $link;
	}


	/**
	 * freeze
	 */
	public function freeze() {
		global $ilCtrl;
		$this->live_voting->setFreezed(1);
		$this->live_voting->doUpdate();
		$pl = ilLiveVotingPlugin::getInstance();
		ilUtil::sendInfo($pl->txt("voting_freezed"), true);
		$ilCtrl->redirect($this, self::CMD_SHOW_CONTENT);
	}


	/**
	 * unfreeze
	 */
	public function unfreeze() {
		global $ilCtrl;
		$this->live_voting->setFreezed(0);
		$this->live_voting->doUpdate();
		$pl = ilLiveVotingPlugin::getInstance();
		ilUtil::sendInfo($pl->txt("voting_unfreezed"), true);
		$ilCtrl->redirect($this, self::CMD_SHOW_CONTENT);
	}


	/**
	 * confirmDelete
	 */
	public function confirmReset() {
		global $ilCtrl, $lng, $tpl, $ilTabs;
		$ilTabs->setTabActive("content");
		$pl = ilLiveVotingPlugin::getInstance();
		include_once './Services/Utilities/classes/class.ilConfirmationGUI.php';
		$conf = new ilConfirmationGUI();
		$conf->setFormAction($ilCtrl->getFormAction($this));
		$conf->setHeaderText($pl->txt('lvo_confirm_reset_title'));
		$conf->addItem('live_vote', $this->live_voting->getId(), $pl->txt('lvo_confirm_reset_text'));
		$conf->setConfirm($pl->txt('reset'), 'resetVotes');
		$conf->setCancel($lng->txt('cancel'), 'cancelReset');
		$tpl->setContent($conf->getHTML());
	}


	/**
	 * cancelDelete
	 */
	public function cancelReset() {
		global $ilCtrl;
		$pl = ilLiveVotingPlugin::getInstance();
		ilUtil::sendInfo($pl->txt("reset_canceled"), true);
		$ilCtrl->redirect($this, self::CMD_SHOW_CONTENT);
	}


	/**
	 * Show content
	 */
	function showContent() {
		global $tpl, $ilTabs;
		$ilTabs->setTabActive("content");
		$contentGUI = new ilLiveVotingContentGUI($this->live_voting, $this);
		$tpl->setContent($contentGUI->getHTML());
	}


	protected function showContentBootStrap() {
		require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/class.xlvoDisplayGUI.php');
		$xlvoDisplayGUI = new xlvoDisplayGUI($this->live_voting);
		$xlvoDisplayGUI->addQRCode('lorem');



		$this->tpl->setContent($xlvoDisplayGUI->getHTML());
	}


	/**
	 * asyncShowContent
	 */
	function asyncShowContent() {
		foreach ($this->live_voting->getOptions() as $option) {
			$json[$option->getId()] = array(
				"votes" => $option->countVotes(),
				"percentage" => $this->live_voting->getRelativePercentageForOption($option->getId()),
				"percentage_round" => round($this->live_voting->getRelativePercentageForOption($option->getId()), 0),
				"abs_percentage" => round($this->live_voting->getPercentageForOption($option->getId()), 2),
			);
		}
		$json['total'] = $this->live_voting->getAbsoluteVotes();
		echo json_encode($json);
		exit;
	}


	/**
	 * @param $selector
	 */
	public static function _addWaitBox($selector) {
		global $tpl;
		$pl = ilLiveVotingPlugin::getInstance();
		$box = $pl->getTemplate('tpl.wait.html');
		$id = $selector;
		foreach (array(
			         '#',
			         '.',
			         '[',
			         ']',
			         ' ',
		         ) as $r) {
			$id = str_ireplace($r, '_', $id);
		}
		$box->setVariable('ID', 'sel_' . $id);
		$box->setVariable('SELECTOR', $selector);
		$tpl->setDescription($box->get());
	}


	function asyncIsActive() {
		if ($this->object->isActive()) {
			$json['is_active'] = 'true';
		} else {
			$json['is_active'] = 'false';
		}
		echo json_encode($json);
		exit;
	}


	function vote() {
		global $ilUser;
		$option_id = $_GET['option_id'];
		$this->live_voting->vote($option_id, $ilUser->getId(), session_id());
		$this->showContent();
	}


	function asyncVote() {
		global $ilUser;
		$option_id = $_GET['option_id'];
		$this->live_voting->vote($option_id, $ilUser->getId(), session_id());
		exit;
	}


	function unvote() {
		global $ilUser;
		$option_id = $_GET['option_id'];
		$this->live_voting->unvote($option_id, $ilUser->getId(), session_id());
		$this->showContent();
	}


	function asyncUnvote() {
		global $ilUser;
		$option_id = $_GET['option_id'];
		$this->live_voting->unvote($option_id, $ilUser->getId(), session_id());
		exit;
	}


	function resetVotes() {
		global $ilCtrl;
		$this->live_voting->deleteAllVotes();
		$ilCtrl->redirect($this, self::CMD_SHOW_CONTENT);
		//$this->showContent();
	}


	/**
	 * getTimestampFromArray
	 *
	 * @access protected
	 *
	 * @param  $a_field array
	 *
	 * @return int
	 */
	protected function getTimestampFromArray($a_field) {
		global $ilUser;
		include_once('./Services/Calendar/classes/class.ilDateTime.php');
		$dt['year'] = (int)$a_field['date']['y'];
		$dt['mon'] = (int)$a_field['date']['m'];
		$dt['mday'] = (int)$a_field['date']['d'];
		$dt['hours'] = (int)$a_field['time']['h'];
		$dt['minutes'] = (int)$a_field['time']['m'];
		$dt['seconds'] = (int)$a_field['time']['s'];
		$date = new ilDateTime($dt, IL_CAL_FKT_GETDATE, $ilUser->getTimeZone());

		return $date->getUnixTime();
	}


	/**
	 * getTimestampFromArray
	 *
	 * @access protected
	 *
	 * @param  $a_timestamp int
	 *
	 * @return array
	 */
	protected function getArrayFromTimestamp($a_timestamp) {
		global $ilUser;
		$ilDate = new ilDateTime($a_timestamp, IL_CAL_UNIX, $ilUser->getTimeZone());
		list($date, $time) = explode(":", $ilDate->get(4, "d-m-Y:H-i-s"));
		list($return['date']['d'], $return['date']['m'], $return['date']['y']) = explode("-", $date);
		list($return['time']['h'], $return['time']['m'], $return['time']['s']) = explode("-", $time);

		return $return;
	}
}

?>
