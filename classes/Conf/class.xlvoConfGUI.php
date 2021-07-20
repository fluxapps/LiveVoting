<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use LiveVoting\Conf\xlvoConf;
use LiveVoting\Conf\xlvoConfFormGUI;
use LiveVoting\Context\Param\ParamManager;
use LiveVoting\GUI\xlvoGUI;
use LiveVoting\Pin\xlvoPin;
use LiveVoting\Voting\xlvoVoting;

/**
 * Class xlvoConfGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy xlvoConfGUI : xlvoMainGUI
 */
class xlvoConfGUI extends xlvoGUI
{

    const CMD_RESET_TOKEN = 'resetToken';


    /**
     * @param string $key
     *
     * @return string
     */
    public function txt($key)
    {
        return self::plugin()->translate($key, 'config');
    }


    public function index()
    {
        if (xlvoConf::getConfig(xlvoConf::F_RESULT_API)) {
            $b = ilLinkButton::getInstance();
            $b->setUrl(self::dic()->ctrl()->getLinkTarget($this, self::CMD_RESET_TOKEN));
            $b->setCaption($this->txt('regenerate_token'), false);
            self::dic()->toolbar()->addButtonInstance($b);
            $b = ilLinkButton::getInstance();
            $xlvoVoting = xlvoVoting::last();
            $xlvoVoting = $xlvoVoting ? $xlvoVoting : new xlvoVoting();
            $url = xlvoConf::getBaseVoteURL() . xlvoConf::RESULT_API_URL . '?token=%s&type=%s&' . ParamManager::PARAM_PIN . '=%s';
            $url = sprintf($url, xlvoConf::getApiToken(), xlvoConf::getConfig(xlvoConf::F_API_TYPE), xlvoPin::lookupPin($xlvoVoting->getObjId()));
            $b->setUrl($url);
            $b->setTarget('_blank');
            $b->setCaption($this->txt('open_result_api'), false);
            self::dic()->toolbar()->addButtonInstance($b);
        }

        $xlvoConfFormGUI = new xlvoConfFormGUI($this);
        $xlvoConfFormGUI->fillForm();
        self::dic()->ui()->mainTemplate()->setContent($xlvoConfFormGUI->getHTML());
    }


    protected function resetToken()
    {
        xlvoConf::set(xlvoConf::F_API_TOKEN, null);
        xlvoConf::getConfig(xlvoConf::F_API_TOKEN);
        $this->cancel();
    }


    protected function update()
    {
        $xlvoConfFormGUI = new xlvoConfFormGUI($this);
        $xlvoConfFormGUI->setValuesByPost();
        if ($xlvoConfFormGUI->saveObject()) {
            ilUtil::sendSuccess($this->txt('msg_success'), true);
            self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
        }
        self::dic()->ui()->mainTemplate()->setContent($xlvoConfFormGUI->getHTML());
    }


    protected function confirmDelete()
    {
    }


    protected function delete()
    {
    }


    protected function add()
    {
    }


    protected function create()
    {
    }


    protected function edit()
    {
    }
}
