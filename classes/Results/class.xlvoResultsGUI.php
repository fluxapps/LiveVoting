<?php

use LiveVoting\GUI\xlvoGUI;
use LiveVoting\Results\xlvoResultsTableGUI;
use LiveVoting\Round\xlvoRound;
use LiveVoting\User\xlvoParticipant;
use LiveVoting\User\xlvoParticipants;
use LiveVoting\User\xlvoUser;
use LiveVoting\User\xlvoVoteHistoryTableGUI;
use LiveVoting\Voting\xlvoVoting;
use LiveVoting\Voting\xlvoVotingConfig;

/**
 * Class xlvoResultsGUI
 *
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 */
class xlvoResultsGUI extends xlvoGUI
{

    const LENGTH = 40;
    const CMD_SHOW = 'showResults';
    const CMD_NEW_ROUND = 'newRound';
    const CMD_CHANGE_ROUND = 'changeRound';
    const CMD_APPLY_FILTER = "applyFilter";
    const CMD_SHOW_HISTORY = "showHistory";
    const CMD_RESET_FILTER = 'resetFilter';
    const CMD_CONFIRM_NEW_ROUND = 'confirmNewRound';
    /**
     * @var xlvoRound
     */
    private $round;
    /**
     * @var int
     */
    private $obj_id = 0;
    /**
     * @var xlvoVotingConfig
     */
    private $config;


    /**
     * xlvoResultsGUI constructor.
     *
     * @param $obj_id
     */
    public function __construct($obj_id)
    {
        parent::__construct();
        $this->obj_id = $obj_id;
        $this->config = xlvoVotingConfig::find($obj_id);
        $this->buildRound();
    }


    /**
     *
     */
    public function executeCommand()
    {
        $cmd = self::dic()->ctrl()->getCmd();
        switch ($cmd) {
            case self::CMD_SHOW:
            case self::CMD_CHANGE_ROUND:
            case self::CMD_NEW_ROUND:
            case self::CMD_APPLY_FILTER:
            case self::CMD_RESET_FILTER:
            case self::CMD_SHOW_HISTORY:
            case self::CMD_CONFIRM_NEW_ROUND:
                $this->$cmd();

                return;
        }
    }


    /**
     *
     */
    private function showResults()
    {
        $this->buildToolbar();

        $table = new xlvoResultsTableGUI($this, self::CMD_SHOW, $this->config->getVotingHistory());
        $this->buildFilters($table);
        $table->initFilter();
        $table->buildData($this->obj_id, $this->round->getId());
        self::dic()->ui()->mainTemplate()->setContent($table->getHTML());
    }


    /**
     *
     */
    private function buildRound()
    {
        if ($_GET['round_id']) {
            $this->round = xlvoRound::find($_GET['round_id']);
        } else {
            $this->round = xlvoRound::getLatestRound($this->obj_id);
        }
    }


    /**
     * @return array
     * @throws \srag\DIC\LiveVoting\Exception\DICException
     */
    private function getRounds()
    {
        /** @var xlvoRound[] $rounds */
        $rounds = xlvoRound::where(array('obj_id' => $this->obj_id))->get();
        $array = array();
        foreach ($rounds as $round) {
            $array[$round->getId()] = $this->getRoundTitle($round);
        }

        return $array;
    }


    /**
     * @param xlvoRound $round
     *
     * @return string
     * @throws \srag\DIC\LiveVoting\Exception\DICException
     */
    private function getRoundTitle(xlvoRound $round)
    {
        return $round->getTitle() ? $round->getTitle() : self::plugin()->translate("common_round") . " " . $round->getRoundNumber();
    }


    /**
     *
     */
    private function changeRound()
    {
        $round = $_POST['round_id'];
        self::dic()->ctrl()->setParameter($this, 'round_id', $round);
        self::dic()->ctrl()->redirect($this, self::CMD_SHOW);
    }


