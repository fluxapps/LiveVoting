<?php
// NOTE: the pin.php may be access directly or by a rewrite rule in the apache configuration.
// So urls which call this file are
//  - http://localhost/ilias_dev/vote?pin=1232
//  - http://localhost/ilias_dev/Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/pin.php
// In Services/Init/classes/class.ilInitialisation.php the gobal 'ILIAS_HTTP_PATH' is defined.
// To work properly you have to defined ILIAS_MODULE path, so it can rewinds the directory correctly.

define('ILIAS_MODULE',"Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting");

// the next line should convert "ilias_dev/vote?pin=1232" to "vote"
// and "ilias_dev/Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/pin.php?pin=123" to "pin.php"
$uri_last_path_element =  basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if( $uri_last_path_element  === basename(__FILE__) ) { // basename(__FILE__) should be "pin.php"
	$use_http_path = NULL;
} elseif( $uri_last_path_element === 'vote') {
	// BUT since we use a rewrite rule for urls, rewinding of path in buildHTTPPath() doesn't work properly,
	// buildHTTPPath() assumes the full path, but infact the URI is only "[...]/vote"
	// -> we fix this with an extra $use_http_path variable
	//    (It would be much cleaner to redefine ILIAS_HTTP_PATH, but it's a constant
	//    and cannot be changed)
	$protocol = (array_key_exists('HTTPS',$_SERVER) && $_SERVER['HTTPS'] == 'on' ) ? "https://" : "http://";
	$use_http_path = $protocol . $_SERVER['HTTP_HOST'] . substr(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH),0,-5); // substrs removes '/vote', five characters
} else  {
	echo "ASSERT FAILED; fix the code";
	exit;
}

// NOTE: current working dir is (must be) "Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting"
require_once('include.php');
require_once('classes/class.ilObjLiveVotingGUI.php');
require_once('classes/class.ilObjLiveVoting.php');
require_once('classes/class.ilLiveVotingPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingPlugin.php');
global $tpl, $ilTpl, $ilPluginAdmin, $lng;
$pl = new ilLiveVotingPlugin();
$tpl->addCss($pl->getStyleSheetLocation('pin_hider.css'));
$_GET['pin'] = str_ireplace('/', '', $_GET['pin']);
if ($_POST['pin'] || $_GET['pin']) {
	$pin = ((! $_POST['pin'] AND $_GET['pin']) ? $_GET['pin'] : $_POST['pin']);
	$link = ilObjLiveVotingGUI::_getLinkByPin($pin, $use_http_path);
	if ($link) {
//		var_dump($link); // FSX
		$pin = $_GET['pin'] ? $_GET['pin'] : $_POST['pin'];
		$obj = ilObjLiveVoting::_getObjectByPin($pin);
		if($obj->getOnline()) {
			header('Location: ' . $link . '');
		}
		else {
			ilUtil::sendFailure($pl->txt('is_not_online'));
		}
	} else {
		ilUtil::sendFailure($pl->txt('pin_doesnt_exists'));
	}
}
// $pin_url is the url to the this script. e.g "http://localhost/ilias_dev/Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/pin.php"
$pin_url = ($use_http_path === NULL ? ILIAS_HTTP_PATH : $use_http_path);
$pin_url .= "/Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/pin.php";

$tpl->getStandardTemplate();
$tpl_form = $pl->getTemplate('tpl.pin_form.html');
$tpl_form->setVariable('PIN_URL', $pin_url);
$tpl_form->setVariable('TXT_BUTTON', $lng->txt('rep_robj_xlvo_send_pin'));
$tpl_form->setVariable('PINFORM_INFO', $pl->txt('pinform_info'));
$tpl_form->setVariable('IL_LIVEVOTE_PINSIZE', ilObjLiveVoting::IL_LIVEVOTE_PINSIZE);
$tpl->addCSS('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/content.css'); // path must be relative to ilias-root dir, because it's in the html head
$tpl->setCurrentBlock('HeadBaseTag');
$tpl->setVariable('BASE', ($use_http_path === NULL ? ILIAS_HTTP_PATH ."/" : $use_http_path . "/" ) );
$tpl->parseCurrentBlock();
$tpl->setContent($tpl_form->get());
$tpl->show();

?>
