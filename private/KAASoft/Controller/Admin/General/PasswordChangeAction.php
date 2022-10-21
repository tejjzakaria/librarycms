<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\General;


    use KAASoft\Controller\Admin\User\UserDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;

    class PasswordChangeAction extends AdminActionBase {

        /**
         * PasswordChangeAction constructor.
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
            /*
             * 1. check received hash and time
             * 2. return password change form if success, error message if fail
             * 3. update user password in database if success
             * 4. remove hash from database and enable user access
             *
             */
            $helper = new GeneralDatabaseHelper($this);
            $hash = $args["token"];

            if (Helper::isGetRequest()) {
                if ($helper->isHashExists($hash)) {
                    $this->smarty->assign("token",
                                          $hash);

                    return new DisplaySwitch('auth/changePassword.tpl');
                }
                else {
                    $errorMessage = _("Specified token is invalid or expired.");
                    $this->session->addSessionMessage($errorMessage,
                                                      Message::MESSAGE_STATUS_ERROR);

                    return new DisplaySwitch(null,
                                             $this->routeController->getRouteString("adminLogin"));
                }
            }
            elseif (Helper::isPostRequest()) {
                if (!isset( $_POST['password'] )) {
                    $this->putArrayToAjaxResponse([ "error" => _("You have to specify password.") ]);

                    return new DisplaySwitch();
                }
                $password = $_POST['password'];
                $userHelper = new UserDatabaseHelper($this);

                $passwordRecovery = $userHelper->getPasswordRecovery($hash);
                if ($passwordRecovery !== null) {
                    if ($this->startDatabaseTransaction()) {
                        $user = $userHelper->getUserByEmail($passwordRecovery->getEmail());
                        if ($user !== null) {
                            $result = $userHelper->saveUserPassword($user->getId(),
                                                                    Helper::encryptPassword($password));
                            if ($result !== false) {
                                $result = $userHelper->activateUser($user->getId(),
                                                                    true);
                                if ($result !== false) {
                                    $result = $helper->deleteRecovery($passwordRecovery->getHash());
                                    if ($result !== false) {
                                        $this->commitDatabaseChanges();
                                        //Session::addSessionMessage("Password is updated. Please try to login.",
                                        //                         Message::MESSAGE_STATUS_INFO);
                                        //Helper::redirectTo($this->routes->getRouteString("adminLogin"));
                                        $this->putArrayToAjaxResponse([ "success" => _("Password is updated. Please try to login.") ]);

                                        return new DisplaySwitch();

                                    }
                                    else {
                                        $this->rollbackDatabaseChanges();
                                        $this->putArrayToAjaxResponse([ "error" => _("Couldn't delete token from database.") ]);

                                        return new DisplaySwitch();
                                    }
                                }
                                else {
                                    $this->rollbackDatabaseChanges();
                                    $this->putArrayToAjaxResponse([ "error" => _("Couldn't activate user.") ]);

                                    return new DisplaySwitch();
                                }
                            }
                            else {
                                $this->rollbackDatabaseChanges();
                                $this->putArrayToAjaxResponse([ "error" => _("Couldn't update user password.") ]);

                                return new DisplaySwitch();
                            }
                        }
                        else {
                            $this->rollbackDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "error" => _("Couldn't find user for specified token.") ]);

                            return new DisplaySwitch();
                        }
                    }
                }
                else {
                    $errorMessage = _("Couldn't find specified token.");
                    $this->session->addSessionMessage($errorMessage,
                                                      Message::MESSAGE_STATUS_ERROR);

                    return new DisplaySwitch(null,
                                             $this->routeController->getRouteString("adminLogin"));
                }
            }

            return new DisplaySwitch();
        }
    }