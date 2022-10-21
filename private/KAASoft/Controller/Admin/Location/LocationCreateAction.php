<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Location;

    use KAASoft\Controller\Admin\Store\StoreDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Location;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class LocationCreateAction
     * @package KAASoft\Controller\Admin\Location
     */
    class LocationCreateAction extends AdminActionBase {
        /**
         * LocationCreateAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         * @throws \Exception
         */
        protected function action($args) {
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {
                            $locationDatabaseHelper = new LocationDatabaseHelper($this);

                            $location = Location::getObjectInstance($_POST);
                            $locationId = $locationDatabaseHelper->saveLocation($location);

                            if ($locationId === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Location saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "locationId" => $locationId ]);
                        }
                    }
                    catch (PDOException $e) {
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
                $storeHelper = new StoreDatabaseHelper($this);
                $this->smarty->assign("action",
                                      "create");
                $this->smarty->assign("stores",
                                      $storeHelper->getStores());

                return new DisplaySwitch('admin/locations/location.tpl');
            }
        }
    }