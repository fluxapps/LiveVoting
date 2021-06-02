<?php

namespace LiveVoting\Context\Initialisation\Version\v7;

require_once './include/inc.ilias_version.php';

use Closure;
use Collator;
use Exception;
use ilAccess;
use ilAppEventHandler;
use ilBenchmark;
use ilCtrl;
use ilDBWrapperFactory;
use ilErrorHandling;
use ilGlobalCache;
use ilGlobalCacheSettings;
use ilGlobalTemplate;
use ilHelp;
use ilHTTPS;
use ILIAS\DI\Container;
use ILIAS\DI\HTTPServices;
use ILIAS\HTTP\Cookies\CookieJarFactoryImpl;
use ILIAS\HTTP\Request\RequestFactoryImpl;
use ILIAS\HTTP\Response\ResponseFactoryImpl;
use ILIAS\HTTP\Response\Sender\DefaultResponseSenderStrategy;
use ilIniFile;
use ilInitialisation;
use iljQueryUtil;
use ilLanguage;
use ilLiveVotingPlugin;
use ilLoggerFactory;
use ilMailMimeSenderFactory;
use ilMailMimeTransportFactory;
use ilMainMenuGUI;
use ilNavigationHistory;
use ilObjectDataCache;
use ilPluginAdmin;
use ilSetting;
use ilTabsGUI;
use ilTimeZone;
use ilToolbarGUI;
use ilTree;
use ilUIFramework;
use ilUtil;
use LiveVoting\Conf\xlvoConf;
use LiveVoting\Context\Param\ParamManager;
use LiveVoting\Context\xlvoContext;
use LiveVoting\Context\xlvoDummyUser54;
use LiveVoting\Context\xlvoILIAS;
use LiveVoting\Context\xlvoObjectDefinition;
use LiveVoting\Context\xlvoRbacReview;
use LiveVoting\Context\xlvoRbacSystem;
use LiveVoting\Session\xlvoSessionHandler;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;
use LiveVoting\Context\xlvoDummyUser6;
use ILIAS\ResourceStorage\StorageHandler\FileSystemStorageHandler;
use ILIAS\FileUpload\Location;
use ILIAS\ResourceStorage\Revision\Repository\RevisionARRepository;
use ILIAS\ResourceStorage\Resource\Repository\ResourceARRepository;
use ILIAS\ResourceStorage\Information\Repository\InformationARRepository;
use ILIAS\ResourceStorage\Stakeholder\Repository\StakeholderARRepository;
use ILIAS\ResourceStorage\Lock\LockHandlerilDB;
use ILIAS\ResourceStorage\Policy\WhiteAndBlacklistedFileNamePolicy;

/**
 * Class xlvoBasicInitialisation for ILIAS 7
 *
 * @author      Nicolas Schaefli <ns@studer-raimann.ch>
 *
 * Initializes a minimal ILIAS environment.
 *
 */
