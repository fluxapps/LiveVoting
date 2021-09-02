<?php

namespace srag\DIC\LiveVoting\DIC;

use Collator;
use ilAccessHandler;
use ilAppEventHandler;
use ilAsqFactory;
use ilAuthSession;
use ilBenchmark;
use ilBookingManagerService;
use ilBookingReservationDBRepositoryFactory;
use ilBrowser;
use ilCertificateActiveValidator;
use ilComponentLogger;
use ilConditionService;
use ilCtrl;
use ilCtrlStructureReader;
use ilDBInterface;
use ilErrorHandling;
use ilExerciseFactory;
use ilFavouritesDBRepository;
use ilHelpGUI;
use ILIAS;
use ILIAS\Data\Factory as DataFactory;
use ILIAS\DI\BackgroundTaskServices;
use ILIAS\DI\Container;
use ILIAS\DI\HTTPServices;
use ILIAS\DI\LoggingServices;
use ILIAS\DI\RBACServices;
use ILIAS\DI\UIServices;
use ILIAS\Filesystem\Filesystems;
use ILIAS\FileUpload\FileUpload;
use ILIAS\GlobalScreen\Services as GlobalScreenService;
use ILIAS\Refinery\Factory as RefineryFactory;
use ILIAS\ResourceStorage\Services as ResourceStorageServices;
use ILIAS\Skill\Service\SkillService;
use ILIAS\UI\Implementation\Render\ImagePathResolver;
use ILIAS\UI\Implementation\Render\JavaScriptBinding;
use ILIAS\UI\Implementation\Render\Loader;
use ILIAS\UI\Implementation\Render\ResourceRegistry;
use ILIAS\UI\Implementation\Render\TemplateFactory;
use ilIniFile;
use ilLanguage;
use ilLearningHistoryService;
use ilLocatorGUI;
use ilLoggerFactory;
use ilMailMimeSenderFactory;
use ilMailMimeTransportFactory;
use ilMainMenuGUI;
use ilMMItemRepository;
use ilNavigationHistory;
use ilNewsService;
use ilObjectDataCache;
use ilObjectDefinition;
use ilObjectService;
use ilObjUseBookDBRepository;
use ilObjUser;
use ilPluginAdmin;
use ilSetting;
use ilStyleDefinition;
use ilTabsGUI;
use ilTaskService;
use ilToolbarGUI;
use ilTree;
use ilUIService;
use Session;
use srag\DIC\LiveVoting\Database\DatabaseInterface;
use srag\DIC\LiveVoting\Exception\DICException;

/**
 * Interface DICInterface
 *
 * @package srag\DIC\LiveVoting\DIC
 */
interface DICInterface
{

    /**
     * DICInterface constructor
     *
     * @param Container $dic
     */
    public function __construct(Container &$dic);


    /**
     * @return ilAccessHandler
     */
    public function access() : ilAccessHandler;


    /**
     * @return ilAppEventHandler
     */
    public function appEventHandler() : ilAppEventHandler;


    /**
     * @return ilAuthSession
     */
    public function authSession() : ilAuthSession;


    /**
     * @return BackgroundTaskServices
     */
    public function backgroundTasks() : BackgroundTaskServices;


    /**
     * @return ilBenchmark
     */
    public function benchmark() : ilBenchmark;


    /**
     * @return ilBookingManagerService
     */
    public function bookingManager() : ilBookingManagerService;


    /**
     * @return ilObjUseBookDBRepository
     */
    public function bookingObjUseBook() : ilObjUseBookDBRepository;


    /**
     * @return ilBookingReservationDBRepositoryFactory
     */
    public function bookingReservation() : ilBookingReservationDBRepositoryFactory;


    /**
     * @return ilBrowser
     */
    public function browser() : ilBrowser;


    /**
     * @return ilCertificateActiveValidator
     */
    public function certificateActiveValidator() : ilCertificateActiveValidator;


    /**
     * @return ilIniFile
     */
    public function clientIni() : ilIniFile;


    /**
     * @return Collator
     */
    public function collator() : Collator;


    /**
     * @return ilConditionService
     */
    public function conditions() : ilConditionService;


    /**
     * @return ilCtrl
     */
    public function ctrl() : ilCtrl;


    /**
     * @return ilCtrlStructureReader
     */
    public function ctrlStructureReader() : ilCtrlStructureReader;


    /**
     * @return DataFactory
     */
    public function data() : DataFactory;


    /**
     * @return DatabaseInterface
     *
     * @throws DICException DatabaseDetector only supports ilDBPdoInterface!
     */
    public function database() : DatabaseInterface;


    /**
     * @return ilDBInterface
     */
    public function databaseCore() : ilDBInterface;


