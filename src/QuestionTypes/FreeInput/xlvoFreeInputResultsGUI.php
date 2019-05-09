<?php

namespace LiveVoting\QuestionTypes\FreeInput;

use LiveVoting\Display\Bar\xlvoBarFreeInputsGUI;
use LiveVoting\Display\Bar\xlvoBarGroupingCollectionGUI;
use LiveVoting\Option\xlvoOption;
use LiveVoting\QuestionTypes\xlvoInputResultsGUI;
use LiveVoting\Vote\xlvoVote;
use xlvoFreeInputGUI;
use xlvoPlayerGUI;
use srag\CustomInputGUIs\LiveVoting\Waiter\Waiter;

/**
 * Class xlvoFreeInputResultsGUI
 *
 * @package LiveVoting\QuestionTypes\FreeInput
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoFreeInputResultsGUI extends xlvoInputResultsGUI {

	/**
	 * @var bool
	 */
	protected $edit_mode = false;

	/**
	 * @return string
	 * @throws \ilException
	 */
	public function getHTML() {
		if (!self::dic()->ctrl()->isAsynch()) {
			$this->initJSAndCSS();
			Waiter::init(Waiter::TYPE_WAITER);
		}

		$button_states = $this->manager->getPlayer()->getButtonStates();
		$this->edit_mode = ($button_states[xlvoFreeInputGUI::BUTTON_CATEGORIZE] == 'true');

		$tpl = self::plugin()->template('default/QuestionTypes/FreeInput/tpl.free_input_results.html');

		$categories = new xlvoFreeInputCategoriesGUI($this->manager->getVoting()->getId());
		$categories->setRemovable($this->edit_mode);

		$bars = new xlvoBarGroupingCollectionGUI();
		$bars->setRemoveable($this->edit_mode);
		$bars->setShowTotalVotes(true);

		/**
		 * @var xlvoOption $option
		 */
		$option = $this->manager->getVoting()->getFirstVotingOption();

		/**
		 * @var xlvoVote[] $votes
		 */
		$votes = $this->manager->getVotesOfOption($option->getId());
		foreach ($votes as $vote) {
			if ($cat_id = $vote->getFreeInputCategory()) {
				$categories->addBar(new xlvoBarFreeInputsGUI($this->manager->getVoting(), $vote), $cat_id);
			} else {
				$bars->addBar(new xlvoBarFreeInputsGUI($this->manager->getVoting(), $vote));
			}
		}

		$bars->setTotalVotes(count($votes));

		$tpl->setVariable('ANSWERS', $bars->getHTML());
		$tpl->setVariable('CATEGORIES', $categories->getHTML());
		if ($this->edit_mode) {
			$tpl->setVariable('BASE_URL', self::dic()->ctrl()->getLinkTargetByClass(xlvoPlayerGUI::class, xlvoPlayerGUI::CMD_API_CALL, "", true));
		}

		return $tpl->get();
	}


	/**
	 * @throws \srag\DIC\LiveVoting\Exception\DICException
	 */
	protected function initJSAndCSS() {
		self::dic()->mainTemplate()->addJavaScript(self::plugin()->directory() . '/node_modules/dragula/dist/dragula.js');
		self::dic()->mainTemplate()->addJavaScript(self::plugin()->directory() . '/js/QuestionTypes/FreeInput/xlvoFreeInputCategorize.js');
		self::dic()->mainTemplate()->addCss(self::plugin()->directory() . '/node_modules/dragula/dist/dragula.min.css');
		self::dic()->mainTemplate()->addCss(self::plugin()->directory() . '/templates/default/QuestionTypes/FreeInput/free_input.css');
	}


	/**
	 * @param xlvoVote[] $votes
	 *
	 * @return string
	 */
	public function getTextRepresentationForVotes(array $votes) {
		$string_votes = array();
		foreach ($votes as $vote) {
			$string_votes[] = str_replace([ "\r\n", "\r", "\n" ], " ", $vote->getFreeInput());
		}

		return implode(", ", $string_votes);
	}

}
