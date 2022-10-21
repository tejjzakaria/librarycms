<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace Installer\Controller;


    use Exception;
    use KAASoft\Controller\Admin\User\UserDatabaseHelper;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Database\Entity\Util\DatabaseVersion;
    use KAASoft\Database\Entity\Util\Role;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Config;
    use KAASoft\Environment\DatabaseConnection;
    use KAASoft\Environment\DatabaseSettings;
    use KAASoft\Environment\Routes\Admin\SessionRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Envato\EnvatoClient;
    use KAASoft\Util\Envato\EnvatoLicense;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use KAASoft\Util\ValidationHelper;
    use PDO;

    /**
     * Class SiteInstallAction
     * @package Installer\Controller
     */
    class SiteInstallAction extends PublicActionBase {

        const DEFAULT_CONNECTION_NAME    = "Initial";
        const SCHEMA_CREATE_FILE         = "library_cms_schema.sql";
        const INITIAL_DATA_FILE          = "library_cms_initial_data.sql";
        const SCHEMA_UPDATE_FILE_PATTERN = "library_cms_schema_update_v%d.sql";

        const HT_ACCESS_TEMPLATE_FILE_NAME = "htaccess_template.txt";
        const HT_ACCESS_FILE_NAME          = ".htaccess";

        const INSTALL_ERROR = "installError";

        /**
         * SiteInstallAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute,
                                false);
        }

        /**
         * @param $args        array
         * @return DisplaySwitch
         */
        protected function action($args) {
            $isUpdate = $this->isUpdate();
            if (Helper::isPostRequest()) {
                try {
                    if (Helper::isAjaxRequest() and isset( $_POST["purchaseCode"] )) {
                        $this->verifyLicense(ValidationHelper::getString($_POST["purchaseCode"]));

                        return new DisplaySwitch();
                    }
                    else {
                        if ($isUpdate) {
                            return $this->update();
                        }
                        else {
                            // install base version of database
                            $result = $this->install();
                            if ($result->isErrorOccurred()) {
                                Session::addSessionValue(SiteInstallAction::INSTALL_ERROR,
                                                         true);

                                return $result;
                            }
                            else {
                                Session::addSessionValue(SiteInstallAction::INSTALL_ERROR,
                                                         false);
                            }

                            // apply all updates if there is no errors in base database creating
                            return $this->update();
                        }
                    }
                }
                catch (Exception $e) {
                    if ($this->kaaSoftDatabase !== null) {
                        $this->rollbackDatabaseChanges();
                    }
                    Session::addSessionValue(SiteInstallAction::INSTALL_ERROR,
                                             true);

                    ControllerBase::getLogger()->error($e->getMessage(),
                                                       $e);
                    Session::addSessionMessage($e->getMessage(),
                                               Message::MESSAGE_STATUS_ERROR);

                    return new DisplaySwitch(null,
                                             FileHelper::getSiteRelativeLocation() . "/",
                                             true);
                }
            }
            else {
                $envatoLicense = new EnvatoLicense();
                $envatoLicense->loadSettings();

                $this->smarty->assign("isPurchased",
                                      $envatoLicense->getHash() !== null);
                $this->smarty->assign("isUpdate",
                                      $isUpdate);
                Session::removeSessionValue(SiteInstallAction::INSTALL_ERROR);

                return new DisplaySwitch("auth/install.tpl");

            }
        }

        private function verifyLicense($purchaseCode) {
            try {
                $envatoClient = new EnvatoClient();

                $license = $envatoClient->verifyPurchaseCode($purchaseCode);

                if ($license === false) {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_WARNING => _("Couldn't verify license for some reason.") ]);
                }
                elseif ($license === null) {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Purchase code is INVALID.") ]);
                }
                else {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Purchase code is successfully verified.") ]);
                    $this->putArrayToAjaxResponse([ "license" => $license ]);
                    $license->saveSettings();
                }
            }
            catch (Exception $e) {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_WARNING => sprintf(_("Couldn't verify license for some reason: %s"),
                                                                                               $e->getMessage()) ]);
            }
        }

        /**
         * @return bool
         */
        private function isUpdate() {
            $isInstallError = Session::getSessionValue(SiteInstallAction::INSTALL_ERROR);
            // if we don't have errors during installation
            if ($isInstallError === null or $isInstallError === false) {
                $databaseConnection = Config::getDatabaseConnection();

                if ($databaseConnection === null) {
                    // there is no stored database connections
                    return false;
                }

                return true;
            }
            else {
                return false;
            }
        }

        /**
         * @return DisplaySwitch
         */
        private function update() {
            // connect to database if it is successfully created
            $this->kaaSoftDatabase = KAASoftDatabase::getInstance(self::getLogger());

            $this->kaaSoftDatabase->getPDO()->setAttribute(PDO::ATTR_ERRMODE,
                                                           PDO::ERRMODE_EXCEPTION);
            $this->kaaSoftDatabase->getPDO()->setAttribute(PDO::ATTR_EMULATE_PREPARES,
                                                           true);

            $databaseVersion = 0;
            if ($this->kaaSoftDatabase->isTableExists(KAASoftDatabase::$DATABASE_VERSION_TABLE_NAME)) {
                $databaseVersionObj = $this->kaaSoftDatabase->getDatabaseVersion();
                if ($databaseVersionObj !== null and $databaseVersionObj instanceof DatabaseVersion) {
                    $databaseVersion = (int)$databaseVersionObj->getVersion();
                }
                else {
                    Session::addSessionMessage(_("Database version couldn't be identified. Please contact developer to solve this issue."),
                                               Message::MESSAGE_STATUS_ERROR);

                    return new DisplaySwitch(null,
                                             FileHelper::getSiteRelativeLocation() . "/",
                                             true);
                }
            }

            $isDatabaseChanged = false;
            if ($this->startDatabaseTransaction()) {

                while (1) {
                    $databaseVersion++;
                    $updateFile = FileHelper::getInstallerLocation() . DIRECTORY_SEPARATOR . sprintf(SiteInstallAction::SCHEMA_UPDATE_FILE_PATTERN,
                                                                                                     $databaseVersion);

                    // check if we have database update
                    if (file_exists($updateFile)) {
                        ControllerBase::getLogger()->info("Applying file: " . $updateFile);
                        $result = $this->kaaSoftDatabase->execFileWithoutTransaction($updateFile);

                        if ($result !== true) {
                            $this->rollbackDatabaseChanges();
                            Session::addSessionMessage(sprintf(_("Couldn't update database by file '%s': %s."),
                                                               $updateFile,
                                                               $this->kaaSoftDatabase->error()),
                                                       Message::MESSAGE_STATUS_ERROR);

                            return new DisplaySwitch(null,
                                                     FileHelper::getSiteRelativeLocation() . "/",
                                                     true);

                        }
                        ControllerBase::getLogger()->info("File applied: " . $updateFile);
                        $isDatabaseChanged = true;
                    }
                    else {
                        // nothing to update in database
                        break;
                    }
                }

                if ($isDatabaseChanged) {
                    $result = $this->kaaSoftDatabase->exec("SET SQL_MODE=ANSI_QUOTES");
                    if ($result === false) {
                        $this->rollbackDatabaseChanges();
                        $errorMessage = _("Couldn't set SQL_MODE.");
                        Session::addSessionMessage($errorMessage,
                                                   Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 FileHelper::getSiteRelativeLocation() . "/",
                                                 true);
                    }
                    $result = $this->kaaSoftDatabase->deleteAllTableContent(KAASoftDatabase::$DATABASE_VERSION_TABLE_NAME);
                    if ($result === false) {
                        $this->rollbackDatabaseChanges();
                        $errorMessage = _("Couldn't clean up database version.");
                        Session::addSessionMessage($errorMessage,
                                                   Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 FileHelper::getSiteRelativeLocation() . "/",
                                                 true);
                    }

                    $result = $this->kaaSoftDatabase->saveDatabaseVersion($databaseVersion - 1);
                    if ($result === false) {
                        $this->rollbackDatabaseChanges();
                        Session::addSessionMessage(sprintf(_("Couldn't save database version v%d: %s."),
                                                           $databaseVersion,
                                                           $this->kaaSoftDatabase->error()),
                                                   Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 FileHelper::getSiteRelativeLocation() . "/",
                                                 true);

                    }
                    $this->commitDatabaseChanges();
                }
            }

            // rename installer index file
            $result = @rename(FileHelper::getSiteRoot() . DIRECTORY_SEPARATOR . "index_working.php",
                              FileHelper::getSiteRoot() . DIRECTORY_SEPARATOR . "index.php");
            if ($result === false) {
                Session::addSessionMessage(_("Please delete file \"index.php\" in root directory and rename file \"index_working.php\" in the same directory to \"index.php\"."),
                                           Message::MESSAGE_STATUS_ERROR);
            }
            Session::addSessionMessage(_("Database is successfully configured.<br/>Please try to login."));

            if (ini_get("opcache.enable")) {
                $result = opcache_reset();
                if ($result === false) {
                    ControllerBase::$LOGGER->warn(_("Couldn't clear cache."));
                }
            }
            $this->updateHTAccess();

            return new DisplaySwitch(null,
                                     $this->routeController->getRouteString(SessionRoutes::INDEX_ADMIN_ROUTE));
        }

        /**
         * @return DisplaySwitch
         */
        private function install() {
            // get post variables
            $databaseName = ValidationHelper::getString($_POST["databaseName"]);
            $databaseHost = ValidationHelper::getString($_POST["databaseHost"]);
            $databasePort = ValidationHelper::getString($_POST["databasePort"]);
            $databaseUserName = ValidationHelper::getString($_POST["databaseUserName"]);
            $databasePassword = ValidationHelper::getString($_POST["databasePassword"]);
            $databaseType = ValidationHelper::getString($_POST["databaseType"]);

            $adminEmail = ValidationHelper::getString($_POST["adminEmail"]);
            $adminPassword = ValidationHelper::getString($_POST["adminPassword"]);

            // create database connection
            $databaseConnection = new DatabaseConnection();
            $databaseConnection->setName(SiteInstallAction::DEFAULT_CONNECTION_NAME);
            $databaseConnection->setHost($databaseHost);
            $databaseConnection->setPort($databasePort);
            $databaseConnection->setUserName($databaseUserName);
            $databaseConnection->setPassword($databasePassword);
            $databaseConnection->setDatabaseType($databaseType);
            $databaseConnection->setDatabaseName($databaseName);
            $databaseConnection->setCharset("utf8");

            //set global database connection
            $databaseConnection->setDatabaseName(null);
            Config::setDatabaseConnection($databaseConnection);

            // connect to database if it is successfully created
            $this->kaaSoftDatabase = KAASoftDatabase::getInstance(self::getLogger());

            $this->kaaSoftDatabase->getPDO()->setAttribute(PDO::ATTR_ERRMODE,
                                                           PDO::ERRMODE_EXCEPTION);
            $this->kaaSoftDatabase->getPDO()->setAttribute(PDO::ATTR_EMULATE_PREPARES,
                                                           true);

            // create new database
            $result = $this->createDatabase($this->kaaSoftDatabase,
                                            $_POST["databaseName"]);


            if ($result === false) {
                Session::addSessionMessage(sprintf(_("Database couldn't be created: %s"),
                                                   $result),
                                           Message::MESSAGE_STATUS_ERROR);

                return new DisplaySwitch(null,
                                         FileHelper::getSiteRelativeLocation() . "/",
                                         true);
            }


            // create tables
            if ($this->startDatabaseTransaction()) {
                $result = $this->kaaSoftDatabase->execFileWithoutTransaction(FileHelper::getInstallerLocation() . DIRECTORY_SEPARATOR . SiteInstallAction::SCHEMA_CREATE_FILE);

                if ($result !== true) {
                    $this->rollbackDatabaseChanges();
                    Session::addSessionMessage(sprintf(_("Tables could be created: %s"),
                                                       $this->kaaSoftDatabase->error()),
                                               Message::MESSAGE_STATUS_ERROR);

                    return new DisplaySwitch(null,
                                             FileHelper::getSiteRelativeLocation() . "/",
                                             true);
                }

                // import some initial data
                $result = $this->kaaSoftDatabase->execFileWithoutTransaction(FileHelper::getInstallerLocation() . DIRECTORY_SEPARATOR . SiteInstallAction::INITIAL_DATA_FILE);
                if ($result !== true) {
                    $this->rollbackDatabaseChanges();
                    Session::addSessionMessage(sprintf(_("Initial data couldn't be imported: %s"),
                                                       $this->kaaSoftDatabase->error()),
                                               Message::MESSAGE_STATUS_ERROR);

                    return new DisplaySwitch(null,
                                             FileHelper::getSiteRelativeLocation() . "/",
                                             true);
                }

                //create admin user
                $userHelper = new UserDatabaseHelper($this);
                $user = new User();
                $user->setId(1);
                $user->setIsActive(true);
                $user->setIsLdapUser(false);
                $user->setRoleId(Role::BUILTIN_USER_ROLES[Role::ADMIN_ROLE_ID]);
                $user->setEmail($adminEmail);
                $user->setPassword(Helper::encryptPassword($adminPassword));
                $user->setCreationDateTime(Helper::getDateTimeString());
                $user->setUpdateDateTime($user->getCreationDateTime());

                $result = $userHelper->saveUser($user,
                                                true,
                                                true);
                if ($result === false) {
                    $this->rollbackDatabaseChanges();
                    Session::addSessionMessage(sprintf(_("Initial data couldn't be imported: %s"),
                                                       $this->kaaSoftDatabase->error()),
                                               Message::MESSAGE_STATUS_ERROR);

                    return new DisplaySwitch(null,
                                             FileHelper::getSiteRelativeLocation() . "/",
                                             true);
                }


            }
            $this->commitDatabaseChanges();

            // save database settings

            $databaseSettings = new DatabaseSettings();
            $databaseSettings->loadSettings();
            // update database name
            $databaseConnection->setDatabaseName($databaseName);
            // and put database connection to settings
            $databaseSettings->addDatabaseConnection($databaseConnection);
            $databaseSettings->setActiveConnectionName(SiteInstallAction::DEFAULT_CONNECTION_NAME);
            $databaseSettings->saveSettings();

            return new DisplaySwitch(null,
                                     $this->routeController->getRouteString(SessionRoutes::INDEX_ADMIN_ROUTE));

        }

        /**
         * @param $kaasoftDatabase    KAASoftDatabase
         * @param $databaseName       string
         * @return bool
         */
        private function createDatabase($kaasoftDatabase, $databaseName) {
            try {
                $sql = sprintf("CREATE DATABASE IF NOT EXISTS `%s` DEFAULT CHARACTER SET utf8",
                               $databaseName);

                $result = $kaasoftDatabase->exec($sql);

                if ($result === false) {
                    return false;
                }

                $sql = sprintf("USE `%s`",
                               $databaseName);
                $result = $kaasoftDatabase->exec($sql);

                if ($result === false) {
                    return false;
                }

                return true;

            }
            catch (Exception $e) {
                return false;
            }
        }

        private function updateHTAccess() {
            $templateFileName = FileHelper::getSiteRoot() . DIRECTORY_SEPARATOR . SiteInstallAction::HT_ACCESS_TEMPLATE_FILE_NAME;
            $fileName = FileHelper::getSiteRoot() . DIRECTORY_SEPARATOR . SiteInstallAction::HT_ACCESS_FILE_NAME;
            $content = file_get_contents($templateFileName);
            $isError = false;
            if ($content !== false) {
                $newContent = $content;
                $siteRelativeLocation = FileHelper::getSiteRelativeLocation();
                if (!ValidationHelper::isEmpty($siteRelativeLocation)) {
                    $newContent = preg_replace("/^.*RewriteBase.*$/imu",
                                               "\tRewriteBase " . FileHelper::getSiteRelativeLocation(),
                                               $content);
                }
                if ($newContent !== null and ( strcmp($content,
                                                      $newContent) !== 0 or ValidationHelper::isEmpty($siteRelativeLocation) )
                ) {
                    if (file_put_contents($fileName,
                                          $newContent) === false
                    ) {
                        Session::addSessionMessage(sprintf("Couldn't save '%s' file in root directory.",
                                                           SiteInstallAction::HT_ACCESS_FILE_NAME),
                                                   Message::MESSAGE_STATUS_ERROR);
                        $isError = true;
                    }
                }
                else {
                    Session::addSessionMessage(sprintf("Couldn't update '%s' file in root directory.",
                                                       SiteInstallAction::HT_ACCESS_TEMPLATE_FILE_NAME),
                                               Message::MESSAGE_STATUS_ERROR);
                    $isError = true;
                }
            }
            else {
                Session::addSessionMessage(sprintf("Couldn't read '%s' file in root directory.",
                                                   SiteInstallAction::HT_ACCESS_TEMPLATE_FILE_NAME),
                                           Message::MESSAGE_STATUS_ERROR);
                $isError = true;
            }
            if ($isError) {
                Session::addSessionMessage(sprintf("Please replace <b>RewriteBase</b> line in <b>.htaccess</b> in root directory by following line:<br/>'%s'",
                                                   "<b><i>RewriteBase " . FileHelper::getSiteRelativeLocation() . "</i></b>"),
                                           Message::MESSAGE_STATUS_INFO);
            }
            else {
                FileHelper::deleteFile($templateFileName);
            }
        }
    }