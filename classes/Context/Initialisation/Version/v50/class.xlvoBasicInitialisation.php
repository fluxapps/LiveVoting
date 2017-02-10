<?php

namespace LiveVoting\Context\Initialisation\Version\v50;

use LiveVoting\Conf\xlvoConf;
use LiveVoting\Context\cookie\CookieManager;
use LiveVoting\Context\xlvoContext;
use LiveVoting\Context\xlvoDummyUser;
use LiveVoting\Context\xlvoILIAS;
use LiveVoting\Context\xlvoObjectDefinition;
use LiveVoting\Context\xlvoRbacReview;
use LiveVoting\xlvoSessionHandler;

/**
 * Class xlvoBasicInitialisation for ILIAS 5.0
 *
 * @author      Nicolas Schaefli <ns@studer-raimann.ch>
 *
 * @description Initializes a minimal ILIAS environment.
 */
class xlvoBasicInitialisation {

	/**
	 * xlvoInitialisation constructor.
	 *
	 * @param int $context
	 */
	protected function __construct($context = null) {
		if ($context) {
			CookieManager::setContext($context);
		}

		$this->bootstrapApp();
	}


	/**
	 * @param int $context
	 *
	 * @return xlvoBasicInitialisation
	 */
	public static function init($context = null) {
		return new self($context);
	}


	private function bootstrapApp() {
		//init context
		xlvoContext::init(xlvoContext::CONTEXT_PIN);

		//bootstrap ILIAS
		$this->removeUnsafeCharacters();
		$this->loadIniFile();
		$this->requireCommonIncludes();
		$this->initErrorHandling();
		$this->determineClient();
		$this->loadClientIniFile();
		$this->initDatabase();
		// $this->initLog();
		$this->initSessionHandler();
		$this->initSettings();  //required
		$this->buildHTTPPath();
		$this->initLocale();
		$this->initLanguage();
		$this->initDataCache();
		$this->initObjectDefinition();
		$this->initControllFlow();
		$this->initPluginAdmin();
		$this->initTemplate();
		$this->initUser();
		$this->initRbac();
		//$this->setCookieParams();
	}


	/**
	 * Remove unsafe characters from GET
	 */
	protected function removeUnsafeCharacters() {
		// Remove unsafe characters from GET parameters.
		// We do not need this characters in any case, so it is
		// feasible to filter them everytime. POST parameters
		// need attention through ilUtil::stripSlashes() and similar functions)
		if (is_array($_GET)) {
			foreach ($_GET as $k => $v) {
				// \r\n used for IMAP MX Injection
				// ' used for SQL Injection
				$_GET[$k] = str_replace(array(
					"\x00",
					"\n",
					"\r",
					"\\",
					"'",
					'"',
					"\x1a",
				), "", $v);

				// this one is for XSS of any kind
				$_GET[$k] = strip_tags($_GET[$k]);
			}
		}
	}


