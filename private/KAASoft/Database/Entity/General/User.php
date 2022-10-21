<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\General;

    use JsonSerializable;
    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\Entity\Util\Image;
    use KAASoft\Database\Entity\Util\Role;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class User
     * @package KAASoft\Database\Entity\General
     */
    class User extends DatabaseEntity implements JsonSerializable {
        /**
         * @var Role
         */
        protected $role;
        /**
         * @var Image
         */
        protected $photo;

        private $email;
        private $password;

        private $firstName;
        private $middleName;
        private $lastName;
        private $isActive;
        private $isLdapUser;
        private $phone;
        private $address;
        private $gender;
        private $roleId;
        private $photoId;
        private $birthday;
        private $provider;
        private $socialId;
        private $socialPage;
        private $creationDateTime;
        private $updateDateTime;

        /**
         * User constructor.
         * @param null $id
         */
        function __construct($id = null) {
            parent::__construct($id);
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            return array_merge(parent::getDatabaseArray(),
                               [ "email"            => $this->email,
                                 "password"         => $this->password,
                                 "firstName"        => $this->firstName,
                                 "middleName"       => $this->middleName,
                                 "lastName"         => $this->lastName,
                                 "isActive"         => $this->isActive,
                                 "isLdapUser"       => $this->isLdapUser,
                                 "phone"            => $this->phone,
                                 "address"          => $this->address,
                                 "gender"           => $this->gender,
                                 "roleId"           => $this->roleId,
                                 "photoId"          => $this->photoId,
                                 "birthday"         => $this->birthday,
                                 "provider"         => $this->provider,
                                 "socialId"         => $this->socialId,
                                 "socialPage"       => $this->socialPage,
                                 "creationDateTime" => $this->creationDateTime,
                                 "updateDateTime"   => $this->updateDateTime ]);
        }

        /**
         * @param array $databaseArray
         * @return User to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $user = new User(ValidationHelper::getNullableInt($databaseArray["id"]));
            $user->setEmail(ValidationHelper::getString($databaseArray["email"]));
            $user->setPassword(ValidationHelper::getString($databaseArray["password"]));
            $user->setFirstName(ValidationHelper::getString($databaseArray["firstName"]));
            $user->setMiddleName(ValidationHelper::getString($databaseArray["middleName"]));
            $user->setLastName(ValidationHelper::getString($databaseArray["lastName"]));
            $user->setIsActive(ValidationHelper::getBool($databaseArray["isActive"]));
            $user->setIsLdapUser(ValidationHelper::getBool($databaseArray["isLdapUser"]));

            $user->setPhone(ValidationHelper::getString($databaseArray["phone"]));
            $user->setPhotoId(ValidationHelper::getNullableInt($databaseArray["photoId"]));
            $user->setRoleId(ValidationHelper::getNullableInt($databaseArray["roleId"]));
            $user->setGender(ValidationHelper::getString($databaseArray["gender"]));
            $user->setAddress(ValidationHelper::getString($databaseArray["address"]));
            $user->setBirthday(ValidationHelper::getString($databaseArray["birthday"]));
            $user->setProvider(ValidationHelper::getString($databaseArray["provider"]));
            $user->setSocialId(ValidationHelper::getString($databaseArray["socialId"]));
            $user->setSocialPage(ValidationHelper::getString($databaseArray["socialPage"]));
            $user->setCreationDateTime(ValidationHelper::getString($databaseArray["creationDateTime"]));
            $user->setUpdateDateTime(ValidationHelper::getString($databaseArray["updateDateTime"]));

            return $user;
        }


        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return array_merge(parent::getDatabaseFieldNames(),
                               [ KAASoftDatabase::$USERS_TABLE_NAME . ".email",
                                 KAASoftDatabase::$USERS_TABLE_NAME . ".password",
                                 KAASoftDatabase::$USERS_TABLE_NAME . ".firstName",
                                 KAASoftDatabase::$USERS_TABLE_NAME . ".middleName",
                                 KAASoftDatabase::$USERS_TABLE_NAME . ".lastName",
                                 KAASoftDatabase::$USERS_TABLE_NAME . ".isActive",
                                 KAASoftDatabase::$USERS_TABLE_NAME . ".isLdapUser",
                                 KAASoftDatabase::$USERS_TABLE_NAME . ".phone",
                                 KAASoftDatabase::$USERS_TABLE_NAME . ".photoId",
                                 KAASoftDatabase::$USERS_TABLE_NAME . ".roleId",
                                 KAASoftDatabase::$USERS_TABLE_NAME . ".gender",
                                 KAASoftDatabase::$USERS_TABLE_NAME . ".address",
                                 KAASoftDatabase::$USERS_TABLE_NAME . ".birthday",
                                 KAASoftDatabase::$USERS_TABLE_NAME . ".provider",
                                 KAASoftDatabase::$USERS_TABLE_NAME . ".socialId",
                                 KAASoftDatabase::$USERS_TABLE_NAME . ".socialPage",
                                 KAASoftDatabase::$USERS_TABLE_NAME . ".creationDateTime",
                                 KAASoftDatabase::$USERS_TABLE_NAME . ".updateDateTime" ]);
        }

        /**
         * (PHP 5 &gt;= 5.4.0)<br/>
         * Specify data which should be serialized to JSON
         * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         */
        function jsonSerialize() {
            return array_merge($this->getDatabaseArray(),
                               [ "role" => $this->role ]);
        }

        /**
         * @return mixed
         */
        public function isActive() {
            return $this->isActive;
        }

        /**
         * @param mixed $isActive
         */
        public function setIsActive($isActive) {
            $this->isActive = $isActive;
        }


        /**
         * @return mixed
         */
        public function getFirstName() {
            return $this->firstName;
        }

        /**
         * @param mixed $firstName
         */
        public function setFirstName($firstName) {
            $this->firstName = $firstName;
        }


        /**
         * @return mixed
         */
        public function getLastName() {
            return $this->lastName;
        }

        /**
         * @param mixed $lastName
         */
        public function setLastName($lastName) {
            $this->lastName = $lastName;
        }


        /**
         * @return mixed
         */
        public function getMiddleName() {
            return $this->middleName;
        }

        /**
         * @param mixed $middleName
         */
        public function setMiddleName($middleName) {
            $this->middleName = $middleName;
        }

        /**
         * @return mixed
         */
        public function getEmail() {
            return $this->email;
        }

        /**
         * @param mixed $email
         */
        public function setEmail($email) {
            $this->email = $email;
        }


        /**
         * @return mixed
         */
        public function getPassword() {
            return $this->password;
        }

        /**
         * @param mixed $password
         */
        public function setPassword($password) {
            $this->password = $password;
        }

        /**
         * @return Role
         */
        public function getRole() {
            return $this->role;
        }

        /**
         * @param Role $role
         */
        public function setRole($role) {
            $this->role = $role;
        }

        /**
         * @return Image
         */
        public function getPhoto() {
            return $this->photo;
        }

        /**
         * @param Image $photo
         */
        public function setPhoto($photo) {
            $this->photo = $photo;
        }

        /**
         * @return mixed
         */
        public function getPhone() {
            return $this->phone;
        }

        /**
         * @param mixed $phone
         */
        public function setPhone($phone) {
            $this->phone = $phone;
        }

        /**
         * @return mixed
         */
        public function getAddress() {
            return $this->address;
        }

        /**
         * @param mixed $address
         */
        public function setAddress($address) {
            $this->address = $address;
        }

        /**
         * @return mixed
         */
        public function getGender() {
            return $this->gender;
        }

        /**
         * @param mixed $gender
         */
        public function setGender($gender) {
            $this->gender = $gender;
        }

        /**
         * @return mixed
         */
        public function getRoleId() {
            return $this->roleId;
        }

        /**
         * @param mixed $roleId
         */
        public function setRoleId($roleId) {
            $this->roleId = $roleId;
        }

        /**
         * @return mixed
         */
        public function getPhotoId() {
            return $this->photoId;
        }

        /**
         * @param mixed $photoId
         */
        public function setPhotoId($photoId) {
            $this->photoId = $photoId;
        }

        /**
         * @return mixed
         */
        public function getCreationDateTime() {
            return $this->creationDateTime;
        }

        /**
         * @param mixed $creationDateTime
         */
        public function setCreationDateTime($creationDateTime) {
            $this->creationDateTime = $creationDateTime;
        }

        /**
         * @return mixed
         */
        public function getUpdateDateTime() {
            return $this->updateDateTime;
        }

        /**
         * @param mixed $updateDateTime
         */
        public function setUpdateDateTime($updateDateTime) {
            $this->updateDateTime = $updateDateTime;
        }

        /**
         * @return mixed
         */
        public function isLdapUser() {
            return $this->isLdapUser;
        }

        /**
         * @param mixed $isLdapUser
         */
        public function setIsLdapUser($isLdapUser) {
            $this->isLdapUser = $isLdapUser;
        }

        /**
         * @return mixed
         */
        public function getBirthday() {
            return $this->birthday;
        }

        /**
         * @param mixed $birthday
         */
        public function setBirthday($birthday) {
            $this->birthday = $birthday;
        }

        /**
         * @return mixed
         */
        public function getProvider() {
            return $this->provider;
        }

        /**
         * @param mixed $provider
         */
        public function setProvider($provider) {
            $this->provider = $provider;
        }

        /**
         * @return mixed
         */
        public function getSocialId() {
            return $this->socialId;
        }

        /**
         * @param mixed $socialId
         */
        public function setSocialId($socialId) {
            $this->socialId = $socialId;
        }

        /**
         * @return mixed
         */
        public function getSocialPage() {
            return $this->socialPage;
        }

        /**
         * @param mixed $socialPage
         */
        public function setSocialPage($socialPage) {
            $this->socialPage = $socialPage;
        }
    }