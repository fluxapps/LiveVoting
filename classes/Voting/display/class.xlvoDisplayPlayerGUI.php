<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/display/class.xlvoBarGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/display/class.xlvoBarCollectionGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/display/class.xlvoBarPercentageGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/display/class.xlvoBarFreeInputGUI.php');

/**
 * Class xlvoDisplayPlayerGUI
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
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

		switch ($this->voting->getVotingType()) {
			case xlvoVotingType::SINGLE_VOTE:
				$this->tpl->setVariable('OPTION_CONTENT', $this->renderSingleVote());
				break;
			case xlvoVotingType::FREE_INPUT:
				$this->tpl->setVariable('OPTION_CONTENT', $this->renderFreeInput());
				break;
		}

		$votings = xlvoVoting::where(array( 'obj_id' => $this->voting->getObjId(), 'voting_status' => xlvoVoting::STAT_ACTIVE ))
			->orderBy('position', 'ASC');

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
	protected function addAnswer(xlvoOption $option) {
		$this->tpl->setCurrentBlock('option');
		$this->tpl->setVariable('OPTION_LETTER', (chr($this->answer_count)));
		$this->tpl->setVariable('OPTION_TEXT', $option->getText());
		$this->tpl->parseCurrentBlock();
	}


	/**
	 * @return string
	 */
	protected function renderSingleVote() {
		/**
		 * @var xlvoBarCollectionGUI
		 */
		$bars = new xlvoBarCollectionGUI();

		/**
		 * @var xlvoOption $options
		 */
		$options = $this->voting->getVotingOptions()->get();
		foreach ($options as $option) {
			$this->answer_count ++;
			/**
			 * @var xlvoVote $votes
			 */
			$votes = $this->voting_manager->getVotesOfVoting($this->voting->getId());

			$bars->addBar(new xlvoBarPercentageGUI($this->voting, $option, $votes, (chr($this->answer_count))));
			$this->addAnswer($option);
		}

		return $bars->getHTML();
	}


	/**
	 * @return string
	 */
	protected function renderFreeInput() {
		$bars = new xlvoBarCollectionGUI();
		/**
		 * @var xlvoOption $option
		 */
		$option = $this->voting->getVotingOptions()->first();
		/**
		 * @var xlvoVote[] $votes
		 */
		$votes = $this->voting_manager->getVotesOfOption($option->getId())->get();
		foreach ($votes as $vote) {
			$bars->addBar(new xlvoBarFreeInputGUI($this->voting, $vote));
		}

		return $bars->getHTML();
	}
}