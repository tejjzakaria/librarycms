<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util\EmailNotification;

    use Exception;
    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class EmailNotificationEnableAction
     * @package KAASoft\Controller\Admin\Util\EmailNotification
     */
    class EmailNotificationEnableAction extends AdminActionBase {
        /**
         * EmailNotificationEnableAction constructor.
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
            $route = $args["route"];
            $isEnabled = $args["enable"] === "enable";
            if (Helper::isAjaxRequest() and Helper::isPostRequest()) { // POST request
                try {
                    if ($this->startDatabaseTransaction()) {
                        $utilDatabaseHelper = new UtilDatabaseHelper($this);

                        $result = $utilDatabaseHelper->enableEmailNotification($route,
                                                                               $isEnabled);
                        if ($result === false) {
                            $this->rollbackDatabaseChanges();
                            $errorMessage = _("EmailNotification updating is failed for some reason.");
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                        }
                        else {
                            $this->commitDatabaseChanges();
                            $successMessage = _("EmailNotification is successfully saved.");
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => $successMessage ]);
                        }
                    }
                }
                catch (Exception $e) {
                    $this->rollbackDatabaseChanges();
                    ControllerBase::getLogger()->error($e->getMessage(),
                                                       $e);
                    $errorMessage = sprintf(_("Couldn't update EmailNotification.%s%s"),
                                            Helper::HTML_NEW_LINE,
                                            $e->getMessage());
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                }
            }

            return new DisplaySwitch();
        }
    }