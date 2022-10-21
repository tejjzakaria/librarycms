<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller;

    use HTMLPurifier;
    use HTMLPurifier_Config;
    use KAASoft\Controller\Admin\Util\ShortCode\ShortCodeDatabaseHelper;
    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Database\Entity\Util\DynamicShortCode;
    use KAASoft\Database\Entity\Util\EmailNotification;
    use KAASoft\Database\Entity\Util\StaticShortCode;
    use KAASoft\Environment\Config;
    use KAASoft\Environment\EmailSettings;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\EmailAddress;
    use KAASoft\Util\Emailer;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use KAASoft\Util\ValidationHelper;
    use LoggerLevel;
    use PDO;
    use Throwable;

    /**
     * Class ActionBase
     * @package KAASoft\Controller
     */
    abstract class ActionBase extends ControllerBase {
        const PER_PAGE    = "perPage";
        const SORT_COLUMN = "sortColumn";
        const SORT_ORDER  = "sortOrder";

        const ESCAPE_CHARACTERS = [ "<",
                                    ">",
                                    "=",
                                    " (",
                                    ")",
                                    ";",
                                    "/" ];
        /**
         * @var array
         */
        protected $dynamicShortCodeQuery = null;

        /**
         * @var array
         */
        protected $externalShortCodes = [];
        /**
         * @var EmailNotification
         */
        protected $emailNotification = null;
        /**
         * @var DisplaySwitch
         */
        protected $displaySwitch = null;

        /**
         * ActionBase constructor.
         * @param null $activeRoute
         * @param bool $isInitDatabase
         */
        public function __construct($activeRoute = null, $isInitDatabase) {
            parent::__construct($activeRoute,
                                $isInitDatabase);

            $this->sanitizeUserInput();
        }

        public function processRequest($args = null) {
            try {
                if (Config::DEMO_MODE === false) {
                    $this->validateLicense();
                }

                $this->baseStartAction();

                if ($this->isPermittedAction === true) {
                    $displaySwitch = $this->action($args);

                    if (Helper::isPostRequest() and !$displaySwitch->isErrorOccurred()) {
                        // send email notification if configured
                        $result = $this->sendEmail();

                        if ($result !== null and $result !== true) {
                            $errorMessage = "Couldn't send email:" . $result;
                            $this->publishMessage($errorMessage,
                                                  Message::MESSAGE_STATUS_WARNING);
                        }
                    }
                    Session::addSessionValue(Session::LAST_ROUTE,
                                             $_SERVER["REQUEST_URI"]);
                    // redirect if required
                    if ($displaySwitch->getRedirectURL() !== null) {
                        if (strcmp($displaySwitch->getRedirectURL(),
                                   $this->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE)) === 0
                        ) {
                            ControllerBase::getLogger()->warn(sprintf("Requested unexpected route: '%s'.",
                                                                      parse_url(urldecode($_SERVER["REQUEST_URI"]),
                                                                                PHP_URL_PATH)));

                        }
                        if (!$this->isJsonContentType()) {
                            Helper::redirectTo($displaySwitch->getRedirectURL());
                        }
                        else {
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_REDIRECT => $displaySwitch->getRedirectURL() ]);
                        }
                    }
                    // display template if required
                    elseif ($displaySwitch->getSmartyTemplate() !== null) {
                        $this->baseEndAction($displaySwitch->getSmartyTemplate(),
                                             $this->isJsonContentType());
                    }
                    // display AJAX response if required
                    if ($this->isJsonContentType() and $this->ajaxResponse !== null and count($this->ajaxResponse) > 0) {
                        Helper::printAsJSON($this->ajaxResponse);
                    }
                }
                else {
                    $errorMessage = sprintf(_("You don't have permissions to use route: \"%s\". Possibly you are not logged in."),
                                            $this->activeRoute->getTitle());
                    Session::addSessionMessage($errorMessage,
                                               Message::MESSAGE_STATUS_ERROR);
                    if (Helper::isAjaxRequest()) {
                        $this->putArrayToAjaxResponse([ //Controller::AJAX_PARAM_NAME_ERROR    => $errorMessage,
                                                        Controller::AJAX_PARAM_NAME_REDIRECT => GeneralPublicRoutes::PAGE_IS_FORBIDDEN_ROUTE ]);
                    }
                    else {
                        Helper::redirectTo($this->getRouteString(GeneralPublicRoutes::PAGE_IS_FORBIDDEN_ROUTE));
                    }
                }
            }
            catch (Throwable $e) {
                ControllerBase::getLogger()->log(LoggerLevel::getLevelFatal(),
                                                 sprintf("Critical error is occurred. See details below: %s",
                                                         Helper::printExceptionAsString($e)));
                Helper::processFatalException($e,
                                              $this->routeController);
            }
        }

        /**
         * @param $args        array
         * @return DisplaySwitch
         */
        protected abstract function action($args);

        /**
         * @param        $errorMessage
         * @param string $status
         */
        protected function publishMessage($errorMessage, $status = Message::MESSAGE_STATUS_INFO) {
            if (Helper::isAjaxRequest()) {
                $this->putArrayToAjaxResponse([ strtolower($status) => $errorMessage ]);
            }
            else {
                $this->session->addSessionMessage($errorMessage,
                                                  $status);
            }
        }

        /**
         * @param string $defaultValue
         * @return array|null|object|string
         */
        protected function getBookViewType($defaultValue = SiteViewOptions::BOOK_VIEW_TYPE_LIST) {
            $bookViewType = null;
            if (isset( $_POST[SiteViewOptions::BOOK_VIEW_TYPE] )) {
                $bookViewType = ValidationHelper::getString($_POST[SiteViewOptions::BOOK_VIEW_TYPE]);
            }
            else {
                $bookViewType = $this->session->getSessionValue(Session::BOOK_VIEW_TYPE,
                                                                $defaultValue);
            }
            Session::addSessionValue(Session::BOOK_VIEW_TYPE,
                                     $bookViewType);

            return $bookViewType;
        }

        /**
         * @param     $sessionPerPageVarName
         * @param int $defaultValue
         * @return array|int|null|object|string
         */
        protected function getPerPage($sessionPerPageVarName, $defaultValue = 10) {
            $perPage = $defaultValue;
            if (isset( $_POST[ActionBase::PER_PAGE] )) {
                $perPage = ValidationHelper::getNullableInt($_POST[ActionBase::PER_PAGE]);
                $this->session->addSessionValue($sessionPerPageVarName,
                                                $perPage);
            }
            else {
                $perPage = $this->session->getSessionValue($sessionPerPageVarName,
                                                           $perPage);
            }

            return $perPage;
        }

        /**
         * @param      $sessionSortingColumnVarName
         * @param null $defaultValue
         * @return array|null|object|string
         */
        protected function getSortingColumn($sessionSortingColumnVarName, $defaultValue = null) {
            $sortColumn = null;
            if (isset( $_POST[ActionBase::SORT_COLUMN] )) {
                $sortColumn = ValidationHelper::getString($_POST[ActionBase::SORT_COLUMN]);
                $this->session->addSessionValue($sessionSortingColumnVarName,
                                                $sortColumn);
            }
            else {
                $sortColumn = $this->session->getSessionValue($sessionSortingColumnVarName,
                                                              $defaultValue);
            }

            return $sortColumn;
        }

        /**
         * @param        $sessionSortingOrderVarName
         * @param string $defaultValue
         * @return array|null|object|string
         */
        protected function getSortingOrder($sessionSortingOrderVarName, $defaultValue = "ASC") {
            $sortOrder = null;
            if (isset( $_POST[ActionBase::SORT_ORDER] )) {
                $sortOrder = ValidationHelper::getDatabaseSortOrder($_POST[ActionBase::SORT_ORDER]);
                $this->session->addSessionValue($sessionSortingOrderVarName,
                                                $sortOrder);
            }
            else {
                $sortOrder = $this->session->getSessionValue($sessionSortingOrderVarName,
                                                             $defaultValue);
                if ($sortOrder === null) {
                    $sortOrder = "ASC";
                }
            }

            return $sortOrder;
        }

        /**
         * Set smarty vars for email notification template
         * @param array $vars
         */
        protected function setEmailNotificationVariables($vars) {
            foreach ($vars as $varName => $varValue) {
                $this->smarty->assign($varName,
                                      $varValue);
            }
        }

        /**
         * @param $varName
         * @param $varValue
         */
        protected function addEmailNotificationVariable($varName, $varValue) {
            $this->smarty->assign($varName,
                                  $varValue);
        }


        /**
         * @return null|string|true
         * @throws \PHPMailer\phpMailerException
         */
        protected function sendEmail() {
            $utilHelper = new UtilDatabaseHelper($this);

            if ($this->emailNotification == null) {
                if ($this->kaaSoftDatabase !== null) {
                    $emailNotification = $utilHelper->getEmailNotification($this->activeRoute->getName());
                }
                else {
                    $emailNotification = false;
                }
            }
            else {
                $emailNotification = $this->emailNotification;
            }

            if ($emailNotification !== null and $emailNotification !== false and $emailNotification->isEnabled()) {
                $emailContent = $emailNotification->getContent();
                $activeTheme = ControllerBase::getThemeSettings()->getActiveTheme();
                FileHelper::createDirectory(FileHelper::getEmailNotificationTemplateDirectory($activeTheme));
                $templateFile = FileHelper::getEmailNotificationTemplateDirectory($activeTheme) . DIRECTORY_SEPARATOR . $emailNotification->getTemplateName();
                if (!file_exists($templateFile)) {
                    if (false === file_put_contents($templateFile,
                                                    $emailContent)
                    ) {
                        $errorMessage = sprintf(_("Couldn't load email template: %s"),
                                                $emailNotification->getRoute());
                        $this->publishErrorMessage($errorMessage);
                    }
                }
                try {
                    //error_reporting(0);
                    //$this->smarty->setDebugging(false);
                    /*register_shutdown_function([ &$this,
                                                 "MyDestructor" ]);*/
                    // apply smarty variables
                    $emailContent = $this->smarty->fetch($templateFile);
                    // apply short codes
                    $emailContent = $this->replaceShortCodes($emailContent,
                                                             array_merge($this->getExternalShortCodes(),
                                                                         $this->getStaticShortCodes(),
                                                                         $this->getDynamicShortCodes($this->dynamicShortCodeQuery)));
                    $emailNotification->setSubject($this->replaceShortCodes($emailNotification->getSubject(),
                                                                            array_merge($this->getExternalShortCodes(),
                                                                                        $this->getStaticShortCodes(),
                                                                                        $this->getDynamicShortCodes($this->dynamicShortCodeQuery))));


                }
                catch (Throwable $e) {
                    $emailContent = "<html><body>" . Helper::printExceptionAsHtmlString($e,
                                                                                        _("It looks like you have a problem with email template. Please check it. Details are below")) . "</body></html>";
                }

                $emailSettings = new EmailSettings();
                $emailSettings->loadSettings();
                $emailer = new Emailer($emailSettings->getSendMethod(),
                                       $emailNotification->getSubject(),
                                       $emailContent);
                $emailer->SetFrom($emailNotification->getFrom()->getEmail(),
                                  $emailNotification->getFrom()->getName());

                //$emailer->ClearAllRecipients();
                $sendingResult = true;
                if (count($emailNotification->getTo()) > 0) {
                    foreach ($emailNotification->getTo() as $address) {
                        if ($address instanceof EmailAddress) {
                            $emailer->ClearAllRecipients();
                            $emailer->AddAddress($address->getEmail(),
                                                 $address->getName());

                            // send email and check result
                            $sendingResultTmp = $emailer->sendEmail();
                            if ($sendingResultTmp !== true) {
                                if ($sendingResult === true) {
                                    $sendingResult = $sendingResultTmp;
                                }
                                else {
                                    $sendingResult .= "\n\r" . $sendingResultTmp;
                                }
                            }
                        }
                    }
                }
                else {
                    $errorMessage = sprintf(_("Couldn't find correspondents for email: %s"),
                                            $emailNotification->getRoute());
                    $this->publishErrorMessage($errorMessage);
                }

                return $sendingResult;
            }

            return null;
        }

        /**
         * @param $errorMessage
         */
        protected function publishErrorMessage($errorMessage) {
            if (Helper::isAjaxRequest()) {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
            }
            else {
                $this->session->addSessionMessage($errorMessage,
                                                  Message::MESSAGE_STATUS_ERROR);
            }
        }

        /**
         * @return EmailNotification
         */
        public function getEmailNotification() {
            return $this->emailNotification;
        }

        /**
         * @param EmailNotification $emailNotification
         */
        public function setEmailNotification($emailNotification) {
            $this->emailNotification = $emailNotification;
        }


        function MyDestructor() {
            $error = error_get_last();
            if ($error !== null and file_exists($error["file"])) {

                $errorMessage = "<table>";

                $errorMessage .= "<tr>";
                $errorMessage .= "<td>";
                $errorMessage .= "Message";
                $errorMessage .= "</td>";
                $errorMessage .= "<td>";
                $errorMessage .= $error["message"];
                $errorMessage .= "</td>";
                $errorMessage .= "</tr>";

                $errorMessage .= "<tr>";
                $errorMessage .= "<td>";
                $errorMessage .= "File";
                $errorMessage .= "</td>";
                $errorMessage .= "<td>";
                $errorMessage .= $error["file"];
                $errorMessage .= "</td>";
                $errorMessage .= "</tr>";

                $errorMessage .= "<tr>";
                $errorMessage .= "<td>";
                $errorMessage .= "Line";
                $errorMessage .= "</td>";
                $errorMessage .= "<td>";
                $errorMessage .= $error["line"];
                $errorMessage .= "</td>";
                $errorMessage .= "</tr>";

                $errorMessage .= "</table>";


                $emailer = new Emailer(EmailSettings::PHP_MAIL,
                                       "Error",
                                       "<html><body>It looks like you have a problem (probably with email template). Please check it. Details are below.<br/>" . $errorMessage . "</body></html>");
                $emailer->SetFrom("your_email@mail.com",
                                  "MEGA_ADMIN");

                $emailer->ClearAllRecipients();
                $emailer->AddAddress("your_email@mail.com",
                                     "MEGA_ADMIN");

                $emailer->sendEmail();
                ob_clean();

                if (Helper::isAjaxRequest()) {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => "Error" ]);
                }
            }
        }

        /**
         * @return array
         */
        protected function getStaticShortCodes() {
            $helper = new ShortCodeDatabaseHelper($this);
            $shortCodes = $helper->getStaticShortCodes();

            return $this->convertStaticShortCodesToArray($shortCodes);

        }

        /**
         * @param $queries
         * @return array
         */
        protected function getDynamicShortCodes($queries) {
            $shortCodesArray = [];
            if ($queries !== null and !empty( $queries ) and is_array($queries)) {
                $helper = new ShortCodeDatabaseHelper($this);
                $shortCodes = $helper->getDynamicShortCodes();

                $shortCodesArray = $this->convertDynamicShortCodesToArray($shortCodes);

                foreach ($queries as $query) {
                    $result = $this->kaaSoftDatabase->query($query);

                    $columnCount = $result->columnCount();
                    $rowCount = $result->rowCount();

                    for ($j = 0; $j < $rowCount; $j++) {
                        $row = $result->fetch(PDO::FETCH_BOTH);
                        for ($i = 0; $i < $columnCount; $i++) {
                            foreach ($shortCodes as $shortCode) {
                                if ($shortCode instanceof DynamicShortCode) {
                                    $columnMeta = $result->getColumnMeta($i);
                                    $fullColumnName = $columnMeta["table"] . "." . $columnMeta["name"];
                                    if (strcmp($fullColumnName,
                                               $shortCode->getColumnName()) === 0
                                    ) {
                                        $shortCodesArray[$shortCode->getCode()] = $row[$i];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $shortCodesArray;
        }

        /**
         * @param $textToReplace string
         * @param $shortCodes
         * @return mixed|string
         */
        protected function replaceShortCodes($textToReplace, $shortCodes) {
            foreach ($shortCodes as $shortCode => $value) {
                $textToReplace = str_replace("[" . $shortCode . "]",
                                             $value,
                                             $textToReplace);
            }

            return $textToReplace;
        }

        /**
         * @param $shortCodes
         * @return array
         */
        private function convertDynamicShortCodesToArray($shortCodes) {
            $array = [];
            foreach ($shortCodes as $shortCode) {
                if ($shortCode instanceof DynamicShortCode) {
                    $array[$shortCode->getCode()] = "";
                }
            }

            return $array;
        }

        /**
         * @param $shortCodes
         * @return array
         */
        private function convertStaticShortCodesToArray($shortCodes) {
            $array = [];
            foreach ($shortCodes as $shortCode) {
                if ($shortCode instanceof StaticShortCode) {
                    $array[$shortCode->getCode()] = $shortCode->getValue();
                }
            }

            return $array;
        }

        /**
         * @return array
         */
        public function getDynamicShortCodeQuery() {
            return $this->dynamicShortCodeQuery;
        }

        /**
         * @param array $dynamicShortCodeQuery
         */
        public function setDynamicShortCodeQuery($dynamicShortCodeQuery) {
            $this->dynamicShortCodeQuery = $dynamicShortCodeQuery;
        }

        /**
         * @return array
         */
        public function getExternalShortCodes() {
            return $this->externalShortCodes;
        }

        /**
         * @param array $externalShortCodes
         */
        public function setExternalShortCodes($externalShortCodes) {
            $this->externalShortCodes = $externalShortCodes;
        }

        /**
         * @param $code
         * @param $value
         */
        public function addShortCode($code, $value) {
            $this->externalShortCodes[$code] = $value;
        }

        /**
         * @param array $excludeKeys
         */
        protected function sanitizeUserInput($excludeKeys = []) {
            if (Helper::isGetRequest()) {
                $_GET = $this->xssDefender($_GET,
                                           $excludeKeys);
            }
            elseif (Helper::isPostRequest()) {
                $_POST = $this->xssDefender($_POST,
                                            $excludeKeys);
            }
        }

        /**
         * @param array $inputArray
         * @param array $excludeKeys
         * @return array
         */
        protected function xssDefender($inputArray, $excludeKeys = []) {

            $config = HTMLPurifier_Config::createDefault();
            $config->set('Core.Encoding',
                         'UTF-8');
            $config->set('HTML.Doctype',
                         'XHTML 1.0 Transitional');

            $purifier = new HTMLPurifier($config);

            foreach ($inputArray as $key => $value) {
                if (ValidationHelper::isArrayEmpty($excludeKeys) or !in_array($key,
                                                                              $excludeKeys)
                ) {
                    if (is_array($value)) {
                        $inputArray[$key] = $this->xssDefender($value,
                                                               $excludeKeys);
                    }
                    else {
                        $inputArray[$key] = $purifier->purify($value);
                    }
                }
            }

            return $inputArray;
        }
    }