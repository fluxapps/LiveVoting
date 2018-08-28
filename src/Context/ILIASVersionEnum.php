<?php

namespace LiveVoting\Context;

use ilLiveVotingPlugin;
use srag\DIC\DICTrait;

/**
 * Class ILIASVersionEnum
 *
 * @package LiveVoting\Context
 */
final class ILIASVersionEnum {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
	const ILIAS_VERSION_5_0 = 0;
	const ILIAS_VERSION_5_1 = 1;
	const ILIAS_VERSION_5_2 = 2;
	const ILIAS_VERSION_5_3 = 3;


	private function __construct() {

	}
}