class xlvoBasicInitialisation
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    /**
     * @var ilIniFile
     */
    protected $iliasIniFile;
    /**
     * @var ilSetting
     */
    protected $settings;


    /**
     * xlvoInitialisation constructor.
     *
     * @param int $context
     */
    protected function __construct($context = null)
    {
        if ($context) {
            xlvoContext::setContext($context);
        }

        $this->bootstrapApp();
    }


    /**
     * @param int $context
     *
     * @return xlvoBasicInitialisation
     */
    public static function init($context = null)
    {
        return new self($context);
    }


    private function bootstrapApp()
    {
        //bootstrap ILIAS

        $this->initDependencyInjection();
        $this->setCookieParams();

        $this->removeUnsafeCharacters();
        $this->loadIniFile();
        $this->requireCommonIncludes();
        $this->initErrorHandling();
        $this->determineClient();
        $this->loadClientIniFile();
        $this->initDatabase();
        $this->initLog(); //<-- required for ilCtrl error messages
        $this->initSessionHandler();
        $this->initSettings();  //required
        $this->initAccessHandling();
        $this->buildHTTPPath();
        $this->initHTTPServices();
        $this->initLocale();
        $this->initLanguage();
        $this->initDataCache();
        $this->initObjectDefinition();
        $this->initControllFlow();
        $this->initUser();
        $this->initPluginAdmin();
        $this->initAccess();
        $this->initTree();
        $this->initAppEventHandler();
        $this->initMail();
        $this->initFilesystem();
        $this->initResourceStorage();
        $this->initGlobalScreen();
        $this->initTemplate();
        $this->initTabs();
        $this->initNavigationHistory();
        $this->initHelp();
        $this->initMainMenu();
    }


    /**
     * Remove unsafe characters from GET
     */
    protected function removeUnsafeCharacters()
    {
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


    private function initTemplate()
    {
        $styleDefinition = new xlvoStyleDefinition();
        $this->makeGlobal('styleDefinition', $styleDefinition);

        $ilias = new xlvoILIAS();
        $this->makeGlobal("ilias", $ilias);

        $tpl = new ilGlobalTemplate("tpl.main.html", true, true, 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting', "DEFAULT", true);

        $param_manager = ParamManager::getInstance();
        //$tpl = self::plugin()->template("default/tpl.main.html");
        if (!$param_manager->getPuk()) {
            $tpl->touchBlock("navbar");
        }

        $tpl->addCss('./templates/default/delos.css');
        $tpl->addCss(self::plugin()->directory() . '/templates/default/default.css');

        $tpl->addBlockFile("CONTENT", "content", "tpl.main_voter.html", 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting');
        $tpl->setVariable('BASE', xlvoConf::getBaseVoteURL());
        $this->makeGlobal("tpl", $tpl);

        iljQueryUtil::initjQuery();
        ilUIFramework::init();

        $ilToolbar = new ilToolbarGUI();
        $this->makeGlobal("ilToolbar", $ilToolbar);
    }


    /**
     * initialise database object $ilDB
     *
     */
    private function initDatabase()
    {
        // build dsn of database connection and connect
        $ilDB = ilDBWrapperFactory::getWrapper(IL_DB_TYPE);
        $ilDB->initFromIniFile();
        $ilDB->connect();

        $this->makeGlobal("ilDB", $ilDB);
    }


    /**
     * Load ilias ini.
     */
    private function loadIniFile()
    {
        $this->iliasIniFile = new ilIniFile("./ilias.ini.php");
        $this->iliasIniFile->read();
        $this->makeGlobal('ilIliasIniFile', $this->iliasIniFile);

        // initialize constants
        define("ILIAS_DATA_DIR", $this->iliasIniFile->readVariable("clients", "datadir"));
        define("ILIAS_WEB_DIR", $this->iliasIniFile->readVariable("clients", "path"));
        define("ILIAS_ABSOLUTE_PATH", $this->iliasIniFile->readVariable('server', 'absolute_path'));

        // logging
        define("ILIAS_LOG_DIR", $this->iliasIniFile->readVariable("log", "path"));
        define("ILIAS_LOG_FILE", $this->iliasIniFile->readVariable("log", "file"));
        define("ILIAS_LOG_ENABLED", $this->iliasIniFile->readVariable("log", "enabled"));
        define("ILIAS_LOG_LEVEL", $this->iliasIniFile->readVariable("log", "level"));
        define("SLOW_REQUEST_TIME", $this->iliasIniFile->readVariable("log", "slow_request_time"));

        // read path + command for third party tools from ilias.ini
        define("PATH_TO_CONVERT", $this->iliasIniFile->readVariable("tools", "convert"));
        define("PATH_TO_FFMPEG", $this->iliasIniFile->readVariable("tools", "ffmpeg"));
        define("PATH_TO_ZIP", $this->iliasIniFile->readVariable("tools", "zip"));
        define("PATH_TO_MKISOFS", $this->iliasIniFile->readVariable("tools", "mkisofs"));
        define("PATH_TO_UNZIP", $this->iliasIniFile->readVariable("tools", "unzip"));
        define("PATH_TO_GHOSTSCRIPT", $this->iliasIniFile->readVariable("tools", "ghostscript"));
        define("PATH_TO_JAVA", $this->iliasIniFile->readVariable("tools", "java"));
        define("PATH_TO_HTMLDOC", $this->iliasIniFile->readVariable("tools", "htmldoc"));
        define("URL_TO_LATEX", $this->iliasIniFile->readVariable("tools", "latex"));
        define("PATH_TO_FOP", $this->iliasIniFile->readVariable("tools", "fop"));

        // read virus scanner settings
        switch ($this->iliasIniFile->readVariable("tools", "vscantype")) {
            case "sophos":
                define("IL_VIRUS_SCANNER", "Sophos");
                define("IL_VIRUS_SCAN_COMMAND", $this->iliasIniFile->readVariable("tools", "scancommand"));
                define("IL_VIRUS_CLEAN_COMMAND", $this->iliasIniFile->readVariable("tools", "cleancommand"));
                break;

            case "antivir":
                define("IL_VIRUS_SCANNER", "AntiVir");
                define("IL_VIRUS_SCAN_COMMAND", $this->iliasIniFile->readVariable("tools", "scancommand"));
                define("IL_VIRUS_CLEAN_COMMAND", $this->iliasIniFile->readVariable("tools", "cleancommand"));
                break;

            case "clamav":
                define("IL_VIRUS_SCANNER", "ClamAV");
                define("IL_VIRUS_SCAN_COMMAND", $this->iliasIniFile->readVariable("tools", "scancommand"));
                define("IL_VIRUS_CLEAN_COMMAND", $this->iliasIniFile->readVariable("tools", "cleancommand"));
                break;

            default:
                define("IL_VIRUS_SCANNER", "None");
                break;
        }

        $tz = ilTimeZone::initDefaultTimeZone($this->iliasIniFile);
        define("IL_TIMEZONE", $tz);
        define('IL_INITIAL_WD', getcwd());
    }


    /**
     * Load ilias client ini.
     *
     * @return bool
     */
    private function loadClientIniFile()
    {
        $ini_file = "./" . ILIAS_WEB_DIR . "/" . CLIENT_ID . "/client.ini.php";

        // get settings from ini file
        $ilClientIniFile = new ilIniFile($ini_file);
        $ilClientIniFile->read();

        // invalid client id / client ini
        if ($ilClientIniFile->ERROR != "") {
            $default_client = $this->iliasIniFile->readVariable("clients", "default");
            ilUtil::setCookie("ilClientId", $default_client);
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

        $ilGlobalCacheSettings = new ilGlobalCacheSettings();
        $ilGlobalCacheSettings->readFromIniFile($ilClientIniFile);
        ilGlobalCache::setup($ilGlobalCacheSettings);

        return true;
    }


    /**
     * Init dummy session handling
     */
    private function initSessionHandler()
    {
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
    }


    /**
     * Init the ioc container. (DI)
     */
    private function initDependencyInjection()
    {
        global $DIC;
        require_once 'libs/composer/vendor/autoload.php';
        //			require_once 'src/DI/Container.php';
        $DIC = new Container();
        $DIC["ilLoggerFactory"] = function ($c) {
            return ilLoggerFactory::getInstance();
        };
    }


    /**
     * Init some ilias settings (required for locale)
     */
    private function initSettings()
    {
        $this->settings = new ilSetting();
        $this->makeGlobal("ilSetting", $this->settings);

        // set anonymous user & role id and system role id
        define("ANONYMOUS_USER_ID", $this->settings->get("anonymous_user_id"));
        define("ANONYMOUS_ROLE_ID", $this->settings->get("anonymous_role_id"));
        define("SYSTEM_USER_ID", $this->settings->get("system_user_id"));
        define("SYSTEM_ROLE_ID", $this->settings->get("system_role_id"));
        define("USER_FOLDER_ID", 7);

        // recovery folder
        define("RECOVERY_FOLDER_ID", $this->settings->get("recovery_folder_id"));

        // installation id
        define("IL_INST_ID", $this->settings->get("inst_id", 0));

        // define default suffix replacements
        define("SUFFIX_REPL_DEFAULT", "php,php3,php4,inc,lang,phtml,htaccess");
        define("SUFFIX_REPL_ADDITIONAL", $this->settings->get("suffix_repl_additional"));

        // payment setting
        define('IS_PAYMENT_ENABLED', false);
    }


    /**
     * Include the required stuff for ilias.
     */
    private function requireCommonIncludes()
    {
        // really always required?
        //		require_once 'Services/Utilities/classes/class.ilFormat.php';
        require_once 'include/inc.ilias_version.php';

        $this->makeGlobal("ilBench", new ilBenchmark());
    }


    /**
     * Init Locale
     */
    private function initLocale()
    {
        if (trim($this->settings->get("locale") != "")) {
            $larr = explode(",", trim($this->settings->get("locale")));
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
                    $this->makeGlobal("ilCollator", new Collator($first));
                }
            }
        }
    }


    /**
     * $lng initialisation
     */
    private function initLanguage()
    {
        $this->makeGlobal('lng', ilLanguage::getGlobalInstance());
    }


    /**
     * Build the http path for ILIAS
     *
     * @return mixed
     */
    private function buildHTTPPath()
    {
        $https = new ilHTTPS();
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

            $module = ilUtil::removeTrailingPathSeparators(ILIAS_MODULE);

            $dirs = explode('/', $module);
            $uri = $path;
            foreach ($dirs as $dir) {
                $uri = dirname($uri);
            }
        }

        $https->enableSecureCookies();
        $https->checkPort();

        return define('ILIAS_HTTP_PATH', ilUtil::removeTrailingPathSeparators($protocol . $host . $uri));
    }


    /**
     * Init ilias error handling
     */
    private function initErrorHandling()
    {
        // error_reporting(((ini_get("error_reporting")) & ~E_DEPRECATED) & ~E_STRICT); // removed reading ini since notices lead to a non working livevoting in 5.2 when E_NOTICE is enabled
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE);

        $this->requireCommonIncludes();

        // error handler
        if (!defined('ERROR_HANDLER')) {
            define('ERROR_HANDLER', 'PRETTY_PAGE');
        }
        if (!defined('DEVMODE')) {
            define('DEVMODE', false);
        }

        require_once "./libs/composer/vendor/filp/whoops/src/Whoops/Util/SystemFacade.php";
        require_once "./libs/composer/vendor/filp/whoops/src/Whoops/RunInterface.php";
        require_once "./libs/composer/vendor/filp/whoops/src/Whoops/Run.php";
        require_once "./libs/composer/vendor/filp/whoops/src/Whoops/Handler/HandlerInterface.php";
        require_once "./libs/composer/vendor/filp/whoops/src/Whoops/Handler/Handler.php";
        require_once "./libs/composer/vendor/filp/whoops/src/Whoops/Handler/CallbackHandler.php";

        require_once "./Services/Init/classes/class.ilErrorHandling.php";
        $ilErr = new ilErrorHandling();
        $this->makeGlobal("ilErr", $ilErr);
        $ilErr->setErrorHandling(PEAR_ERROR_CALLBACK, array($ilErr, 'errorHandler'));
    }


    /**
     * Init ilias data cache.
     */
    private function initDataCache()
    {
        $this->makeGlobal("ilObjDataCache", new ilObjectDataCache());
    }


    /**
     * Init ilias object definition.
     */
    private function initObjectDefinition()
    {
        $this->makeGlobal("objDefinition", new xlvoObjectDefinition());
    }


    private function initControllFlow()
    {
        $this->makeGlobal("ilCtrl", new ilCtrl());
    }


    private function initPluginAdmin()
    {
        $this->makeGlobal("ilPluginAdmin", new ilPluginAdmin());
    }


    /**
     * Init log instance
     */
    private function initLog()
    {
        $log = ilLoggerFactory::getRootLogger();

        $this->makeGlobal("ilLog", $log);
        // deprecated
        $this->makeGlobal("log", $log);
    }


    /**
     * set session cookie params for path, domain, etc.
     */
    private function setCookieParams()
    {
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

        /*$cookie_secure = !$this->settings->get('https', 0) && ilHTTPS::getInstance()->isDetected();*/
        $cookie_secure = true;

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
    private function determineClient()
    {
        // check whether ini file object exists
        if (!is_object($this->iliasIniFile)) {
            throw new Exception("Fatal Error: ilInitialisation::determineClient called without initialisation of ILIAS ini file object.");
        }

        // set to default client if empty
        if ($_GET["client_id"] != "") {
            $_GET["client_id"] = ilUtil::stripSlashes($_GET["client_id"]);
            if (!defined("IL_PHPUNIT_TEST")) {
                ilUtil::setCookie("ilClientId", $_GET["client_id"]);
            }
        } else {
            if (!$_COOKIE["ilClientId"]) {
                // to do: ilias ini raus nehmen
                $client_id = $this->iliasIniFile->readVariable("clients", "default");
                ilUtil::setCookie("ilClientId", $client_id);
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
     * @param string $name  The name of the global variable.
     * @param object $value The value where the global variable should point at.
     */
    private function makeGlobal($name, $value)
    {
        global $DIC;
        $GLOBALS[$name] = $value;
        $DIC[$name] = function ($c) use ($name) {
            return $GLOBALS[$name];
        };
    }


    /**
     * Initialise a fake user service to satisfy the help system module.
     */
    private function initUser()
    {
        $this->makeGlobal('ilUser', new xlvoDummyUser6());
    }


    /**
     * Starting from ILIAS 5.2 basic initialisation also needs rbac stuff.
     * You may ask why? well: deep down ilias wants to initialize the footer. Event hough we don't
     * want the footer. This may not seem too bad... but the footer wants to translate something
     * and the translation somehow needs rbac. god...
     *
     * We can remove this when this gets fixed: Services/UICore/classes/class.ilTemplate.php:479
     */
    private function initAccessHandling()
    {
        // thisone we can mock
        $this->makeGlobal('rbacreview', new xlvoRbacReview());

        $rbacsystem = new xlvoRbacSystem();
        $this->makeGlobal("rbacsystem", $rbacsystem);
    }


    /**
     * Initialise a fake access service to satisfy the help system module.
     */
    private function initAccess()
    {
        $this->makeGlobal('ilAccess', new ilAccess());
    }


    /**
     * Initialise a fake three service to satisfy the help system module.
     */
    private function initTree()
    {
        $this->makeGlobal('tree', new ilTree(ROOT_FOLDER_ID));
    }


    /**
     * Initialise a fake http services to satisfy the help system module.
     */
    private static function initHTTPServices()
    {
        global $DIC;

        $DIC['http.request_factory'] = function ($c) {
            return new RequestFactoryImpl();
        };

        $DIC['http.response_factory'] = function ($c) {
            return new ResponseFactoryImpl();
        };

        $DIC['http.cookie_jar_factory'] = function ($c) {
            return new CookieJarFactoryImpl();
        };

        $DIC['http.response_sender_strategy'] = function ($c) {
            return new DefaultResponseSenderStrategy();
        };

        $DIC['http'] = function ($c) {
            return new HTTPServices($c['http.response_sender_strategy'], $c['http.cookie_jar_factory'], $c['http.request_factory'], $c['http.response_factory']);
        };
    }


    /**
     * Initialise a fake tabs service to satisfy the help system module.
     */
    private function initTabs()
    {
        $this->makeGlobal('ilTabs', new ilTabsGUI());
    }


    /**
     * Initialise a fake NavigationHistory service to satisfy the help system module.
     */
    private function initNavigationHistory()
    {
        $this->makeGlobal('ilNavigationHistory', new ilNavigationHistory());
    }


    /**
     * Initialise a fake help service to satisfy the help system module.
     */
    private function initHelp()
    {
        $this->makeGlobal('ilHelp', new ilHelp());
    }


    /**
     * Initialise a fake MainMenu service to satisfy the help system module.
     */
    private function initMainMenu()
    {
        $this->makeGlobal('ilMainMenu', new ilMainMenuGUI());
    }


    /**
     *
     */
    private function initAppEventHandler()
    {
        $this->makeGlobal("ilAppEventHandler", new ilAppEventHandler());
    }


    /**
     *
     */
    private function initMail() {
        $this->makeGlobal("mail.mime.transport.factory", new ilMailMimeTransportFactory(self::dic()->settings(), self::dic()->appEventHandler()));

        $this->makeGlobal("mail.mime.sender.factory", new ilMailMimeSenderFactory(self::dic()->settings()));
    }


    /**
     *
     */
    private function initGlobalScreen() {
        Closure::bind(function(Container $dic) {
            self::initGlobalScreen($dic);
        }, null, ilInitialisation::class)(self::dic()->dic());
    }


    /**
     *
     */
    private function initFilesystem() {
        Closure::bind(function() {
            self::bootstrapFilesystems();
        }, null, ilInitialisation::class)();
    }

    protected function initResourceStorage() : void
    {
        global $DIC;

        $DIC['resource_storage'] = static function (Container $c) : \ILIAS\ResourceStorage\Services {
            return new \ILIAS\ResourceStorage\Services(
                new FileSystemStorageHandler($c['filesystem.storage'], Location::STORAGE),
                new RevisionARRepository(),
                new ResourceARRepository(),
                new InformationARRepository(),
                new StakeholderARRepository(),
                new LockHandlerilDB($c->database()),
                new WhiteAndBlacklistedFileNamePolicy([], [])
            );
        };
    }}
