<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-02-15
     */


    namespace KAASoft\Util\LDAP;


    use KAASoft\Environment\LdapSettings;
    use KAASoft\Util\ValidationHelper;

    class LdapUser {
        const MEMBER_OF_ATTRIBUTE = "memberof";
        /**
         * @var string
         */
        private $login;
        /**
         * @var string
         */
        private $dn;

        private $mail;
        private $firstName;
        private $lastName;
        /**
         * @var array
         */
        private $groups;

        /**
         * LdapUser constructor.
         * @param array $userData
         * @param LdapSettings      $ldapSettings
         */
        public function __construct(array $userData, $ldapSettings) {
            $userData = ValidationHelper::getArray($userData);

            if (isset( $userData[$ldapSettings->getEmailAttributeName()] ) and isset( $userData[$ldapSettings->getEmailAttributeName()]["count"] ) and $userData[$ldapSettings->getEmailAttributeName()]["count"] > 0) {
                $this->mail = $userData[$ldapSettings->getEmailAttributeName()][0];
            }

            if (isset( $userData[$ldapSettings->getFirstNameAttributeName()] ) and isset( $userData[$ldapSettings->getFirstNameAttributeName()]["count"] ) and $userData[$ldapSettings->getFirstNameAttributeName()]["count"] > 0) {
                $this->firstName = $userData[$ldapSettings->getFirstNameAttributeName()][0];
            }

            if (isset( $userData[$ldapSettings->getLastNameAttributeName()] ) and isset( $userData[$ldapSettings->getLastNameAttributeName()]["count"] ) and $userData[$ldapSettings->getLastNameAttributeName()]["count"] > 0) {
                $this->lastName = $userData[$ldapSettings->getLastNameAttributeName()][0];
            }

            if (isset( $userData[$ldapSettings->getLoginAttributeName()] ) and isset( $userData[$ldapSettings->getLoginAttributeName()]["count"] ) and $userData[$ldapSettings->getLoginAttributeName()]["count"] > 0) {
                $this->login = $userData[$ldapSettings->getLoginAttributeName()][0];
            }
            if (isset( $userData[$ldapSettings->getDnAttributeName()] ) and isset( $userData[$ldapSettings->getDnAttributeName()]["count"] ) and $userData[$ldapSettings->getDnAttributeName()]["count"] > 0) {
                $this->dn = $userData[$ldapSettings->getDnAttributeName()][0];
            }
            elseif (isset( $userData[$ldapSettings->getDnAttributeName()] )) {
                $this->dn = $userData[$ldapSettings->getDnAttributeName()];
            }

            if (isset( $userData[LdapUser::MEMBER_OF_ATTRIBUTE] ) and isset( $userData[LdapUser::MEMBER_OF_ATTRIBUTE]["count"] ) and $userData[LdapUser::MEMBER_OF_ATTRIBUTE]["count"] > 0) {
                $this->groups = [];
                foreach ($userData[LdapUser::MEMBER_OF_ATTRIBUTE] as $index => $groupDN) {
                    if (!is_integer($index)) {
                        continue;
                    }

                    $this->groups[] = $groupDN;
                }
            }
        }

        /**
         * @return mixed
         */
        public function getLogin() {
            return $this->login;
        }

        /**
         * @param mixed $login
         */
        public function setLogin($login) {
            $this->login = $login;
        }

        /**
         * @return mixed
         */
        public function getDn() {
            return $this->dn;
        }

        /**
         * @param mixed $dn
         */
        public function setDn($dn) {
            $this->dn = $dn;
        }

        /**
         * @return mixed
         */
        public function getGroups() {
            return $this->groups;
        }

        /**
         * @param mixed $groups
         */
        public function setGroups($groups) {
            $this->groups = $groups;
        }

        /**
         * @return mixed
         */
        public function getMail() {
            return $this->mail;
        }

        /**
         * @param mixed $mail
         */
        public function setMail($mail) {
            $this->mail = $mail;
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
    }