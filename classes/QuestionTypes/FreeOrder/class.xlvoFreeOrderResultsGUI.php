<?php

/**
 * Class xlvoFreeOrderResultsGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoFreeOrderResultsGUI extends xlvoCorrectOrderResultsGUI
{

	/**
	 * @return string
	 */
	public function getHTML()
	{
		$bars = new xlvoBarCollectionGUI();
		$bars->setShowTotalVoters(true);
		$total_voters = $this->manager->countVoters();
		$bars->setTotalVoters($total_voters);

		$option_amount = $this->manager->countOptions();
		$option_weight = array();

		foreach ($this->manager->getVotesOfVoting() as $xlvoVote)
		{
			$option_amount2 = $option_amount;
			foreach (json_decode($xlvoVote->getFreeInput()) as $option_id)
			{
				$option_weight[$option_id] = $option_weight[$option_id] + $option_amount2;
				$option_amount2 --;
			}
		}
		
		$possible_max = $option_amount;
		// Sort button if selected
		if ($this->isShowCorrectOrder() && $this->manager->hasVotes())
		{
			$unsorted_options = $this->manager->getOptions();
			$options = array();
			arsort($option_weight);
			foreach ($option_weight as $option_id => $weight)
			{
				$options[] = $unsorted_options[$option_id];
			}
		} else
		{
			$options = $this->manager->getOptions();
		}

		// Add bars
		foreach ($options as $xlvoOption)
		{
			$xlvoBarPercentageGUI = new xlvoBarPercentageGUI();
			$xlvoBarPercentageGUI->setRound(2);
			$xlvoBarPercentageGUI->setShowInPercent(false);
			$xlvoBarPercentageGUI->setMaxVotes($possible_max);
			$xlvoBarPercentageGUI->setTitle($xlvoOption->getTextForPresentation());
			$xlvoBarPercentageGUI->setVotes($option_weight[$xlvoOption->getId()] / $total_voters);
			$xlvoBarPercentageGUI->setOptionLetter($xlvoOption->getCipher());

			$bars->addBar($xlvoBarPercentageGUI);
		}

		return $bars->getHTML();
	}


	/**
	 * @return string
	 */
	public function getHTML2()
	{
		$bars = new xlvoBarCollectionGUI();

		$option_amount = $this->manager->countOptions();
		$option_weight = array();

		foreach ($this->manager->getVotesOfVoting() as $xlvoVote)
		{
			$option_amount2 = $option_amount;
			foreach (json_decode($xlvoVote->getFreeInput()) as $option_id)
			{
				$option_weight[$option_id] = $option_weight[$option_id] + $option_amount2;
				$option_amount2 --;
			}
		}

		$total = array_sum($option_weight);
		if ($this->isShowCorrectOrder() && $this->manager->hasVotes())
		{
			$unsorted_options = $this->manager->getOptions();
			$options = array();
			arsort($option_weight);
			foreach ($option_weight as $option_id => $weight)
			{
				$options[] = $unsorted_options[$option_id];
			}
		} else
		{
			$options = $this->manager->getOptions();
		}

		$absolute = $this->isShowAbsolute();

		foreach ($options as $xlvoOption)
		{
			$xlvoBarPercentageGUI = new xlvoBarPercentageGUI();
			$xlvoBarPercentageGUI->setTotal($total);
			$xlvoBarPercentageGUI->setTitle($xlvoOption->getTextForPresentation());
			$xlvoBarPercentageGUI->setId($xlvoOption->getId());
			$xlvoBarPercentageGUI->setVotes($option_weight[$xlvoOption->getId()]);
			if ($option_weight)
			{
				$xlvoBarPercentageGUI->setMax(max($option_weight));
			}
			$xlvoBarPercentageGUI->setShowAbsolute($absolute);
			$xlvoBarPercentageGUI->setOptionLetter($xlvoOption->getCipher());

			$bars->addBar($xlvoBarPercentageGUI);
		}

		$bars->setShowTotalVotes(true);
		$bars->setTotalVotes($this->manager->countVotes());

		return $bars->getHTML();
	}
}
