<?php

include_once('./Services/Repository/classes/class.ilRepositoryObjectPlugin.php');
require_once('class.ilLiveVotingConfig.php');

/**
 * LiveVoting repository object plugin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version $Id$
 *
 */
class ilLiveVotingPlugin extends ilRepositoryObjectPlugin {

	/**
	 * @var ilLiveVotingPlugin
	 */
	protected static $instance;


	/**
	 * @return ilLiveVotingPlugin
	 */
	public static function getInstance() {
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * @return string
	 */
	function getPluginName() {
		return 'LiveVoting';
	}


	/**
	 * @return string
	 */
	public function getRootPath() {
		return substr(__FILE__, 0, strpos(__FILE__, 'classes/' . basename(__FILE__)));
	}


	/**
	 * @return ilLiveVotingConfig
	 */
	public function getConfigObject() {
		$conf = new ilLiveVotingConfig($this->getConfigTableName());

		return $conf;
	}


	/**
	 * @return string
	 */
	public function getConfigTableName() {
		return 'rep_robj_xlvo_conf';
	}
}

?>
