<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util;


    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\Util\Language;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Routes\Admin\UtilRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use PDOException;

    /**
     * Class LanguagesViewAction
     * @package KAASoft\Controller\Admin\Util
     */
    class LanguagesViewAction extends AdminActionBase {
        /**
         * LanguagesViewAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $utilHelper = new UtilDatabaseHelper($this);
            if (Helper::isPostRequest()) {
                if (isset( $_POST["name"] )) {
                    $names = $_POST["name"];
                    $codes = $_POST["code"];
                    $shortCodes = $_POST["shortCode"];
                    $ids = $_POST["id"];
                    $RTLs = $_POST["RTL"];
                    $isActives = $_POST["status"];

                    try {
                        if ($this->startDatabaseTransaction()) {

                            $result = $this->kaaSoftDatabase->deleteAllTableContent(KAASoftDatabase::$LANGUAGES_TABLE_NAME);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Couldn't clean languages list.");
                                ControllerBase::getLogger()->error($errorMessage);
                                Session::addSessionMessage($errorMessage,
                                                           Message::MESSAGE_STATUS_ERROR);

                                return new DisplaySwitch(null,
                                                         $this->routeController->getRouteString(UtilRoutes::LANGUAGE_LIST_VIEW_ROUTE));
                            }

                            // add all controls
                            foreach ($names as $index => $name) {
                                $language = Language::getObjectInstance([ "code"      => $codes[$index],
                                                                          "isActive"  => $isActives[$index],
                                                                          "name"      => $names[$index],
                                                                          "shortCode" => $shortCodes[$index],
                                                                          "isRTL"     => $RTLs[$index] ]);

                                if (isset( $ids[$index] ) && !empty( $ids[$index] )) {
                                    $language->setId($ids[$index]);
                                }
                                $result = $utilHelper->insertLanguage($language);
                                if ($result === false) {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = _("Couldn't save language.");
                                    ControllerBase::getLogger()->error($errorMessage);
                                    Session::addSessionMessage($errorMessage,
                                                               Message::MESSAGE_STATUS_ERROR);

                                    return new DisplaySwitch(null,
                                                             $this->routeController->getRouteString(UtilRoutes::LANGUAGE_LIST_VIEW_ROUTE));
                                }
                            }

                            $this->commitDatabaseChanges();
                        }
                    }
                    catch (PDOException $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't update languages.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }
            }

            $this->smarty->assign("allLanguages",
                                  $this->kaaSoftDatabase->getLanguages());

            return new DisplaySwitch('admin/languages.tpl');
        }
    }