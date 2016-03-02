<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarGUI.php');

/**
 * Class xlvoBarPercentageGUI
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoBarPercentageGUI implements xlvoBarGUI {

	/**
	 * @var int
	 */
	protected $votes = 0;
	/**
	 * @var int
	 */
	protected $total = 0;
	/**
	 * @var string
	 */
	protected $option_letter;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var string
	 */
	protected $title = '';
	/**
	 * @var string
	 */
	protected $id = '';


	/**
	 * xlvoBarPercentageGUI constructor.
	 */
	public function __construct() {
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Display/Bar/tpl.bar_percentage.html', true, true);
	}


	/**
	 * @param xlvoOption $xlvoOption
	 * @param $votes
	 * @param $total
	 * @param $option_letter
	 * @return xlvoBarPercentageGUI
	 */
	public static function getInstanceFromOption(xlvoOption $xlvoOption, $votes, $total, $option_letter = null) {
		$obj = new self();
		$obj->setTitle($xlvoOption->getText());
		$obj->setId($xlvoOption->getId());
		$obj->setVotes($votes);
		$obj->setTotal($total);
		$obj->setOptionLetter($option_letter);

		return $obj;
	}


	protected function render() {
		$this->tpl->setVariable('PERCENT', $this->getPercentage());
		$this->tpl->setVariable('ID', $this->getId());
		$this->tpl->setVariable('TITLE', $this->getTitle());
		if ($this->getOptionLetter()) {
			$this->tpl->setCurrentBlock('option_letter');
			$this->tpl->setVariable('OPTION_LETTER', $this->getOptionLetter());
			$this->tpl->parseCurrentBlock();
		}
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		$this->render();

		return $this->tpl->get();
	}


	/**
	 * @return float|int
	 */
	protected function getPercentage() {
		$total_votes = $this->getTotal();
		if ($this->getTotal() === 0) {
			return 0;
		}
		$option_votes = $this->getVotes();
		$percentage = ($option_votes / $total_votes) * 100;

		return round($percentage, 1);
	}


	/**
	 * @return int
	 */
	public function getVotes() {
		return $this->votes;
	}


	/**
	 * @param int $votes
	 */
	public function setVotes($votes) {
		$this->votes = $votes;
	}


	/**
	 * @return int
	 */
	public function getTotal() {
		return $this->total;
	}


	/**
	 * @param int $total
	 */
	public function setTotal($total) {
		$this->total = $total;
	}


	/**
	 * @return string
	 */
	public function getOptionLetter() {
		return $this->option_letter;
	}


	/**
	 * @param string $option_letter
	 */
	public function setOptionLetter($option_letter) {
		$this->option_letter = $option_letter;
	}


	/**
	 * @return ilTemplate
	 */
	public function getTpl() {
		return $this->tpl;
	}


	/**
	 * @param ilTemplate $tpl
	 */
	public function setTpl($tpl) {
		$this->tpl = $tpl;
	}


	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}


	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}


	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param string $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
}