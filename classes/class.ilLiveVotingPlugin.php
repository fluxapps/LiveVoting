<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/OpenCast/classes/class.xoctDynLan.php');
include_once('./Services/Repository/classes/class.ilRepositoryObjectPlugin.php');

/**
 * LiveVoting repository object plugin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version $Id$
 *
 */
class ilLiveVotingPlugin extends ilRepositoryObjectPlugin implements xoctDynLanInterface {

	const PLUGIN_NAME = 'LiveVoting';
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
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}


	protected function uninstallCustom() {
		// TODO: Implement uninstallCustom() method.

	}


	/**
	 * @return string
	 */
	public function getCsvPath() {
		return './Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/lang/lang.csv';
	}


	/**
	 * @return string
	 */
	public function getAjaxLink() {
		return NULL;
	}


	/**
	 * @param $a_var
	 *
	 * @return string
	 */
	public function txt22($a_var) {
		return xoctDynLan::getInstance($this, xoctDynLan::MODE_DEV)->txt($a_var);
	}
}
