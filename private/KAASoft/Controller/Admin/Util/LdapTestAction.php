<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util;


    use Exception;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Database\Entity\Util\Role;
    use KAASoft\Environment\LdapSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Exception\BindException;
    use KAASoft\Util\Helper;
    use KAASoft\Util\LDAP\LDAP;
    use KAASoft\Util\LDAP\LdapHelper;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class LdapTestAction
     * @package KAASoft\Controller\Admin\Util
     */
    class LdapTestAction extends AdminActionBase {
        /**
         * LdapTestAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         * @throws \Exception
         */
        protected function action($args) {
            if (Helper::isPostRequest() and Helper::isAjaxRequest()) {
                $ldapSetting = new LdapSettings();
                $ldapSetting->copySettings($_POST);

                $login = ValidationHelper::getString($_POST["login"]);
                $password = ValidationHelper::getString($_POST["password"]);

                $ldap = new LDAP([ "server"        => $ldapSetting->getServer(),
                                   "port"          => $ldapSetting->getPort(),
                                   "version"       => 3,
                                   "timeout"       => 5,
                                   "baseDN"        => $ldapSetting->getSearchBase(),
                                   "adminUserName" => $ldapSetting->getServiceAccountDN(),
                                   "adminPassword" => $ldapSetting->getServiceAccountPassword() ]);
                // connect to ldap
                try {
                    $ldap->connect();
                }
                catch (BindException $be) {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Connection to LDAP server is not established. Please check connection details.") ]);

                    return new DisplaySwitch();
                }

                $ldapUser = $ldap->findUserByLogin($login,
                                                   $ldapSetting->getLoginAttributeName(),
                                                   $ldapSetting->getSearchBase(),
                                                   $ldapSetting);

                if ($ldapUser !== null) {
                    try {
                        if ($ldap->attemptLogin($ldapUser->getDn(),
                                                $password)
                        ) {
                            $roleId = LdapHelper::getUserRoleId($ldapUser,
                                                                $ldapSetting);
                            if ($roleId !== null) {

                                $user = new User();
                                $user->setRoleId($roleId);
                                $user->setFirstName($ldapUser->getFirstName());
                                $user->setLastName($ldapUser->getLastName());
                                $user->setEmail($ldapUser->getMail() === null ? $login : $ldapUser->getMail());
                                $utilHelper = new UtilDatabaseHelper($this);

                                $role = $utilHelper->getRole($roleId);
                                if ($role === null) {
                                    $role = new Role($roleId);
                                }
                                $user->setRole($role);

                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => $user ]);

                            }
                            else {
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_WARNING => _("Connection is successfully established.<br/> Specified user is logged in to LDAP but user doesn't have required permissions to log in to Library CMS (user is not a member of required group).") ]);
                            }
                        }
                        else {
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_WARNING => _("Connection is successfully established but specified user is not logged in.") ]);
                        }
                    }
                    catch (Exception $exception) {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $exception->getMessage() ]);
                    }
                }
                else {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_WARNING => _("Connection is successfully established but specified user is not found.") ]);
                }
            }
            else {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("POST AJAX request is required only.") ]);
            }

            return new DisplaySwitch();
        }
    }