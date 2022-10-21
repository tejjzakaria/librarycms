<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Publisher;

    use Exception;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Publisher;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;

    /**
     * Class PublisherEditAction
     * @package KAASoft\Controller\Admin\Publisher
     */
    class PublisherEditAction extends AdminActionBase {
        /**
         * PublisherEditAction constructor.
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
            $publisherId = $args["publisherId"];
            $publisherDatabaseHelper = new PublisherDatabaseHelper($this);
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {
                            //$publisherId = $_POST["publisherId"];
                            $publisher = Publisher::getObjectInstance($_POST);
                            $publisher->setId($publisherId);

                            $result = $publisherDatabaseHelper->savePublisher($publisher);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Publisher saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "publisherId" => $publisherId ]);
                        }
                    }
                    catch (Exception $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't save Publisher.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }

                return new DisplaySwitch();
            }
            else {
                $publisher = null;
                if ($publisherId !== null) {
                    $publisher = $publisherDatabaseHelper->getPublisher($publisherId);

                    if ($publisher === null) {
                        $this->session->addSessionMessage(sprintf(_("Publisher with id = '%d' is not found."),
                                                                  $publisherId),
                                                          Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
                    }
                }
                $this->smarty->assign("action",
                                      "edit");
                $this->smarty->assign("publisher",
                                      $publisher);

                return new DisplaySwitch('admin/publishers/publisher.tpl');
            }
        }
    }