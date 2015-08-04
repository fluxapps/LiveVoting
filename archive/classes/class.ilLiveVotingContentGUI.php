<?php
require_once('class.ilLiveVotingQR.php');
require_once('class.ilLiveVotingConfigGUI.php');
require_once('./Services/UIComponent/Toolbar/classes/class.ilToolbarGUI.php');
@include_once('./classes/class.ilBrowser.php');

/**
 * Class ilLiveVotingContentGUI
 *
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version $Id$
 *
 */
class ilLiveVotingContentGUI {

	/**
	 * @var ilObjLiveVoting
	 */
	protected $live_voting;
	/**
	 * @var ilObjLiveVotingGUI
	 */
	protected $live_voting_gui;
	/**
	 * @var null
	 */
	protected $local_ctrl;


	/**
	 * @param      $live_voting
	 * @param      $live_voting_gui
	 * @param null $alternativeCtrl
	 */
	public function __construct($live_voting, $live_voting_gui, $alternativeCtrl = NULL) {
		global $ilCtrl;
		if ($alternativeCtrl) {
			$this->local_ctrl = $alternativeCtrl;
		} else {
			$this->local_ctrl = $ilCtrl;
		}
		$this->live_voting = $live_voting;
		$this->live_voting_gui = $live_voting_gui;
	}