    /**
     * @throws \srag\DIC\LiveVoting\Exception\DICException
     */
    private function newRound()
    {
        $lastRound = xlvoRound::getLatestRound($this->obj_id);
        $newRound = new xlvoRound();
        $newRound->setRoundNumber($lastRound->getRoundNumber() + 1);
        $newRound->setObjId($this->obj_id);
        $newRound->store();
        self::dic()->ctrl()->setParameter($this, 'round_id', xlvoRound::getLatestRound($this->obj_id)->getId());
        ilUtil::sendSuccess(self::plugin()->translate("common_new_round_created"), true);
        self::dic()->ctrl()->redirect($this, self::CMD_SHOW);
    }


    /**
     *
     */
    private function applyFilter()
    {
        $table = new xlvoResultsTableGUI($this, self::CMD_SHOW);
        $this->buildFilters($table);
        $table->initFilter();
        $table->writeFilterToSession();
        self::dic()->ctrl()->redirect($this, self::CMD_SHOW);
    }


    /**
     *
     */
    private function resetFilter()
    {
        $table = new xlvoResultsTableGUI($this, self::CMD_SHOW);
        $this->buildFilters($table);
        $table->initFilter();
        $table->resetFilter();
        self::dic()->ctrl()->redirect($this, self::CMD_SHOW);
    }


    /**
     * @throws \srag\DIC\LiveVoting\Exception\DICException
     */
    private function showHistory()
    {
        self::dic()->tabs()->setBackTarget(self::plugin()->translate('common_back'), self::dic()->ctrl()->getLinkTarget($this, self::CMD_SHOW));

        $user_id = $_GET['user_id'] ? $_GET['user_id'] : $_GET['user_identifier'];
        $participants = xlvoParticipants::getInstance($this->obj_id)->getParticipantsForRound($this->round->getId(), $this->user_id);
        /** @var xlvoParticipant $participant */
        $participant = array_shift($participants);

        $q = new ilNonEditableValueGUI(self::plugin()->translate("common_question"));
        $q->setValue(strip_tags(xlvoVoting::find($_GET['voting_id'])->getQuestion()));

        $p = new ilNonEditableValueGUI(self::plugin()->translate("common_participant"));
        $p->setValue($this->getParticipantName($participant));

        $d = new ilNonEditableValueGUI(self::plugin()->translate("common_round"));
        $d->setValue($this->getRoundTitle($this->round));

        $form = new ilPropertyFormGUI();
        $form->setItems(array($q, $p, $d));

        $table = new xlvoVoteHistoryTableGUI($this, self::CMD_SHOW_HISTORY);
        $table->parseData($_GET['user_id'], $_GET['user_identifier'], $_GET['voting_id'], $this->round->getId());
        self::dic()->ui()->mainTemplate()->setContent($form->getHTML() . $table->getHTML());
    }


    /**
     * @return Closure
     */
    public function getParticipantNameCallable()
    {
        return function (xlvoParticipant $participant) {
            if ($participant->getUserIdType() == xlvoUser::TYPE_ILIAS
                && $participant->getUserId()
            ) {
                $name = ilObjUser::_lookupName($participant->getUserId());

                return $name['firstname'] . " " . $name['lastname'];
            }

            return self::plugin()->translate("common_participant") . " " . substr($participant->getUserIdentifier(), 0, 4);
        };
    }


    /**
     * @param xlvoParticipant $participant
     *
     * @return string
     */
    public function getParticipantName(xlvoParticipant $participant = null)
    {
        if (!$participant instanceof xlvoParticipant) {
            return '';
        }
        $closure = $this->getParticipantNameCallable();

        return $closure($participant);
    }


