<?php
require_once('include.php');
require_once('classes/class.ilObjLiveVotingGUI.php');
require_once('classes/class.ilObjLiveVoting.php');
require_once('classes/class.ilLiveVotingPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingPlugin.php');
global $tpl, $ilTpl, $ilPluginAdmin, $lng;
$pl = ilLiveVotingPlugin::getInstance();

$_GET['pin'] = str_ireplace('/', '', $_GET['pin']);

if ($_POST['pin'] || $_GET['pin']) {
	$pin = ((!$_POST['pin'] AND $_GET['pin']) ? $_GET['pin'] : $_POST['pin']);
	$link = ilObjLiveVotingGUI::getLinkByPin($pin);
	if ($link) {
		$pin = $_GET['pin'] ? $_GET['pin'] : $_POST['pin'];
		$obj = ilObjLiveVoting::_getObjectByPin($pin);
		if ($obj->getOnline()) {
			header('Location: ' . $link . '');
		} else {
			ilUtil::sendFailure($pl->txt('is_not_online'));
		}
	} else {
		ilUtil::sendFailure($pl->txt('pin_doesnt_exists'));
	}
}
$pin_url = "/Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/pin.php";
$tpl->getStandardTemplate();
$tpl_form = $pl->getTemplate('tpl.pin_form.html');
$tpl_form->setVariable('PIN_URL', ilObjLiveVotingGUI::getHttpPath() . $pin_url);
$tpl_form->setVariable('TXT_BUTTON', $lng->txt('rep_robj_xlvo_send_pin'));
$tpl_form->setVariable('PINFORM_INFO', $pl->txt('pinform_info'));
$tpl->addCSS('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/content.css');
$tpl->addCSS('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/pin_hider.css');
$tpl->addCSS('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/responsive.css');
$tpl->setCurrentBlock('HeadBaseTag');
$tpl->setVariable('BASE', ilObjLiveVotingGUI::getHttpPath() . '/');
$tpl->parseCurrentBlock();
$tpl->setContent($tpl_form->get());
$tpl->show();

?>
