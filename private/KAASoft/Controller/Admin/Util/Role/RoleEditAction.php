<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util\Role;

    use Exception;
    use KAASoft\Environment\Routes\Admin\UtilRoutes;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\Util\Permission;
    use KAASoft\Database\Entity\Util\Role;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;

    /**
     * Class RoleEditAction
     * @package KAASoft\Controller\Admin\Util\Role
     */
    class RoleEditAction extends AdminActionBase {
        /**
         * RoleEditAction constructor.
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
            $roleId = $args["roleId"];
            $utilDatabaseHelper = new UtilDatabaseHelper($this);
            if (Helper::isPostRequest()) { // POST request
                try {
                    if ($this->startDatabaseTransaction()) {

                        $role = Role::getObjectInstance(array_merge($_POST,
                                                                    [ "id" => $roleId ]));

                        $result = $utilDatabaseHelper->saveRole($role);
                        if ($result === false) {
                            $this->rollbackDatabaseChanges();
                            $errorMessage = _("Role saving is failed for some reason.");
                            $this->session->addSessionMessage($errorMessage,
                                                              Message::MESSAGE_STATUS_ERROR);
                        }
                        else {
                            if ($utilDatabaseHelper->processRolePermissions($roleId,
                                                                            $this->session) !== true
                            ) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Role Permissions saving is failed for some reason.");
                                $this->session->addSessionMessage($errorMessage,
                                                                  Message::MESSAGE_STATUS_ERROR);
                            }
                            else {
                                $this->commitDatabaseChanges();
                                $errorMessage = _("Role Permissions is successfully saved.");
                                $this->session->addSessionMessage($errorMessage,
                                                                  Message::MESSAGE_STATUS_INFO);
                            }
                        }
                    }
                }
                catch (Exception $e) {
                    $this->rollbackDatabaseChanges();
                    ControllerBase::getLogger()->error($e->getMessage(),
                                                       $e);
                    $errorMessage = sprintf(_("Couldn't save User.%s%s"),
                                            Helper::HTML_NEW_LINE,
                                            $e->getMessage());
                    $this->session->addSessionMessage($errorMessage,
                                                      Message::MESSAGE_STATUS_ERROR);

                }

                return new DisplaySwitch(null,
                                         $this->routeController->getRouteString(UtilRoutes::ROLE_EDIT_ROUTE,
                                                                                [ 'roleId' => $roleId ]));

            }
            else {
                $role = $utilDatabaseHelper->getRole($roleId);

                if ($role === null) {
                    $this->session->addSessionMessage(sprintf(_("Role with ID = '%d' is not found."),
                                                              $roleId),
                                                      Message::MESSAGE_STATUS_ERROR);

                    return new DisplaySwitch(null,
                                             $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
                }

                $this->smarty->assign("action",
                                      "edit");
                $this->smarty->assign("role",
                                      $role);

                $utilDatabaseHelper = new UtilDatabaseHelper($this);
                $rolePermissions = $this->kaaSoftDatabase->getRolePermissions($role->getId());
                $permissions = $utilDatabaseHelper->getAllPermissions();

                foreach ($permissions as $permission) {
                    if ($permission instanceof Permission) {
                        $permission->setIsRolePermission(false);
                        foreach ($rolePermissions as $rolePermission) {
                            if ($rolePermission instanceof Permission) {
                                if ($permission->getId() === $rolePermission->getId()) {
                                    $permission->setIsRolePermission(true);
                                    break;
                                }
                            }
                        }
                    }
                }

                $this->smarty->assign("permissions",
                                      $permissions);

                return new DisplaySwitch('admin/roles/role.tpl');
            }
        }
    }