<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util\Role;

    use KAASoft\Environment\Routes\Admin\UtilRoutes;
    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\Util\Role;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use PDOException;

    /**
     * Class RoleCreateAction
     * @package KAASoft\Controller\Admin\Util\Role
     */
    class RoleCreateAction extends AdminActionBase {
        /**
         * RoleCreateAction constructor.
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
            $this->smarty->assign("action",
                                  "create");
            if (Helper::isPostRequest()) { // POST request
                try {
                    if ($this->startDatabaseTransaction()) {
                        $utilDatabaseHelper = new UtilDatabaseHelper($this);

                        $role = Role::getObjectInstance($_POST);

                        $roleId = $utilDatabaseHelper->saveRole($role);
                        if ($roleId === false) {
                            $this->rollbackDatabaseChanges();
                            $errorMessage = _("Role saving is failed for some reason.");
                            $this->session->addSessionMessage($errorMessage,
                                                              Message::MESSAGE_STATUS_ERROR);

                            return new DisplaySwitch(null,
                                                     $this->routeController->getRouteString(UtilRoutes::ROLE_CREATE_ROUTE));

                        }
                        else {
                            if ($utilDatabaseHelper->processRolePermissions($roleId,
                                                                            $this->session) !== true
                            ) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("RolePermission saving is failed for some reason.");
                                $this->session->addSessionMessage($errorMessage,
                                                                  Message::MESSAGE_STATUS_ERROR);

                                return new DisplaySwitch(null,
                                                         $this->routeController->getRouteString(UtilRoutes::ROLE_CREATE_ROUTE));
                            }
                        }

                        $this->commitDatabaseChanges();
                        $message = _("Role is saved successfully.");
                        $this->session->addSessionMessage($message,
                                                          Message::MESSAGE_STATUS_INFO);

                        return new DisplaySwitch(null,
                                                 $this->routeController->getRouteString(UtilRoutes::ROLE_EDIT_ROUTE,
                                                                                        [ 'roleId' => $roleId ]));
                    }
                }
                catch (PDOException $e) {
                    $this->rollbackDatabaseChanges();
                    ControllerBase::getLogger()->error($e->getMessage(),
                                                       $e);
                    $errorMessage = sprintf(_("Couldn't create Role.%s%s"),
                                            Helper::HTML_NEW_LINE,
                                            $e->getMessage());
                    $this->session->addSessionMessage($errorMessage,
                                                      Message::MESSAGE_STATUS_ERROR);

                    return new DisplaySwitch(null,
                                             $this->routeController->getRouteString(UtilRoutes::ROLE_CREATE_ROUTE));
                }

                return new DisplaySwitch('admin/roles/role.tpl');
            }
            else {
                $utilDatabaseHelper = new UtilDatabaseHelper($this);
                $this->smarty->assign("role",
                                      null);
                $this->smarty->assign("permissions",
                                      $utilDatabaseHelper->getAllPermissions());

                return new DisplaySwitch('admin/roles/role.tpl');
            }
        }
    }