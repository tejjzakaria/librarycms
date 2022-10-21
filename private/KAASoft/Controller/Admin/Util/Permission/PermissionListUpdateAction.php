<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util\Permission;


    use KAASoft\Environment\Routes\PublicRoute;
    use KAASoft\Environment\Routes\Route;
    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Database\Entity\Util\Permission;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class PermissionListUpdateAction
     * @package KAASoft\Controller\Admin\Util\Permission
     */
    class PermissionListUpdateAction extends AdminActionBase {
        /**
         * PermissionListUpdateAction constructor.
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
            $utilDatabaseHelper = new UtilDatabaseHelper($this);
            if (Helper::isPostRequest()) {

                $allRoutes = $this->routeController->getRoutes();

                $allPermissions = $utilDatabaseHelper->getAllPermissions();

                $isError = false;
                if ($this->startDatabaseTransaction()) {
                    foreach ($allRoutes as $routeName => $route) {
                        if (!$this->isRouteInPermissionList($routeName,
                                                            $allPermissions)
                        ) {
                            $permission = new Permission();
                            $permission->setRouteName($routeName);
                            $permission->setIsPublic($route instanceof PublicRoute);
                            if ($route instanceof Route) {
                                $permission->setRouteTitle($route->getTitle());
                            }

                            if ($utilDatabaseHelper->savePermission($permission) === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("Permission '%s' couldn't be saved."),
                                                        $permission->getRouteName());
                                $this->session->addSessionMessage($errorMessage);
                                $isError = true;
                                break;
                            }
                        }
                    }
                    if (!$isError) {
                        foreach ($allPermissions as $permission) {
                            if ($permission instanceof Permission) {
                                if (!$this->isPermissionInRouteList($permission,
                                                                    $allRoutes)
                                ) {
                                    if ($utilDatabaseHelper->deletePermission($permission->getId()) === false) {
                                        $this->rollbackDatabaseChanges();
                                        $errorMessage = sprintf(_("Permission '%s' couldn't be deleted."),
                                                                $permission->getRouteName());
                                        $this->session->addSessionMessage($errorMessage);
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    $this->commitDatabaseChanges();
                    $message = _("Permissions are successfully updated.");
                    $this->session->addSessionMessage($message);
                }
            }

            $this->smarty->assign("permissions",
                                  $utilDatabaseHelper->getAllPermissions());

            return new DisplaySwitch('admin/roles/permissions.tpl');
        }

        private function isRouteInPermissionList($routeName, $permissionList) {
            foreach ($permissionList as $permission) {
                if ($permission instanceof Permission) {
                    if ($permission->getRouteName() === $routeName) {
                        return true;
                    }
                }
            }

            return false;
        }

        private function isPermissionInRouteList(Permission $permission, $routeList) {
            foreach ($routeList as $routeName => $route) {
                if ($permission->getRouteName() === $routeName) {
                    return true;
                }
            }

            return false;
        }
    }