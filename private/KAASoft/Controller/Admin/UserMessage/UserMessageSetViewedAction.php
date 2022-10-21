<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\UserMessage;

    use Exception;
    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class UserMessageSetViewedAction
     * @package KAASoft\Controller\Admin\UserMessage
     */
    class UserMessageSetViewedAction extends AdminActionBase {
        /**
         * UserMessageSetViewedAction constructor.
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
            $messageId = isset( $_POST["messageId"] ) ? $_POST["messageId"] : null;

            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($messageId === null) {
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Message ID is not specified in request.") ]);

                            return new DisplaySwitch();
                        }
                        $utilHelper = new UtilDatabaseHelper($this);
                        if ($this->startDatabaseTransaction()) {
                            $result = $utilHelper->setUserMessageViewed($messageId);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Message updating is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Messages is successfully updated.") ]);
                        }
                    }
                    catch (Exception $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't update message.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }
                else {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("POST method is required only.") ]);
                }

            }
            else {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
            }

            return new DisplaySwitch();
        }
    }