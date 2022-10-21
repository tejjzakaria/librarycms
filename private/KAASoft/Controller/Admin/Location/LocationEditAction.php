<?php
    /**
 * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
 */

    namespace KAASoft\Controller\Admin\Location;

    use Exception;
    use KAASoft\Controller\Admin\Store\StoreDatabaseHelper;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Location;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;

    /**
     * Class LocationEditAction
     * @package KAASoft\Controller\Admin\Location
     */
    class LocationEditAction extends AdminActionBase {
        /**
         * LocationEditAction constructor.
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
            $locationId = $args["locationId"];
            $locationDatabaseHelper = new LocationDatabaseHelper($this);
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {
                            //$locationId = $_POST["locationId"];
                            $location = Location::getObjectInstance($_POST);
                            $location->setId($locationId);

                            $result = $locationDatabaseHelper->saveLocation($location);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Location saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "locationId" => $locationId ]);
                        }
                    }
                    catch (Exception $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't save Location.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }

                return new DisplaySwitch();
            }
            else {
                $location = null;
                if ($locationId !== null) {
                    $location = $locationDatabaseHelper->getLocation($locationId);

                    if ($location === null) {
                        $this->session->addSessionMessage(sprintf(_("Location with id = '%d' is not found."),
                                                                  $locationId),
                                                          Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
                    }
                }
                $storeHelper = new StoreDatabaseHelper($this);
                $this->smarty->assign("stores",
                                      $storeHelper->getStores());
                $this->smarty->assign("action",
                                      "edit");
                $this->smarty->assign("location",
                                      $location);

                return new DisplaySwitch('admin/locations/location.tpl');
            }
        }
    }