	public function getHTML() {
		global $ilUser;
		$pl = ilLiveVotingPlugin::getInstance();
		$tpl = $pl->getTemplate('tpl.content.html');
		$options = $this->live_voting->getOptions();
		$this->local_ctrl->saveParameterByClass('ilObjLiveVotingGUI', 'full', 1);
		/* SETTING THE HEADER */
		if (ilLiveVotingConfigGUI::_getValue('allow_fullscreen_jquery')) {
			$tpl->touchBlock('jquery_fullscreen');
		}
		// Voting Instructions
		if (get_class($this->local_ctrl) == 'ilCtrl' OR get_class($this->local_ctrl) == 'ilCtrl2') {
			$tpl->setCurrentBlock("pinblock");
			$tpl->setVariable("PIN", $this->live_voting->getPin());
			if (ilLiveVotingConfigGUI::_getValue("allow_shortlink")) {
				$tpl->setVariable("PIN_URL", ilLiveVotingConfigGUI::_getValue("allow_shortlink_link"));
			}
			$tpl->parseCurrentBlock();
			if (ilLiveVotingConfigGUI::_getValue("aspsms") AND ilLiveVotingConfigGUI::_getValue("aspsms_number")
				AND $this->live_voting->getAnonym()
			) {
				$tpl->setCurrentBlock("sms");
				$tpl->setVariable("SMS", $pl->txt("send_sms") . " <b>" . $this->live_voting->getPin() . " <span class='rotatePin'>X</span></b> "
					. $pl->txt("to_nr") . " <b>" . ilLiveVotingConfigGUI::_getValue("aspsms_number") . "</b>");
				$tpl->parseCurrentBlock();
			}
			if (ilLiveVotingConfigGUI::_getValue("sragsms") AND ilLiveVotingConfigGUI::_getValue("sragsms_number")
				AND $this->live_voting->getAnonym()
			) {
				$tpl->setCurrentBlock("sms");
				if (ilLiveVotingConfigGUI::_getValue("sragsms_costs")) {
					$costs = "<br>(sFr. 0." . ilLiveVotingConfigGUI::_getValue("sragsms_costs") . ")";
				}
				$tpl->setVariable("SMS", $pl->txt("send_sms") . " <b>" . ilLiveVotingConfigGUI::_getValue("sragsms_keyword") . " "
					. $this->live_voting->getPin() . " <span class='rotatePin'>X</span></b> " . $pl->txt("to_nr") . " <b>"
					. ilLiveVotingConfigGUI::_getValue("sragsms_number") . "</b>" . $costs);
				$tpl->parseCurrentBlock();
			}
			$tpl->setVariable("FS_CLOSE", $pl->txt('qr_close'));
			if (ilLiveVotingConfigGUI::_getValue("use_qr")) {
				$tpl->setCurrentBlock("qrcode");
				$link = ilObjLiveVoting::getShortLinkByPin($this->live_voting->getPin());
				$tpl->setVariable("QR", ilLiveVotingQR::getQRasBase64(array( 'str' => $link, 'size' => 3 )));
				$tpl->setVariable("QR_HIDDEN", ilLiveVotingQR::getQRasBase64(array( 'str' => $link, 'size' => 20 )));
				$tpl->parseCurrentBlock();
			}
		}
		// END
		$tpl->setVariable("HEADER_TITLE_QUESTION", $this->live_voting->getQuestion());
		$tpl->setVariable("TOTAL_VOTES", $this->live_voting->getAbsoluteVotes());
		$tpl->setVariable("TOTAL_VOTES_TEXT", $pl->txt("total_votes_text"));
		/* ToolBar */
		$br = new ilBrowser();
		$tb = new ilToolbarGUI();
		if ($this->live_voting_gui->hasPermission("write")) {
			$tb->addButton($pl->txt("reset_votes"), $this->local_ctrl->getLinkTargetByClass("ilObjLiveVotingGUI", "confirmReset"));
		}
		if ($this->live_voting_gui->hasPermission("write") AND $br->getAgent() != "IE" AND ilLiveVotingConfigGUI::_getValue("allow_fullscreen")) {
			$tb->addButton($pl->txt("body_fullscreen_text"), "#", "", "", "", "lvo_fullscreen_button");
		}
		if ($this->live_voting_gui->hasPermission("write")) {
			$tb->addButton($pl->txt("body_hide_text"), "#", "", "", "", "lvo_body_hider");
		}
		if ($this->live_voting_gui->hasPermission("write") AND ilLiveVotingConfigGUI::_getValue("allow_freeze")) {
			if ($this->live_voting->getFreezed() == 1) {
				$tb->addButton($pl->txt("unfreeze"), $this->local_ctrl->getLinkTargetByClass("ilObjLiveVotingGUI", "unfreeze"), "", "", "", "freeze_button");
			} else {
				$tb->addButton($pl->txt("freeze"), $this->local_ctrl->getLinkTargetByClass("ilObjLiveVotingGUI", "freeze"), "", "", "", "freeze_button");
			}
		}
		$tpl->setVariable("ACTIONS", $tb->getHTML());
		/* AsyncLink */
		$tpl->setVariable("asyncShowContent", $this->local_ctrl->getLinkTargetByClass("ilObjLiveVotingGUI", "asyncShowContent"));
		$tpl->setVariable("asyncIsActive", $this->local_ctrl->getLinkTargetByClass("ilObjLiveVotingGUI", "asyncIsActive"));

		// Show a message, if the LiveVoting isn't active
		if (! $this->live_voting->getOnline()) {
			$tpl->setVariable("ISACTIVE", $pl->txt("is_not_online"));
		} elseif (! $this->live_voting->isActive()) {
			$tpl->setVariable("ISACTIVE", $pl->txt("is_not_active"));
		}

		/* SETTING THE HEADER TITLES WITH CIPHERS */
		$numKeys = array_values($options);
		for ($i = 0; $i < count($options) / 2; $i ++) {
			$tpl->setCurrentBlock("title_choice");
			$tpl->setVariable("CIPHER1", chr(65 + $i * 2) . ": ");
			$tpl->setVariable("TITLE1", $numKeys[$i * 2]->getTitle());
			if ($i < (count($options) - 1) / 2) {
				$tpl->setVariable("CIPHER2", chr(65 + $i * 2 + 1) . ": ");
				$tpl->setVariable("TITLE2", $numKeys[$i * 2 + 1]->getTitle());
			}
			$tpl->parseCurrentBlock();
		}
		$tpl->parseCurrentBlock();
		// see templates/content.css for all colors
		$COUNT_OF_DIFFERENT_COLORS = 4; // colors are counted from 0 to $COUNT_OF_DIFFERENT_COLORS-1
		for ($i = 0; $i < count($options); $i ++) {
			$this->local_ctrl->setParameterByClass('ilObjLiveVotingGUI', 'option_id', $numKeys[$i]->getId());
			$this->local_ctrl->setParameterByClass('ilObjLiveVotingGUI', 'full', '1');
			$tpl->setCurrentBlock('choice');
			if ($this->live_voting_gui->hasPermission('write')) {
				$tpl->setCurrentBlock('choice_view');
				$tpl->setVariable('VOTE_COUNT', $numKeys[$i]->countVotes());
				$tpl->setVariable('VOTE_PERCENTAGE', round($this->live_voting->getRelativePercentageForOption($numKeys[$i]->getId()), 0));
				$tpl->setVariable('VOTE_PERCENTAGE_SHOW', round($this->live_voting->getPercentageForOption($numKeys[$i]->getId()), 2));
			} else {
				$tpl->setVariable('HIDE', 'hidden');
			}
			$tpl->setVariable('WIDTH_PERCENTAGE', round(((100) / count($options)) - 0.5, 0));
			$tpl->setVariable('CHOICE_TITLE', $numKeys[$i]->getTitle());
			$tpl->setVariable('CHOICE_CIPHER', chr(65 + $i));
			$tpl->setVariable('CHOICE_ID', $numKeys[$i]->getId());

			if ($this->live_voting->isActive()) {
				if (! $numKeys[$i]->isVoter($ilUser->getId(), session_id())) {
					$tpl->setVariable("VOTE_LINK", $this->local_ctrl->getLinkTargetByClass("ilObjLiveVotingGUI", "vote") . '#lvo_isactive');
					$tpl->setVariable("VOTE_TEXT", $pl->txt("vote"));
				} else {
					$tpl->setVariable("VOTE_LINK", $this->local_ctrl->getLinkTargetByClass("ilObjLiveVotingGUI", "unvote") . '#lvo_isactive');
					$tpl->setVariable("VOTE_TEXT", $pl->txt("unvote"));
					$tpl->setVariable("VOTED_CLASS", " lvo_voted");
					$tpl->setVariable("GLOW", " glow");
				}
			} elseif ($numKeys[$i]->isVoter($ilUser->getId(), session_id())) {
				$tpl->setVariable("GLOW", " glow");
				$tpl->setVariable("VOTE_LINK", "javascript:void(0)"); // when the voting is over, don't allow clicking on the bars
				$tpl->setVariable("INACTIVE", "inactive");
			} else {
				$tpl->setVariable("VOTE_LINK", "javascript:void(0)"); // when the voting is over, don't allow clicking on the bars
				$tpl->setVariable("INACTIVE", "inactive");
			}
			// set color
			if ($this->live_voting->getColorful()) {
				$tpl->setVariable("CHOICE_COLOR", strval($i % $COUNT_OF_DIFFERENT_COLORS));
			} else {
				$tpl->setVariable("CHOICE_COLOR", "0");
			} // use only one color

			$tpl->parseCurrentBlock();
		}

		// only enable polling if the webpage actually shows the result
		// the normal voter doesn't see the result
		if ($this->live_voting_gui->hasPermission('write')) {
			$tpl->setVariable("ENABLE_POLLING", "true");
		} else {
			$tpl->setVariable("ENABLE_POLLING", "false");
		}

		return $tpl->get();
	}
}

?>
