<#1>
<?php
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Menu/class.ilCtrlMainMenuPlugin.php');
ilCtrlMainMenuPlugin::loadActiveRecord();


require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntry.php');
ctrlmmEntry::installDB();


require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Menu/class.ilCtrlMainMenuConfig.php');
ilCtrlMainMenuConfig::installDB();

ilCtrlMainMenuConfig::set(ilCtrlMainMenuConfig::F_CSS_PREFIX, 'il');
ilCtrlMainMenuConfig::set(ilCtrlMainMenuConfig::F_CSS_ACTIVE, 'MMActive');
ilCtrlMainMenuConfig::set(ilCtrlMainMenuConfig::F_CSS_INACTIVE, 'MMInactive');
ilCtrlMainMenuConfig::set(ilCtrlMainMenuConfig::F_REPLACE_FULL_HEADER, false);

require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ctrlmmData.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ctrlmmTranslation.php');

ctrlmmData::installDB();
ctrlmmTranslation::installDB();

ctrlmmMenu::includeAllTypes();

$desktop = new ctrlmmEntryDesktop();
$desktop->setPosition(1);
$desktop->create();

$repo = new ctrlmmEntryRepository();
$repo->setPosition(2);
$repo->create();

$admin = new ctrlmmEntryAdmin();
$admin->setPosition(3);
$admin->create();
?>
<#5>
<?php

?>