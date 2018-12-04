<?php
// Determine Client
if (is_file('client.txt')) {
	$_GET['client_id'] = trim(file_get_contents('client.txt'));
}

// Try to determine ILIAS-root
$directory = strstr($_SERVER['SCRIPT_FILENAME'], 'Customizing', true);
if (is_file('path.txt')) {
	$directory = trim(file_get_contents('path.txt'));
}

chdir($directory);
switch (false) {
	case is_dir($directory):
		throw new Exception('LiveVoting cannot determine correct directory. If your installation isn\'t located at \'' . $directory
			. '\', LiveVoting is currently unable to run');
	case is_file('./Services/Init/classes/class.ilInitialisation.php'):
		throw new Exception('LiveVoting cannot find ilInitialisation. If your installation isn\'t located at \'' . $directory
			. '\', LiveVoting is currently unable to run');
}
