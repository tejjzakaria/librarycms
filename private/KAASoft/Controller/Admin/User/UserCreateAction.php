<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\User;

    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Database\Entity\Util\Role;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class UserCreateAction
     * @package KAASoft\Controller\Admin\User
     */
    class UserCreateAction extends AdminActionBase {
        /**
         * UserCreateAction constructor.
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
                            $userDatabaseHelper = new UserDatabaseHelper($this);
                            $userCreator = $this->session->getUser();
                            $isCreatorAdmin = $userCreator->getRoleId() === Role::BUILTIN_USER_ROLES[Role::ADMIN_ROLE_ID];

                            $user = User::getObjectInstance($_POST);
                            $isAdmin = $user->getRoleId() === Role::BUILTIN_USER_ROLES[Role::ADMIN_ROLE_ID];
                            // check if creator is admin and created user is also admin (admin can create new admin only!!!)
                            if ($isAdmin and !$isCreatorAdmin) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("You don't have permissions to create ADMIN user.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            $user->setPassword(Helper::encryptPassword($user->getPassword()));
                            $user->setIsLdapUser(false);
                            $user->setCreationDateTime(Helper::getDateTimeString());
                            $user->setUpdateDateTime($user->getCreationDateTime());
                            $userId = $userDatabaseHelper->saveUser($user);

                            if ($userId === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("User saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "userId" => $userId ]);
                        }
                    }
                    catch (PDOException $e) {
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
                $utilDatabaseHelper = new UtilDatabaseHelper($this);

                $this->smarty->assign("action",
                                      "create");
                $this->smarty->assign("editedUser",
                                      null);
                $this->smarty->assign("roles",
                                      $utilDatabaseHelper->getRoles());

                return new DisplaySwitch('admin/users/user.tpl');
            }
        }
    }