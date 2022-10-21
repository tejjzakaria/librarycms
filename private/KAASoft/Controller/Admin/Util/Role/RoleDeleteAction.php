<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util\Role;

    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\Util\Role;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class RoleDeleteAction
     * @package KAASoft\Controller\Admin\Util\Role
     */
    class RoleDeleteAction extends AdminActionBase {
        /**
         * RoleDeleteAction constructor.
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
            try {
                if (Helper::isAjaxRequest()) {
                    if ($this->startDatabaseTransaction()) {
                        if ($roleId == Role::BUILTIN_USER_ROLES[Role::ADMIN_ROLE_ID]) {
                            $errorMessage = _("Build-in user role (Admin, etc.) couldn't be deleted.");
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                            return new DisplaySwitch();
                        }
                        if ($utilDatabaseHelper->isRoleExist($roleId)) {
                            $result = $utilDatabaseHelper->deleteRole($roleId);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("Couldn't delete Role '%d' for some reason."),
                                                        $roleId);
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }
                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Role is deleted successfully.") ]);

                        }
                        else {
                            $errorMessage = sprintf(_("There is no Role with ID '%d' in table '%s'."),
                                                    $roleId,
                                                    KAASoftDatabase::$ROLES_TABLE_NAME);
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                        }

                        return new DisplaySwitch();
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
                $errorMessage = sprintf(_("Couldn't delete Role '%d'.%s%s"),
                                        $roleId,
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
            }

            return new DisplaySwitch();
        }
    }