	private function initTemplate() {
		$ilias = new xlvoILIAS();
		$this->makeGlobal("ilias", $ilias);

		$tpl = new \ilTemplate("tpl.main.html", true, true, 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting');
		$tpl->addCss('./templates/default/delos.css');
		$tpl->addBlockFile("CONTENT", "content", "tpl.main_voter.html", 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting');
		$tpl->setVariable('BASE', xlvoConf::getBaseURL());
		$this->makeGlobal("tpl", $tpl);

		include_once("./Services/jQuery/classes/class.iljQueryUtil.php");
		\iljQueryUtil::initjQuery();
		include_once("./Services/UICore/classes/class.ilUIFramework.php");
		\ilUIFramework::init();

		require_once "./Services/User/classes/class.ilObjUser.php";
		require_once "./Services/UIComponent/Toolbar/classes/class.ilToolbarGUI.php";

		$ilToolbar = new \ilToolbarGUI();
		$this->makeGlobal("ilToolbar", $ilToolbar);
	}


	/**
	 * initialise database object $ilDB
	 *
	 */
	private function initDatabase() {
		// build dsn of database connection and connect
		require_once("./Services/Database/classes/class.ilDBWrapperFactory.php");
		$ilDB = \ilDBWrapperFactory::getWrapper(IL_DB_TYPE);
		$ilDB->initFromIniFile();
		$ilDB->connect();

		$this->makeGlobal("ilDB", $ilDB);
	}


	/**
	 * Load ilias ini.
	 */
	private function loadIniFile() {
		require_once("./Services/Init/classes/class.ilIniFile.php");
		$ilIliasIniFile = new \ilIniFile("./ilias.ini.php");
		$ilIliasIniFile->read();
		$this->makeGlobal('ilIliasIniFile', $ilIliasIniFile);

		// initialize constants
		define("ILIAS_DATA_DIR", $ilIliasIniFile->readVariable("clients", "datadir"));
		define("ILIAS_WEB_DIR", $ilIliasIniFile->readVariable("clients", "path"));
		define("ILIAS_ABSOLUTE_PATH", $ilIliasIniFile->readVariable('server', 'absolute_path'));

		// logging
		define("ILIAS_LOG_DIR", $ilIliasIniFile->readVariable("log", "path"));
		define("ILIAS_LOG_FILE", $ilIliasIniFile->readVariable("log", "file"));
		define("ILIAS_LOG_ENABLED", $ilIliasIniFile->readVariable("log", "enabled"));
		define("ILIAS_LOG_LEVEL", $ilIliasIniFile->readVariable("log", "level"));
		define("SLOW_REQUEST_TIME", $ilIliasIniFile->readVariable("log", "slow_request_time"));

		// read path + command for third party tools from ilias.ini
		define("PATH_TO_CONVERT", $ilIliasIniFile->readVariable("tools", "convert"));
		define("PATH_TO_FFMPEG", $ilIliasIniFile->readVariable("tools", "ffmpeg"));
		define("PATH_TO_ZIP", $ilIliasIniFile->readVariable("tools", "zip"));
		define("PATH_TO_MKISOFS", $ilIliasIniFile->readVariable("tools", "mkisofs"));
		define("PATH_TO_UNZIP", $ilIliasIniFile->readVariable("tools", "unzip"));
		define("PATH_TO_GHOSTSCRIPT", $ilIliasIniFile->readVariable("tools", "ghostscript"));
		define("PATH_TO_JAVA", $ilIliasIniFile->readVariable("tools", "java"));
		define("PATH_TO_HTMLDOC", $ilIliasIniFile->readVariable("tools", "htmldoc"));
		define("URL_TO_LATEX", $ilIliasIniFile->readVariable("tools", "latex"));
		define("PATH_TO_FOP", $ilIliasIniFile->readVariable("tools", "fop"));

		// read virus scanner settings
		switch ($ilIliasIniFile->readVariable("tools", "vscantype")) {
			case "sophos":
				define("IL_VIRUS_SCANNER", "Sophos");
				define("IL_VIRUS_SCAN_COMMAND", $ilIliasIniFile->readVariable("tools", "scancommand"));
				define("IL_VIRUS_CLEAN_COMMAND", $ilIliasIniFile->readVariable("tools", "cleancommand"));
				break;

			case "antivir":
				define("IL_VIRUS_SCANNER", "AntiVir");
				define("IL_VIRUS_SCAN_COMMAND", $ilIliasIniFile->readVariable("tools", "scancommand"));
				define("IL_VIRUS_CLEAN_COMMAND", $ilIliasIniFile->readVariable("tools", "cleancommand"));
				break;

			case "clamav":
				define("IL_VIRUS_SCANNER", "ClamAV");
				define("IL_VIRUS_SCAN_COMMAND", $ilIliasIniFile->readVariable("tools", "scancommand"));
				define("IL_VIRUS_CLEAN_COMMAND", $ilIliasIniFile->readVariable("tools", "cleancommand"));
				break;

			default:
				define("IL_VIRUS_SCANNER", "None");
				break;
		}

		include_once './Services/Calendar/classes/class.ilTimeZone.php';
		$tz = $ilIliasIniFile->readVariable('server', 'timezone');
		if (!strlen($tz)) {
			$tz = \ilTimeZone::_getDefaultTimeZone();
		}
		if (!strlen($tz)) {
			$tz = 'UTC';
		}
		date_default_timezone_set($tz);

		define("IL_TIMEZONE", $tz);
		define('IL_INITIAL_WD', getcwd());
	}


	/**
	 * Load ilias client ini.
	 *
	 * @return bool
	 */
	private function loadClientIniFile() {
		global $ilIliasIniFile;

		$ini_file = "./" . ILIAS_WEB_DIR . "/" . CLIENT_ID . "/client.ini.php";

		// get settings from ini file
		require_once("./Services/Init/classes/class.ilIniFile.php");
		$ilClientIniFile = new \ilIniFile($ini_file);
		$ilClientIniFile->read();

		// invalid client id / client ini
		if ($ilClientIniFile->ERROR != "") {
			$default_client = $ilIliasIniFile->readVariable("clients", "default");
			\ilUtil::setCookie("ilClientId", $default_client);
		}

		$this->makeGlobal("ilClientIniFile", $ilClientIniFile);

		// set constants
		define("SESSION_REMINDER_LEADTIME", 30);
		define("DEBUG", $ilClientIniFile->readVariable("system", "DEBUG"));
		define("DEVMODE", $ilClientIniFile->readVariable("system", "DEVMODE"));
		define("SHOWNOTICES", $ilClientIniFile->readVariable("system", "SHOWNOTICES"));
		define("DEBUGTOOLS", $ilClientIniFile->readVariable("system", "DEBUGTOOLS"));
		define("ROOT_FOLDER_ID", $ilClientIniFile->readVariable('system', 'ROOT_FOLDER_ID'));
		define("SYSTEM_FOLDER_ID", $ilClientIniFile->readVariable('system', 'SYSTEM_FOLDER_ID'));
		define("ROLE_FOLDER_ID", $ilClientIniFile->readVariable('system', 'ROLE_FOLDER_ID'));
		define("MAIL_SETTINGS_ID", $ilClientIniFile->readVariable('system', 'MAIL_SETTINGS_ID'));
		$error_handler = $ilClientIniFile->readVariable('system', 'ERROR_HANDLER');
		define("ERROR_HANDLER", $error_handler ? $error_handler : "PRETTY_PAGE");
		$log_error_trace = $ilClientIniFile->readVariable('system', 'LOG_ERROR_TRACE');
		define("LOG_ERROR_TRACE", $log_error_trace ? $log_error_trace : false);

		// this is for the online help installation, which sets OH_REF_ID to the
		// ref id of the online module
		define("OH_REF_ID", $ilClientIniFile->readVariable("system", "OH_REF_ID"));

		define("SYSTEM_MAIL_ADDRESS", $ilClientIniFile->readVariable('system', 'MAIL_SENT_ADDRESS')); // Change SS
		define("MAIL_REPLY_WARNING", $ilClientIniFile->readVariable('system', 'MAIL_REPLY_WARNING')); // Change SS

		define("CLIENT_DATA_DIR", ILIAS_DATA_DIR . "/" . CLIENT_ID);
		define("CLIENT_WEB_DIR", ILIAS_ABSOLUTE_PATH . "/" . ILIAS_WEB_DIR . "/" . CLIENT_ID);
		define("CLIENT_NAME", $ilClientIniFile->readVariable('client', 'name')); // Change SS

		$val = $ilClientIniFile->readVariable("db", "type");
		if ($val == "") {
			define("IL_DB_TYPE", "mysql");
		} else {
			define("IL_DB_TYPE", $val);
		}

		return true;
	}


	/**
	 * Init dummy session handling
	 */
	private function initSessionHandler() {
		$session = new xlvoSessionHandler();

		session_set_save_handler(array(
			&$session,
			"open",
		), array(
			&$session,
			"close",
		), array(
			&$session,
			"read",
		), array(
			&$session,
			"write",
		), array(
			&$session,
			"destroy",
		), array(
			&$session,
			"gc",
		));

		session_start();
		require_once('./Services/Authentication/classes/class.ilSession.php');
	}


	/**
	 * Init some ilias settings (required for locale)
	 */
	private function initSettings() {
		global $ilSetting;

		require_once "Services/Administration/classes/class.ilSetting.php";
		$this->makeGlobal("ilSetting", new \ilSetting());

		// set anonymous user & role id and system role id
		define("ANONYMOUS_USER_ID", $ilSetting->get("anonymous_user_id"));
		define("ANONYMOUS_ROLE_ID", $ilSetting->get("anonymous_role_id"));
		define("SYSTEM_USER_ID", $ilSetting->get("system_user_id"));
		define("SYSTEM_ROLE_ID", $ilSetting->get("system_role_id"));
		define("USER_FOLDER_ID", 7);

		// recovery folder
		define("RECOVERY_FOLDER_ID", $ilSetting->get("recovery_folder_id"));

		// installation id
		define("IL_INST_ID", $ilSetting->get("inst_id", 0));

		// define default suffix replacements
		define("SUFFIX_REPL_DEFAULT", "php,php3,php4,inc,lang,phtml,htaccess");
		define("SUFFIX_REPL_ADDITIONAL", $ilSetting->get("suffix_repl_additional"));

		// payment setting
		define('IS_PAYMENT_ENABLED', false);
	}


	/**
	 * Include the required stuff for ilias.
	 */
	private function requireCommonIncludes() {
		require_once "Services/Component/classes/class.ilComponent.php";

		// pear
		require_once("include/inc.get_pear.php");
		require_once("include/inc.check_pear.php");
		require_once "PEAR.php";

		// ilTemplate
		if (\ilContext::usesTemplate()) {
			// HTML_Template_IT support
			require_once "HTML/Template/ITX.php";
			require_once "./Services/UICore/classes/class.ilTemplateHTMLITX.php";
			require_once "./Services/UICore/classes/class.ilTemplate.php";
		}

		// really always required?
		require_once "./Services/Utilities/classes/class.ilUtil.php";
		require_once "./Services/Utilities/classes/class.ilFormat.php";
		require_once "./Services/Calendar/classes/class.ilDatePresentation.php";
		require_once "include/inc.ilias_version.php";

		require_once './Services/Utilities/classes/class.ilBenchmark.php';
		$this->makeGlobal("ilBench", new \ilBenchmark());
	}


	/**
	 * Init Locale
	 */
	private function initLocale() {
		global $ilSetting;

		if (trim($ilSetting->get("locale") != "")) {
			$larr = explode(",", trim($ilSetting->get("locale")));
			$ls = array();
			$first = $larr[0];
			foreach ($larr as $l) {
				if (trim($l) != "") {
					$ls[] = $l;
				}
			}
			if (count($ls) > 0) {
				setlocale(LC_ALL, $ls);

				// #15347 - making sure that floats are not changed
				setlocale(LC_NUMERIC, "C");

				if (class_exists("Collator")) {
					$this->makeGlobal("ilCollator", new \Collator($first));
				}
			}
		}
	}


	/**
	 * $lng initialisation
	 */
	private function initLanguage() {
		global $ilSetting;

		$lang = (isset($_GET['lang']) && $_GET['lang']) ? $_GET['lang'] : \ilSession::get('lang');

		\ilSession::set('lang', $lang);

		// check whether lang selection is valid
		require_once "./Services/Language/classes/class.ilLanguage.php";
		$langs = \ilLanguage::getInstalledLanguages();
		if (!in_array($lang, $langs)) {
			if (is_object($ilSetting) && $ilSetting->get('language') != '') {
				\ilSession::set('lang', $ilSetting->get('language'));
			} else {
				\ilSession::set('lang', $langs[0]);
			}
		}

		$lang = \ilSession::get('lang');
		$_GET['lang'] = $lang;

		$lng = new \ilLanguage($lang);
		$this->makeGlobal('lng', $lng);
	}


	/**
	 * Build the http path for ILIAS
	 *
	 * @return mixed
	 */
	private function buildHTTPPath() {
		include_once './Services/Http/classes/class.ilHTTPS.php';
		$https = new \ilHTTPS();
		$this->makeGlobal("https", $https);

		if ($https->isDetected()) {
			$protocol = 'https://';
		} else {
			$protocol = 'http://';
		}
		$host = $_SERVER['HTTP_HOST'];

		$rq_uri = $_SERVER['REQUEST_URI'];

		// security fix: this failed, if the URI contained "?" and following "/"
		// -> we remove everything after "?"
		if (is_int($pos = strpos($rq_uri, "?"))) {
			$rq_uri = substr($rq_uri, 0, $pos);
		}

		if (!defined('ILIAS_MODULE')) {
			$path = pathinfo($rq_uri);
			if (!$path['extension']) {
				$uri = $rq_uri;
			} else {
				$uri = dirname($rq_uri);
			}
		} else {
			// if in module remove module name from HTTP_PATH
			$path = dirname($rq_uri);

			// dirname cuts the last directory from a directory path e.g content/classes return content

			$module = \ilUtil::removeTrailingPathSeparators(ILIAS_MODULE);

			$dirs = explode('/', $module);
			$uri = $path;
			foreach ($dirs as $dir) {
				$uri = dirname($uri);
			}
		}

		$https->enableSecureCookies();
		$https->checkPort();

		return define('ILIAS_HTTP_PATH', \ilUtil::removeTrailingPathSeparators($protocol . $host
		                                                                       . $uri));
	}


	/**
	 * Init ilias error handling
	 */
	private function initErrorHandling() {
		global $ilErr;

		error_reporting(((ini_get("error_reporting")) & ~E_DEPRECATED) & ~E_STRICT);

		// error handler
		require_once "./Services/Init/classes/class.ilErrorHandling.php";
		$this->makeGlobal("ilErr", new \ilErrorHandling());
		$ilErr->setErrorHandling(PEAR_ERROR_CALLBACK, array( $ilErr, 'errorHandler' ));
	}


	/**
	 * Init ilias data cache.
	 */
	private function initDataCache() {
		require_once "./Services/Object/classes/class.ilObjectDataCache.php";
		$this->makeGlobal("ilObjDataCache", new \ilObjectDataCache());
	}


	/**
	 * Init ilias object definition.
	 */
	private function initObjectDefinition() {
		$this->makeGlobal("objDefinition", new xlvoObjectDefinition());
	}


	private function initControllFlow() {
		require_once "./Services/UICore/classes/class.ilCtrl.php";
		$this->makeGlobal("ilCtrl", new \ilCtrl());
	}


	private function initPluginAdmin() {
		require_once "./Services/Component/classes/class.ilPluginAdmin.php";
		$this->makeGlobal("ilPluginAdmin", new \ilPluginAdmin());
	}


	/**
	 * Init log instance
	 */
	private function initLog() {
		include_once './Services/Logging/classes/public/class.ilLoggerFactory.php';
		$log = \ilLoggerFactory::getRootLogger();

		$this->makeGlobal("ilLog", $log);
		// deprecated
		$this->makeGlobal("log", $log);
	}


	/**
	 * set session cookie params for path, domain, etc.
	 */
	private function setCookieParams() {
		global $ilSetting;

		$GLOBALS['COOKIE_PATH'] = '/';
		$cookie_path = '/';

		/* if ilias is called directly within the docroot $cookie_path
		is set to '/' expecting on servers running under windows..
		here it is set to '\'.
		in both cases a further '/' won't be appended due to the following regex
		*/
		$cookie_path .= (!preg_match("/[\\/|\\\\]$/", $cookie_path)) ? "/" : "";

		if ($cookie_path == "\\") {
			$cookie_path = '/';
		}

		include_once './Services/Http/classes/class.ilHTTPS.php';
		$cookie_secure = !$ilSetting->get('https', 0) && \ilHTTPS::getInstance()->isDetected();

		define('IL_COOKIE_EXPIRE', 0);
		define('IL_COOKIE_PATH', $cookie_path);
		define('IL_COOKIE_DOMAIN', '');
		define('IL_COOKIE_SECURE', $cookie_secure); // Default Value

		define('IL_COOKIE_HTTPONLY', true); // Default Value
		session_set_cookie_params(IL_COOKIE_EXPIRE, IL_COOKIE_PATH, IL_COOKIE_DOMAIN, IL_COOKIE_SECURE, IL_COOKIE_HTTPONLY);
	}


	/**
	 * This method determines the current client and sets the
	 * constant CLIENT_ID.
	 */
	private function determineClient() {
		global $ilIliasIniFile;

		// check whether ini file object exists
		if (!is_object($ilIliasIniFile)) {
			throw new \Exception("Fatal Error: ilInitialisation::determineClient called without initialisation of ILIAS ini file object.");
		}

		// set to default client if empty
		if ($_GET["client_id"] != "") {
			$_GET["client_id"] = \ilUtil::stripSlashes($_GET["client_id"]);
			if (!defined("IL_PHPUNIT_TEST")) {
				\ilUtil::setCookie("ilClientId", $_GET["client_id"]);
			}
		} else {
			if (!$_COOKIE["ilClientId"]) {
				// to do: ilias ini raus nehmen
				$client_id = $ilIliasIniFile->readVariable("clients", "default");
				\ilUtil::setCookie("ilClientId", $client_id);
			}
		}
		if (!defined("IL_PHPUNIT_TEST")) {
			define("CLIENT_ID", $_COOKIE["ilClientId"]);
		} else {
			define("CLIENT_ID", $_GET["client_id"]);
		}
	}


	/**
	 * Create or override a global variable.
	 *
	 * @param string $name The name of the global variable.
	 * @param object $value The value where the global variable should point at.
	 */
	private function makeGlobal($name, $value) {
		$GLOBALS[$name] = $value;
	}


	/**
	 * Initialise a fake user service to satisfy the help system module.
	 */
	private function initUser() {
		$this->makeGlobal('ilUser', new xlvoDummyUser());
	}


	/**
	 * Initialise a fake rbac to satisfy other plugins
	 */
	private function initRbac() {
		$this->makeGlobal('rbacreview', new xlvoRbacReview());
	}
}
