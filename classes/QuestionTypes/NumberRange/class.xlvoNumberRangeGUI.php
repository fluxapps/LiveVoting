<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use LiveVoting\Js\xlvoJs;
use LiveVoting\QuestionTypes\xlvoQuestionTypes;
use LiveVoting\QuestionTypes\xlvoQuestionTypesGUI;
use LiveVoting\Vote\xlvoVote;
use LiveVoting\Voting\xlvoVotingManager2;

/**
 * Class xlvoNumberRange
 *
 * @author            Nicolas Schäfli <ns@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy xlvoNumberRangeGUI: xlvoVoter2GUI
 */
class xlvoNumberRangeGUI extends xlvoQuestionTypesGUI
{

    const USER_SELECTED_NUMBER = 'user_selected_number';
    const SAVE_BUTTON_VOTE = 'voter_start_button_vote';
    const CLEAR_BUTTON = 'voter_clear';
    const SAVE_BUTTON_UNVOTE = 'voter_start_button_unvote';


    /**
     * @param xlvoVotingManager2 $manager
     *
     * @throws ilException
     */
    public function setManager($manager)
    {

        if ($manager === null) {
            throw new ilException('The manager must not be null.');
        }

        parent::setManager($manager);
    }


    /**
     * @param bool $current
     */
    public function initJS($current = false)
    {
        xlvoJs::getInstance()->api($this)->name(xlvoQuestionTypes::NUMBER_RANGE)->category('QuestionTypes')->addLibToHeader('bootstrap-slider.min.js')
            ->addSettings([
                "step" => $this->getStep()
            ])->init();
    }


    protected function clear()
    {
        $this->manager->unvoteAll();
        $this->afterSubmit();
    }


    /**
     *
     */
    protected function submit()
    {
        if ($this->manager === null) {
            throw new ilException('The NumberRange question got no voting manager! Please set one via setManager.');
        }

        //get all votes of the currents user
        // $votes = $this->manager->getVotesOfUser(false); TODO: ???

        //check if we voted or unvoted

        //we voted

        //filter the input and convert to int
        $filteredInput = filter_input(INPUT_POST, self::USER_SELECTED_NUMBER, FILTER_VALIDATE_INT);

        //check if the filter failed
        if ($filteredInput !== false && $filteredInput !== null) {
            //filter succeeded set value and store vote

            //validate user input
            if ($this->isVoteValid($this->getStart(), $this->getEnd(), $filteredInput)) {
                //vote
                $this->manager->inputOne([
                    'input'   => $filteredInput,
                    'vote_id' => '-1',
                ]);

                return;
            }
        }
    }


    /**
     * @return string
     */
    public function getMobileHTML()
    {
        $template = self::plugin()->template('default/QuestionTypes/NumberRange/tpl.number_range.html');
        $template->setVariable('ACTION', self::dic()->ctrl()->getFormAction($this));
        $template->setVariable('SHOW_PERCENTAGE', (int) $this->manager->getVoting()->getPercentage());

        /**
         * @var xlvoVote[] $userVotes
         */
        $userVotes = $this->manager->getVotesOfUser(false);
        $userVotes = array_values($userVotes);

        $template->setVariable('SLIDER_MIN', $this->getStart());
        $template->setVariable('SLIDER_MAX', $this->getEnd());
        $template->setVariable('SLIDER_STEP', $this->getStep());
        if ($userVotes[0] instanceof xlvoVote) {
            $user_has_voted = true;
            $value = (int) $userVotes[0]->getFreeInput();
        } else {
            $user_has_voted = false;
            $value = $this->getDefaultValue();
        }
        $template->setVariable('SLIDER_VALUE', $value);
        $template->setVariable('BTN_SAVE', $this->txt(self::SAVE_BUTTON_VOTE));
        $template->setVariable('BTN_CLEAR', $this->txt(self::CLEAR_BUTTON));

        if (!$user_has_voted) {
            $template->setVariable('BTN_RESET_DISABLED', 'disabled="disabled"');
        }

        return $template->get() . xlvoJs::getInstance()->name(xlvoQuestionTypes::NUMBER_RANGE)->category('QuestionTypes')->getRunCode();
    }


    /**
     * @return int
     */
    private function getDefaultValue()
    {
        return $this->snapToStep(($this->getStart() + $this->getEnd()) / 2);
    }


    /**
     * @param int   $start
     * @param int   $step
     * @param float $value
     *
     * @return bool
     */
    private function isVoteValid($start, $end, $value)
    {
        return ($value >= $start && $value <= $end && $value === $this->snapToStep($value));
    }


    /**
     * @return int
     */
    private function getStart()
    {
        return (int) $this->manager->getVoting()->getStartRange();
    }


    /**
     * @return int
     */
    private function getEnd()
    {
        return (int) $this->manager->getVoting()->getEndRange();
    }


    /**
     * @return int
     */
    private function getStep()
    {
        return (int) $this->manager->getVoting()->getStepRange();
    }


    /**
     * @param float $value
     *
     * @return int
     */
    private function snapToStep($value)
    {
        return intval(ceil(($value - $this->getStart()) / $this->getStep()) * $this->getStep()) + $this->getStart();
    }
}
