<?php
    /**
 * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
 */

    namespace KAASoft\Controller\Admin\Location;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class LocationDeleteAction
     * @package KAASoft\Controller\Admin\Location
     */
    class LocationDeleteAction extends AdminActionBase {
        /**
         * LocationDeleteAction constructor.
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
            try {
                if (Helper::isAjaxRequest()) {
                    if ($this->startDatabaseTransaction()) {
                        if ($locationDatabaseHelper->isLocationExist($locationId)) {
                            $result = $locationDatabaseHelper->deleteLocation($locationId);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("Couldn't delete Location '%d' for some reason."),
                                                        $locationId);
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }
                        }
                        else {
                            $this->kaaSoftDatabase->rollbackTransaction();
                            $errorMessage = sprintf(_("There is no Location with Id '%d' in database table \"%s\"."),
                                                    $locationId,
                                                    KAASoftDatabase::$LOCATIONS_TABLE_NAME);
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                            return new DisplaySwitch();
                        }

                        $this->commitDatabaseChanges();
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Location is deleted successfully.") ]);
                    }
                    else {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Database transaction couldn't be created.") ]);
                    }
                }
                else {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
                }
            }
            catch (PDOException $e) {
                $this->rollbackDatabaseChanges();
                ControllerBase::getLogger()->error($e->getMessage(),
                                                   $e);
                $errorMessage = sprintf(_("Couldn't delete Location '%d'.%s%s"),
                                        $locationId,
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
            }

            return new DisplaySwitch();

        }
    }