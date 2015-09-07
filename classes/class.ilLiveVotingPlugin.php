<?php

include_once('./Services/Repository/classes/class.ilRepositoryObjectPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoDynamicLanguage.php');

/**
 * LiveVoting repository object plugin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version $Id$
 *
 */
class ilLiveVotingPlugin extends ilRepositoryObjectPlugin implements xlvoDynamicLanguageInterface {

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
//			global $ilDB;
//			require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/sql/dbupdate.php');
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
	 * @param $a_var
	 *
	 * @return string
	 */
	public function txt($a_var, $real_call = false) {
		if ($real_call) {
			return parent::txt($a_var);
		} else {
			return xlvoDynamicLanguage::getInstance($this, xlvoDynamicLanguage::MODE_DEV)->txt($a_var);
		}
	}


	/**
	 * @return string
	 */
	public function getCsvPath() {
		return './Customizing/global/plugins/Services/Repository/RepositoryObject/OpenCast/lang/lang.csv';
	}


	/**
	 * @return string
	 */
	public function getAjaxLink() {
		return '';
		// TODO: Implement getAjaxLink() method.
	}
}

?>
