<?php
$path = stristr(__FILE__, 'Customizing', true);
switch (trim(shell_exec('hostname'))) {
	case 'ilias-webt1':
	case 'ilias-webn1':
	case 'ilias-webn2':
	case 'ilias-webn3':
		$path = '/var/www/ilias-4.3.x';
		break;
}
chdir($path);


require_once('include/inc.ilias_version.php');
require_once('Services/Component/classes/class.ilComponent.php');
if (ilComponent::isVersionGreaterString(ILIAS_VERSION_NUMERIC, '4.2.999')) {
	require_once('context/srContext.php');
	require_once('context/srInitialisation.php');
	srInitialisation::setContext(new srContext());
	srInitialisation::initILIAS();
} else {
	$_GET['baseClass'] = 'ilStartUpGUI';
	require_once('include/inc.get_pear.php');
	require_once('include/inc.header.php');
}

?>
