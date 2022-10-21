<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\Util;


    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class Permission
     * @package KAASoft\Database\Entity\Util
     */
    class Permission extends DatabaseEntity {
        /**
         * @var bool
         */
        private $isRolePermission = false;

        private $routeName;
        private $routeTitle;
        private $isPublic;

        public function __construct($id = null) {
            parent::__construct($id);
        }

        /**
         * @param array $databaseArray
         * @return Permission to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {

            $role = new Permission(ValidationHelper::getNullableInt($databaseArray["id"]));
            $role->setRouteName(ValidationHelper::getString($databaseArray["routeName"]));
            $role->setRouteTitle(ValidationHelper::getString($databaseArray["routeTitle"]));
            $role->setIsPublic(ValidationHelper::getBool($databaseArray["isPublic"]));

            return $role;
        }

        public function getDatabaseArray() {
            return array_merge(parent::getDatabaseArray(),
                               [ "routeName"  => $this->routeName,
                                 "routeTitle" => $this->routeTitle,
                                 "isPublic"   => $this->isPublic ]);
        }

        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return [ KAASoftDatabase::$PERMISSIONS_TABLE_NAME . ".routeName",
                     KAASoftDatabase::$PERMISSIONS_TABLE_NAME . ".routeTitle",
                     KAASoftDatabase::$PERMISSIONS_TABLE_NAME . ".isPublic" ];
        }

        /**
         * @return mixed
         */
        public function getRouteTitle() {
            return $this->routeTitle;
        }

        /**
         * @param mixed $routeTitle
         */
        public function setRouteTitle($routeTitle) {
            $this->routeTitle = $routeTitle;
        }

        /**
         * @return mixed
         */
        public function getRouteName() {
            return $this->routeName;
        }

        /**
         * @param mixed $routeName
         */
        public function setRouteName($routeName) {
            $this->routeName = $routeName;
        }

        /**
         * @return boolean
         */
        public function isRolePermission() {
            return $this->isRolePermission;
        }

        /**
         * @param boolean $isRolePermission
         */
        public function setIsRolePermission($isRolePermission) {
            $this->isRolePermission = $isRolePermission;
        }

        /**
         * @return mixed
         */
        public function isPublic() {
            return $this->isPublic;
        }

        /**
         * @param mixed $isPublic
         */
        public function setIsPublic($isPublic) {
            $this->isPublic = $isPublic;
        }
    }