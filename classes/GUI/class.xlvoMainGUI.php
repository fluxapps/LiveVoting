<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use LiveVoting\GUI\xlvoGUI;

/**
 * Class xlvoMainGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 *
 * @ilCtrl_IsCalledBy xlvoMainGUI : ilLiveVotingConfigGUI
 */
class xlvoMainGUI extends xlvoGUI
{

    const TAB_SETTINGS = 'settings';
    const TAB_SYSTEM_ACCOUNTS = 'system_accounts';
    const TAB_PUBLICATION_USAGE = 'publication_usage';
    const TAB_EXPORT = 'export';


    /**
     * @return void
     */
    public function executeCommand()
    {
        $nextClass = self::dic()->ctrl()->getNextClass();
        switch ($nextClass) {
            default:
                $xlvoConfGUI = new xlvoConfGUI();
                self::dic()->ctrl()->forwardCommand($xlvoConfGUI);
                break;
        }
    }
}
