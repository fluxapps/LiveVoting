<?php

use LiveVoting\Voting\xlvoVoting;

/**
 *
 */
class xlvoFreeInputVotingFormGUI extends xlvoVotingFormGUI {

	const USE_F_COLUMNS = false;


	/**
	 * @param xlvoVotingGUI $parent_gui
	 * @param xlvoVoting    $xlvoVoting
	 */
	public function __construct(xlvoVotingGUI $parent_gui, xlvoVoting $xlvoVoting) {
		parent::__construct($parent_gui, $xlvoVoting);
	}
}
