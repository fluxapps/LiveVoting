<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use LiveVoting\Display\Bar\xlvoBarMovableGUI;
use LiveVoting\Js\xlvoJs;
use LiveVoting\Option\xlvoOption;
use LiveVoting\QuestionTypes\xlvoQuestionTypes;
use LiveVoting\QuestionTypes\xlvoQuestionTypesGUI;
use LiveVoting\Vote\xlvoVote;
use LiveVoting\UIComponent\GlyphGUI;

/**
 * Class xlvoCorrectOrderGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy xlvoCorrectOrderGUI: xlvoVoter2GUI
 */
class xlvoCorrectOrderGUI extends xlvoQuestionTypesGUI
{

    const BUTTON_TOTTLE_DISPLAY_CORRECT_ORDER = 'display_correct_order';
    const BUTTON_TOGGLE_PERCENTAGE = 'toggle_percentage';


    /**
     * @return string
     */
    public function getMobileHTML()
    {
        return $this->getFormContent() . xlvoJs::getInstance()->name(xlvoQuestionTypes::CORRECT_ORDER)->category('QuestionTypes')->getRunCode();
    }


    /**
     * @param bool $current
     */
    public function initJS($current = false)
    {
        xlvoJs::getInstance()->api($this)->name(xlvoQuestionTypes::CORRECT_ORDER)->category('QuestionTypes')
            ->addLibToHeader('jquery.ui.touch-punch.min.js')->init();
    }


    /**
     *
     */
    protected function submit()
    {
        $this->manager->inputOne(array(
            "input"   => json_encode($_POST['id']),
            "vote_id" => $_POST['vote_id']
        ));
    }


    /**
     *
     */
    protected function clear()
    {
        $this->manager->unvoteAll();
        $this->afterSubmit();
    }


    /**
     * @return string
     */
    protected function getFormContent()
    {
        $tpl = self::plugin()->template('default/QuestionTypes/FreeOrder/tpl.free_order.html', true, false);
        $tpl->setVariable('ACTION', self::dic()->ctrl()->getFormAction($this));
        $tpl->setVariable('ID', 'xlvo_sortable');
        $tpl->setVariable('BTN_RESET', self::plugin()->translate('qtype_4_clear'));
        $tpl->setVariable('BTN_SAVE', self::plugin()->translate('qtype_4_save'));

        $votes = array_values($this->manager->getVotesOfUser());
        $vote = array_shift($votes);
        $order = array();
        $vote_id = null;
        if ($vote instanceof xlvoVote) {
            $order = json_decode($vote->getFreeInput());
            $vote_id = $vote->getId();
        }
        if (!$vote_id) {
            $tpl->setVariable('BTN_RESET_DISABLED', 'disabled="disabled"');
        }

        $options = $this->manager->getVoting()->getVotingOptions();
        if ($this->isRandomizeOptions()) {
            //randomize the options for the voters
            $options = $this->randomizeWithoutCorrectSequence($options);
        }
        $bars = new xlvoBarMovableGUI($options, $order, $vote_id);
        $bars->setShowOptionLetter(true);
        $tpl->setVariable('CONTENT', $bars->getHTML());

        if ($this->isShowCorrectOrder()) {
            $correct_order = $this->getCorrectOrder();
            $solution_html = '<p>' . $this->txt('correct_solution');

            foreach ($correct_order as $item) {
                $solution_html .= ' <span class="label label-primary">' . $item->getCipher() . '</span>';
            }
            $solution_html .= '</p>';
            $tpl->setVariable('YOUR_SOLUTION', $solution_html);
        }

        return $tpl->get();
    }


