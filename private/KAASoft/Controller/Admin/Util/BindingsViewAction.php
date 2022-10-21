<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util;


    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Binding;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Routes\Admin\UtilRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use KAASoft\Util\ValidationHelper;
    use PDOException;

    /**
     * Class BindingsViewAction
     * @package KAASoft\Controller\Admin\Util
     */
    class BindingsViewAction extends AdminActionBase {
        /**
         * BindingsViewAction constructor.
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
                $names = ValidationHelper::getArray( $_POST["names"] );
                if ($names !== null) {
                    try {
                        if ($this->startDatabaseTransaction()) {

                            $result = $this->kaaSoftDatabase->deleteAllTableContent(KAASoftDatabase::$BINDINGS_TABLE_NAME);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Couldn't clean binding list.");
                                ControllerBase::getLogger()->error($errorMessage);
                                Session::addSessionMessage($errorMessage,
                                                           Message::MESSAGE_STATUS_ERROR);

                                return new DisplaySwitch(null,
                                                         $this->routeController->getRouteString(UtilRoutes::BINDING_LIST_VIEW_ROUTE));
                            }

                            $bindings = [];

                            foreach ($names as $name) {
                                $bindings[] = Binding::getObjectInstance([ "name" => $name ]);
                            }
                            $result = $utilHelper->saveBindings($bindings);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Couldn't save bindings.");
                                ControllerBase::getLogger()->error($errorMessage);
                                Session::addSessionMessage($errorMessage,
                                                           Message::MESSAGE_STATUS_ERROR);

                                return new DisplaySwitch(null,
                                                         $this->routeController->getRouteString(UtilRoutes::BINDING_LIST_VIEW_ROUTE));
                            }

                            $this->commitDatabaseChanges();
                        }
                    }
                    catch (PDOException $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't update bindings.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }
            }

            $this->smarty->assign("bindings",
                                  $this->kaaSoftDatabase->getBindings());

            return new DisplaySwitch('admin/utils/bindings.tpl');
        }
    }