<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;


    use KAASoft\Util\FileHelper;
    use KAASoft\Util\ValidationHelper;

    class LdapSettings extends AbstractSettings {

        const LdapSettingsFileNameJSON = '/KAASoft/Config/LdapSettings.json';

        private $isEnabled;
        private $isUserAutoRegistration;
        private $isUseBothAuth;
        private $server;
        private $port = 389;
        private $serviceAccountDN; // cn=administrator,cn=Users,dc=example,dc=com
        private $serviceAccountPassword;
        private $searchBase;// cn=Users,dc=example,dc=com

        private $adminGroupName;
        private $librarianGroupName;
        private $userGroupName;


        private $loginAttributeName; // uid
        private $dnAttributeName; // dn
        private $emailAttributeName;
        private $firstNameAttributeName;
        private $lastNameAttributeName;

        protected static $INSTANCE = null;

        final function __construct() {

        }

        /**
         * copy data from assoc array to object fields
         * @param $settings mixed
         */
        public function copySettings($settings) {
            $this->setIsEnabled(ValidationHelper::getBool($settings["isEnabled"]));
            $this->setIsUserAutoRegistration(ValidationHelper::getBool($settings["isUserAutoRegistration"]));
            $this->setIsUseBothAuth(ValidationHelper::getBool($settings["isUseBothAuth"]));

            $this->setServer(ValidationHelper::getString($settings["server"]));
            $this->setPort(ValidationHelper::getInt($settings["port"]));

            $this->setServiceAccountDN(ValidationHelper::getString($settings["serviceAccountDN"]));
            $this->setServiceAccountPassword(ValidationHelper::getString($settings["serviceAccountPassword"]));
            $this->setSearchBase(ValidationHelper::getString($settings["searchBase"]));

            $this->setAdminGroupName(ValidationHelper::getString($settings["adminGroupName"]));
            $this->setLibrarianGroupName(ValidationHelper::getString($settings["librarianGroupName"]));
            $this->setUserGroupName(ValidationHelper::getString($settings["userGroupName"]));

            $this->setLoginAttributeName(ValidationHelper::getString($settings["loginAttributeName"]));
            $this->setDnAttributeName(ValidationHelper::getString($settings["dnAttributeName"]));
            $this->setEmailAttributeName(ValidationHelper::getString($settings["emailAttributeName"]));
            $this->setFirstNameAttributeName(ValidationHelper::getString($settings["firstNameAttributeName"]));
            $this->setLastNameAttributeName(ValidationHelper::getString($settings["lastNameAttributeName"]));
        }

        /**
         * copy data from another Settings object
         * @param $settings LdapSettings
         */
        public function cloneSettings($settings) {
            $this->setIsEnabled($settings->isEnabled());
            $this->setIsUserAutoRegistration($settings->isUserAutoRegistration());
            $this->setIsUseBothAuth($settings->isUseBothAuth());

            $this->setServer($settings->getServer());
            $this->setPort($settings->getPort());

            $this->setServiceAccountDN($settings->getServiceAccountDN());
            $this->setServiceAccountPassword($settings->getServiceAccountPassword());
            $this->setSearchBase($settings->getSearchBase());
            $this->setLoginAttributeName($settings->getLoginAttributeName());
            $this->setDnAttributeName($settings->getDnAttributeName());

            $this->setAdminGroupName($settings->getAdminGroupName());
            $this->setLibrarianGroupName($settings->getLibrarianGroupName());
            $this->setUserGroupName($settings->getUserGroupName());

            $this->setEmailAttributeName($settings->getEmailAttributeName());
            $this->setFirstNameAttributeName($settings->getFirstNameAttributeName());
            $this->setLastNameAttributeName($settings->getLibrarianGroupName());
        }

        /**
         * Returns config file to load/store settings
         * @return string
         */
        public function getConfigFileName() {
            return realpath(FileHelper::getPrivateFolderLocation()) . LdapSettings::LdapSettingsFileNameJSON;
        }

        /**
         * @return LdapSettings
         */
        public static function getInstance() {
            if (LdapSettings::$INSTANCE === null) {
                LdapSettings::$INSTANCE = new LdapSettings();
            }

            return LdapSettings::$INSTANCE;
        }

        /**
         * Sets default settings
         */
        public function setDefaultSettings() {
            $this->setIsEnabled(false);
            $this->setIsUserAutoRegistration(false);
            $this->setIsUseBothAuth(true);

            $this->setServer("localhost");
            $this->setPort(389);

            $this->setServiceAccountDN("cn=administrator,cn=Users,dc=library-cms,dc=com");
            $this->setServiceAccountPassword("123");
            $this->setSearchBase("cn=Users,dc=library-cms,dc=com");

            $this->setAdminGroupName("cn=Administrators,dc=library-cms,dc=com");
            $this->setLibrarianGroupName("cn=Librarians,dc=library-cms,dc=com");
            $this->setUserGroupName("cn=Users,dc=library-cms,dc=com");

            $this->setLoginAttributeName("uid");
            $this->setDnAttributeName("dn");
            $this->setEmailAttributeName("mail");
            $this->setFirstNameAttributeName("fn");
            $this->setLastNameAttributeName("ln");
        }

        /**
         * Specify data which should be serialized to JSON
         * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         * @since 5.4.0
         */
        function jsonSerialize() {
            return [ "isEnabled"              => $this->isEnabled,
                     "isUserAutoRegistration" => $this->isUserAutoRegistration,
                     "isUseBothAuth"          => $this->isUseBothAuth,

                     "server"                 => $this->server,
                     "port"                   => $this->port,
                     "serviceAccountDN"       => $this->serviceAccountDN,
                     "serviceAccountPassword" => $this->serviceAccountPassword,
                     "searchBase"             => $this->searchBase,

                     "adminGroupName"     => $this->adminGroupName,
                     "librarianGroupName" => $this->librarianGroupName,
                     "userGroupName"      => $this->userGroupName,

                     "loginAttributeName"     => $this->loginAttributeName,
                     "dnAttributeName"        => $this->dnAttributeName,
                     "emailAttributeName"     => $this->emailAttributeName,
                     "firstNameAttributeName" => $this->firstNameAttributeName,
                     "lastNameAttributeName"  => $this->lastNameAttributeName ];
        }

        /**
         * @return mixed
         */
        public function isUseBothAuth() {
            return $this->isUseBothAuth;
        }

        /**
         * @param mixed $isUseBothAuth
         */
        public function setIsUseBothAuth($isUseBothAuth) {
            $this->isUseBothAuth = $isUseBothAuth;
        }

        /**
         * @return mixed
         */
        public function isEnabled() {
            return $this->isEnabled;
        }

        /**
         * @param mixed $isEnabled
         */
        public function setIsEnabled($isEnabled) {
            $this->isEnabled = $isEnabled;
        }

        /**
         * @return mixed
         */
        public function isUserAutoRegistration() {
            return $this->isUserAutoRegistration;
        }

        /**
         * @param mixed $isUserAutoRegistration
         */
        public function setIsUserAutoRegistration($isUserAutoRegistration) {
            $this->isUserAutoRegistration = $isUserAutoRegistration;
        }

        /**
         * @return mixed
         */
        public function getServer() {
            return $this->server;
        }

        /**
         * @param mixed $server
         */
        public function setServer($server) {
            $this->server = $server;
        }

        /**
         * @return int
         */
        public function getPort() {
            return $this->port;
        }

        /**
         * @param int $port
         */
        public function setPort($port) {
            $this->port = $port;
        }

        /**
         * @return mixed
         */
        public function getServiceAccountDN() {
            return $this->serviceAccountDN;
        }

        /**
         * @param mixed $serviceAccountDN
         */
        public function setServiceAccountDN($serviceAccountDN) {
            $this->serviceAccountDN = $serviceAccountDN;
        }

        /**
         * @return mixed
         */
        public function getServiceAccountPassword() {
            return $this->serviceAccountPassword;
        }

        /**
         * @param mixed $serviceAccountPassword
         */
        public function setServiceAccountPassword($serviceAccountPassword) {
            $this->serviceAccountPassword = $serviceAccountPassword;
        }

        /**
         * @return mixed
         */
        public function getSearchBase() {
            return $this->searchBase;
        }

        /**
         * @param mixed $searchBase
         */
        public function setSearchBase($searchBase) {
            $this->searchBase = $searchBase;
        }

        /**
         * @return mixed
         */
        public function getAdminGroupName() {
            return $this->adminGroupName;
        }

        /**
         * @param mixed $adminGroupName
         */
        public function setAdminGroupName($adminGroupName) {
            $this->adminGroupName = $adminGroupName;
        }

        /**
         * @return mixed
         */
        public function getLibrarianGroupName() {
            return $this->librarianGroupName;
        }

        /**
         * @param mixed $librarianGroupName
         */
        public function setLibrarianGroupName($librarianGroupName) {
            $this->librarianGroupName = $librarianGroupName;
        }

        /**
         * @return mixed
         */
        public function getUserGroupName() {
            return $this->userGroupName;
        }

        /**
         * @param mixed $userGroupName
         */
        public function setUserGroupName($userGroupName) {
            $this->userGroupName = $userGroupName;
        }

        /**
         * @return mixed
         */
        public function getEmailAttributeName() {
            return $this->emailAttributeName;
        }

        /**
         * @param mixed $emailAttributeName
         */
        public function setEmailAttributeName($emailAttributeName) {
            $this->emailAttributeName = $emailAttributeName;
        }

        /**
         * @return mixed
         */
        public function getFirstNameAttributeName() {
            return $this->firstNameAttributeName;
        }

        /**
         * @param mixed $firstNameAttributeName
         */
        public function setFirstNameAttributeName($firstNameAttributeName) {
            $this->firstNameAttributeName = $firstNameAttributeName;
        }

        /**
         * @return mixed
         */
        public function getLastNameAttributeName() {
            return $this->lastNameAttributeName;
        }

        /**
         * @param mixed $lastNameAttributeName
         */
        public function setLastNameAttributeName($lastNameAttributeName) {
            $this->lastNameAttributeName = $lastNameAttributeName;
        }

        /**
         * @return mixed
         */
        public function getLoginAttributeName() {
            return $this->loginAttributeName;
        }

        /**
         * @param mixed $loginAttributeName
         */
        public function setLoginAttributeName($loginAttributeName) {
            $this->loginAttributeName = $loginAttributeName;
        }

        /**
         * @return mixed
         */
        public function getDnAttributeName() {
            return $this->dnAttributeName;
        }

        /**
         * @param mixed $dnAttributeName
         */
        public function setDnAttributeName($dnAttributeName) {
            $this->dnAttributeName = $dnAttributeName;
        }
    }