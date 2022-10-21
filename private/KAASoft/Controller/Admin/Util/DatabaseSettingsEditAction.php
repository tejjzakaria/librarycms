<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util;

    use Exception;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\DatabaseConnection;
    use KAASoft\Environment\DatabaseSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;


    /**
     * Class DatabaseSettingsEditAction
     * @package KAASoft\Controller\Admin\Util
     */
    class DatabaseSettingsEditAction extends AdminActionBase {
        /**
         * DatabaseSettingsEditAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute,
                                false);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         * @throws Exception
         */
        protected function action($args) {
            $settings = new DatabaseSettings();
            if (Helper::isPostRequest()) {

                if (isset( $_POST["name"] )) {
                    $connectionNumber = count($_POST["name"]);
                    for ($i = 0; $i < $connectionNumber; $i++) {
                        $databaseConnection = new DatabaseConnection();
                        $databaseConnection->setName($_POST["name"][$i]);
                        $databaseConnection->setHost($_POST["host"][$i]);
                        $databaseConnection->setPort($_POST["port"][$i]);
                        $databaseConnection->setUserName($_POST["username"][$i]);
                        $databaseConnection->setPassword($_POST["password"][$i]);
                        $databaseConnection->setCharset($_POST["charset"][$i]);
                        $databaseConnection->setDatabaseName($_POST["databaseName"][$i]);
                        $databaseConnection->setDatabaseType($_POST["databaseType"][$i]);

                        $settings->addDatabaseConnection($databaseConnection);

                        if (isset( $_POST["connectionStatus"][$i] )) {
                            $settings->setActiveConnectionName($databaseConnection->getName());
                        }
                    }
                    $settings->saveSettings();
                }
            }
            else {
                $settings->loadSettings();
            }

            $this->smarty->assign("databaseSettings",
                                  $settings);

            return new DisplaySwitch('admin/databaseSettings.tpl');
        }
    }