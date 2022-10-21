<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;

    use KAASoft\Controller\ActionBase;
    use KAASoft\Controller\Admin\User\UserDatabaseHelper;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Environment\Routes\Admin\SessionRoutes;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\Routes\Route;
    use KAASoft\Util\Helper;
    use KAASoft\Util\LDAP\LdapHelper;
    use KAASoft\Util\Message;

    /**
     * Class Session
     * @package KAASoft\Environment
     */
    class Session {

        const TODO_STATUS_FILTER = "toDoStatusFilter";

        const USER_SORTING_ORDER   = "userSortingOrder";
        const USER_SORTING_COLUMN  = "userSortingColumn";
        const USER_PER_PAGE_NUMBER = "userPerPage";

        const ISSUED_BOOK_SORTING_ORDER  = "issuedBookSortingOrder";
        const ISSUED_BOOK_SORTING_COLUMN = "issuedBookSortingColumn";
        const BOOK_SORTING_ORDER         = "bookSortingOrder";
        const BOOK_SORTING_COLUMN        = "bookSortingColumn";
        const BOOK_PER_PAGE_NUMBER       = "bookPerPage";

        const BOOK_VIEW_TYPE = "bookViewType";

        const BOOK_SORTING_ORDER_PUBLIC          = "bookSortingOrderPublic";
        const BOOK_SORTING_COLUMN_PUBLIC         = "bookSortingColumnPublic";
        const BOOK_PER_PAGE_NUMBER_PUBLIC        = "bookPerPagePublic";
        const BOOK_PER_PAGE_NUMBER_FILTER_PUBLIC = "bookPerPageFilterPublic";

        const ISSUE_PER_PAGE_NUMBER = "issuePerPage";
        const ISSUE_SORTING_ORDER   = "issueSortingOrder";
        const ISSUE_SORTING_COLUMN  = "issueSortingColumn";

        const ISSUE_LOG_PER_PAGE_NUMBER = "issueLogPerPage";
        const ISSUE_LOG_SORTING_ORDER   = "issueLogSortingOrder";
        const ISSUE_LOG_SORTING_COLUMN  = "issueLogSortingColumn";

        const REQUEST_PER_PAGE_NUMBER = "requestPerPage";
        const REQUEST_SORTING_ORDER   = "requestSortingOrder";
        const REQUEST_SORTING_COLUMN  = "requestSortingColumn";

        const REVIEW_PER_PAGE_NUMBER = "reviewPerPage";
        const REVIEW_SORTING_ORDER   = "reviewSortingOrder";
        const REVIEW_SORTING_COLUMN  = "reviewSortingColumn";

        const AUTHOR_SORTING_ORDER   = "authorSortingOrder";
        const AUTHOR_SORTING_COLUMN  = "authorSortingColumn";
        const AUTHOR_PER_PAGE_NUMBER = "authorPerPage";

        const AUTHOR_SORTING_ORDER_PUBLIC   = "authorSortingOrderPublic";
        const AUTHOR_SORTING_COLUMN_PUBLIC  = "authorSortingColumnPublic";
        const AUTHOR_PER_PAGE_NUMBER_PUBLIC = "authorPerPagePublic";

        const POST_SORTING_ORDER   = "postSortingOrder";
        const POST_SORTING_COLUMN  = "postSortingColumn";
        const POST_PER_PAGE_NUMBER = "postPerPage";

        const PAGE_SORTING_ORDER   = "pageSortingOrder";
        const PAGE_SORTING_COLUMN  = "pageSortingColumn";
        const PAGE_PER_PAGE_NUMBER = "pagePerPage";

        const POST_SORTING_ORDER_PUBLIC   = "postSortingOrderPublic";
        const POST_SORTING_COLUMN_PUBLIC  = "postSortingColumnPublic";
        const POST_PER_PAGE_NUMBER_PUBLIC = "postPerPagePublic";

        const PUBLISHER_SORTING_ORDER   = "publisherSortingOrder";
        const PUBLISHER_SORTING_COLUMN  = "publisherSortingColumn";
        const PUBLISHER_PER_PAGE_NUMBER = "publisherPerPage";

        const BOOK_FIELD_SORTING_ORDER   = "bookFieldSortingOrder";
        const BOOK_FIELD_SORTING_COLUMN  = "bookFieldSortingColumn";
        const BOOK_FIELD_PER_PAGE_NUMBER = "bookFieldPerPage";

        const SERIES_SORTING_ORDER   = "seriesSortingOrder";
        const SERIES_SORTING_COLUMN  = "seriesSortingColumn";
        const SERIES_PER_PAGE_NUMBER = "seriesPerPage";

        const GENRE_SORTING_ORDER   = "genreSortingOrder";
        const GENRE_SORTING_COLUMN  = "genreSortingColumn";
        const GENRE_PER_PAGE_NUMBER = "genrePerPage";

        const TAG_SORTING_ORDER   = "tagSortingOrder";
        const TAG_SORTING_COLUMN  = "tagSortingColumn";
        const TAG_PER_PAGE_NUMBER = "tagPerPage";

        const STORE_SORTING_ORDER   = "storeSortingOrder";
        const STORE_SORTING_COLUMN  = "storeSortingColumn";
        const STORE_PER_PAGE_NUMBER = "storePerPage";

        const LOCATION_SORTING_ORDER   = "locationSortingOrder";
        const LOCATION_SORTING_COLUMN  = "locationSortingColumn";
        const LOCATION_PER_PAGE_NUMBER = "locationPerPage";

        const MENU_SORTING_ORDER   = "menuSortingOrder";
        const MENU_SORTING_COLUMN  = "menuSortingColumn";
        const MENU_PER_PAGE_NUMBER = "menuPerPage";

        const USER_MESSAGES_PER_PAGE_NUMBER = "userMessagesPerPage";
        const USER_MESSAGES_SORTING_COLUMN  = "userMessagesSortingColumn";
        const USER_MESSAGES_SORTING_ORDER   = "userMessagesSortingOrder";

        const USER                            = "user";
        const REMEMBER_ME                     = "rememberMe";
        const SESSION_MESSAGES                = 'messages';
        const LAST_ROUTE                      = "lastRoute";
        const SESSION_TIMEOUT                 = 3600;//60 * 60;
        const SESSION_ID_REGENERATION_TIMEOUT = 3600;//60 * 60; in seconds

        const  LAST_ACTIVITY = "lastActivity";
        const  CREATED       = "created";

        private $user;
        private $routeController;
        private $isAdminRoute;

        protected static $INSTANCE = null;

        final protected function __clone() {
        }

        /**
         * Session constructor.
         * @param RouteController $routeController
         * @param Route           $currentRoute
         */
        final protected function __construct(RouteController $routeController, Route $currentRoute) {
            $this->routeController = $routeController;
            session_start();

            $this->checkLogin();

            $this->isAdminRoute = ( $currentRoute === null or !ControllerBase::isPublicRoute($currentRoute) );

            $this->checkSessionTimeout();
            $this->regenerateSessionId();

            if ($this->isAdminRoute) {
                if ($this->isLoggedIn()) {
                    // actions to take right away if user is logged in
                }
                else {
                    // actions to take right away if user is not logged in
                    if ($_SERVER['REQUEST_URI'] != $this->routeController->getRouteString(SessionRoutes::LOGIN_ROUTE)) {
                        Session::addSessionValue(Session::LAST_ROUTE,
                                                 $_SERVER['REQUEST_URI']);

                        if (Helper::isAjaxRequest()) {
                            Helper::printAsJSON([ Controller::AJAX_PARAM_NAME_REDIRECT => $this->routeController->getRouteString(SessionRoutes::LOGIN_ROUTE) ]);
                            die( 0 );
                        }
                        else {
                            Helper::redirectTo($this->routeController->getRouteString(SessionRoutes::LOGIN_ROUTE));
                        }
                    }
                }
            }
        }

        public static function startSession() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        }

        /**
         * @param RouteController $routeController
         * @param Route           $currentRoute
         * @return Session
         */
        public static function getInstance(RouteController $routeController, Route $currentRoute) {
            if (Session::$INSTANCE === null) {
                Session::$INSTANCE = new Session($routeController,
                                                 $currentRoute);
            }

            return Session::$INSTANCE;
        }

        /**
         * @param      $key
         * @param null $defaultValue
         * @return null|object|array|string default value or actual value
         */
        public static function getSessionValue($key, $defaultValue = null) {
            if (isset( $_SESSION[$key] )) {
                return $_SESSION[$key];
            }

            return $defaultValue;
        }

        /**
         * @param $key
         */
        public static function removeSessionValue($key) {
            unset( $_SESSION[$key] );
        }

        /**
         * @param $key
         * @param $value
         */
        public static function addSessionValue($key, $value) {
            $_SESSION[$key] = $value;
        }

        /**
         * @param string $msg
         * @param string $status
         */
        public static function addSessionMessage($msg = "", $status = Message::MESSAGE_STATUS_INFO) {
            if (!empty( $msg )) {
                Session::startSession();
                $sessionMessages = Session::getSessionMessages();

                $sessionMessages[] = new Message($msg,
                                                 $status);

                Session::addSessionValue(Session::SESSION_MESSAGES,
                                         $sessionMessages);
            }
        }

        /**
         *
         */
        public static function clearSessionMessages() {
            Session::removeSessionValue(Session::SESSION_MESSAGES);
        }

        /**
         * @return bool
         */
        public function isLoggedIn() {
            if (isset( $this->user ) && $this->user instanceof User) {
                return true;
            }

            return false;
        }

        /**
         * @return bool|User
         */
        public function getLogin() {
            $user = $this->getUser();

            if ($user !== null) {
                return $user->getEmail();
            }
            else {
                return null;
            }
        }

        /**
         * @return null| User
         */
        public function getUser() {
            if (isset( $this->user ) && $this->user instanceof User) {
                return $this->user;
            }

            return null;
        }

        /**
         * @param ActionBase           $action
         * @param                      $login
         * @param                      $password
         * @return bool|User
         */
        public function attemptLogin(ActionBase $action, $login, $password) {
            $ldapSetting = LdapSettings::getInstance();
            $ldapSetting->loadSettings();

            if (!$ldapSetting->isEnabled() or ( $ldapSetting->isEnabled() and $ldapSetting->isUseBothAuth() )) {
                $userHelper = new UserDatabaseHelper($action);
                $user = $userHelper->getUserByEmail($login);

                if ($user != null && $user instanceof User) {
                    // user exists, will check password
                    if (Helper::checkPassword($password,
                                              $user->getPassword())
                    ) {
                        // password matches

                        return $user;
                    }
                    else {
                        // if password is wrong
                        return false;
                    }
                }
            }

            // if there is no user in database trying to get it ldap if it is enabled
            if ($ldapSetting->isEnabled()) {
                return LdapHelper::ldapAttemptLogin($action,
                                                    $ldapSetting,
                                                    $login,
                                                    $password);
            }

            // user is wrong
            return false;
        }


        /**
         * @param      $user
         * @param bool $rememberMe
         */
        public function login($user, $rememberMe = false) {
            // database should find user based on username/password
            if ($user && $user instanceof User) {
                if (!$user->isActive()) {
                    Session::addSessionMessage(_("User is not activated yet. Please ask administrator to activate your account."),
                                               Message::MESSAGE_STATUS_ERROR);
                    Helper::redirectTo($this->routeController->getRouteString(SessionRoutes::LOGIN_ROUTE));
                }
                Session::addSessionValue(Session::USER,
                                         $user);
                Session::addSessionValue(Session::REMEMBER_ME,
                                         $rememberMe);

                $lastRoute = Session::getSessionValue(Session::LAST_ROUTE,
                                                      $this->routeController->getRouteString(SessionRoutes::INDEX_ADMIN_ROUTE));
                if (strcmp($lastRoute,
                           $this->routeController->getRouteString(SessionRoutes::LOGIN_ROUTE)) == 0
                ) {
                    Helper::redirectTo($this->routeController->getRouteString(SessionRoutes::INDEX_ADMIN_ROUTE));
                }
                elseif (strcmp($lastRoute,
                               $this->routeController->getRouteString(GeneralPublicRoutes::PUBLIC_LOGIN_ROUTE)) == 0
                ) {

                    Helper::redirectTo($this->routeController->getRouteString(GeneralPublicRoutes::USER_PROFILE_ROUTE_NAME));
                }
                else {
                    Helper::redirectTo($lastRoute);
                }
            }
            else {
                Session::addSessionMessage(_("Wrong login or password!"),
                                           Message::MESSAGE_STATUS_ERROR);
                Helper::redirectTo($this->routeController->getRouteString(SessionRoutes::LOGIN_ROUTE));
            }
        }

        /**
         *
         */
        public function logout() {
            Session::removeSessionValue(Session::USER);
            Session::removeSessionValue(Session::LAST_ACTIVITY);
            //session_unset();     // unset $_SESSION variable for the run-time
            //session_destroy();   // destroy session data in storage
            //Session::$INSTANCE = null;
        }

        public function sessionReset() {
            session_unset();     // unset $_SESSION variable for the run-time
            session_destroy();   // destroy session data in storage
            Session::$INSTANCE = null;
        }

        /**
         * @return mixed|string
         */
        public function getUserName() {
            $user = $this->getUser();

            if ($user !== null) {
                return $user->getFirstName();
            }
            else {
                return "User";
            }
        }

        /**
         * @return array
         */
        public static function getSessionMessages() {
            // return $this->sessionMessages;

            if (Session::getSessionValue(Session::SESSION_MESSAGES) == null) {
                Session::addSessionValue(Session::SESSION_MESSAGES,
                                         []);
            }

            return Session::getSessionValue(Session::SESSION_MESSAGES);
        }

        /**
         *
         */
        private function checkLogin() {
            if (Session::getSessionValue(Session::USER) != null) {
                $this->user = Session::getSessionValue(Session::USER);
            }
            else {
                unset( $this->user );
            }
        }

        /**
         *
         */
        private function regenerateSessionId() {
            if (!isset( $_SESSION[Session::CREATED] )) {
                $_SESSION[Session::CREATED] = time();
            }
            else {
                if (time() - $_SESSION[Session::CREATED] > Session::SESSION_ID_REGENERATION_TIMEOUT) {
                    // session started more than "sessionRegenerationTimeout" minutes ago
                    session_regenerate_id(true);    // change session ID for the current session and invalidate old session ID
                    $_SESSION[Session::CREATED] = time();  // update creation time
                }
            }
        }

        /**
         *
         */
        private function checkSessionTimeout() {
            if ($this->isAdminRoute) {
                $isRememberedUser = Session::getSessionValue(Session::REMEMBER_ME) == null ? false : Session::getSessionValue(Session::REMEMBER_ME);
                if (!$isRememberedUser) {
                    if (isset( $_SESSION[Session::LAST_ACTIVITY] ) && ( time() - $_SESSION[Session::LAST_ACTIVITY] > Session::SESSION_TIMEOUT )) {
                        // last request was more than "sessionTimeout" minutes ago
                        //session_unset();     // unset $_SESSION variable for the run-time
                        //session_destroy();   // destroy session data in storage
                        $this->logout();
                        session_start();
                        Session::addSessionValue(Session::LAST_ROUTE,
                                                 $_SERVER['REQUEST_URI']);

                        if ($_SERVER['REQUEST_URI'] != $this->routeController->getRouteString(SessionRoutes::LOGIN_ROUTE)) {
                            $this->addSessionMessage(_("Session is expired. Please log in again."),
                                                     Message::MESSAGE_STATUS_ERROR);
                            if (Helper::isAjaxRequest()) {
                                Helper::printAsJSON([ Controller::AJAX_PARAM_NAME_REDIRECT => SessionRoutes::LOGIN_ROUTE ]);
                                die( 0 );
                            }
                            else {
                                Helper::redirectTo($this->routeController->getRouteString(SessionRoutes::LOGIN_ROUTE));
                            }
                        }
                    }
                }
            }
            $_SESSION[Session::LAST_ACTIVITY] = time(); // update last activity time stamp
        }
    }


    ?>