    /**
     * @throws \srag\DIC\LiveVoting\Exception\DICException
     */
    public function confirmNewRound()
    {
        $conf = new ilConfirmationGUI();
        $conf->setFormAction(self::dic()->ctrl()->getFormAction($this));
        $conf->setHeaderText(self::plugin()->translate('common_confirm_new_round'));
        $conf->setConfirm(self::plugin()->translate("common_new_round"), self::CMD_NEW_ROUND);
        $conf->setCancel(self::plugin()->translate('common_cancel'), self::CMD_SHOW);
        self::dic()->ui()->mainTemplate()->setContent($conf->getHTML());
    }


    /**
     * @param xlvoResultsTableGUI $table
     */
    private function buildFilters(&$table)
    {
        $filter = new ilSelectInputGUI(self::plugin()->translate("common_participant"), "participant");
        $participants = xlvoParticipants::getInstance($this->obj_id)->getParticipantsForRound($this->round->getId());
        $options = array(0 => self::plugin()->translate("common_all"));
        foreach ($participants as $participant) {
            $options[($participant->getUserIdentifier()
                != null) ? $participant->getUserIdentifier() : $participant->getUserId()]
                = $this->getParticipantName($participant);
        }
        $filter->setOptions($options);
        $table->addFilterItem($filter);
        $filter->readFromSession();

        //		 Title
        $filter = new ilSelectInputGUI(self::plugin()->translate("voting_title"), "voting_title");
        $titles = array();
        $titles[0] = self::plugin()->translate("common_all");
        $titles = array_replace($titles, xlvoVoting::where(array("obj_id" => $this->obj_id))
            ->getArray("id", "title")); //dont use array_merge: it kills the keys.
        $closure = $this->getShortener(40);
        array_walk($titles, $closure);
        $filter->setOptions($titles);
        $table->addFilterItem($filter);
        $filter->readFromSession();

        // Question
        $filter = new ilSelectInputGUI(self::plugin()->translate("common_question"), "voting");

        $votings = array();
        $votings[0] = self::plugin()->translate("common_all");
        $votings = array_replace($votings, xlvoVoting::where(array("obj_id" => $this->obj_id))
            ->getArray("id", "question")); //dont use array_merge: it kills the keys.
        array_walk($votings, $closure);
        $filter->setOptions($votings);
        $table->addFilterItem($filter);
        $filter->readFromSession();

        // Read values
        $table->setFormAction(self::dic()->ctrl()->getFormAction($this, self::CMD_APPLY_FILTER));
    }


    /**
     *
     */
    private function buildToolbar()
    {
        $button = ilLinkButton::getInstance();
        $button->setUrl(self::dic()->ctrl()->getLinkTargetByClass(xlvoResultsGUI::class, xlvoResultsGUI::CMD_CONFIRM_NEW_ROUND));
        $button->setCaption(self::plugin()->translate("new_round"), false);
        self::dic()->toolbar()->addButtonInstance($button);

        self::dic()->toolbar()->addSeparator();

        $table_selection = new ilSelectInputGUI('', 'round_id');
        $options = $this->getRounds();
        $table_selection->setOptions($options);
        $table_selection->setValue($this->round->getId());

        self::dic()->toolbar()->setFormAction(self::dic()->ctrl()->getFormAction($this, self::CMD_CHANGE_ROUND));
        self::dic()->toolbar()->addText(self::plugin()->translate("common_round"));
        self::dic()->toolbar()->addInputItem($table_selection);

        $button = ilSubmitButton::getInstance();
        $button->setCaption(self::plugin()->translate('common_change'), false);
        $button->setCommand(self::CMD_CHANGE_ROUND);
        self::dic()->toolbar()->addButtonInstance($button);
    }


    /**
     * @param int $length
     *
     * @return Closure
     */
    public function getShortener($length = self::LENGTH)
    {
        return function (&$question) use ($length) {
            $qs = nl2br($question, false);
            $qs = strip_tags($qs);

            $question = strlen($qs) > $length ? substr($qs, 0, $length) . "..." : $qs;

            return $question;
        };
    }
}
