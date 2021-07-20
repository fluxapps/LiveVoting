<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use LiveVoting\Conf\xlvoConf;
use LiveVoting\Context\Param\ParamManager;
use LiveVoting\Exceptions\xlvoVoterException;
use LiveVoting\Exceptions\xlvoVotingManagerException;
use LiveVoting\GUI\xlvoGUI;
use LiveVoting\Js\xlvoJs;
use LiveVoting\Js\xlvoJsResponse;
use LiveVoting\Pin\xlvoPin;
use LiveVoting\Player\xlvoPlayer;
use LiveVoting\QuestionTypes\xlvoQuestionTypes;
use LiveVoting\QuestionTypes\xlvoQuestionTypesGUI;
use LiveVoting\Voter\xlvoVoter;
use LiveVoting\Voting\xlvoVotingConfig;
use LiveVoting\Voting\xlvoVotingManager2;
use LiveVoting\UIComponent\GlyphGUI;
use srag\CustomInputGUIs\LiveVoting\TextInputGUI\TextInputGUI;

/**
 * Class xlvoVoter2GUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy xlvoVoter2GUI: ilUIPluginRouterGUI
 */
class xlvoVoter2GUI extends xlvoGUI
{

    const CMD_CHECK_PIN = 'checkPin';
    const F_PIN_INPUT = 'pin_input';
    const CMD_START_VOTER_PLAYER = 'startVoterPlayer';
    const CMD_GET_VOTING_DATA = 'loadVotingData';
    const DEBUG = false;
    /**
     * @var string
     */
    protected $pin = '';
    /**
     * @var xlvoVotingManager2
     */
    protected $manager;


    /**
     * @param string $key
     *
     * @return string
     */
    protected function txt($key)
    {
        return self::plugin()->translate($key, 'voter');
    }


    /**
     * @throws ilCtrlException
     * @throws xlvoVotingManagerException
     */
    public function executeCommand()
    {

        $param_manager = ParamManager::getInstance();

        $this->pin = $param_manager->getPin();
        $this->manager = new xlvoVotingManager2($this->pin);
        $nextClass = self::dic()->ctrl()->getNextClass();
        switch ($nextClass) {
            case '':
                if (!$this->manager->getVotingConfig()->isAnonymous()
                    && (is_null(self::dic()->user()) || self::dic()->user()->getId() == 13
                        || self::dic()->user()->getId() == 0)
                ) {
                    //remove plugin path to get "real" web root otherwise we break installations with context paths -> http://demo.ilias.ch/test/goto.php
                    $plugin_path = substr(self::plugin()->directory(), 2); // Remove ./
                    $ilias_base_path = str_replace($plugin_path, '', ILIAS_HTTP_PATH);
                    $login_target = "{$ilias_base_path}goto.php?target=xlvo_1_pin_" . $this->pin;

                    //redirect
                    self::dic()->ctrl()->redirectToURL($login_target);
                } else {
                    parent::executeCommand();
                }

                break;
            default:
                // Question-types
                require_once self::dic()->ctrl()->lookupClassPath($nextClass);
                $gui = new $nextClass();
                if ($gui instanceof xlvoQuestionTypesGUI) {
                    $gui->setManager($this->manager);
                }
                self::dic()->ctrl()->forwardCommand($gui);
                break;
        }
    }


