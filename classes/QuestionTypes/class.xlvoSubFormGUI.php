<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoTextAreaInputGUI.php');

/**
 * Class xlvoFreeInputSubFormGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
abstract class xlvoSubFormGUI {

	/**
	 * @var xlvoSubFormGUI
	 */
	protected static $instance;


	/**
	 * @param xlvoVoting $xlvoVoting
	 * @return xlvoSubFormGUI
	 */
	public static function getInstance(xlvoVoting $xlvoVoting) {
		if (!self::$instance instanceof self) {

			$class = xlvoQuestionTypes::getClassName($xlvoVoting->getVotingType());
			/**
			 * @var $class_name xlvoFreeInputSubFormGUI
			 * @var $subform    xlvoFreeInputSubFormGUI
			 */
			$class_name = 'xlvo' . $class . 'SubFormGUI';
			require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/' . $class . '/class.'
			             . $class_name . '.php');

			self::$instance = new $class_name($xlvoVoting);
		}

		return self::$instance;
	}


	/**
	 * @var xlvoVoting
	 */
	protected $xlvoVoting;
	/**
	 * @var ilTextInputGUI[]
	 */
	protected $form_elements = array();


	/**
	 * xlvoFreeInputSubFormGUI constructor.
	 */
	public function __construct(xlvoVoting $xlvoVoting) {
		$this->xlvoVoting = $xlvoVoting;
		$this->pl = ilLiveVotingPlugin::getInstance();
		$this->initFormElements();
	}


	abstract protected function initFormElements();


	/**$
	 * @param $key
	 * @return string
	 */
	protected function txt($key) {
		return $this->pl->txt('qtype_' . $this->getXlvoVoting()->getVotingType() . '_' . $key);
	}


	/**
	 * @return xlvoVoting
	 */
	public function getXlvoVoting() {
		return $this->xlvoVoting;
	}


	/**
	 * @param xlvoVoting $xlvoVoting
	 */
	public function setXlvoVoting($xlvoVoting) {
		$this->xlvoVoting = $xlvoVoting;
	}


	/**
	 * @return ilTextInputGUI[]
	 */
	public function getFormElements() {
		return $this->form_elements;
	}


	/**
	 * @param ilTextInputGUI[] $form_elements
	 */
	public function setFormElements($form_elements) {
		$this->form_elements = $form_elements;
	}


	/**
	 * @param ilFormPropertyGUI $element
	 */
	public function addFormElement(ilFormPropertyGUI $element) {
		$this->form_elements[] = $element;
	}


	/**
	 * @param ilPropertyFormGUI $ilPropertyFormGUI
	 */
	public function appedElementsToForm(ilPropertyFormGUI $ilPropertyFormGUI) {
		if (count($this->getFormElements()) > 0) {
			$h = new ilFormSectionHeaderGUI();
			$h->setTitle($this->pl->txt('qtype_form_header'));
			$ilPropertyFormGUI->addItem($h);
		}
		foreach ($this->getFormElements() as $formElement) {
			$ilPropertyFormGUI->addItem($formElement);
		}
	}


	/**
	 * @param ilPropertyFormGUI $ilPropertyFormGUI
	 */
	public function handleAfterSubmit(ilPropertyFormGUI $ilPropertyFormGUI) {
		foreach ($this->getFormElements() as $formElement) {
			$value = $ilPropertyFormGUI->getInput($formElement->getPostVar());
			$this->handleField($formElement, $value);
		}
	}


	/**
	 * @param xlvoVoting $xlvoVoting
	 */
	public function handleAfterCreation(xlvoVoting $xlvoVoting) {
		$this->setXlvoVoting($xlvoVoting);
		$this->handleOptions();
	}


	/**
	 * @param array $existing
	 * @return array
	 */
	public function appendValues(array $existing) {
		foreach ($this->getFormElements() as $formElement) {
			$existing[$formElement->getPostVar()] = $this->getFieldValue($formElement);
		}

		return $existing;
	}


	protected function handleOptions() {
		$xlvoOption = xlvoOption::where(array( 'voting_id' => $this->getXlvoVoting()->getId() ))->first();
		if (!$xlvoOption instanceof xlvoOption) {
			$xlvoOption = new xlvoOption();
			$xlvoOption->create();
		}
		$xlvoOption->setStatus(xlvoOption::STAT_ACTIVE);
		$xlvoOption->setVotingId($this->getXlvoVoting()->getId());
		$xlvoOption->setType($this->getXlvoVoting()->getVotingType());
		$xlvoOption->update();
	}


	/**
	 * @param ilFormPropertyGUI $element
	 * @param $value
	 * @return mixed
	 */
	abstract protected function handleField(ilFormPropertyGUI $element, $value);


	/**
	 * @param ilFormPropertyGUI $element
	 * @return mixed
	 */
	abstract protected function getFieldValue(ilFormPropertyGUI $element);
}