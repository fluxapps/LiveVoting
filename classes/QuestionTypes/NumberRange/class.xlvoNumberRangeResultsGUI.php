<?php
use LiveVoting\Vote\xlvoVote;

/**
 * Class xlvoNumberRangeResultsGUI
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class xlvoNumberRangeResultsGUI extends xlvoInputResultsGUI{

	const BAR_COUNT = 5;
	
	public function getHTML() {
		$values = $this->getAllVoteValues();

		$bars = new xlvoBarCollectionGUI();
		$voteSum = array_sum($values);

		foreach ($values as $key => $value) {
			$bar = new xlvoBarPercentageGUI();
			$bar->setMaxVotes($voteSum);
			$bar->setVotes($value);
			$bar->setTitle($key);
			$bars->addBar($bar);
		}

		return $bars->getHTML();
	}


	/**
	 * Creates a CSV of the given votes.
	 *
	 * @param xlvoVote[] $votes An array of votes which should be parsed into a string representation.
	 *
	 * @return string   The string representation of the votes.
	 */
	public function getTextRepresentationForVotes($votes) {
		$result = xlvoNumberRangeResultGUI::getInstance($this->voting);
		return $result->getTextRepresentation($votes);
	}


	/**
	 * Fetches all data and simplifies them to an array with 10 values.
	 *
	 * The array keys indicates the range and the value reflects the sum of all votes within it.
	 *
	 * @return string[]
	 */
	private function getAllVoteValues()
	{
		$percentage = ((int)$this->manager->getVoting()->getPercentage() === 1) ? '%' : '';

		//generate array which is equal in its length to the range from start to end
		$start = $this->manager->getVoting()->getStartRange();
		$end = $this->manager->getVoting()->getEndRange();
		$count = ($end - $start) + 1;
		$values = array_fill($start, $count, 0);

		$votes = $this->manager->getVotesOfVoting();

		//count all votes per option
		/**
		 * @var xlvoVote $vote
		 */
		foreach ($votes as $vote)
		{
			$value = (int)$vote->getFreeInput();
			$values[$value]++;
		}

		//Create 10 slices and sum each slice
		$slices = [];
		$sliceWidth = ceil($count / self::BAR_COUNT);

		for($i = 0; $i < $count; $i += $sliceWidth)
		{
			//create a slice
			$slice = array_slice($values, $i, $sliceWidth, true);

			//sum slice values
			$sum = array_sum($slice);

			//fetch keys to generate new key for slices
			$keys = array_keys($slice);
			$keyCount = count($keys);

			//only display a range if we got more than one element
			if($keyCount > 1)
				$key = "{$keys[0]}$percentage - {$keys[$keyCount - 1]}$percentage";
			else
				$key = "{$keys[0]}$percentage";

			//create now slice entry
			$slices[$key] = $sum;
		}


		return $slices;
	}
}