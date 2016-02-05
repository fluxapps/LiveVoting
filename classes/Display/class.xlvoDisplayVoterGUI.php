<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarCollectionGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarPercentageGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarOptionGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoInputMobileGUI.php');

/**
 * Class xlvoDisplayVoterGUI
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 *
 * renders the voting mask for the voter
 */
class xlvoDisplayVoterGUI {

	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var xlvoVoting
	 */
	protected $voting;
	/**
	 * @var string
	 */
	protected $error_msg;
	/**
	 * @var int
	 */
	protected $answer_count = 64;


	/**
	 * @param xlvoVoting $voting
	 * @param string $error_msg
	 */
	public function __construct(xlvoVoting $voting, $error_msg = '') {
		$this->voting = $voting;
		$this->error_msg = $error_msg;
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Voting/display/tpl.display_voter.html', true, true);
	}


	protected function render() {
		$xlvoInputMobileGUI = xlvoInputMobileGUI::getInstance($this->voting);
		$this->tpl->setVariable('OPTION_CONTENT', $xlvoInputMobileGUI->getHTML());

		if ($xlvoInputMobileGUI->isShowOption()) {
			foreach ($this->voting->getVotingOptions() as $item) {
				$this->addOption($item);
			}
		}

		$votings = xlvoVoting::where(array(
			'obj_id' => $this->voting->getObjId(),
			'voting_status' => xlvoVoting::STAT_ACTIVE
		))->orderBy('position', 'ASC');

		$votings_count = $votings->count();

		$voting_position = 1;
		foreach ($votings->getArray() as $key => $voting) {
			if ($this->voting->getId() == $key) {
				break;
			}
			$voting_position ++;
		}

		$this->tpl->setVariable('ERROR', $this->error_msg);
		$this->tpl->setVariable('TITLE', $this->voting->getTitle());
		$this->tpl->setVariable('QUESTION', $this->voting->getQuestion());
		$this->tpl->setVariable('VOTING_ID', $this->voting->getId());
		$this->tpl->setVariable('OBJ_ID', $this->voting->getObjId());
		$this->tpl->setVariable('COUNT', $votings_count);
		$this->tpl->setVariable('POSITION', $voting_position);
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		$this->render();

		return $this->tpl->get();
	}


	/**
	 * @param xlvoOption $option
	 */
	protected function addOption(xlvoOption $option) {
		if ($option->getType() == xlvoQuestionTypes::TYPE_FREE_INPUT) {
			return;
		}
		$this->answer_count ++;
		$this->tpl->setCurrentBlock('option');
		$this->tpl->setVariable('OPTION_LETTER', (chr($this->answer_count)));
		$this->tpl->setVariable('OPTION_TEXT', $option->getText());
		$this->tpl->parseCurrentBlock();
	}
}