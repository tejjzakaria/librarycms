<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller;

    use DateInterval;
    use Exception;
    use KAASoft\Controller\Pub\EpicFailAction;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Database\Entity\General\DatabaseField;
    use KAASoft\Database\Entity\General\ListValue;
    use KAASoft\Database\Entity\Util\Permission;
    use KAASoft\Database\Entity\Util\Role;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Config;
    use KAASoft\Environment\Locale;
    use KAASoft\Environment\RouteController;
    use KAASoft\Environment\Routes\Admin\SessionRoutes;
    use KAASoft\Environment\Routes\Pub\EnvatoRoutes;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\Routes\PublicRoute;
    use KAASoft\Environment\Routes\Route;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Environment\Theme;
    use KAASoft\Environment\ThemeSettings;
    use KAASoft\Util\Envato\EnvatoClient;
    use KAASoft\Util\Envato\EnvatoLicense;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\HTTP\HttpClient;
    use KAASoft\Util\Message;
    use Logger;
    use LoggerAppenderRollingFile;
    use LoggerLayoutPattern;
    use LoggerLevel;
    use PDOException;
    use Throwable;

    /**
     * Class ControllerBase
     * @package KAASoft\Controller
     */
    abstract class ControllerBase implements Controller {
        const INSTALLER_ROUTE_NAME = "installer";
        /**
         * @var string
         */
        protected $contentType;
        /**
         * @var array
         */
        protected $ajaxResponse;

        /**
         * @var Route
         */
        protected $activeRoute;
        /**
         * @var ConfiguredSmarty
         */
        protected $smarty;
        /**
         * @var KAASoftDatabase
         */
        protected $kaaSoftDatabase;
        /**
         * @var Session
         */
        protected $session;
        /**
         * @var RouteController
         */
        protected $routeController;
        /**
         * @var SiteViewOptions
         */
        protected $siteViewOptions;
        /**
         * @var ThemeSettings
         */
        protected static $THEME_SETTINGS;
        /**
         * @var Logger
         */
        protected static $LOGGER = null;
        /**
         * @var bool
         */
        protected $isPermittedAction = false;

        private static $LANGUAGES = null;

        const PUBLIC_ADMIN_ROUTES = [ SessionRoutes::LOGIN_ROUTE ];

        /**
         * ControllerBase constructor.
         * @param Route $activeRoute
         * @param bool  $isInitDatabase
         */
        public function __construct(Route $activeRoute = null, $isInitDatabase = true) {
            try {
                if (Config::DEBUG_MODE) {
                    error_reporting(E_ALL);
                    ini_set('display_errors',
                            1);
                }
                else {
                    error_reporting(E_ERROR);
                    ini_set('display_errors',
                            0);
                }

                $this->activeRoute = $activeRoute;
                $this->siteViewOptions = SiteViewOptions::getInstance(strcmp($activeRoute->getName(),
                                                                             ControllerBase::INSTALLER_ROUTE_NAME) === 0);
                $this->siteViewOptions->loadSiteViewOptions();
                $this->routeController = RouteController::getInstance();

                $this->smarty = ConfiguredSmarty::getInstance($activeRoute);
                /*if (!Config::DEBUG_MODE) {
                    $this->smarty->register_outputfilter([ $this,
                                                           "smartyOutputFilter" ]);
                }*/
                $this->session = Session::getInstance($this->routeController,
                                                      $this->activeRoute);
                // !!!!smarty vars init should be after session initialization
                $this->initGlobalSmartyVariables();

                if (Helper::isPostRequest()) {
                    Session::addSessionValue("POST",
                                             $_POST);
                }
                if ($isInitDatabase) {
                    $this->kaaSoftDatabase = KAASoftDatabase::getInstance(self::getLogger());
                    $this->smarty->assign("languages",
                                          ControllerBase::getLanguages($this->kaaSoftDatabase));
                    Book::setCustomFields(ControllerBase::getCustomBookFields($this->kaaSoftDatabase));
                }

                /////////////////////////////////
                // Default headers and response code
                if (Helper::isAjaxRequest()) {
                    $this->setJsonContentType();
                }
                else {
                    $this->setHtmlContentType();
                }
            }
            catch (Exception $e) {
                ControllerBase::getLogger()->log(LoggerLevel::getLevelFatal(),
                                                 sprintf("Critical error is occurred. See details below: %s",
                                                         Helper::printExceptionAsString($e)));
                Helper::processFatalException($e,
                                              RouteController::getInstance());
            }
        }

        /**
         * @param KAASoftDatabase $kaaSoftDatabase
         * @return null
         */
        public static function getLanguages($kaaSoftDatabase) {
            if (self::$LANGUAGES === null) {
                self::$LANGUAGES = $kaaSoftDatabase->getLanguages();
            }

            return self::$LANGUAGES;
        }

        /**
         * @param KAASoftDatabase $kaaSoftDatabase
         * @param null            $offset
         * @param null            $perPage
         * @param null            $sortColumn
         * @param null            $sortOrder
         * @return array|null
         */
        public static function getCustomBookFields($kaaSoftDatabase, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = null;
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }

            $queryResult = $kaaSoftDatabase->select(KAASoftDatabase::$BOOK_FIELDS_TABLE_NAME,
                                                    array_merge(DatabaseField::getDatabaseFieldNames(),
                                                                [ KAASoftDatabase::$BOOK_FIELDS_TABLE_NAME . ".id" ]),
                                                    $queryParams);

            if ($queryResult !== false) {
                $bookFields = [];

                $selectIds = [];
                foreach ($queryResult as $bookFieldRow) {
                    $databaseField = DatabaseField::getObjectInstance($bookFieldRow);

                    if (strcmp($databaseField->getControl(),
                               DatabaseField::CONTROL_TYPE_SELECT) === 0
                    ) {
                        $selectIds [] = $databaseField->getId();
                    }
                    $bookFields[] = $databaseField;
                }

                if (count($selectIds) > 0) {
                    $queryResult = $kaaSoftDatabase->select(KAASoftDatabase::$LIST_VALUES_TABLE_NAME,
                                                            array_merge(ListValue::getDatabaseFieldNames(),
                                                                        [ KAASoftDatabase::$LIST_VALUES_TABLE_NAME . ".id" ]),
                                                            [ "fieldId" => $selectIds ]);
                    if ($queryResult !== false) {
                        foreach ($queryResult as $listValueRow) {
                            $listValue = ListValue::getObjectInstance($listValueRow);
                            foreach ($bookFields as $bookField) {
                                if ($bookField instanceof DatabaseField) {
                                    if ($listValue->getFieldId() == $bookField->getId()) {
                                        $bookField->addListValue($listValue);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }

                return $bookFields;
            }

            return null;
        }

        /**
         * @return ThemeSettings
         */
        public static function getThemeSettings() {
            if (self::$THEME_SETTINGS === null) {
                self::$THEME_SETTINGS = new ThemeSettings();
                self::$THEME_SETTINGS->loadSettings();
            }

            return self::$THEME_SETTINGS;
        }

        /**
         * @return Logger
         */
        public static function getLogger() {
            if (self::$LOGGER === null) {
                $appenderLayout = new LoggerLayoutPattern();
                $pattern = "%date{Y-m-d H:i:s.u} %-15server{REMOTE_ADDR} [v" . Config::getVersion() . "][%level][%class:%method] %message%newline%exception";
                $appenderLayout->setConversionPattern($pattern);
                $appenderLayout->activateOptions();

                $appender = new LoggerAppenderRollingFile("KAASoftAppender");
                $appender->setFile(FileHelper::getLogFileName());
                $appender->setMaxBackupIndex(50);
                $appender->setMaxFileSize("10MB");
                $appender->setLayout($appenderLayout);
                $appender->setAppend(true);
                $appender->activateOptions();

                self::$LOGGER = Logger::getRootLogger();//new Logger("KAASoftLogger");
                self::$LOGGER->setLevel(LoggerLevel::toLevel(LoggerLevel::INFO));
                self::$LOGGER->removeAllAppenders();
                self::$LOGGER->addAppender($appender);
            }

            return self::$LOGGER;
        }

        protected function initGlobalSmartyVariables() {
            $this->smarty->assign("DOCUMENT_ROOT",
                                  FileHelper::getSiteRoot());
            $this->smarty->assign("siteViewOptions",
                                  $this->siteViewOptions);
            $this->smarty->assign("isDemoMode",
                                  Config::DEMO_MODE);
            $this->smarty->assign("copyright",
                                  "&copy; " . date("Y") . " Library CMS. <a href=\"https://kaasoft.pro/\">KAASoft</a>.");
            $this->smarty->assign("SiteURL",
                                  Config::getSiteURL());
            $resourcePath = Config::getSiteURL() . FileHelper::getSiteRelativeLocation() . HttpClient::HTTP_PATH_SEPARATOR;
            $this->smarty->assign("resourcePath",
                                  $resourcePath);
            $isPublicRoute = $this->activeRoute instanceof PublicRoute;


            if ($isPublicRoute) {
                $themeSettings = ControllerBase::getThemeSettings();
                $themePath = $themeSettings->getThemeWebPath();
                $themeConfigFileName = $themeSettings->getThemeConfigFileName();

                if (file_exists($themeConfigFileName)) {
                    $themeFileContent = file_get_contents($themeConfigFileName);
                    $themeArray = json_decode($themeFileContent,
                                              true);
                    $theme = new Theme();
                    $theme->copySettings($themeArray);

                    $this->smarty->assign("theme",
                                          $theme);
                }

                $this->smarty->assign("themePath",
                                      $themePath);
            }
            $this->smarty->assign("pageTitle",
                                  "Library CMS");
            $this->smarty->assign("activeRoute",
                                  $this->activeRoute);
            $this->smarty->assign("serverTimeZone",
                                  Helper::getDefaultTimeZone());
            $this->smarty->assign("serverDateTime",
                                  Helper::getDateTimeString(time(),
                                                            $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_TIME_FORMAT)));

            if (Session::getSessionValue(Locale::ACTIVE_LANGUAGE) === null) {
                Session::addSessionValue(Locale::ACTIVE_LANGUAGE,
                                         Locale::getDefaultLanguage());
            }
            $this->smarty->assign(Locale::ACTIVE_LANGUAGE,
                                  Session::getSessionValue(Locale::ACTIVE_LANGUAGE));
        }

        protected function setLocale() {
            $locale = new Locale(FileHelper::getLocaleLocation(),
                                 "messages");
            $locale->setLanguage(Session::getSessionValue(Locale::ACTIVE_LANGUAGE));

        }

        protected function baseStartAction() {
            try {
                Helper::setCacheControl("no-cache, no-store, must-revalidate");
                Helper::setResponseCode(200);
                ////////////////////////////////
                $this->isPermittedAction = $this->isPermittedUser();
                if ($this->isPermittedAction === true) {
                    $this->setLocale();
                    $this->smarty->assign("builtinRoles",
                                          Role::BUILTIN_USER_ROLES);
                    $this->smarty->assign("routes",
                                          $this->routeController);
                    $this->smarty->assign("user",
                                          $this->session->getUser());
                    $this->smarty->assign("version",
                                          Config::getVersion());
                }
            }
            catch (Exception $e) {
                ControllerBase::getLogger()->log(LoggerLevel::toLevel(LoggerLevel::FATAL),
                                                 sprintf("Critical error is occurred. See details below: %s",
                                                         Helper::printExceptionAsString($e)));
                Helper::processFatalException($e,
                                              $this->routeController);
            }
        }

        /**
         * @param $tpl_source
         * @param $smarty
         * @return mixed
         */
        public function smartyOutputFilter($tpl_source, /** @noinspection PhpUnusedParameterInspection */
                                           $smarty) {
            $replace = [ //remove tabs before and after HTML tags
                         "/>[^\S ]+/s"                => ">",
                         "/[^\S ]+</s"                => "<",
                         //shorten multiple whitespace sequences; keep new-line characters because they matter in JS!!!
                         "/([\t ])+/s"                => " ",
                         //remove leading and trailing spaces
                         "/^([\t ])+/m"               => "",
                         "/([\t ])+$/m"               => "",
                         // remove JS line comments (simple only); do NOT remove lines containing URL (e.g. 'src="http://server.com/"')!!!
                         //'~//[a-zA-Z0-9 ]+$~m'                                             => "",
                         //remove empty lines (sequence of line-end and white-space characters)
                         "/[\r\n]+([\t ]?[\r\n]+)+/s" => "\n",
                         //remove empty lines (between HTML tags); cannot remove just any line-end characters because in inline JS they can matter!
                         "/>[\r\n\t ]+</s"            => "><",
                         //remove "empty" lines containing only JS's block end character; join with next line (e.g. "}\n}\n</script>" --> "}}</script>"
                         //'/}[\r\n\t ]+/s'                                                  => '}',
                         //'/}[\r\n\t ]+,[\r\n\t ]+/s'                                       => '},',
                         //remove new-line after JS's function or condition start; join with next line
                         //'/\)[\r\n\t ]?{[\r\n\t ]+/s'                                      => '){',
                         //'/,[\r\n\t ]?{[\r\n\t ]+/s'                                       => ',{',
                         //remove new-line after JS's line end (only most obvious and safe cases)
                         //'/\),[\r\n\t ]+/s'                                                => '),',
                         //remove quotes from HTML attributes that does not contain spaces; keep quotes around URLs!
                         //'~([\r\n\t ])?([a-zA-Z0-9]+)="([a-zA-Z0-9_/\\-]+)"([\r\n\t ])?~s' => '$1$2=$3$4',
                         //$1 and $4 insert first white-space character found before/after attribute
            ];
            $tpl_source = preg_replace(array_keys($replace),
                                       array_values($replace),
                                       $tpl_source);

            return $tpl_source;
        }

        protected function baseEndAction($templateToDisplay, $isCompileOnly = false) {
            try {
                if ($this->kaaSoftDatabase !== null) {
                    ControllerBase::getLogger()->trace("Number of queries to database: " . count($this->kaaSoftDatabase->getQueryLog()));
                }
                $this->smarty->assign("isDebug",
                                      Config::DEBUG_MODE);

                if (!$isCompileOnly) {
                    $this->smarty->display($templateToDisplay);
                }
                else {
                    $renderedOutput = $this->smarty->fetch($templateToDisplay);
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_HTML => $renderedOutput ]);
                }

                if (!isset( $this->ajaxResponse[Controller::AJAX_PARAM_NAME_REDIRECT] )) {
                    $this->session->clearSessionMessages();
                }
            }
            catch (Throwable $e) {
                ControllerBase::getLogger()->log(LoggerLevel::getLevelFatal(),
                                                 sprintf(_("Critical error is occurred. See details below: %s"),
                                                         Helper::printExceptionAsString($e)));
                Helper::processFatalException($e,
                                              RouteController::getInstance(),
                                              $this instanceof EpicFailAction);
            }
        }

        protected function validateLicense() {
            if (strcmp($this->activeRoute->getName(),
                       EnvatoRoutes::ENVATO_LICENSE_VERIFICATION_PUBLIC_ROUTE) !== 0 and strcmp($this->activeRoute->getName(),
                                                                                                ActionBase::INSTALLER_ROUTE_NAME) !== 0
            ) {
                $license = new EnvatoLicense();
                $license->loadSettings();

                /*try {
                    $this->revalidateLicense($license);
                }
                catch (Exception $e) {
                    ControllerBase::$LOGGER->warn("Couldn't revalidate license.");
                }*/
                if (Helper::checkPassword(EnvatoClient::KAASOFT . $license->getItemId() . $license->getBuyer(),
                                          $license->getHash()) === false
                ) {
                    Session::addSessionMessage(_("Unlicensed software is detected. Please specify your purchase code."),
                                               Message::MESSAGE_STATUS_ERROR);

                    if ($this->isJsonContentType()) {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_REDIRECT => EnvatoRoutes::ENVATO_LICENSE_VERIFICATION_PUBLIC_ROUTE ]);
                        exit( 0 );
                    }
                    else {
                        Helper::redirectTo($this->routeController->getRouteString(EnvatoRoutes::ENVATO_LICENSE_VERIFICATION_PUBLIC_ROUTE));
                    }
                }
            }
        }

        protected function revalidateLicense($license) {
            if ($license !== null and $license instanceof EnvatoLicense) {
                $dateInterval = new DateInterval("P30D");
                $expDateTime = Helper::getDateTimeFromString(Helper::getDateTimeString(),
                                                             date_default_timezone_get(),
                                                             Helper::DATABASE_DATE_TIME_FORMAT);

                $monthAgo = $expDateTime->sub($dateInterval)->format(Helper::DATABASE_DATE_FORMAT);

                $lastValidationDateTime = Helper::getDateTimeFromString($license->getLastValidation(),
                                                                        date_default_timezone_get(),
                                                                        Helper::DATABASE_DATE_TIME_FORMAT);

                if ($lastValidationDateTime === null or $lastValidationDateTime < $monthAgo) {
                    //todo: implement sending license request
                }

            }
        }

        /**
         * @return bool
         */
        protected function isPermittedUser() {
            $method = $_SERVER["REQUEST_METHOD"];
            $isAjax = Helper::isAjaxRequest();

            if ($this->activeRoute instanceof PublicRoute || in_array($this->activeRoute->getName(),
                                                                      ControllerBase::PUBLIC_ADMIN_ROUTES)
            ) {
                ControllerBase::getLogger()->info(sprintf("Public route is in using '%s'[%s:%s].",
                                                          $this->activeRoute->getName(),
                                                          $method . ( $isAjax ? "-AJAX" : "" ),
                                                          $_SERVER["REQUEST_URI"]));

                // don't check permissions for public routes
                return true;
            }
            $user = $this->session->getUser();
            if ($user !== null) {
                // log user's action
                ControllerBase::getLogger()->info(sprintf("User %s %s [id=%d] is using route '%s'[%s:%s].",
                                                          $user->getLastName(),
                                                          $user->getFirstName(),
                                                          $user->getId(),
                                                          $this->activeRoute->getName(),
                                                          $method . ( $isAjax ? "-AJAX" : "" ),
                                                          $_SERVER["REQUEST_URI"]));
                // log user's action end

                $userRole = $user->getRole();
                if ($userRole === null) {
                    Session::addSessionMessage(sprintf(_("User role does not set. Please logout and login again.%sContact site administrator if solution above doesn't work for you."),
                                                       Helper::HTML_NEW_LINE),
                                               Message::MESSAGE_STATUS_ERROR);
                    if ($this->isJsonContentType()) {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_REDIRECT => GeneralPublicRoutes::EPIC_FAIL_ROUTE_NAME ]);
                        exit( 0 );
                    }
                    else {
                        Helper::redirectTo($this->routeController->getRouteString(GeneralPublicRoutes::EPIC_FAIL_ROUTE_NAME));
                    }
                }

                if ($this->isAdminRole($userRole)) {
                    if ($user->isActive() == true) {
                        return true; // grant full access to administrators even if they don't have some permissions in database
                        // !!!!!!!!!but for active admins only!!!!!!!!!!!
                    }
                }
                if ($user->isActive() === true) {
                    $permissions = $this->kaaSoftDatabase->getPermissions($userRole);
                    foreach ($permissions as $permission) {
                        if ($permission instanceof Permission) {
                            if ($permission->getRouteName() === $this->activeRoute->getName()) {
                                return true;
                            }
                        }
                    }
                }
                else {
                    $messageString = _("User is not activated yet. Please ask administrator to activate your account.");
                    Session::addSessionMessage($messageString,
                                               Message::MESSAGE_STATUS_ERROR);

                    if ($this->isJsonContentType()) {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_REDIRECT => GeneralPublicRoutes::PAGE_IS_FORBIDDEN_ROUTE ]);
                        exit( 0 );
                    }
                    else {
                        Helper::redirectTo($this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_FORBIDDEN_ROUTE));
                    }
                }
            }

            return false;
        }

        /**
         * @return string
         */
        public function getActiveRoute() {
            return $this->activeRoute;
        }

        /**
         * @param string $activeRoute
         */
        public function setActiveRoute($activeRoute) {
            $this->activeRoute = $activeRoute;
        }

        protected function startDatabaseTransaction() {
            if (!$this->kaaSoftDatabase->startTransaction()) {
                throw new PDOException(_("Database transaction couldn't be started."));
            }

            return true;
        }

        protected function commitDatabaseChanges() {
            if ($this->kaaSoftDatabase->isInTransaction() and !$this->kaaSoftDatabase->commitTransaction()) {
                throw new PDOException(_("Database transaction couldn't be committed."));
            }
        }

        protected function rollbackDatabaseChanges() {
            if ($this->kaaSoftDatabase->isInTransaction() and !$this->kaaSoftDatabase->rollbackTransaction()) {
                throw new PDOException(_("Database transaction couldn't be rolled back."));
            }
        }

        /**
         * @param $role Role
         * @return bool
         */
        private function isAdminRole($role) {
            if ($role instanceof Role) {
                if ($role->getId() == Role::BUILTIN_USER_ROLES[Role::ADMIN_ROLE_ID]) {
                    return true;
                }
            }

            return false;
        }

        /**
         * @return array
         */
        public function getAjaxResponse() {
            return $this->ajaxResponse;
        }

        /**
         * @param array $ajaxResponse
         */
        public function setAjaxResponse($ajaxResponse) {
            $this->ajaxResponse = $ajaxResponse;
        }

        public function putArrayToAjaxResponse(array $array) {
            if ($this->ajaxResponse == null) {
                $this->ajaxResponse = [];
            }
            $this->ajaxResponse = array_merge($this->ajaxResponse,
                                              $array);
        }

        /**
         * @return mixed
         */
        public function getContentType() {
            return $this->contentType;
        }

        /**
         * @param mixed  $contentType
         * @param string $charset
         * @param bool   $override
         * @param int    $responseCode
         */
        public function setContentType($contentType, $charset = "utf-8", $override = true, $responseCode = 200) {
            $this->contentType = $contentType;
            Helper::setContentType($contentType,
                                   $charset,
                                   $override,
                                   $responseCode);
        }

        public function setHtmlContentType() {
            self::setContentType(ControllerBase::CONTENT_TYPE_HTML);
        }

        public function setXmlContentType() {
            self::setContentType(ControllerBase::CONTENT_TYPE_XML);
        }

        /**
         * @return bool
         */
        public function isHtmlContentType() {
            return strcmp($this->contentType,
                          Controller::CONTENT_TYPE_HTML) === 0;
        }

        public function setJsonContentType() {
            self::setContentType(Controller::CONTENT_TYPE_JSON);
        }

        /**
         * @return bool
         */
        public function isJsonContentType() {
            return strcmp($this->contentType,
                          Controller::CONTENT_TYPE_JSON) === 0;
        }

        /**
         * @param            $routeName
         * @param array|null $params
         * @return mixed|null|string
         */
        public function getRouteString($routeName, array $params = null) {
            return $this->routeController->getRouteString($routeName,
                                                          $params);
        }

        public function getMessages() {
            return Session::getSessionMessages();
        }


        /**
         * @return KAASoftDatabase
         */
        public function getKaaSoftDatabase() {
            return $this->kaaSoftDatabase;
        }

        /**
         * @return SiteViewOptions
         */
        public function getSiteViewOptions() {
            return $this->siteViewOptions;
        }

        /**
         * @return Session
         */
        public function getSession() {
            return $this->session;
        }

        /**
         * @param Session $session
         */
        public function setSession($session) {
            $this->session = $session;
        }

        /**
         * @param $route Route
         * @return bool
         */
        public static function isPublicRoute($route) {
            if ($route instanceof PublicRoute) {
                return true;
            }
            foreach (ControllerBase::PUBLIC_ADMIN_ROUTES as $routeN) {
                if (strcmp($route->getName(),
                           $routeN) === 0
                ) {
                    return true;
                }
            }

            return false;
        }
    }