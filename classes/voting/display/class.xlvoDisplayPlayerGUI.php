<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoBarGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoBarCollectionGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoBarPercentageGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoBarFreeInputGUI.php');

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
		$this->voting_manager = new xlvoVotingManager();
		$this->voting = $voting;
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/display/tpl.display_player.html', true, true);
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

		$this->tpl->setVariable('TITLE', $this->voting->getTitle());
		$this->tpl->setVariable('QUESTION', $this->voting->getQuestion());
		$this->tpl->setVariable('VOTING_ID', $this->voting->getId());
		$this->tpl->setVariable('OBJ_ID', $this->voting->getObjId());
		$this->tpl->setVariable('FROZEN', $player->isFrozen());
		$this->tpl->setVariable('PIN', $config->getPin());
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


	protected function renderSingleVote() {
		$bars = new xlvoBarCollectionGUI();
		foreach ($this->voting->getVotingOptions()->get() as $option) {
			$this->answer_count ++;
			$votes = $this->voting_manager->getVotes($this->voting->getId(), NULL, false);
			$bars->addBar(new xlvoBarPercentageGUI($this->voting, $option, $votes, (chr($this->answer_count))));
			$this->addAnswer($option);
		}

		return $bars->getHTML();
	}


	protected function renderFreeInput() {
		$bars = new xlvoBarCollectionGUI();
		$option = $this->voting->getVotingOptions()->first();
		$votes = $this->voting_manager->getVotes($this->voting->getId(), $option->getId(), false);
		foreach ($votes->get() as $vote) {
			$bars->addBar(new xlvoBarFreeInputGUI($this->voting, $vote));
		}

		return $bars->getHTML();
	}
}