    /**
     * @return array
     */
    public function getButtonInstances()
    {
        if (!$this->manager->getPlayer()->isShowResults()) {
            return array();
        }
        $states = $this->getButtonsStates();
        $b = ilLinkButton::getInstance();
        $b->setId(self::BUTTON_TOTTLE_DISPLAY_CORRECT_ORDER);
        if ($states[self::BUTTON_TOTTLE_DISPLAY_CORRECT_ORDER]) {
            $b->setCaption(GlyphGUI::get('eye-close'), false);
        } else {
            $b->setCaption(GlyphGUI::get('eye-open'), false);
        }

        $t = ilLinkButton::getInstance();
        $t->setId(self::BUTTON_TOGGLE_PERCENTAGE);
        if ($states[self::BUTTON_TOGGLE_PERCENTAGE]) {
            $t->setCaption(' %', false);
        } else {
            $t->setCaption(GlyphGUI::get('user'), false);
        }

        return array($b, $t);
    }


    /**
     * @return mixed
     */
    protected function isShowCorrectOrder()
    {
        $states = $this->getButtonsStates();

        return ((bool) $states[xlvoCorrectOrderGUI::BUTTON_TOTTLE_DISPLAY_CORRECT_ORDER] && $this->manager->getPlayer()->isShowResults());
    }


    /**
     * @param $button_id
     * @param $data
     */
    public function handleButtonCall($button_id, $data)
    {
        $states = $this->getButtonsStates();
        $this->saveButtonState($button_id, !$states[$button_id]);
    }


    /**
     * Checks whether the options displayed to the voter is randomized.
     *
     * @return bool
     */
    protected function isRandomizeOptions()
    {
        return false;
    }


    /**
     * @return xlvoOption[]
     */
    protected function getCorrectOrder()
    {
        $correct_order = array();
        foreach ($this->manager->getVoting()->getVotingOptions() as $xlvoOption) {
            $correct_order[(int) $xlvoOption->getCorrectPosition()] = $xlvoOption;
        };
        ksort($correct_order);

        return $correct_order;
    }


    /**
     * Randomizes an array of xlvoOption.
     * This function never returns the correct sequence of options.
     *
     * @param xlvoOption[] $options The options which should get randomized.
     *
     * @return xlvoOption[] The randomized option array.
     */
    private function randomizeWithoutCorrectSequence(array &$options)
    {
        if (count($options) < 2) {
            return $options;
        }

        //shuffle array items (can't use the PHP shuffle function because the keys are not preserved.)
        $optionsClone = $this->shuffleArray($options);

        $lastCorrectPosition = 0;

        /**
         * @var xlvoOption $option
         */
        foreach ($optionsClone as $option) {
            //get correct item position
            $currentCurrentPosition = $option->getCorrectPosition();

            //calculate the difference
            $difference = $lastCorrectPosition - $currentCurrentPosition;
            $lastCorrectPosition = $currentCurrentPosition;

            //check if we shuffled the correct answer by accident.
            //the correct answer would always produce a difference of -1.
            //1 - 2 = -1, 2 - 3 = -1, 3 - 4 = -1 ...
            if ($difference !== -1) {
                return $optionsClone;
            }
        }

        //try to shuffle again because we got the right answer by accident.
        //we pass the original array, this should enable php to drop the array clone out of the memory.
        return $this->randomizeWithoutCorrectSequence($options);
    }


    /**
     * Shuffles the array given array the keys are preserved.
     * Please note that the array passed into this method get never modified.
     *
     * @param array $array The array which should be shuffled.
     *
     * @return array The newly shuffled array.
     */
    private function shuffleArray(array &$array)
    {
        $clone = $this->cloneArray($array);
        $shuffledArray = [];

        while (count($clone) > 0) {
            $key = array_rand($clone);
            $shuffledArray[$key] = &$clone[$key];
            unset($clone[$key]);
        }

        return $shuffledArray;
    }


    /**
     * Create a shallow copy of the given array.
     *
     * @param array $array The array which should be copied.
     *
     * @return array    The newly created shallow copy of the given array.
     */
    private function cloneArray(array &$array)
    {
        $clone = [];
        foreach ($array as $key => $value) {
            $clone[$key] = &$array[$key]; //get the ref on the array value not the foreach value.
        }

        return $clone;
    }
}
