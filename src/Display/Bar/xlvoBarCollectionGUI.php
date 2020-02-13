<?php

namespace LiveVoting\Display\Bar;

use ilLiveVotingPlugin;
use ilTemplate;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoBarCollectionGUI
 *
 * @package LiveVoting\Display\Bar
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoBarCollectionGUI
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    /**
     * @var ilTemplate
     */
    protected $tpl;
    /**
     * @var int
     */
    protected $total_votes = 0;
    /**
     * @var bool
     */
    protected $show_total_votes = false;
    /**
     * @var int
     */
    protected $total_voters = 0;
    /**
     * @var bool
     */
    protected $show_total_voters = false;


    /**
     *
     */
    public function __construct()
    {
        $this->tpl = self::plugin()->template('default/Display/Bar/tpl.bar_collection.html');
    }


    /**
     * @return string
     */
    public function getHTML()
    {
        $this->renderVotersAndVotes();

        return $this->tpl->get();
    }


    /**
     * @param xlvoGeneralBarGUI $bar_gui
     */
    public function addBar(xlvoGeneralBarGUI $bar_gui)
    {
        $this->tpl->setCurrentBlock('bar');
        $this->tpl->setVariable('BAR', $bar_gui->getHTML());
        $this->tpl->parseCurrentBlock();
    }


    /**
     * @param $html
     */
    public function addSolution($html)
    {
        $this->tpl->setCurrentBlock('solution');
        $this->tpl->setVariable('SOLUTION', $html);
        $this->tpl->parseCurrentBlock();
    }


    /**
     * @return int
     */
    public function getTotalVotes()
    {
        return $this->total_votes;
    }


    /**
     * @param int $total_votes
     */
    public function setTotalVotes($total_votes)
    {
        $this->total_votes = $total_votes;
    }


    /**
     * @return boolean
     */
    public function isShowTotalVotes()
    {
        return $this->show_total_votes;
    }


    /**
     * @param boolean $show_total_votes
     */
    public function setShowTotalVotes($show_total_votes)
    {
        $this->show_total_votes = $show_total_votes;
    }


    /**
     * @return int
     */
    public function getTotalVoters()
    {
        return $this->total_voters;
    }


    /**
     * @param int $total_voters
     */
    public function setTotalVoters($total_voters)
    {
        $this->total_voters = $total_voters;
    }


    /**
     * @return boolean
     */
    public function isShowTotalVoters()
    {
        return $this->show_total_voters;
    }


    /**
     * @param boolean $show_total_voters
     */
    public function setShowTotalVoters($show_total_voters)
    {
        $this->show_total_voters = $show_total_voters;
    }


    /**
     *
     */
    protected function renderVotersAndVotes()
    {
        if ($this->isShowTotalVotes()) {
            $this->tpl->setCurrentBlock('total_votes');
            $this->tpl->setVariable('TOTAL_VOTES', self::plugin()->translate('qtype_1_total_votes') . ': ' . $this->getTotalVotes());
            $this->tpl->parseCurrentBlock();
        }
        if ($this->isShowTotalVoters()) {
            $this->tpl->setCurrentBlock('total_voters');
            $this->tpl->setVariable('TOTAL_VOTERS', self::plugin()->translate('qtype_1_total_voters') . ': ' . $this->getTotalVoters());
            $this->tpl->parseCurrentBlock();
        }
    }
}
