<?php

namespace LiveVoting\QuestionTypes;

use ilException;
use ilLiveVotingPlugin;
use LiveVoting\Option\xlvoOption;
use LiveVoting\QuestionTypes\CorrectOrder\xlvoCorrectOrderResultGUI;
use LiveVoting\QuestionTypes\FreeInput\xlvoFreeInputResultGUI;
use LiveVoting\QuestionTypes\FreeOrder\xlvoFreeOrderResultGUI;
use LiveVoting\QuestionTypes\NumberRange\xlvoNumberRangeResultGUI;
use LiveVoting\QuestionTypes\SingleVote\xlvoSingleVoteResultGUI;
use LiveVoting\Utils\LiveVotingTrait;
use LiveVoting\Vote\xlvoVote;
use LiveVoting\Voting\xlvoVoting;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoResultGUI
 *
 *
 * @package LiveVoting\QuestionTypes
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
abstract class xlvoResultGUI
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    /**
     * @var xlvoVoting
     */
    protected $voting;
    /**
     * @var xlvoOption[]
     */
    protected $options;


    /**
     * xlvoResultGUI constructor.
     *
     * @param xlvoVoting $voting
     */
    public function __construct($voting)
    {
        $this->voting = $voting;
        $this->options = $voting->getVotingOptions();
    }


    /**
     * @param xlvoVote[] $votes
     *
     * @return string
     */
    public abstract function getTextRepresentation(array $votes);


    /**
     * @param xlvoVote[] $votes
     *
     * @return string
     */
    public abstract function getAPIRepresentation(array $votes);


    /**
     * Creates an instance of the result gui.
     *
     * @param xlvoVoting $voting Finished or ongoing voting.
     *
     * @return xlvoResultGUI        Result GUI to display the voting results.
     * @throws ilException         Throws an ilException if no result gui class was found for the
     *                              given voting type.
     */
    public static function getInstance(xlvoVoting $voting)
    {
        $class = xlvoQuestionTypes::getClassName($voting->getVotingType());

        switch ($class) {
            case xlvoQuestionTypes::CORRECT_ORDER:
                return new xlvoCorrectOrderResultGUI($voting);
            case xlvoQuestionTypes::FREE_INPUT:
                return new xlvoFreeInputResultGUI($voting);
            case xlvoQuestionTypes::FREE_ORDER:
                return new xlvoFreeOrderResultGUI($voting);
            case xlvoQuestionTypes::SINGLE_VOTE:
                return new xlvoSingleVoteResultGUI($voting);
            case xlvoQuestionTypes::NUMBER_RANGE:
                return new xlvoNumberRangeResultGUI($voting);
            default:
                throw new ilException('Could not find the result gui for the given voting.');
        }
    }
}