    /**
     *
     */
    protected function index()
    {
        if ($this->manager->getObjId() > 0) {
            self::dic()->ctrl()->redirect($this, self::CMD_START_VOTER_PLAYER);
        }

        $tpl = self::plugin()->template('default/Voter/tpl.pin.html', true, false);
        self::dic()->ui()->mainTemplate()->addCss(self::plugin()->directory() . '/templates/default/Voter/pin.css');
        $pin_form = new ilPropertyFormGUI();
        $pin_form->setFormAction(self::dic()->ctrl()->getLinkTarget($this, self::CMD_CHECK_PIN));
        $pin_form->addCommandButton(self::CMD_CHECK_PIN, $this->txt('send'));

        $xlvoPin = new xlvoPin();

        $te = new TextInputGUI($this->txt(self::F_PIN_INPUT), self::F_PIN_INPUT);
        $te->setMaxLength($xlvoPin->getPinLength());
        $pin_form->addItem($te);

        $tpl->setVariable('TITLE', $this->txt('pin_form_title'));
        $tpl->setVariable('FORM', $pin_form->getHTML());

        if (self::version()->is6()) {
            self::dic()->ui()->mainTemplate()->setVariable('PLAYER_CONTENT', $tpl->get());
        } else {
            self::dic()->ui()->mainTemplate()->setContent($tpl->get());
        }
    }


    /**
     * @throws Exception
     */
    protected function checkPin()
    {
        $param_manager = ParamManager::getInstance();

        try {
            $pin = filter_input(INPUT_POST, self::F_PIN_INPUT);

            xlvoPin::checkPinAndGetObjId($pin);

            $param_manager->setPin($_POST[self::F_PIN_INPUT]);

            self::dic()->ctrl()->redirect($this, self::CMD_START_VOTER_PLAYER);
        } catch (xlvoVoterException $e) {
            $param_manager->setPin('');

            ilUtil::sendFailure($this->txt('msg_validation_error_pin_' . $e->getCode()));

            $this->index();
        }
    }


    /**
     * @throws ilException
     */
    protected function startVoterPlayer()
    {
        try {
            xlvoPin::checkPinAndGetObjId($this->pin);
        } catch (Throwable $e) {
            throw new ilException("Voter2GUI Wrong PIN!");
        }

        $this->initJsAndCss();
        self::dic()->ui()->mainTemplate()->addCss(self::plugin()->directory() . '/templates/default/default.css');
        $tpl = self::plugin()->template('default/Voter/tpl.voter_player.html', true, false);
        if (self::version()->is6()) {
            self::dic()->ui()->mainTemplate()->setVariable('PLAYER_CONTENT', $tpl->get());
        } else {
            self::dic()->ui()->mainTemplate()->setContent($tpl->get());
        }
    }


    /**
     *
     */
    protected function getVotingData()
    {
        /**
         * @var xlvoVotingConfig $showAttendees
         */
        $showAttendees = xlvoVotingConfig::find($this->manager->getVoting()->getObjId());
        if ($showAttendees->isShowAttendees()) {
            xlvoVoter::register($this->manager->getPlayer()->getId());
        }

        xlvoJsResponse::getInstance($this->manager->getPlayer()->getStdClassForVoter())->send();
    }


    /**
     * @throws ilException
     */
    protected function initJsAndCss()
    {
        self::dic()->ui()->mainTemplate()->addCss(self::plugin()->directory() . '/templates/default/Voter/voter.css');
        self::dic()->ui()->mainTemplate()->addCss(self::plugin()->directory() . '/templates/default/libs/bootstrap-slider.min.css');
        self::dic()->ui()->mainTemplate()->addCss(self::plugin()->directory() . '/templates/default/QuestionTypes/NumberRange/number_range.css');
        iljQueryUtil::initjQueryUI();

        $t = array('player_seconds');

        xlvoJs::getInstance()->initMathJax();

        /**
         * @var string $delay
         */
        $delay = xlvoConf::getConfig(xlvoConf::F_REQUEST_FREQUENCY);

        //check if we get some valid settings otherwise fall back to default value.
        if (is_numeric($delay)) {
            $delay = ((float) $delay);
        } else {
            $delay = xlvoVoter::DEFAULT_CLIENT_UPDATE_DELAY;
        }
        $delay *= 1000;

        $mathJaxSetting = new ilSetting("MathJax");
        $settings = array(
            'use_mathjax' => (bool) $mathJaxSetting->get("enable"),
            'debug'       => self::DEBUG,
            'delay'       => $delay,
        );

        xlvoJs::getInstance()->api($this, array(ilUIPluginRouterGUI::class))->addSettings($settings)->name('Voter')->addTranslations($t)->init()
            ->setRunCode();
        foreach (xlvoQuestionTypes::getActiveTypes() as $type) {
            xlvoQuestionTypesGUI::getInstance($this->manager, $type)->initJS($type == $this->manager->getVoting()->getVotingType());
        }
    }