    /**
     * @return Container
     */
    public function &dic() : Container;


    /**
     * @return ilErrorHandling
     */
    public function error() : ilErrorHandling;


    /**
     * @return ilExerciseFactory
     */
    public function exercise() : ilExerciseFactory;


    /**
     * @return ilFavouritesDBRepository
     */
    public function favourites() : ilFavouritesDBRepository;


    /**
     * @return Filesystems
     */
    public function filesystem() : Filesystems;


    /**
     * @return GlobalScreenService
     */
    public function globalScreen() : GlobalScreenService;


    /**
     * @return ilHelpGUI
     */
    public function help() : ilHelpGUI;


    /**
     * @return ilNavigationHistory
     */
    public function history() : ilNavigationHistory;


    /**
     * @return HTTPServices
     */
    public function http() : HTTPServices;


    /**
     * @return ILIAS
     */
    public function ilias() : ILIAS;


    /**
     * @return ilIniFile
     */
    public function iliasIni() : ilIniFile;


    /**
     * @return ImagePathResolver
     *
     * @throws DICException ImagePathResolver not exists in ILIAS 6 or below!
     */
    public function imagePathResolver() : ImagePathResolver;


    /**
     * @return JavaScriptBinding
     */
    public function javaScriptBinding() : JavaScriptBinding;


    /**
     * @return ilLanguage
     */
    public function language() : ilLanguage;


    /**
     * @return ilLearningHistoryService
     */
    public function learningHistory() : ilLearningHistoryService;


    /**
     * @return ilLocatorGUI
     */
    public function locator() : ilLocatorGUI;


    /**
     * @return ilComponentLogger
     */
    public function log() : ilComponentLogger;


    /**
     * @return LoggingServices
     */
    public function logger() : LoggingServices;


    /**
     * @return ilLoggerFactory
     */
    public function loggerFactory() : ilLoggerFactory;


    /**
     * @return ilMailMimeSenderFactory
     */
    public function mailMimeSenderFactory() : ilMailMimeSenderFactory;


    /**
     * @return ilMailMimeTransportFactory
     */
    public function mailMimeTransportFactory() : ilMailMimeTransportFactory;


    /**
     * @return ilMainMenuGUI
     */
    public function mainMenu() : ilMainMenuGUI;


    /**
     * @return ilMMItemRepository
     */
    public function mainMenuItem() : ilMMItemRepository;


    /**
     * @return ilNewsService
     */
    public function news() : ilNewsService;


    /**
     * @return ilObjectDataCache
     */
    public function objDataCache() : ilObjectDataCache;


    /**
     * @return ilObjectDefinition
     */
    public function objDefinition() : ilObjectDefinition;


    /**
     * @return ilObjectService
     */
    public function object() : ilObjectService;


    /**
     * @return ilPluginAdmin
     */
    public function pluginAdmin() : ilPluginAdmin;


    /**
     * @return ilAsqFactory
     */
    public function question() : ilAsqFactory;


    /**
     * @return RBACServices
     */
    public function rbac() : RBACServices;


    /**
     * @return RefineryFactory
     */
    public function refinery() : RefineryFactory;


    /**
     * @return Loader
     */
    public function rendererLoader() : Loader;


    /**
     * @return ilTree
     */
    public function repositoryTree() : ilTree;


    /**
     * @return ResourceRegistry
     */
    public function resourceRegistry() : ResourceRegistry;


    /**
     * @return ResourceStorageServices
     *
     * @throws DICException ResourceStorageServices not exists in ILIAS 6 or below!
     */
    public function resourceStorage() : ResourceStorageServices;


    /**
     * @return Session
     */
    public function session() : Session;


    /**
     * @return ilSetting
     */
    public function settings() : ilSetting;


    /**
     * @return SkillService
     *
     * @throws DICException SkillService not exists in ILIAS 6 or below!"
     */
    public function skills() : SkillService;


    /**
     * @return ilStyleDefinition
     */
    public function systemStyle() : ilStyleDefinition;


    /**
     * @return ilTabsGUI
     */
    public function tabs() : ilTabsGUI;


    /**
     * @return ilTaskService
     */
    public function task() : ilTaskService;


    /**
     * @return TemplateFactory
     */
    public function templateFactory() : TemplateFactory;


    /**
     * @return ilToolbarGUI
     */
    public function toolbar() : ilToolbarGUI;


    /**
     * @return UIServices
     */
    public function ui() : UIServices;


    /**
     * @return ilUIService
     */
    public function uiService() : ilUIService;


    /**
     * @return FileUpload
     */
    public function upload() : FileUpload;


    /**
     * @return ilObjUser
     */
    public function user() : ilObjUser;
}
