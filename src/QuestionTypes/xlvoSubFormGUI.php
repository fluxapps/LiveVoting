<?php

namespace LiveVoting\QuestionTypes;

use ilException;
use ilFormPropertyGUI;
use ilFormSectionHeaderGUI;
use ilLiveVotingPlugin;
use ilPropertyFormGUI;
use ilTextInputGUI;
use LiveVoting\Exceptions\xlvoSubFormGUIHandleFieldException;
use LiveVoting\Option\xlvoOption;
use LiveVoting\QuestionTypes\CorrectOrder\xlvoCorrectOrderSubFormGUI;
use LiveVoting\QuestionTypes\FreeInput\xlvoFreeInputSubFormGUI;
use LiveVoting\QuestionTypes\FreeOrder\xlvoFreeOrderSubFormGUI;
use LiveVoting\QuestionTypes\NumberRange\xlvoNumberRangeSubFormGUI;
use LiveVoting\QuestionTypes\SingleVote\xlvoSingleVoteSubFormGUI;
use LiveVoting\Utils\LiveVotingTrait;
use LiveVoting\Voting\xlvoVoting;
use srag\DIC\LiveVoting\DICTrait;
use ilTemplate;
use ilGlobalPageTemplate;

/**
 * Class xlvoFreeInputSubFormGUI
 *
 * @package LiveVoting\QuestionTypes
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
abstract class xlvoSubFormGUI
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    /**
     * @var xlvoSubFormGUI
     */
    protected static $instance;


    /**
     * @param xlvoVoting $xlvoVoting
     *
     * @return xlvoSubFormGUI
     * @throws ilException                 Throws an ilException if no sub form gui class was found.
     */
    public static function getInstance(xlvoVoting $xlvoVoting)
    {
        if (!self::$instance instanceof self) {

            $class = xlvoQuestionTypes::getClassName($xlvoVoting->getVotingType());

            $gui = null;
            switch ($class) {
                case xlvoQuestionTypes::CORRECT_ORDER:
                    $gui = new xlvoCorrectOrderSubFormGUI($xlvoVoting);
                    break;
                case xlvoQuestionTypes::FREE_INPUT:
                    $gui = new xlvoFreeInputSubFormGUI($xlvoVoting);
                    break;
                case xlvoQuestionTypes::FREE_ORDER:
                    $gui = new xlvoFreeOrderSubFormGUI($xlvoVoting);
                    break;
                case xlvoQuestionTypes::SINGLE_VOTE:
                    $gui = new xlvoSingleVoteSubFormGUI($xlvoVoting);
                    break;
                case xlvoQuestionTypes::NUMBER_RANGE:
                    $gui = new xlvoNumberRangeSubFormGUI($xlvoVoting);
                    break;
                default:
                    throw new ilException("Could not find the sub form gui for the given voting.");
            }

            self::$instance = $gui;
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
    public function __construct(xlvoVoting $xlvoVoting)
    {
        $this->xlvoVoting = $xlvoVoting;
        $this->initFormElements();
    }


    /**
     *
     */
    protected abstract function initFormElements();


    /**$
     * @param string $key
     *
     * @return string
     */
    protected function txt($key)
    {
        return self::plugin()->translate($this->getXlvoVoting()->getVotingType() . '_' . $key, 'qtype');
    }


    /**
     * @return xlvoVoting
     */
    public function getXlvoVoting()
    {
        return $this->xlvoVoting;
    }


    /**
     * @param xlvoVoting $xlvoVoting
     */
    public function setXlvoVoting($xlvoVoting)
    {
        $this->xlvoVoting = $xlvoVoting;
    }


    /**
     * @return ilTextInputGUI[]
     */
    public function getFormElements()
    {
        return $this->form_elements;
    }


    /**
     * @param ilTextInputGUI[] $form_elements
     */
    public function setFormElements($form_elements)
    {
        $this->form_elements = $form_elements;
    }


    /**
     * @param ilFormPropertyGUI $element
     */
    protected function addFormElement(ilFormPropertyGUI $element)
    {
        $this->form_elements[] = $element;
    }


    /**
     * @param ilPropertyFormGUI $ilPropertyFormGUI
     */
    public function appedElementsToForm(ilPropertyFormGUI $ilPropertyFormGUI)
    {
        if (count($this->getFormElements()) > 0) {
            $h = new ilFormSectionHeaderGUI();
            $h->setTitle(self::plugin()->translate('qtype_form_header'));
            $ilPropertyFormGUI->addItem($h);
        }
        foreach ($this->getFormElements() as $formElement) {
            $ilPropertyFormGUI->addItem($formElement);
        }
    }


    /**
     * @param ilPropertyFormGUI $ilPropertyFormGUI
     *
     * @throws xlvoSubFormGUIHandleFieldException|ilException
     */
    public function handleAfterSubmit(ilPropertyFormGUI $ilPropertyFormGUI)
    {
        foreach ($this->getFormElements() as $formElement) {
            $value = $ilPropertyFormGUI->getInput($formElement->getPostVar());
            $this->handleField($formElement, $value);
        }

        $this->validateForm();
    }


    /**
     * @param xlvoVoting $xlvoVoting
     */
    public function handleAfterCreation(xlvoVoting $xlvoVoting)
    {
        $this->setXlvoVoting($xlvoVoting);
        $this->handleOptions();
    }


    /**
     * @param array $existing
     *
     * @return array
     * @throws ilException
     */
    public function appendValues(array $existing)
    {
        foreach ($this->getFormElements() as $formElement) {
            $existing[$formElement->getPostVar()] = $this->getFieldValue($formElement);
        }

        return $existing;
    }

    public function addJsAndCss(ilGlobalPageTemplate $ilTemplate)
    {
    }

    /**
     *
     */
    protected function handleOptions()
    {
        $xlvoOption = xlvoOption::where(array('voting_id' => $this->getXlvoVoting()->getId()))->first();
        if (!$xlvoOption instanceof xlvoOption) {
            $xlvoOption = new xlvoOption();
        }
        $xlvoOption->setStatus(xlvoOption::STAT_ACTIVE);
        $xlvoOption->setVotingId($this->getXlvoVoting()->getId());
        $xlvoOption->setType($this->getXlvoVoting()->getVotingType());
        $xlvoOption->store();
    }


    /**
     * @param ilFormPropertyGUI $element
     * @param string|array      $value
     *
     * @throws xlvoSubFormGUIHandleFieldException|ilException
     */
    protected abstract function handleField(ilFormPropertyGUI $element, $value);


    /**
     * @param ilFormPropertyGUI $element
     *
     * @return string|int|float|array
     * @throws ilException
     */
    protected abstract function getFieldValue(ilFormPropertyGUI $element);


    /**
     * @return void
     * @throws ilException
     */
    protected function validateForm()
    {
        //virtual
    }
}
