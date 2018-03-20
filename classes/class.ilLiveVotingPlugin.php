<?php

require_once __DIR__ . '/../vendor/autoload.php';
include_once('./Services/Repository/classes/class.ilRepositoryObjectPlugin.php');

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
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}


	protected function uninstallCustom() {
		$tables = array(
			\LiveVoting\Conf\xlvoConfOld::TABLE_NAME,
			xlvoVotingConfig::TABLE_NAME,
			\LiveVoting\Option\xlvoData::TABLE_NAME,
			\LiveVoting\Option\xlvoData::TABLE_NAME . "_seq",
			\LiveVoting\Option\xlvoOption::TABLE_NAME,
			\LiveVoting\Option\xlvoOption::TABLE_NAME . "_seq",
			\LiveVoting\Option\xlvoOptionOld::TABLE_NAME,
			\LiveVoting\Option\xlvoOptionOld::TABLE_NAME . "_seq",
			\LiveVoting\Player\xlvoPlayer::TABLE_NAME,
			\LiveVoting\Player\xlvoPlayer::TABLE_NAME . "_seq",
			\LiveVoting\Round\xlvoRound::TABLE_NAME,
			\LiveVoting\Round\xlvoRound::TABLE_NAME . "_seq",
			\LiveVoting\Vote\xlvoVote::TABLE_NAME,
			\LiveVoting\Vote\xlvoVote::TABLE_NAME . "_seq",
			\LiveVoting\Vote\xlvoVoteOld::TABLE_NAME,
			\LiveVoting\Vote\xlvoVoteOld::TABLE_NAME . "_seq",
			\LiveVoting\User\xlvoVoteHistoryObject::TABLE_NAME,
			\LiveVoting\User\xlvoVoteHistoryObject::TABLE_NAME . "_seq",
			\LiveVoting\Voting\xlvoVoting::TABLE_NAME,
			\LiveVoting\Voting\xlvoVoting::TABLE_NAME . "_seq",
			\LiveVoting\Conf\xlvoConf::TABLE_NAME,
			\LiveVoting\Voter\xlvoVoter::TABLE_NAME,
			\LiveVoting\Voter\xlvoVoter::TABLE_NAME . "_seq",
		);
		global $DIC;
		$ilDB = $DIC->database();
		foreach ($tables as $table) {
			$substr = substr($table, - 4);
			if ($substr == '_seq') {
				$table_name_from_sequence = substr($table, 0, - 4);
				if ($ilDB->sequenceExists($table_name_from_sequence)) {
					$ilDB->dropSequence($table_name_from_sequence, false);
				}
			} else {
				$ilDB->dropTable($table, false);
			}
		}

		return true;
	}


	//		/**
	//		 * @param $key
	//		 * @return mixed|string
	//		 * @throws \ilException
	//		 */
	//		public function txt($key) {
	//			require_once('./Customizing/global/plugins/Libraries/PluginTranslator/class.sragPluginTranslator.php');
	//
	//			return sragPluginTranslator::getInstance($this)->active()->write()->txt($key);
	//		}
}
