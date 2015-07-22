<?php
$path = stristr(__FILE__, 'Customizing', true);

if (is_file('path')) {
	$path = file_get_contents('path');
}

chdir($path);

require_once('./include/inc.ilias_version.php');
require_once('./Services/Component/classes/class.ilComponent.php');
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
