<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Request;


    use Exception;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Request;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;

    class RequestEditAction extends AdminActionBase {
        /**
         * RequestEditAction constructor.
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
            $requestId = $args["requestId"];
            $requestDatabaseHelper = new RequestDatabaseHelper($this);
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {
                            //$publisherId = $_POST["publisherId"];
                            $request = Request::getObjectInstance($_POST);
                            $request->setId($requestId);

                            $result = $requestDatabaseHelper->saveRequest($request);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Request saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "requestId" => $requestId ]);
                        }
                    }
                    catch (Exception $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't save Request.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }

                return new DisplaySwitch();
            }
            else {
                $request = null;
                if ($requestId !== null) {
                    $request = $requestDatabaseHelper->getRequest($requestId);

                    if ($request === null) {
                        $this->session->addSessionMessage(sprintf(_("Request with id = '%d' is not found."),
                                                                  $requestId),
                                                          Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
                    }
                }
                $this->smarty->assign("action",
                                      "edit");
                $this->smarty->assign("request",
                                      $request);

                return new DisplaySwitch('admin/requests/request.tpl');
            }
        }
    }