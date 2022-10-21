<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\UserMessage;

    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class UserMessageDeleteAction
     * @package KAASoft\Controller\Admin\UserMessage
     */
    class UserMessageDeleteAction extends AdminActionBase {
        /**
         * UserMessageDeleteAction constructor.
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
            $messageId = $args["messageId"];
            $userDatabaseHelper = new UtilDatabaseHelper($this);
            try {
                if (Helper::isAjaxRequest()) {
                    if ($this->startDatabaseTransaction()) {
                        if ($userDatabaseHelper->isUserMessageExist($messageId)) {
                            $result = $userDatabaseHelper->deleteUserMessage($messageId);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("Couldn't delete message '%d' for some reason."),
                                                        $messageId);
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }
                        }
                        else {
                            $this->kaaSoftDatabase->rollbackTransaction();
                            $errorMessage = sprintf(_("There is no message with Id '%d' in database table \"%s\"."),
                                                    $messageId,
                                                    KAASoftDatabase::$USER_MESSAGES_TABLE_NAME);
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                            return new DisplaySwitch();
                        }

                        $this->commitDatabaseChanges();
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("message is deleted successfully.") ]);
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
                $errorMessage = sprintf(_("Couldn't delete message '%d'.%s%s"),
                                        $messageId,
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
            }

            return new DisplaySwitch();
        }
    }