    /**
     * @throws xlvoVotingManagerException
     * @throws ilException
     */
    protected function getHTML()
    {
        $tpl = self::plugin()->template('default/Voter/tpl.inner_screen.html');

        if ($this->manager->getPlayer()->isFrozen()) {
            $tpl->setVariable('TITLE', $this->txt('header_frozen'));
            $tpl->setVariable('DESCRIPTION', $this->txt('info_frozen'));
            $tpl->setVariable('COUNT', $this->manager->countVotings());
            $tpl->setVariable('POSITION', $this->manager->getVotingPosition());
            $tpl->setVariable('PIN', xlvoPin::formatPin($this->manager->getVotingConfig()->getPin()));
            $tpl->setVariable('GLYPH', GlyphGUI::get('pause'));
            echo $tpl->get();
            exit;
        }

        switch ($this->manager->getPlayer()->getStatus(false)) {
            case xlvoPlayer::STAT_STOPPED:
                $tpl->setVariable('TITLE', $this->txt('header_stopped'));
                $tpl->setVariable('DESCRIPTION', $this->txt('info_stopped'));
                $tpl->setVariable('COUNT', $this->manager->countVotings());
                $tpl->setVariable('POSITION', $this->manager->getVotingPosition());
                $tpl->setVariable('PIN', xlvoPin::formatPin($this->manager->getVotingConfig()->getPin()));
                break;
            case xlvoPlayer::STAT_RUNNING:
                $tpl->setVariable('TITLE', $this->manager->getVoting()->getTitle());
                $tpl->setVariable('DESCRIPTION', $this->manager->getVoting()->getDescription());
                $tpl->setVariable('COUNT', $this->manager->countVotings());
                $tpl->setVariable('POSITION', $this->manager->getVotingPosition());
                $tpl->setVariable('PIN', xlvoPin::formatPin($this->manager->getVotingConfig()->getPin()));

                $xlvoQuestionTypesGUI = xlvoQuestionTypesGUI::getInstance($this->manager);
                if ($xlvoQuestionTypesGUI->isShowQuestion()) {
                    $tpl->setCurrentBlock('question_text');
                    $tpl->setVariable('QUESTION_TEXT', $this->manager->getVoting()->getQuestionForPresentation());
                    $tpl->parseCurrentBlock();
                }
                $tpl->setVariable('QUESTION', $xlvoQuestionTypesGUI->getMobileHTML());
                break;
            case xlvoPlayer::STAT_START_VOTING:
                $tpl->setVariable('TITLE', $this->txt('header_start'));
                $tpl->setVariable('DESCRIPTION', $this->txt('info_start'));
                $tpl->setVariable('GLYPH', GlyphGUI::get('pause'));
                break;
            case xlvoPlayer::STAT_END_VOTING:
                $tpl->setVariable('TITLE', $this->txt('header_end'));
                $tpl->setVariable('DESCRIPTION', $this->txt('info_end'));;
                $tpl->setVariable('GLYPH', GlyphGUI::get('stop'));
                break;
            case xlvoPlayer::STAT_FROZEN:
                $tpl->setVariable('TITLE', $this->txt('header_frozen'));
                $tpl->setVariable('DESCRIPTION', $this->txt('info_frozen'));
                $tpl->setVariable('COUNT', $this->manager->countVotings());
                $tpl->setVariable('POSITION', $this->manager->getVotingPosition());
                $tpl->setVariable('PIN', xlvoPin::formatPin($this->manager->getVotingConfig()->getPin()));
                $tpl->setVariable('GLYPH', GlyphGUI::get('pause'));
                break;
        }
        echo $tpl->get();
        exit;
    }
}
