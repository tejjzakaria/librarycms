<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\User;

    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Database\Entity\Util\Image;
    use KAASoft\Database\Entity\Util\PasswordRecovery;
    use KAASoft\Database\Entity\Util\Role;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\Helper;

    /**
     * Class UserDatabaseHelper
     * @package KAASoft\Controller\Admin\User
     */
    class UserDatabaseHelper extends DatabaseHelper {

        /**
         * @param $email
         * @return bool
         */
        public function isUserEmailExists($email) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$USERS_TABLE_NAME,
                                               [ "AND" => [ "email"    => $email,
                                                            "provider" => null,
                                                            "socialId" => null ] ]);
        }

        /**
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getUsers($offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = null;
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }

            $usersQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$USERS_TABLE_NAME,
                                                               [ "[><]" . KAASoftDatabase::$ROLES_TABLE_NAME => [ "roleId" => "id" ] ],
                                                               array_merge(User::getDatabaseFieldNames(),
                                                                           [ KAASoftDatabase::$USERS_TABLE_NAME . ".id" ],
                                                                           Role::getDatabaseFieldNames()),
                                                               $queryParams);

            if ($usersQueryResult !== false) {
                $users = [];

                foreach ($usersQueryResult as $userRow) {
                    $user = User::getObjectInstance($userRow);

                    $role = Role::getObjectInstance($userRow);
                    $role->setId($user->getRoleId());
                    $user->setRole($role);

                    $users[] = $user;
                }

                return $users;
            }

            return null;
        }

        /**
         * @return bool|int
         */
        public function getUsersCount() {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$USERS_TABLE_NAME);
        }

        /**
         * @param $roleId
         * @return bool|int
         */
        public function getRoleUsersCount($roleId) {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$USERS_TABLE_NAME,
                                                 [ "roleId" => $roleId ]);
        }

        /**
         * @param $userId
         * @return User|null
         */
        public function getUser($userId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$USERS_TABLE_NAME,
                                                       [ "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME => [ "photoId" => "id" ],
                                                         "[>]" . KAASoftDatabase::$ROLES_TABLE_NAME  => [ "roleId" => "id" ] ],
                                                       array_merge(User::getDatabaseFieldNames(),
                                                                   Role::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$USERS_TABLE_NAME . ".id",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime" ]),
                                                       [ KAASoftDatabase::$USERS_TABLE_NAME . ".id" => $userId ]);
            if ($queryResult !== false) {
                $user = User::getObjectInstance($queryResult);

                if (isset( $queryResult["photoId"] )) {
                    $image = Image::getObjectInstance($queryResult);
                    if (file_exists($image->getAbsolutePath())) {
                        $image->setId($queryResult["photoId"]);
                        $image->setTitle($queryResult["imageTitle"]);
                        $user->setPhoto($image);
                    }
                }

                if (isset( $queryResult["roleId"] )) {
                    $role = Role::getObjectInstance($queryResult);
                    $role->setId($queryResult["roleId"]);
                    $user->setRole($role);
                }

                return $user;

            }

            return null;
        }

        /**
         * @param $email
         * @return User|null
         */
        public function getUserByEmail($email) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$USERS_TABLE_NAME,
                                                       [ "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME => [ "photoId" => "id" ],
                                                         "[>]" . KAASoftDatabase::$ROLES_TABLE_NAME  => [ "roleId" => "id" ] ],
                                                       array_merge(User::getDatabaseFieldNames(),
                                                                   Role::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$USERS_TABLE_NAME . ".id",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime" ]),
                                                       [ KAASoftDatabase::$USERS_TABLE_NAME . ".email" => $email ]);
            if ($queryResult !== false) {
                $user = User::getObjectInstance($queryResult);

                if (isset( $queryResult["photoId"] )) {
                    $image = Image::getObjectInstance($queryResult);
                    if (file_exists($image->getAbsolutePath())) {
                        $image->setId($queryResult["photoId"]);
                        $image->setTitle($queryResult["imageTitle"]);
                        $user->setPhoto($image);
                    }
                }

                if (isset( $queryResult["roleId"] )) {
                    $role = Role::getObjectInstance($queryResult);
                    $role->setId($queryResult["roleId"]);
                    $user->setRole($role);
                }

                return $user;

            }

            return null;
        }

        /**
         * @param User $user
         * @param bool $isSaveRole
         * @param bool $isInstaller
         * @return bool $userId
         */
        public function saveUser(User $user, $isSaveRole = true, $isInstaller = false) {
            $data = $user->getDatabaseArray();
            unset( $data["userId"] );
            if (!$isSaveRole) {
                unset( $data["roleId"] );
            }
            if ($isInstaller) {
                unset( $data["isLdapUser"] );
                unset( $data["birthday"] );
                unset( $data["provider"] );
                unset( $data["socialId"] );
                unset( $data["socialPage"] );
            }
            if ($user->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$USERS_TABLE_NAME,
                                                      $data);
            }

            else {
                unset( $data["creationDateTime"] );
                // no need to replace existing password by null
                if ($data["password"] == null) {
                    unset( $data["password"] );
                }

                return $this->kaaSoftDatabase->update(KAASoftDatabase::$USERS_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $user->getId() ]);
            }
        }

        public function saveUserPublic(User $user) {
            $data = $user->getDatabaseArray();
            if ($user->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$USERS_TABLE_NAME,
                                                      $data);
            }

            else {
                unset( $data["roleId"] );
                unset( $data["isLdapUser"] );
                unset( $data["isActive"] );
                unset( $data["creationDateTime"] );
                // no need to replace existing password by null
                if ($data["password"] == null) {
                    unset( $data["password"] );
                }

                return $this->kaaSoftDatabase->update(KAASoftDatabase::$USERS_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $user->getId() ]);
            }
        }

        /**
         * @param $userId
         * @param $password
         * @return bool|int
         */
        public function saveUserPassword($userId, $password) {
            return $this->kaaSoftDatabase->update(KAASoftDatabase::$USERS_TABLE_NAME,
                                                  [ "password" => $password ],
                                                  [ "id" => $userId ]);
        }

        /**
         * @param $userId   int
         * @param $isActive bool
         * @return bool|int
         */
        public function activateUser($userId, $isActive) {
            return $this->kaaSoftDatabase->update(KAASoftDatabase::$USERS_TABLE_NAME,
                                                  [ "isActive" => $isActive ],
                                                  [ "id" => $userId ]);
        }

        /**
         * @param $userId
         * @return bool
         */
        public function deleteUser($userId) {
            $user = $this->getUser($userId);
            if ($user !== null) {
                return $this->kaaSoftDatabase->delete(KAASoftDatabase::$USERS_TABLE_NAME,
                                                      [ "id" => $userId ]);
            }

            return false;
        }

        public function isUserExist($userId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$USERS_TABLE_NAME,
                                               [ "id" => $userId ]);
        }

        public function getUsersByRoleId($roleId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [ KAASoftDatabase::$USERS_TABLE_NAME . ".roleId" => $roleId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }

            $usersQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$USERS_TABLE_NAME,
                                                               [ "[><]" . KAASoftDatabase::$ROLES_TABLE_NAME => [ "roleId" => "id" ] ],
                                                               array_merge(User::getDatabaseFieldNames(),
                                                                           [ KAASoftDatabase::$USERS_TABLE_NAME . ".id" ],
                                                                           Role::getDatabaseFieldNames()),
                                                               $queryParams);

            if ($usersQueryResult !== false) {
                $users = [];

                foreach ($usersQueryResult as $userRow) {
                    $user = User::getObjectInstance($userRow);

                    $role = Role::getObjectInstance($userRow);
                    $role->setId($user->getRoleId());
                    $user->setRole($role);

                    $users[] = $user;
                }

                return $users;
            }

            return null;
        }

        public function searchUsersByName($searchText, $limit = 10) {
            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$USERS_TABLE_NAME,
                                                          [ "[><]" . KAASoftDatabase::$ROLES_TABLE_NAME => [ "roleId" => "id" ] ],
                                                          array_merge([ KAASoftDatabase::$USERS_TABLE_NAME . ".id" ],
                                                                      User::getDatabaseFieldNames(),
                                                                      Role::getDatabaseFieldNames()),
                                                          [ "OR"    => [ "firstName[~]" => $searchText,
                                                                         "lastName[~]"  => $searchText ],
                                                            "LIMIT" => $limit ]);
            if ($queryResult !== false) {
                $result = [];
                foreach ($queryResult as $row) {
                    $user = User::getObjectInstance($row);

                    $role = Role::getObjectInstance($row);
                    $role->setId($row["roleId"]);
                    $user->setRole($role);

                    $result[] = $user;
                }

                return $result;
            }

            return null;
        }

        /**
         * @param $email
         * @param $hash
         * @param $validTime
         * @return array|bool
         */
        public function saveRecovery($email, $hash, $validTime) {
            return $this->kaaSoftDatabase->insert(KAASoftDatabase::$PASSWORD_CONFIRMATION_TABLE_NAME,
                                                  [ "email"         => $email,
                                                    "hash"          => $hash,
                                                    "validDateTime" => Helper::getDateTimeString(time() + $validTime) ]);
        }

        /**
         * @param $hash
         * @return bool|int
         */
        public function deleteRecovery($hash) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$PASSWORD_CONFIRMATION_TABLE_NAME,
                                                  [ "hash" => $hash ]);
        }

        /**
         * @param $email
         * @return bool|int
         */
        public function deleteRecoveryByEmail($email) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$PASSWORD_CONFIRMATION_TABLE_NAME,
                                                  [ "email" => $email ]);
        }

        /**
         * @param $hash
         * @return bool
         */
        public function isHashExists($hash) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$PASSWORD_CONFIRMATION_TABLE_NAME,
                                               [ "AND" => [ "hash"             => $hash,
                                                            "validDateTime[>]" => Helper::getDateTimeString() ] ]);
        }

        /**
         * @param $email
         * @return bool
         */
        public function isEmailExists($email) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$PASSWORD_CONFIRMATION_TABLE_NAME,
                                               [ "email" => $email ]);
        }

        /**
         * @param $hash
         * @return bool|PasswordRecovery
         */
        public function getPasswordRecovery($hash) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$PASSWORD_CONFIRMATION_TABLE_NAME,
                                                       "*",
                                                       [ "hash" => $hash ]);
            if ($queryResult !== false) {
                return PasswordRecovery::getObjectInstance($queryResult);

            }

            return null;
        }

        /**
         * @param $provider
         * @param $socialId
         * @return User|null
         */
        public function getUserBySocialNetwork($provider, $socialId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$USERS_TABLE_NAME,
                                                       [ "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME => [ "photoId" => "id" ],
                                                         "[>]" . KAASoftDatabase::$ROLES_TABLE_NAME  => [ "roleId" => "id" ] ],
                                                       array_merge(User::getDatabaseFieldNames(),
                                                                   Role::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$USERS_TABLE_NAME . ".id",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime" ]),
                                                       [ "AND" => [ KAASoftDatabase::$USERS_TABLE_NAME . ".provider" => $provider,
                                                                    KAASoftDatabase::$USERS_TABLE_NAME . ".socialId" => $socialId ] ]);
            if ($queryResult !== false) {
                $user = User::getObjectInstance($queryResult);


                if (isset( $queryResult["photoId"] )) {
                    $image = Image::getObjectInstance($queryResult);
                    if (file_exists($image->getAbsolutePath())) {
                        $image->setId($queryResult["photoId"]);
                        $image->setTitle($queryResult["imageTitle"]);
                        $user->setPhoto($image);
                    }
                }

                if (isset( $queryResult["roleId"] )) {
                    $role = Role::getObjectInstance($queryResult);
                    $role->setId($queryResult["roleId"]);
                    $user->setRole($role);
                }

                return $user;

            }

            return null;
        }
    }