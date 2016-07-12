<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoQuestionTypesGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarMovableGUI.php');

/**
 * Class xlvoCorrectOrderGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy xlvoCorrectOrderGUI: xlvoVoter2GUI
 */
class xlvoCorrectOrderGUI extends xlvoQuestionTypesGUI {

	const BUTTON_TOTTLE_DISPLAY_CORRECT_ORDER = 'display_correct_order';
	const BUTTON_TOGGLE_PERCENTAGE = 'toggle_percentage';


	/**
	 * @return string
	 */
	public function getMobileHTML() {
		return $this->getFormContent();
	}


	public function initJS() {
		xlvoJs::getInstance()->api($this)->name('CorrectOrder')->category('QuestionTypes')->addLibToHeader('jquery.ui.touch-punch.min.js')->init();
	}


	protected function submit() {
		$this->manager->input(json_encode($_POST['id']), $_POST['vote_id']);
	}


	protected function clear() {
		$this->manager->unvoteAll();
		$this->afterSubmit();
	}


	/**
	 * @return string
	 */
	protected function getFormContent() {
		$pl = ilLiveVotingPlugin::getInstance();

		$tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/QuestionTypes/FreeOrder/tpl.free_order.html', true, false);
		$tpl->setVariable('ACTION', $this->ctrl->getFormAction($this));
		$tpl->setVariable('ID', 'xlvo_sortable');
		$tpl->setVariable('BTN_RESET', $pl->txt('qtype_4_clear'));
		$tpl->setVariable('BTN_SAVE', $pl->txt('qtype_4_save'));

		$vote = array_shift(array_values($this->manager->getVotesOfUser()));
		$order = array();
		$vote_id = null;
		if ($vote instanceof xlvoVote) {
			$order = json_decode($vote->getFreeInput());
			$vote_id = $vote->getId();
		}
		if (!$vote_id) {
			$tpl->setVariable('BTN_RESET_DISABLED', 'disabled="disabled"');
		}

		$bars = new xlvoBarMovableGUI($this->manager->getVoting()->getVotingOptions(), $order, $vote_id);
		$bars->setShowOptionLetter(true);
		$tpl->setVariable('CONTENT', $bars->getHTML());

		if ($this->isShowCorrectOrder()) {
			$correct_order = array();
			foreach ($this->manager->getVoting()->getVotingOptions() as $xlvoOption) {
				$correct_order[(int)$xlvoOption->getCorrectPosition()] = $xlvoOption;
			};
			ksort($correct_order);
			$solution_html = $this->txt('correct_solution');
			/**
			 * @var $item xlvoOption
			 */
			foreach ($correct_order as $item) {
				$solution_html .= ' <span class="label label-primary">' . $item->getCipher() . '</span>';
			}

			$tpl->setVariable('YOUR_SOLUTION', $solution_html);
		}

		return $tpl->get();
	}


	/**
	 * @return array
	 */
	public function getButtonInstances() {
		if (!$this->manager->getPlayer()->isShowResults()) {
			return array();
		}
		$states = $this->getButtonsStates();
		$b = ilLinkButton::getInstance();
		$b->setId(self::BUTTON_TOTTLE_DISPLAY_CORRECT_ORDER);
		if ($states[self::BUTTON_TOTTLE_DISPLAY_CORRECT_ORDER]) {
			$b->setCaption(xlvoGlyphGUI::get('eye-close'), false);
		} else {
			$b->setCaption(xlvoGlyphGUI::get('eye-open'), false);
		}

		$t = ilLinkButton::getInstance();
		$t->setId(self::BUTTON_TOGGLE_PERCENTAGE);
		if ($states[self::BUTTON_TOGGLE_PERCENTAGE]) {
			$t->setCaption('%', false);
		} else {
			$t->setCaption(xlvoGlyphGUI::get('user'), false);
		}

		return array( $b, $t );
	}


	/**
	 * @return mixed
	 */
	protected function isShowCorrectOrder() {
		$states = $this->getButtonsStates();

		return ((bool)$states[xlvoCorrectOrderGUI::BUTTON_TOTTLE_DISPLAY_CORRECT_ORDER] && $this->manager->getPlayer()->isShowResults());
	}


	/**
	 * @param $button_id
	 * @param $data
	 */
	public function handleButtonCall($button_id, $data) {
		$states = $this->getButtonsStates();
		$this->saveButtonState($button_id, !$states[$button_id]);
	}
}
