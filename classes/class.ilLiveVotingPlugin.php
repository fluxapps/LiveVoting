<?php

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * LiveVoting repository object plugin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version $Id$
 *
 */
class ilLiveVotingPlugin extends \ilRepositoryObjectPlugin {

	const PLUGIN_ID = 'xlvo';
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
	 * @var ilDB
	 */
	protected $db;


	/**
	 *
	 */
	public function __construct() {
		parent::__construct();

		global $DIC;

		$this->db = $DIC->database();
	}


	/**
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}


	/**
	 * @return bool
	 */
	protected function uninstallCustom() {
		$this->db->dropTable(\LiveVoting\Conf\xlvoConfOld::TABLE_NAME, false);
		$this->db->dropTable(xlvoVotingConfig::TABLE_NAME, false);
		$this->db->dropTable(\LiveVoting\Option\xlvoData::TABLE_NAME, false);
		$this->db->dropTable(\LiveVoting\Option\xlvoOption::TABLE_NAME, false);
		$this->db->dropTable(\LiveVoting\Option\xlvoOptionOld::TABLE_NAME, false);
		$this->db->dropTable(\LiveVoting\Player\xlvoPlayer::TABLE_NAME, false);
		$this->db->dropTable(\LiveVoting\Round\xlvoRound::TABLE_NAME, false);
		$this->db->dropTable(\LiveVoting\Vote\xlvoVote::TABLE_NAME, false);
		$this->db->dropTable(\LiveVoting\Vote\xlvoVoteOld::TABLE_NAME, false);
		$this->db->dropTable(\LiveVoting\User\xlvoVoteHistoryObject::TABLE_NAME, false);
		$this->db->dropTable(\LiveVoting\Voting\xlvoVoting::TABLE_NAME, false);
		$this->db->dropTable(\LiveVoting\Conf\xlvoConf::TABLE_NAME, false);
		$this->db->dropTable(\LiveVoting\Voter\xlvoVoter::TABLE_NAME, false);

		return true;
	}


	//		/**
	//		 * @param $key
	//		 * @return mixed|string
	//		 * @throws \ilException
	//		 */
	//		public function txt($key) {
	//			require_once 'Customizing/global/plugins/Libraries/PluginTranslator/class.sragPluginTranslator.php;
	//
	//			return sragPluginTranslator::getInstance($this)->active()->write()->txt($key);
	//		}
}
