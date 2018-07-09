<?php

use LiveVoting\Conf\xlvoConf;
use LiveVoting\Conf\xlvoConfOld;
use LiveVoting\Option\xlvoData;
use LiveVoting\Option\xlvoOption;
use LiveVoting\Option\xlvoOptionOld;
use LiveVoting\Player\xlvoPlayer;
use LiveVoting\Round\xlvoRound;
use LiveVoting\User\xlvoVoteHistoryObject;
use LiveVoting\Vote\xlvoVote;
use LiveVoting\Vote\xlvoVoteOld;
use LiveVoting\Voter\xlvoVoter;
use LiveVoting\Voting\xlvoVoting;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * LiveVoting repository object plugin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version $Id$
 *
 */
class ilLiveVotingPlugin extends ilRepositoryObjectPlugin {

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
		$this->db->dropTable(xlvoConfOld::TABLE_NAME, false);
		$this->db->dropTable(xlvoVotingConfig::TABLE_NAME, false);
		$this->db->dropTable(xlvoData::TABLE_NAME, false);
		$this->db->dropTable(xlvoOption::TABLE_NAME, false);
		$this->db->dropTable(xlvoOptionOld::TABLE_NAME, false);
		$this->db->dropTable(xlvoPlayer::TABLE_NAME, false);
		$this->db->dropTable(xlvoRound::TABLE_NAME, false);
		$this->db->dropTable(xlvoVote::TABLE_NAME, false);
		$this->db->dropTable(xlvoVoteOld::TABLE_NAME, false);
		$this->db->dropTable(xlvoVoteHistoryObject::TABLE_NAME, false);
		$this->db->dropTable(xlvoVoting::TABLE_NAME, false);
		$this->db->dropTable(xlvoConf::TABLE_NAME, false);
		$this->db->dropTable(xlvoVoter::TABLE_NAME, false);

		return true;
	}


	//		/**
	//		 * @param string $key
	//		 * @return mixed|string
	//		 * @throws ilException
	//		 */
	//		public function txt($key) {
	//			require_once 'Customizing/global/plugins/Libraries/PluginTranslator/class.sragPluginTranslator.php;
	//
	//			return sragPluginTranslator::getInstance($this)->active()->write()->txt($key);
	//		}
}
