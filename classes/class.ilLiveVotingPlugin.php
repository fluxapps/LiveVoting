<?php

include_once('./Services/Repository/classes/class.ilRepositoryObjectPlugin.php');

/**
 * LiveVoting repository object plugin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version $Id$
 *
 */
class ilLiveVotingPlugin extends ilRepositoryObjectPlugin {

	const PLUGIN_NAME = 'LiveVoting';

	/**
	 * @var ilLiveVotingPlugin
	 */
	protected static $instance;


	/**
	 * @return ilLiveVotingPlugin
	 */
	public static function getInstance() {
		if (! isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}


	/**
	 * @return string
	 */
	public function getRootPath() {
		return substr(__FILE__, 0, strpos(__FILE__, 'classes/' . basename(__FILE__)));
	}

	/**
	 * @return string
	 */
	public function getConfigTableName() {
		return 'rep_robj_xlvo_conf';
	}

	public static function loadAR() {
		$ILIAS_AR = './Services/ActiveRecord/class.ActiveRecord.php';
		$CUSTOM_AR = './Customizing/global/plugins/Libraries/ActiveRecord/class.ActiveRecord.php';

		if (class_exists('ActiveRecord')) {
			return true;
		}

		if (class_exists('ActiveRecordList')) {
			return true;
		}

		if (is_file($ILIAS_AR)) {
			require_once($ILIAS_AR);
		} elseif (is_file($CUSTOM_AR)) {
			require_once($CUSTOM_AR);
		} else {
			throw new Exception('Please install ILIAS ActiveRecord or use ILIAS 5');
		}
	}
}

?>
