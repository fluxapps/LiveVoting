<?php

namespace LiveVoting\Display\Bar;

use ilLiveVotingPlugin;
use ilTemplate;
use LiveVoting\Option\xlvoOption;
use LiveVoting\Vote\xlvoVote;
use LiveVoting\Voting\xlvoVoting;
use LiveVoting\Voting\xlvoVotingManager2;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoBarOptionGUI
 *
 * @package LiveVoting\Display\Bar
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoBarOptionGUI implements xlvoGeneralBarGUI
{

    use DICTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    /**
     * @var xlvoVoting
     */
    protected $voting;
    /**
     * @var xlvoOption
     */
    protected $option;
    /**
     * @var string
     */
    protected $option_letter;
    /**
     * @var ilTemplate
     */
    protected $tpl;
    /**
     * @var xlvoVotingManager2
     */
    protected $voting_manager;


    /**
     * @param xlvoVoting $voting
     * @param xlvoOption $option
     * @param string     $option_letter
     */
    public function __construct(xlvoVoting $voting, xlvoOption $option, $option_letter)
    {
        $this->voting_manager = xlvoVotingManager2::getInstanceFromObjId($voting->getObjId());
        $this->voting = $voting;
        $this->option = $option;
        $this->option_letter = $option_letter;
        $this->tpl = self::plugin()->template('default/Display/Bar/tpl.bar_option.html');
    }


    /**
     *
     */
    protected function render()
    {
        $this->tpl->setVariable('OPTION_LETTER', $this->option_letter);
        $this->tpl->setVariable('OPTION_ID', $this->option->getId());
        $this->tpl->setVariable('TITLE', $this->option->getTextForPresentation());
        $this->tpl->setVariable('OPTION_ACTIVE', $this->getActiveBar());
        $this->tpl->setVariable('VOTE_ID', $this->getVoteId());
    }


    /**
     * @return string
     */
    public function getHTML()
    {
        $this->render();

        return $this->tpl->get();
    }


    /**
     * @return string
     */
    private function getActiveBar()
    {
        /**
         * @var xlvoVote $vote
         */
        $vote = $this->voting_manager->getVotesOfUserOfOption($this->voting->getId(), $this->option->getId())->first(); // TODO: Invalid method call?
        if ($vote instanceof xlvoVote) {
            if ($vote->getStatus() == 1) {
                return "active";
            } else {
                return "";
            }
        } else {
            return "";
        }
    }


    /**
     * @return int|string
     */
    private function getVoteId()
    {
        /**
         * @var xlvoVote $vote
         */
        $vote = $this->voting_manager->getVotesOfUserOfOption($this->voting->getId(), $this->option->getId())->first(); // TODO: Invalid method call?
        if ($vote instanceof xlvoVote) {
            return $vote->getId();
        } else {
            $no_existing_vote = 0;

            return $no_existing_vote;
        }
    }
}
