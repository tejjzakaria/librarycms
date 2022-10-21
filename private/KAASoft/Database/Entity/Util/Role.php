<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\Util;


    use JsonSerializable;
    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class Role
     * @package KAASoft\Database\Entity\Util
     */
    class Role extends DatabaseEntity implements JsonSerializable {
        const ADMIN_ROLE_ID     = "ADMIN_ROLE_ID";
        const LIBRARIAN_ROLE_ID = "LIBRARIAN_ROLE_ID";
        const READER_ROLE_ID    = "READER_ROLE_ID";

        const BUILTIN_USER_ROLES = [ Role::ADMIN_ROLE_ID     => 1,
                                     Role::LIBRARIAN_ROLE_ID => 2,
                                     Role::READER_ROLE_ID    => 3 ];

        private $name;
        private $issueDayLimit;
        private $issueBookLimit;
        private $finePerDay;
        private $priority;

        public function __construct($id = null) {
            parent::__construct($id);
        }

        /**
         * @param array $databaseArray
         * @return Role to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {

            $role = new Role(ValidationHelper::getNullableInt($databaseArray["id"]));
            $role->setName(ValidationHelper::getString($databaseArray["name"]));
            $role->setIssueDayLimit(ValidationHelper::getNullableInt($databaseArray["issueDayLimit"]));
            $role->setIssueBookLimit(ValidationHelper::getNullableInt($databaseArray["issueBookLimit"]));
            $role->setFinePerDay(ValidationHelper::getFloat($databaseArray["finePerDay"]));
            $role->setPriority(ValidationHelper::getNullableInt($databaseArray["priority"]));

            return $role;
        }

        public function getDatabaseArray() {
            return array_merge(parent::getDatabaseArray(),
                               [ "name"           => $this->name,
                                 "issueDayLimit"  => $this->issueDayLimit,
                                 "issueBookLimit" => $this->issueBookLimit,
                                 "finePerDay"     => $this->finePerDay,
                                 "priority"       => $this->priority ]);
        }

        public static function getDatabaseFieldNames() {
            return [ KAASoftDatabase::$ROLES_TABLE_NAME . ".name",
                     KAASoftDatabase::$ROLES_TABLE_NAME . ".issueDayLimit",
                     KAASoftDatabase::$ROLES_TABLE_NAME . ".issueBookLimit",
                     KAASoftDatabase::$ROLES_TABLE_NAME . ".finePerDay",
                     KAASoftDatabase::$ROLES_TABLE_NAME . ".priority" ];
        }

        /**
         * (PHP 5 &gt;= 5.4.0)<br/>
         * Specify data which should be serialized to JSON
         * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         */
        function jsonSerialize() {
            return $this->getDatabaseArray();
        }

        /**
         * @return mixed
         */
        public function getPriority() {
            return $this->priority;
        }

        /**
         * @param mixed $priority
         */
        public function setPriority($priority) {
            $this->priority = $priority;
        }

        /**
         * @return mixed
         */
        public function getName() {
            return $this->name;
        }

        /**
         * @param mixed $name
         */
        public function setName($name) {
            $this->name = $name;
        }

        /**
         * @return mixed
         */
        public function getIssueDayLimit() {
            return $this->issueDayLimit;
        }

        /**
         * @param mixed $issueDayLimit
         */
        public function setIssueDayLimit($issueDayLimit) {
            $this->issueDayLimit = $issueDayLimit;
        }

        /**
         * @return mixed
         */
        public function getIssueBookLimit() {
            return $this->issueBookLimit;
        }

        /**
         * @param mixed $issueBookLimit
         */
        public function setIssueBookLimit($issueBookLimit) {
            $this->issueBookLimit = $issueBookLimit;
        }

        /**
         * @return mixed
         */
        public function getFinePerDay() {
            return $this->finePerDay;
        }

        /**
         * @param mixed $finePerDay
         */
        public function setFinePerDay($finePerDay) {
            $this->finePerDay = $finePerDay;
        }
    }