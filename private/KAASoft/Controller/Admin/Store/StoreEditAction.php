<?php
    /**
 * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
 */

    namespace KAASoft\Controller\Admin\Store;

    use Exception;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Store;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;

    /**
     * Class StoreEditAction
     * @package KAASoft\Controller\Admin\Store
     */
    class StoreEditAction extends AdminActionBase {
        /**
         * StoreEditAction constructor.
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
            $storeId = $args["storeId"];
            $storeDatabaseHelper = new StoreDatabaseHelper($this);
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {
                            //$storeId = $_POST["storeId"];
                            $store = Store::getObjectInstance($_POST);
                            $store->setId($storeId);

                            $result = $storeDatabaseHelper->saveStore($store);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Store saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "storeId" => $storeId ]);
                        }
                    }
                    catch (Exception $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't save Store.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }

                return new DisplaySwitch();
            }
            else {
                $store = null;
                if ($storeId !== null) {
                    $store = $storeDatabaseHelper->getStore($storeId);

                    if ($store === null) {
                        $this->session->addSessionMessage(sprintf(_("Store with id = '%d' is not found."),
                                                                  $storeId),
                                                          Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
                    }
                }
                $this->smarty->assign("action",
                                      "edit");
                $this->smarty->assign("store",
                                      $store);

                return new DisplaySwitch('admin/stores/store.tpl');
            }
        }
    }