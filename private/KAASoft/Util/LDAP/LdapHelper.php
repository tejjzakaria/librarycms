<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-02-15
     */


    namespace KAASoft\Util\LDAP;


    use Exception;
    use KAASoft\Controller\ActionBase;
    use KAASoft\Controller\Admin\User\UserDatabaseHelper;
    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Database\Entity\Util\Role;
    use KAASoft\Environment\LdapSettings;
    use KAASoft\Util\Helper;

    class LdapHelper {
        /**
         * @param ActionBase    $action
         * @param  LdapSettings $ldapSetting
         * @param string        $login
         * @param string        $password
         * @return bool|User
         */
        public static function ldapAttemptLogin(ActionBase $action, $ldapSetting, $login, $password) {
            if ($ldapSetting !== null and $ldapSetting instanceof LdapSettings) {
                $ldap = new LDAP([ "server"        => $ldapSetting->getServer(),
                                   "port"          => $ldapSetting->getPort(),
                                   "version"       => 3,
                                   "timeout"       => 5,
                                   "baseDN"        => $ldapSetting->getSearchBase(),
                                   "adminUserName" => $ldapSetting->getServiceAccountDN(),
                                   "adminPassword" => $ldapSetting->getServiceAccountPassword() ]);
                // connect to ldap
                $ldap->connect();

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
                                $user->setIsActive(true);
                                $user->setIsLdapUser(true);
                                $user->setFirstName($ldapUser->getFirstName());
                                $user->setLastName($ldapUser->getLastName());
                                $user->setEmail($ldapUser->getMail() === null ? $login : $ldapUser->getMail());
                                $user->setPassword(Helper::encryptPassword($password));
                                $user->setCreationDateTime(Helper::getDateTimeString());
                                $user->setUpdateDateTime($user->getCreationDateTime());
                                $utilHelper = new UtilDatabaseHelper($action);

                                $role = $utilHelper->getRole($roleId);
                                if ($role === null) {
                                    $role = new Role($roleId);
                                }
                                $user->setRole($role);

                                if ($ldapSetting->isUserAutoRegistration()) {
                                    $userHelper = new UserDatabaseHelper($action);

                                    $dbUser = $userHelper->getUserByEmail($user->getEmail());

                                    if ($dbUser !== null) {
                                        $user->setId($dbUser->getId());
                                    }
                                    if ($action->getKaaSoftDatabase()->startTransaction()) {
                                        $userId = $userHelper->saveUser($user);
                                        if ($userId === false) {
                                            $action->getKaaSoftDatabase()->rollbackTransaction();
                                            ControllerBase::getLogger()->warn(sprintf("Couldn't save LDAP user '%s' in database.",
                                                                                      $login));
                                        }
                                        else {
                                            $action->getKaaSoftDatabase()->commitTransaction();
                                            ControllerBase::getLogger()->info(sprintf("LDAP user '%s' is successfully saved in database.",
                                                                                      $login));
                                            if ($dbUser === null) {
                                                $user->setId($userId);
                                            }
                                        }
                                    }
                                }

                                return $user;
                            }
                        }
                    }
                    catch (Exception $exception) {
                        return false;
                    }
                }
            }

            return false;
        }

        /**
         * @param $ldapUser
         * @param $ldapSetting
         * @return null|integer
         */
        public static function getUserRoleId($ldapUser, $ldapSetting) {
            if ($ldapUser !== null and $ldapSetting !== null) {
                if ($ldapUser instanceof LdapUser and $ldapSetting instanceof LdapSettings) {
                    if (in_array($ldapSetting->getAdminGroupName(),
                                 $ldapUser->getGroups())) {
                        return Role::BUILTIN_USER_ROLES[Role::ADMIN_ROLE_ID];
                    }
                    elseif (in_array($ldapSetting->getLibrarianGroupName(),
                                     $ldapUser->getGroups())) {
                        return Role::BUILTIN_USER_ROLES[Role::LIBRARIAN_ROLE_ID];
                    }
                    elseif (in_array($ldapSetting->getUserGroupName(),
                                     $ldapUser->getGroups())) {
                        return Role::BUILTIN_USER_ROLES[Role::READER_ROLE_ID];
                    }
                }
            }

            return null;
        }
    }