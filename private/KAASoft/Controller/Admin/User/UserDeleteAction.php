<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\User;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class UserDeleteAction
     * @package KAASoft\Controller\Admin\User
     */
    class UserDeleteAction extends AdminActionBase {
        /**
         * UserDeleteAction constructor.
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
            $userId = $args["userId"];
            if ($userId === 1) {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("There is no ability to delete user with ID = 1.") ]);
            }
            else {
                $userDatabaseHelper = new UserDatabaseHelper($this);
                try {
                    if (Helper::isAjaxRequest()) {
                        if ($this->startDatabaseTransaction()) {
                            if ($userDatabaseHelper->isUserExist($userId)) {
                                $result = $userDatabaseHelper->deleteUser($userId);
                                if ($result === false) {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = sprintf(_("Couldn't delete User '%d' for some reason."),
                                                            $userId);
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                    return new DisplaySwitch();
                                }
                            }
                            else {
                                $this->kaaSoftDatabase->rollbackTransaction();
                                $errorMessage = sprintf(_("There is no User with Id '%d' in database table \"%s\"."),
                                                        $userId,
                                                        KAASoftDatabase::$USERS_TABLE_NAME);
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("User is deleted successfully.") ]);
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
                    $errorMessage = sprintf(_("Couldn't delete User '%d'.%s%s"),
                                            $userId,
                                            Helper::HTML_NEW_LINE,
                                            $e->getMessage());
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                }
            }

            return new DisplaySwitch();
        }
    }