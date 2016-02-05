<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarCollectionGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarPercentageGUI.php');
require_once('Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoInputResultsGUI.php');

/**
 * Class xlvoDisplayPlayerGUI
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 *
 * The display is used in the view of a admin when presenting the livevoting, only the content of a voting
 */
class xlvoDisplayPlayerGUI {

	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var xlvoVoting
	 */
	protected $voting;
	/**
	 * @var int
	 */
	protected $answer_count = 64;
	/**
	 * @var xlvoVotingManager
	 */
	protected $voting_manager;


	/**
	 * @param xlvoVoting $voting
	 */
	public function __construct(xlvoVoting $voting) {
		global $tpl;
		/**
		 * @var $tpl       ilTemplate
		 */
		$tpl->addJavaScript('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Voting/display/display_player.js');
		$this->voting_manager = new xlvoVotingManager();
		$this->voting = $voting;
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Voting/display/tpl.display_player.html', true, true);
	}


	protected function render() {
		/**
		 * @var xlvoVotingConfig $config
		 */
		$config = $this->voting_manager->getVotingConfig($this->voting->getObjId());
		/**
		 * @var xlvoPlayer $player
		 */
		$player = $this->voting_manager->getPlayer($this->voting->getObjId());

		$xlvoInputDisplayGUI = xlvoInputResultsGUI::getInstance($this->voting, $this->voting_manager);
		$this->tpl->setVariable('OPTION_CONTENT', $xlvoInputDisplayGUI->getHTML());
		$xlvoOptions = $this->voting->getVotingOptions();
		if ($xlvoInputDisplayGUI->isShuffleResults()) {
			shuffle($xlvoOptions);
		}
		foreach ($xlvoOptions as $item) {
			$this->addOption($item);
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

		$this->tpl->setVariable('TITLE', $this->voting->getTitle());
		$this->tpl->setVariable('QUESTION', $this->voting->getQuestion());
		$this->tpl->setVariable('VOTING_ID', $this->voting->getId());
		$this->tpl->setVariable('OBJ_ID', $this->voting->getObjId());
		$this->tpl->setVariable('FROZEN', $player->isFrozen());
		$this->tpl->setVariable('PIN', $config->getPin());
		//		$this->tpl->setVariable('ONLINE', $config->getPin());
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