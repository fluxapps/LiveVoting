<?php

namespace LiveVoting\Context;

use Exception;
use ILIAS\DI\Container;
use ilInitialisation;
use iljQueryUtil;
use ilLiveVotingPlugin;
use ilTree;
use ilUIFramework;
use LiveVoting\Conf\xlvoConf;
use LiveVoting\Session\xlvoSessionHandler;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;
use ilContext;
use ilGlobalPageTemplate;
use ilUserRequestTargetAdjustment;
use ilMainMenuGUI;
use ilSession;
use ilGlobalTemplate;

/**
 * Class xlvoInitialisation
 *
 * @package     LiveVoting\Context
 * @author      Fabian Schmid <fs@studer-raimann.ch>
 *
 * Initializes a ILIAS environment depending on Context (PIN or ILIAS).
 * This is used in every entry-point for users and AJAX requests
 *
 */
class xlvoInitialisation extends ilInitialisation
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    const USE_OWN_GLOBAL_TPL = true;
    const CONTEXT_PIN = 1;
    const CONTEXT_ILIAS = 2;
    /**
     * @var ilTree
     */
    protected static $tree;
    /**
     * @var int
     */
    protected static $context = self::CONTEXT_PIN;


    /**
     * xlvoInitialisation constructor.
     *
     * @param int $context
     */
    protected function __construct($context = null)
    {
        if ($context) {
            self::saveContext($context);
        } else {
            self::setContext(xlvoContext::getContext());
        }
        $this->run();
    }


    /**
     *
     */
    protected function run()
    {
        //		$this->setContext(self::CONTEXT_ILIAS);
        switch (self::getContext()) {
            case self::CONTEXT_ILIAS:
                require_once 'include/inc.header.php';
                self::initHTML2();
                //				self::initILIAS();
                break;
            case self::CONTEXT_PIN:
                xlvoContext::init(xlvoContextLiveVoting::class);
                self::initILIAS2();
                break;
        }
    }


    /**
     * @param int $context
     *
     * @return xlvoInitialisation
     */
    public static function init($context = null)
    {
        return new self($context);
    }


    /**
     * @param int $context
     *
     * @throws Exception
     */
    public static function saveContext($context)
    {
        self::setContext($context);
        xlvoContext::setContext($context);
    }


    /**
     * set Custom Session handler which does not use db
     */
    public static function setSessionHandler()
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
    }


    /**
     *
     */
    public static function initILIAS2()
    {
        global $DIC;
        require_once 'include/inc.ilias_version.php';
        self::initDependencyInjection();
        self::initCore();
        self::initClient();
        self::initUser();
        self::initLanguage();
        self::$tree->initLangCode();
        self::initHTML2();
        $GLOBALS["objDefinition"] = $DIC["objDefinition"] = new xlvoObjectDefinition();
    }


    /**
     *
     */
    public static function initDependencyInjection()
    {
        global $DIC;
        $DIC = new Container();
        $DIC["ilLoggerFactory"] = function ($c) {
            return ilLoggerFactory::getInstance();
        };
    }


    /**
     * @param string $a_name
     * @param string $a_class
     * @param null   $a_source_file
     */
    protected static function initGlobal($a_name, $a_class, $a_source_file = null)
    {
        global $DIC;

        if ($DIC->offsetExists($a_name)) {
            $DIC->offsetUnset($a_name);
        }

        parent::initGlobal($a_name, $a_class, $a_source_file);
    }


    /**
     *
     */
    protected static function initHTML2()
    {
        global $DIC;
        if ($DIC->offsetExists("tpl")) {
            $DIC->offsetUnset("tpl");
        }
        if ($DIC->offsetExists("ilNavigationHistory")) {
            $DIC->offsetUnset("ilNavigationHistory");
        }
        if ($DIC->offsetExists("ilHelp")) {
            $DIC->offsetUnset("ilHelp");
        }
        if ($DIC->offsetExists("styleDefinition")) {
            $DIC->offsetUnset("styleDefinition");
        }
        self::initHTML();
        if (self::USE_OWN_GLOBAL_TPL) {
            if (self::version()->is6()) {
                $tpl = new ilGlobalTemplate("tpl.main.html", true, true, 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting', "DEFAULT", true);
            } else {
                $tpl = self::plugin()->template("default/tpl.main.html");
            }
            $tpl->touchBlock("navbar");
            $tpl->addCss('./templates/default/delos.css');
            $tpl->addBlockFile("CONTENT", "content", "tpl.main_voter.html", self::plugin()->directory());

            if ($DIC->offsetExists("tpl")) {
                $DIC->offsetUnset("tpl");
            }

            self::initGlobal("tpl", $tpl);
        }
        if (!self::USE_OWN_GLOBAL_TPL) {
            $tpl->getStandardTemplate();
        }

        $tpl->setVariable('BASE', xlvoConf::getBaseVoteURL());
        if (self::USE_OWN_GLOBAL_TPL) {
            iljQueryUtil::initjQuery();
            ilUIFramework::init();
        }
    }

    /**
     * copied parent function, commented out the lti section
     */
    protected static function initHTML()
    {
        if (!self::version()->is6()) {
            return parent::initHTML();
        }
        // copied parent function
        global $ilUser, $DIC;

        if (ilContext::hasUser()) {
            // load style definitions
            // use the init function with plugin hook here, too
            self::initStyle();
        }

        self::initUIFramework($GLOBALS["DIC"]);
        $tpl = new ilGlobalPageTemplate($DIC->globalScreen(), $DIC->ui(), $DIC->http());
        self::initGlobal("tpl", $tpl);

        if (ilContext::hasUser()) {
            if (self::version()->is7()) {
                $dispatcher = new \ILIAS\Init\StartupSequence\StartUpSequenceDispatcher($DIC);
                $dispatcher->dispatch();
            } else {
                $request_adjuster = new ilUserRequestTargetAdjustment(
                    $ilUser,
                    $GLOBALS['DIC']['ilCtrl'],
                    $GLOBALS['DIC']->http()->request()
                );
                $request_adjuster->adjust();
            }
        }

        require_once "./Services/UICore/classes/class.ilFrameTargetInfo.php";

        self::initGlobal(
            "ilNavigationHistory",
            "ilNavigationHistory",
            "Services/Navigation/classes/class.ilNavigationHistory.php"
        );

        self::initGlobal(
            "ilBrowser",
            "ilBrowser",
            "./Services/Utilities/classes/class.ilBrowser.php"
        );

        self::initGlobal(
            "ilHelp",
            "ilHelpGUI",
            "Services/Help/classes/class.ilHelpGUI.php"
        );

        self::initGlobal(
            "ilToolbar",
            "ilToolbarGUI",
            "./Services/UIComponent/Toolbar/classes/class.ilToolbarGUI.php"
        );

        self::initGlobal(
            "ilLocator",
            "ilLocatorGUI",
            "./Services/Locator/classes/class.ilLocatorGUI.php"
        );

        self::initGlobal(
            "ilTabs",
            "ilTabsGUI",
            "./Services/UIComponent/Tabs/classes/class.ilTabsGUI.php"
        );

        if (ilContext::hasUser()) {
            include_once './Services/MainMenu/classes/class.ilMainMenuGUI.php';
            $ilMainMenu = new ilMainMenuGUI("_top");

            self::initGlobal("ilMainMenu", $ilMainMenu);
            unset($ilMainMenu);

            // :TODO: tableGUI related

            // set hits per page for all lists using table module
            $_GET['limit'] = (int) $ilUser->getPref('hits_per_page');
            ilSession::set('tbl_limit', $_GET['limit']);

            // the next line makes it impossible to save the offset somehow in a session for
            // a specific table (I tried it for the user administration).
            // its not posssible to distinguish whether it has been set to page 1 (=offset = 0)
            // or not set at all (then we want the last offset, e.g. being used from a session var).
            // So I added the wrapping if statement. Seems to work (hopefully).
            // Alex April 14th 2006
            if (isset($_GET['offset']) && $_GET['offset'] != "") {							// added April 14th 2006
                $_GET['offset'] = (int) $_GET['offset'];		// old code
            }

            // leads to error in live voting
//            self::initGlobal("lti", "ilLTIViewGUI", "./Services/LTI/classes/class.ilLTIViewGUI.php");
//            $GLOBALS["DIC"]["lti"]->init();
//            self::initKioskMode($GLOBALS["DIC"]);
        } else {
            // several code parts rely on ilObjUser being always included
            include_once "Services/User/classes/class.ilObjUser.php";
        }
    }

    /**
     *
     */
    protected static function initClient()
    {
        self::determineClient();
        self::initClientIniFile();
        self::initDatabase();
        if (!is_object($GLOBALS["ilPluginAdmin"])) {
            self::initGlobal("ilPluginAdmin", "ilPluginAdmin", "./Services/Component/classes/class.ilPluginAdmin.php");
        }
        self::setSessionHandler();
        self::initSettings();
        self::initLocale();

        //		if (ilContext::usesHTTP()) {
        //			self::initGlobal("https", "ilHTTPS", "./Services/Http/classes/class.ilHTTPS.php");
        //			$https->enableSecureParams();
        //			$https->checkPort();
        //		}

        self::initGlobal("ilObjDataCache", "ilObjectDataCache", "./Services/Object/classes/class.ilObjectDataCache.php");
        self::$tree = new ilTree(ROOT_FOLDER_ID);
        self::initGlobal("tree", self::$tree);
        //unset(self::$tree);
        self::initGlobal("ilCtrl", "ilCtrl", "./Services/UICore/classes/class.ilCtrl.php");
        $GLOBALS['COOKIE_PATH'] = '/';
        self::setParamParams();
        self::initLog();
    }

    public static function initUIFramework(Container $c)
    {
        parent::initUIFramework($c);
        parent::initRefinery($c);
    }

    /**
     * @return int
     */
    public static function getContext()
    {
        return self::$context;
    }


    /**
     * @param int $context
     */
    public static function setContext($context)
    {
        self::$context = $context;
    }
}
