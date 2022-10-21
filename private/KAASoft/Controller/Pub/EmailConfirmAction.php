<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub;


    use KAASoft\Environment\Routes\Admin\UserRoutes;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Controller\Admin\User\UserDatabaseHelper;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Environment\Session;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Message;

    /**
     * Class EmailConfirmAction
     * @package KAASoft\Controller\Admin\General
     */
    class EmailConfirmAction extends PublicActionBase {

        /**
         * EmailConfirmAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute,
                                true);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $helper = new UserDatabaseHelper($this);
            $hash = $args["token"];

            $passwordRecovery = $helper->getPasswordRecovery($hash);
            if ($passwordRecovery !== null) {
                if ($this->startDatabaseTransaction()) {
                    $user = $helper->getUserByEmail($passwordRecovery->getEmail());
                    if ($user !== null) {
                        $result = $helper->activateUser($user->getId(),
                                                        true);
                        if ($result !== false) {
                            $result = $helper->deleteRecovery($passwordRecovery->getHash());
                            if ($result !== false) {
                                $this->commitDatabaseChanges();
                                Session::addSessionMessage(_("You are confirmed yor email. Please log in by using your credentials."),
                                                           Message::MESSAGE_STATUS_INFO);

                                return new DisplaySwitch(null,
                                                         $this->getRouteString(UserRoutes::USER_EDIT_ROUTE,
                                                                               [ "userId" => $user->getId() ]));
                            }
                            else {
                                $this->rollbackDatabaseChanges();
                                Session::addSessionMessage(_("Couldn't delete token from database."),
                                                           Message::MESSAGE_STATUS_ERROR);

                                return new DisplaySwitch(null,
                                                         $this->getRouteString(GeneralPublicRoutes::PUBLIC_INDEX_ROUTE));
                            }
                        }
                        else {
                            $this->rollbackDatabaseChanges();
                            Session::addSessionMessage(_("Couldn't activate user."),
                                                       Message::MESSAGE_STATUS_ERROR);

                            return new DisplaySwitch(null,
                                                     $this->getRouteString(GeneralPublicRoutes::PUBLIC_INDEX_ROUTE));
                        }
                    }
                    else {
                        $this->rollbackDatabaseChanges();
                        Session::addSessionMessage(_("Couldn't find user for specified token."),
                                                   Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 $this->getRouteString(GeneralPublicRoutes::PUBLIC_INDEX_ROUTE));
                    }
                }
                else {
                    $errorMessage = _("Couldn't create database transaction.");
                    $this->session->addSessionMessage($errorMessage,
                                                      Message::MESSAGE_STATUS_ERROR);

                    return new DisplaySwitch(null,
                                             $this->getRouteString(GeneralPublicRoutes::PUBLIC_INDEX_ROUTE));
                }
            }
            else {
                $errorMessage = _("Couldn't find specified token.");
                $this->session->addSessionMessage($errorMessage,
                                                  Message::MESSAGE_STATUS_ERROR);

                return new DisplaySwitch(null,
                                         $this->getRouteString(GeneralPublicRoutes::PUBLIC_INDEX_ROUTE));
            }
        }
    }