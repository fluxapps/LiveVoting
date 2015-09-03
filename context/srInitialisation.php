<?php

include_once("Services/Init/classes/class.ilInitialisation.php");

/**
 * Class srInitialisation
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class srInitialisation extends ilInitialisation {

	const USE_OWN_GLOBAL_TPL = false;
	/**
	 * @var string
	 */
	protected static $context;


	/**
	 * @param ilContext
	 */
	public static function setContext($context) {
		srContext::init($context);
		self::$context = $context;
	}


	public static function initILIAS() {
		parent::initILIAS();
		self::overrideGlobalTpl();
	}


	protected static function overrideGlobalTpl() {
		if (self::USE_OWN_GLOBAL_TPL) {
			$tpl = new ilTemplate("tpl.main.html", true, true, 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting');
			$tpl->addCss('./templates/default/delos.css');
			$tpl->addBlockFile("CONTENT", "content", "tpl.main_voter.html", 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting');

			self::initGlobal("tpl", $tpl);
		}
		global $tpl;
		if (! self::USE_OWN_GLOBAL_TPL) {

			$tpl->getStandardTemplate();
		}
		$tpl->setVariable('BASE', '/'); // FSX TODO set to real root
		if (self::USE_OWN_GLOBAL_TPL) {
			include_once("./Services/jQuery/classes/class.iljQueryUtil.php");
			iljQueryUtil::initjQuery();
			include_once("./Services/UICore/classes/class.ilUIFramework.php");
			ilUIFramework::init();
		}
	}
}
