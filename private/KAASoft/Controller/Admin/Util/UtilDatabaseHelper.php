<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util;

    use Exception;
    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\General\Binding;
    use KAASoft\Database\Entity\General\BookSize;
    use KAASoft\Database\Entity\General\BookType;
    use KAASoft\Database\Entity\General\PhysicalForm;
    use KAASoft\Database\Entity\Util\EmailNotification;
    use KAASoft\Database\Entity\Util\Language;
    use KAASoft\Database\Entity\Util\Permission;
    use KAASoft\Database\Entity\Util\Role;
    use KAASoft\Database\Entity\Util\UserMessage;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\EmailAddress;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;

    /**
     * Class UtilDatabaseHelper
     * @package KAASoft\Controller\Admin\Util
     */
    class UtilDatabaseHelper extends DatabaseHelper {


        public function isRoleExist($roleId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$ROLES_TABLE_NAME,
                                               [ "id" => $roleId ]);
        }

        /**
         * @param Role $role
         * @return bool|int
         */
        public function saveRole(Role $role) {
            if ($role->getId() == null) {
                // associate have to be created
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$ROLES_TABLE_NAME,
                                                      $role->getDatabaseArray());
            }
            else {
                $data = $role->getDatabaseArray();

                return $this->kaaSoftDatabase->update(KAASoftDatabase::$ROLES_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $role->getId() ]);
            }
        }

        /**
         * @param $language Language
         * @return array|bool
         */
        public function insertLanguage($language) {
            return $this->kaaSoftDatabase->insert(KAASoftDatabase::$LANGUAGES_TABLE_NAME,
                                                  $language->getDatabaseArray());
        }

        /**
         * @param $bindings
         * @return array|bool
         */
        public function saveBindings($bindings) {
            $data = [];
            foreach ($bindings as $binding) {
                if ($binding instanceof Binding) {
                    $data[] = $binding->getDatabaseArray();
                }
            }

            if (count($data) > 0) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$BINDINGS_TABLE_NAME,
                                                      $data);

            }

            return true;
        }

        /**
         * @param $bookSizes
         * @return array|bool
         */
        public function saveBookSizes($bookSizes) {
            $data = [];
            foreach ($bookSizes as $bookSize) {
                if ($bookSize instanceof BookSize) {
                    $data[] = $bookSize->getDatabaseArray();
                }
            }

            if (count($data) > 0) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$BOOK_SIZES_TABLE_NAME,
                                                      $data);

            }

            return true;
        }

        /**
         * @param $bookTypes
         * @return array|bool
         */
        public function saveBookTypes($bookTypes) {
            $data = [];
            foreach ($bookTypes as $bookType) {
                if ($bookType instanceof BookType) {
                    $data[] = $bookType->getDatabaseArray();
                }
            }

            if (count($data) > 0) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$BOOK_TYPES_TABLE_NAME,
                                                      $data);

            }

            return true;
        }

        /**
         * @param $physicalForms
         * @return array|bool
         */
        public function savePhysicalForms($physicalForms) {
            $data = [];
            foreach ($physicalForms as $physicalForm) {
                if ($physicalForm instanceof PhysicalForm) {
                    $data[] = $physicalForm->getDatabaseArray();
                }
            }

            if (count($data) > 0) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$PHYSICAL_FORMS_TABLE_NAME,
                                                      $data);

            }

            return true;
        }

        /**
         * @param     $searchText
         * @param int $limit
         * @return array|null
         */
        public function searchRoleByName($searchText, $limit = 10) {
            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$ROLES_TABLE_NAME,
                                                          array_merge([ KAASoftDatabase::$ROLES_TABLE_NAME . ".id" ],
                                                                      Role::getDatabaseFieldNames()),
                                                          [ "name[~]" => $searchText,
                                                            "LIMIT"   => $limit ]);
            if ($queryResult !== false) {
                $result = [];
                foreach ($queryResult as $row) {
                    $role = Role::getObjectInstance($row);

                    $result[] = $role;

                    return $result;
                }
            }

            return null;
        }

        private function isRoleHasPermission($rolePermissions, $permissionId) {
            foreach ($rolePermissions as $rolePermission) {
                if ($rolePermission instanceof Permission) {
                    if ($rolePermission->getId() == $permissionId) {
                        return true;
                    }
                }
            }

            return false;
        }

        /**
         * @param         $roleId
         * @param Session $session
         * @return bool
         */
        public function processRolePermissions($roleId, Session $session) {
            $globalResult = true;
            try {
                $rolePermissions = $this->kaaSoftDatabase->getRolePermissions($roleId);
                $newPermissions = $_POST["permissions"];


                if ($newPermissions != null) {
                    foreach ($newPermissions as $newPermission) {
                        if (!$this->isRoleHasPermission($rolePermissions,
                                                        $newPermission)
                        ) {
                            $result = $this->saveRolePermission($roleId,
                                                                $newPermission) !== false;
                            $globalResult = ( $globalResult and $result );
                        }
                    }
                }

                foreach ($rolePermissions as $rolePermission) {
                    if ($rolePermission instanceof Permission) {
                        if (!in_array($rolePermission->getId(),
                                      $newPermissions)
                        ) {
                            $result = $this->deleteRolePermission($roleId,
                                                                  $rolePermission->getId()) !== false;
                            $globalResult = ( $globalResult and $result );

                        }
                    }
                }
            }
            catch (Exception $e) {
                $errorMessage = sprintf(_("Couldn't save Role Permissions for role '%d'.%s%s"),
                                        $roleId,
                                        Helper::HTML_NEW_LINE,
                                        $this->kaaSoftDatabase->error()[2]);
                $session->addSessionMessage($errorMessage,
                                            Message::MESSAGE_STATUS_ERROR);

                return false;
            }

            return $globalResult;
        }

        /**
         * @param $permissionId
         * @return bool
         */
        public function isPermissionIdExist($permissionId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$PERMISSIONS_TABLE_NAME,
                                               [ "id" => $permissionId ]);
        }

        /**
         * @param Permission|Role $permission
         * @return bool|int
         */
        public function savePermission(Permission $permission) {
            if ($permission->getId() == null) {
                // associate have to be created
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$PERMISSIONS_TABLE_NAME,
                                                      $permission->getDatabaseArray());
            }
            else {
                $data = $permission->getDatabaseArray();

                return $this->kaaSoftDatabase->update(KAASoftDatabase::$PERMISSIONS_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $permission->getId() ]);
            }
        }

        /**
         * @param $permissionId
         * @return bool
         */
        public function deletePermission($permissionId) {
            if ($this->isPermissionIdExist($permissionId)) {
                return $this->kaaSoftDatabase->delete(KAASoftDatabase::$PERMISSIONS_TABLE_NAME,
                                                      [ "id" => $permissionId ]);
            }

            return false;
        }

        /**
         * @param $roleId
         * @param $permissionId
         * @return bool
         */
        public function deleteRolePermission($roleId, $permissionId) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$ROLE_PERMISSIONS_TABLE_NAME,
                                                  [ "AND" => [ "roleId"       => $roleId,
                                                               "permissionId" => $permissionId ] ]);
        }

        /**
         * @param $roleId
         * @param $permissionId
         * @return bool|int
         */
        public function saveRolePermission($roleId, $permissionId) {
            return $this->kaaSoftDatabase->insert(KAASoftDatabase::$ROLE_PERMISSIONS_TABLE_NAME,
                                                  [ "roleId"       => $roleId,
                                                    "permissionId" => $permissionId ]);
        }

        /**
         * @param $roleId
         * @return Role|null
         */
        public function getRole($roleId) {
            if ($roleId !== null) {
                $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$ROLES_TABLE_NAME,
                                                           array_merge(Role::getDatabaseFieldNames(),
                                                                       [ KAASoftDatabase::$ROLES_TABLE_NAME . ".id" ]),
                                                           [ "id" => $roleId ]);
                if ($queryResult !== false) {
                    return Role::getObjectInstance($queryResult);
                }
            }

            return null;
        }


        /**
         * @param $roleId
         * @return bool
         */
        public function deleteRole($roleId) {
            $role = $this->getRole($roleId);
            if ($role !== null) {
                return $this->kaaSoftDatabase->delete(KAASoftDatabase::$ROLES_TABLE_NAME,
                                                      [ "id" => $roleId ]);
            }

            return false;
        }

        public function getUserMessagesCount() {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$USER_MESSAGES_TABLE_NAME);
        }

        /**
         * @param null $offset
         * @param null $perPage
         * @return array|null
         */
        public function getNonViewedUserMessages($offset = null, $perPage = null) {

            $queryParams = [ "isViewed" => false,
                             "ORDER"    => [ 'creationDate' => 'DESC' ] ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }

            $userMessagesQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$USER_MESSAGES_TABLE_NAME,
                                                                      array_merge(UserMessage::getDatabaseFieldNames(),
                                                                                  [ KAASoftDatabase::$USER_MESSAGES_TABLE_NAME . ".id" ]),
                                                                      $queryParams);

            if ($userMessagesQueryResult !== false) {
                $userMessages = [];

                foreach ($userMessagesQueryResult as $userMessageRow) {

                    $userMessage = UserMessage::getObjectInstance($userMessageRow);
                    $userMessage->setCreationDate(Helper::reformatDateString($userMessage->getCreationDate(),
                                                                             $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_FORMAT),
                                                                             Helper::DATABASE_DATE_TIME_FORMAT));
                    $userMessages[] = $userMessage;
                }

                return $userMessages;
            }

            return null;
        }

        public function getUserMessages($offset = null, $perPage = null, $sortColumn = "creationDate", $sortOrder = "DESC") {
            $queryParams = null;
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }

            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$USER_MESSAGES_TABLE_NAME,
                                                          array_merge(UserMessage::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$USER_MESSAGES_TABLE_NAME . ".id" ]),
                                                          $queryParams);


            if ($queryResult !== false) {
                $messages = [];

                foreach ($queryResult as $row) {
                    $message = UserMessage::getObjectInstance($row);
                    $message->setCreationDate(Helper::reformatDateTimeString($message->getCreationDate(),
                                                                             $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_TIME_FORMAT)));
                    $messages[] = $message;
                }

                return $messages;
            }

            return null;
        }

        /**
         * @param $messageId
         * @return bool
         */
        public function isUserMessageExist($messageId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$USER_MESSAGES_TABLE_NAME,
                                               [ "id" => $messageId ]);
        }

        /**
         * @param $messageId
         * @return bool|int
         */
        public function deleteUserMessage($messageId) {
            $userMessage = $this->getUserMessage($messageId);
            if ($userMessage !== null) {
                return $this->kaaSoftDatabase->delete(KAASoftDatabase::$USER_MESSAGES_TABLE_NAME,
                                                      [ "id" => $messageId ]);
            }

            return false;
        }


        public function setUserMessageViewed($messageId) {
            return $this->kaaSoftDatabase->update(KAASoftDatabase::$USER_MESSAGES_TABLE_NAME,
                                                  [ "isViewed" => true ],
                                                  [ "id" => $messageId ]);
        }

        /**
         * @param $messageId
         * @return UserMessage|null
         */
        private function getUserMessage($messageId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$USER_MESSAGES_TABLE_NAME,
                                                       array_merge(UserMessage::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$USER_MESSAGES_TABLE_NAME . ".id" ]),
                                                       [ "id" => $messageId ]);
            if ($queryResult !== false) {
                $message = UserMessage::getObjectInstance($queryResult);

                return $message;

            }

            return null;
        }

        /**
         * @param EmailNotification $emailNotification
         * @return array|bool|int
         */
        public function saveEmailNotification(EmailNotification $emailNotification) {

            $data = $emailNotification->getDatabaseArray();

            $data["to"] = json_encode($emailNotification->getTo());
            $data["from"] = json_encode($emailNotification->getFrom());

            if ($emailNotification->getId() === null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$EMAIL_NOTIFICATIONS_TABLE_NAME,
                                                      $data);
            }
            else {
                unset( $data["creationDateTime"] );

                return $this->kaaSoftDatabase->update(KAASoftDatabase::$EMAIL_NOTIFICATIONS_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $emailNotification->getId() ]);
            }
        }

        /**
         * @param $route
         * @param $isEnabled bool
         * @return bool|int
         */
        public function enableEmailNotification($route, $isEnabled) {
            return $this->kaaSoftDatabase->update(KAASoftDatabase::$EMAIL_NOTIFICATIONS_TABLE_NAME,
                                                  [ "isEnabled" => $isEnabled ],
                                                  [ "route" => $route ]);
        }

        /**.
         * @param $route
         * @return EmailNotification|null
         */
        public function getEmailNotification($route) {
            if ($route !== null) {
                $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$EMAIL_NOTIFICATIONS_TABLE_NAME,
                                                           array_merge(EmailNotification::getDatabaseFieldNames(),
                                                                       [ KAASoftDatabase::$EMAIL_NOTIFICATIONS_TABLE_NAME . ".id" ]),
                                                           [ "route" => $route ]);
                if ($queryResult !== false) {
                    $emailNotification = EmailNotification::getObjectInstance($queryResult);

                    $from = json_decode($emailNotification->getFrom());
                    $emailNotification->setFrom(new EmailAddress($from->email,
                                                                 $from->name));

                    $to = json_decode($emailNotification->getTo());
                    $toArray = [];
                    foreach ($to as $addressObject) {
                        $toArray[] = new EmailAddress($addressObject->email,
                                                      $addressObject->name);
                    }
                    $emailNotification->setTo($toArray);


                    return $emailNotification;
                }
            }

            return null;
        }

        /**
         * @return array|null
         */
        public function getEmailNotifications() {
            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$EMAIL_NOTIFICATIONS_TABLE_NAME,
                                                          array_merge(EmailNotification::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$EMAIL_NOTIFICATIONS_TABLE_NAME . ".id" ]),
                                                          [ "ORDER" => [ "id" => "ASC" ] ]);
            if ($queryResult !== false) {
                $result = [];
                foreach ($queryResult as $row) {
                    $result[] = EmailNotification::getObjectInstance($row);
                }

                return $result;
            }

            return null;
        }

        /**
         * @return array|null
         */
        public function getRoles() {
            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$ROLES_TABLE_NAME,
                                                          array_merge(Role::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$ROLES_TABLE_NAME . ".id" ]),
                                                          [ "ORDER" => [ 'name' => "ASC" ] ]);
            if ($queryResult !== false) {
                $result = [];
                foreach ($queryResult as $row) {
                    $result[] = Role::getObjectInstance($row);
                }

                return $result;
            }

            return null;
        }

        /**
         * @return array|null
         */
        public function getAllPermissions() {
            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$PERMISSIONS_TABLE_NAME,
                                                          array_merge(Permission::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$PERMISSIONS_TABLE_NAME . ".id" ]),
                                                          [ "ORDER" => [ 'routeTitle' => "ASC" ] ]);
            if ($queryResult !== false) {
                $result = [];
                foreach ($queryResult as $row) {
                    $result[] = Permission::getObjectInstance($row);
                }

                return $result;
            }

            return null;
        }
    }