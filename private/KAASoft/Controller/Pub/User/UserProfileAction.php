<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub\User;

    use Exception;
    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\Admin\User\UserDatabaseHelper;
    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;

    /**
     * Class UserProfileAction
     * @package KAASoft\Controller\Admin\User
     */
    class UserProfileAction extends PublicActionBase {
        /**
         * UserProfileAction constructor.
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
            $userDatabaseHelper = new UserDatabaseHelper($this);
            $utilDatabaseHelper = new UtilDatabaseHelper($this);
            $user = $this->session->getUser();
            if ($user !== null and $user instanceof User) {
                $userId = $user->getId();
                if (Helper::isAjaxRequest()) {
                    if (Helper::isPostRequest()) { // POST request
                        try {
                            if ($this->startDatabaseTransaction()) {

                                $userUpdate = User::getObjectInstance($_POST);
                                $userUpdate->setId($userId);
                                $userUpdate->setIsLdapUser($user->isLdapUser());
                                $userUpdate->setIsActive($user->isActive());
                                $userUpdate->setRoleId($user->getRoleId());
                                $userUpdate->setRole($user->getRole());
                                if ($userUpdate->getPhotoId() !== null) {
                                    $userUpdate->setPhoto($user->getPhoto());
                                }
                                $userUpdate->setCreationDateTime($user->getCreationDateTime());
                                $userUpdate->setUpdateDateTime(Helper::getDateTimeString());

                                if (!empty( $userUpdate->getPassword() )) {
                                    $userUpdate->setPassword(Helper::encryptPassword($userUpdate->getPassword()));
                                }

                                $result = $userDatabaseHelper->saveUserPublic($userUpdate);
                                if ($result === false) {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = _("User saving is failed for some reason.");
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                                }

                                $this->commitDatabaseChanges();
                                $this->putArrayToAjaxResponse([ "userId" => $userId ]);
                                //update user
                                Session::addSessionValue(Session::USER,
                                                         $userUpdate);
                            }
                        }
                        catch (Exception $e) {
                            $this->rollbackDatabaseChanges();
                            ControllerBase::getLogger()->error($e->getMessage(),
                                                               $e);
                            $errorMessage = sprintf(_("Couldn't save User.%s%s"),
                                                    Helper::HTML_NEW_LINE,
                                                    $e->getMessage());
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                        }
                    }

                    return new DisplaySwitch();
                }
                else {
                    $this->smarty->assign("action",
                                          "edit");
                    $this->smarty->assign("editedUser",
                                          $user);
                    $this->smarty->assign("roles",
                                          $utilDatabaseHelper->getRoles());

                    $bookHelper = new BookDatabaseHelper($this);
                    $this->smarty->assign("lastIssuedBooks",
                                          $bookHelper->getUserIssuedBooks($userId,
                                                                          0,
                                                                          10));
                    $this->smarty->assign("lastRequestedBooks",
                                          $bookHelper->getUserRequestedBooks($userId,
                                                                             0,
                                                                             10));


                    return new DisplaySwitch('user/profile.tpl');
                }
            }
            else {
                $this->session->addSessionMessage(_("Please log in to view this page."),
                                                  Message::MESSAGE_STATUS_ERROR);

                return new DisplaySwitch(null,
                                         $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_FORBIDDEN_ROUTE));
            }
        }
    }