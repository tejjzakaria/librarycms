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
    use KAASoft\Util\Exception\BindException;
    use KAASoft\Util\Exception\KaaSoftException;
    use KAASoft\Util\Exception\PasswordRequiredException;
    use KAASoft\Util\Exception\UsernameRequiredException;
    use KAASoft\Util\ValidationHelper;

    class LDAP {
        use LdapFunctionSupportTrait;

        /**
         * The SSL LDAP protocol string.
         *
         * @var string
         */
        const PROTOCOL_SSL = 'ldaps://';

        /**
         * The standard LDAP protocol string.
         *
         * @var string
         */
        const PROTOCOL = 'ldap://';

        /**
         * The LDAP SSL port number.
         *
         * @var string
         */
        const PORT_SSL = 636;

        /**
         * The standard LDAP port number.
         *
         * @var string
         */
        const PORT = 389;

        /**
         * The active LDAP connection.
         *
         * @var resource
         */
        protected $connection;

        /**
         * Stores the bool whether or not
         * the current connection is bound.
         *
         * @var bool
         */
        protected $bound = false;

        private $server;
        private $port;
        private $baseDN;
        private $version;
        private $timeout;
        private $adminUserName;
        private $adminPassword;
        private $adminAccountPrefix;
        private $adminAccountSuffix;
        private $accountPrefix;
        private $accountSuffix;
        private $useSSL          = false;
        private $useTLS          = false;
        private $followReferrals = false;
        private $customOptions   = [];


        /**
         * LDAP constructor.
         * @param array $options
         */
        public function __construct($options = []) {

            $this->server = ValidationHelper::getString($options["server"]);
            $this->port = ValidationHelper::getInt($options["port"],
                                                   389);
            $this->version = ValidationHelper::getInt($options["version"],
                                                      3);
            $this->timeout = ValidationHelper::getInt($options["timeout"],
                                                      5);
            $this->baseDN = ValidationHelper::getString($options["baseDN"]);
            $this->adminUserName = ValidationHelper::getString($options["adminUserName"]);
            $this->adminPassword = ValidationHelper::getString($options["adminPassword"]);
        }

        public function connect($username = null, $password = null) {
            $this->connection = $this->createConnection();

            if (is_null($username) && is_null($password)) {
                // If both the username and password are null, we'll connect to the server
                // using the configured administrator username and password.
                $this->bindAsAdministrator();
            }
            else {
                // Bind to the server with the specified username and password otherwise.
                $this->bindUser($username,
                                $password);
            }

            return $this;
        }

        protected function createConnection() {
            $this->prepareConnection();
            $connection = $this->getConnectionString($this->server,
                                                     $this->getProtocol(),
                                                     $this->port);

            return ldap_connect($connection);
        }

        protected function prepareConnection() {
            if ($this->isUseSSL()) {
                $this->ssl();
            }
            elseif ($this->isUseTLS()) {
                $this->tls();
            }

            $options = array_replace($this->customOptions,
                                     [ LDAP_OPT_PROTOCOL_VERSION => $this->version,
                                       LDAP_OPT_NETWORK_TIMEOUT  => $this->timeout,
                                       LDAP_OPT_REFERRALS        => $this->followReferrals ]);

            $this->setOptions($options);
        }

        public function bindAsAdministrator() {
            $this->bindUser($this->adminUserName,
                            $this->adminPassword,
                            $this->adminAccountPrefix,
                            $this->adminAccountSuffix);
        }

        public function bindUser($username = null, $password = null, $prefix = null, $suffix = null) {

            if ($username) {
                $username = $this->applyPrefixAndSuffix($username,
                                                        $prefix,
                                                        $suffix);
            }

            // We'll mute any exceptions / warnings here. All we need to know
            // is if binding failed and we'll throw our own exception.
            if (!@$this->bind($username,
                              $password)
            ) {
                throw new BindException($this->getLastError(),
                                        $this->errNo());
            }
        }

        public function bind($username, $password, $sasl = false) {
            if ($this->isUseTLS()) {
                $this->startTLS();
            }

            if ($sasl) {
                return $this->bound = ldap_sasl_bind($this->getConnection(),
                                                     null,
                                                     null,
                                                     'GSSAPI');
            }

            $this->bound = ldap_bind($this->getConnection(),
                                     $username,
                                     $password);

            return $this->bound;
        }

        protected function applyPrefixAndSuffix($username, $prefix = null, $suffix = null) {
            $prefix = is_null($prefix) ? $this->accountPrefix : $prefix;
            $suffix = is_null($suffix) ? $this->accountSuffix : $suffix;

            return $prefix . $username . $suffix;
        }

        public function attemptLogin($username, $password, $bindAsUser = false) {
            $this->validateCredentials($username,
                                       $password);

            try {
                $this->bindUser($username,
                                $password);

                $result = true;
            }
            catch (BindException $e) {
                // We'll catch the BindException here to allow
                // developers to use a simple if / else
                // using the attempt method.
                $result = false;
            }

            // If we're not allowed to bind as the user,
            // we'll rebind as administrator.
            if ($bindAsUser === false) {
                // We won't catch any BindException here so we can
                // catch rebind failures. However this shouldn't
                // occur if our credentials are correct
                // in the first place.
                $this->bindAsAdministrator();
            }

            return $result;
        }

        protected function validateCredentials($username, $password) {
            if (empty( $username )) {
                // Check for an empty username.
                throw new UsernameRequiredException('LDAP username must be specified.');
            }

            if (empty( $password )) {
                // Check for an empty password.
                throw new PasswordRequiredException('LDAP password must be specified.');
            }
        }

        /**
         * @param $login
         * @param $loginAttribute
         * @param $searchBase
         * @param LdapSettings $ldapSettings
         * @return LdapUser|null
         */
        public function findUserByLogin($login, $loginAttribute, $searchBase, $ldapSettings) {
            $filter = "($loginAttribute=$login)";
            $result = ldap_search($this->connection,
                                  $searchBase,
                                  $filter);

            if ($result !== false) {
                $users = ldap_get_entries($this->connection,
                                          $result);

                if ($users !== null and isset($users["count"]) and $users["count"] > 0) {
                    return new LdapUser($users[0],$ldapSettings);
                }
            }

            return null;
        }

        /**
         * {@inheritdoc}
         */
        public function isBound() {
            return $this->bound;
        }

        /**
         * {@inheritdoc}
         */
        public function canChangePasswords() {
            return $this->isUseSSL() || $this->isUseTLS();
        }

        /**
         * {@inheritdoc}
         */
        public function ssl($enabled = true) {
            $this->useSSL = $enabled;

            return $this;
        }

        /**
         * {@inheritdoc}
         */
        public function tls($enabled = true) {
            $this->useTLS = $enabled;

            return $this;
        }

        /**
         * {@inheritdoc}
         */
        public function getConnection() {
            return $this->connection;
        }

        /**
         * {@inheritdoc}
         */
        public function getEntries($searchResults) {
            return ldap_get_entries($this->getConnection(),
                                    $searchResults);
        }

        /**
         * {@inheritdoc}
         */
        public function getFirstEntry($searchResults) {
            return ldap_first_entry($this->getConnection(),
                                    $searchResults);
        }

        /**
         * {@inheritdoc}
         */
        public function getNextEntry($entry) {
            return ldap_next_entry($this->getConnection(),
                                   $entry);
        }

        /**
         * {@inheritdoc}
         */
        public function getAttributes($entry) {
            return ldap_get_attributes($this->getConnection(),
                                       $entry);
        }

        /**
         * {@inheritdoc}
         */
        public function countEntries($searchResults) {
            return ldap_count_entries($this->getConnection(),
                                      $searchResults);
        }

        /**
         * {@inheritdoc}
         */
        public function compare($dn, $attribute, $value) {
            return ldap_compare($this->getConnection(),
                                $dn,
                                $attribute,
                                $value);
        }

        /**
         * {@inheritdoc}
         */
        public function getLastError() {
            return ldap_error($this->getConnection());
        }

        /**
         * {@inheritdoc}
         */
        public function getValuesLen($entry, $attribute) {
            return ldap_get_values_len($this->getConnection(),
                                       $entry,
                                       $attribute);
        }

        /**
         * {@inheritdoc}
         */
        public function setOption($option, $value) {
            return ldap_set_option($this->getConnection(),
                                   $option,
                                   $value);
        }

        /**
         * {@inheritdoc}
         */
        public function setOptions(array $options = []) {
            foreach ($options as $option => $value) {
                $this->setOption($option,
                                 $value);
            }
        }

        /**
         * {@inheritdoc}
         */
        public function setRebindCallback(callable $callback) {
            return ldap_set_rebind_proc($this->getConnection(),
                                        $callback);
        }

        /**
         * {@inheritdoc}
         */
        public function startTLS() {
            return ldap_start_tls($this->getConnection());
        }


        /**
         * {@inheritdoc}
         */
        public function close() {
            $connection = $this->getConnection();

            return is_resource($connection) ? ldap_close($connection) : false;
        }

        /**
         * {@inheritdoc}
         */
        public function search($dn, $filter, array $fields, $onlyAttributes = false, $size = 0, $time = 0) {
            return ldap_search($this->getConnection(),
                               $dn,
                               $filter,
                               $fields,
                               $onlyAttributes,
                               $size,
                               $time);
        }

        /**
         * {@inheritdoc}
         */
        public function listing($dn, $filter, array $fields, $onlyAttributes = false, $size = 0, $time = 0) {
            return ldap_list($this->getConnection(),
                             $dn,
                             $filter,
                             $fields,
                             $onlyAttributes,
                             $size,
                             $time);
        }

        /**
         * {@inheritdoc}
         */
        public function read($dn, $filter, array $fields, $onlyAttributes = false, $size = 0, $time = 0) {
            return ldap_read($this->getConnection(),
                             $dn,
                             $filter,
                             $fields,
                             $onlyAttributes,
                             $size,
                             $time);
        }

        /**
         * {@inheritdoc}
         */
        public function add($dn, array $entry) {
            return ldap_add($this->getConnection(),
                            $dn,
                            $entry);
        }

        /**
         * {@inheritdoc}
         */
        public function delete($dn) {
            return ldap_delete($this->getConnection(),
                               $dn);
        }

        /**
         * {@inheritdoc}
         */
        public function rename($dn, $newRdn, $newParent, $deleteOldRdn = false) {
            return ldap_rename($this->getConnection(),
                               $dn,
                               $newRdn,
                               $newParent,
                               $deleteOldRdn);
        }

        /**
         * {@inheritdoc}
         */
        public function modify($dn, array $entry) {
            return ldap_modify($this->getConnection(),
                               $dn,
                               $entry);
        }

        /**
         * {@inheritdoc}
         */
        public function modifyBatch($dn, array $values) {
            return ldap_modify_batch($this->getConnection(),
                                     $dn,
                                     $values);
        }

        /**
         * {@inheritdoc}
         */
        public function modAdd($dn, array $entry) {
            return ldap_mod_add($this->getConnection(),
                                $dn,
                                $entry);
        }

        /**
         * {@inheritdoc}
         */
        public function modReplace($dn, array $entry) {
            return ldap_mod_replace($this->getConnection(),
                                    $dn,
                                    $entry);
        }

        /**
         * {@inheritdoc}
         */
        public function modDelete($dn, array $entry) {
            return ldap_mod_del($this->getConnection(),
                                $dn,
                                $entry);
        }

        /**
         * {@inheritdoc}
         */
        public function controlPagedResult($pageSize = 1000, $isCritical = false, $cookie = "") {
            if ($this->isPagingSupported()) {
                return ldap_control_paged_result($this->getConnection(),
                                                 $pageSize,
                                                 $isCritical,
                                                 $cookie);
            }

            throw new KaaSoftException('LDAP Pagination is not supported on your current PHP installation.');
        }

        /**
         * {@inheritdoc}
         */
        public function controlPagedResultResponse($result, &$cookie) {
            if ($this->isPagingSupported()) {
                return ldap_control_paged_result_response($this->getConnection(),
                                                          $result,
                                                          $cookie);
            }

            throw new KaaSoftException('LDAP Pagination is not supported on your current PHP installation.');
        }

        /**
         * {@inheritdoc}
         */
        public function errNo() {
            return ldap_errno($this->getConnection());
        }

        /**
         * {@inheritdoc}
         */
        public function getExtendedError() {
            return $this->getDiagnosticMessage();
        }

        /**
         * {@inheritdoc}
         */
        public function getExtendedErrorHex() {
            if (preg_match("/(?<=data\s).*?(?=,)/",
                           $this->getExtendedError(),
                           $code)) {
                return $code[0];
            }

            return null;
        }

        /**
         * {@inheritdoc}
         */
        public function getExtendedErrorCode() {
            return $this->extractDiagnosticCode($this->getExtendedError());
        }

        /**
         * {@inheritdoc}
         */
        public function err2Str($number) {
            return ldap_err2str($number);
        }

        /**
         * {@inheritdoc}
         */
        public function getDiagnosticMessage() {
            ldap_get_option($this->getConnection(),
                            LDAP_OPT_ERROR_STRING,
                            $diagnosticMessage);

            return $diagnosticMessage;
        }

        /**
         * {@inheritdoc}
         */
        public function extractDiagnosticCode($message) {
            preg_match('/^([\da-fA-F]+):/',
                       $message,
                       $matches);

            return isset( $matches[1] ) ? $matches[1] : false;
        }

        /**
         * Returns the LDAP protocol to utilize for the current connection.
         *
         * @return string
         */
        public function getProtocol() {
            return $this->isUseSSL() ? $this::PROTOCOL_SSL : $this::PROTOCOL;
        }

        /**
         * Generates an LDAP connection string for each host given.
         *
         * @param        $server
         * @param string $protocol
         * @param string $port
         * @return string
         */
        protected function getConnectionString($server, $protocol, $port) {
            return "{$protocol}{$server}:{$port}";
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
         * @return mixed
         */
        public function getPort() {
            return $this->port;
        }

        /**
         * @param mixed $port
         */
        public function setPort($port) {
            $this->port = $port;
        }

        /**
         * @return mixed
         */
        public function getBaseDN() {
            return $this->baseDN;
        }

        /**
         * @param mixed $baseDN
         */
        public function setBaseDN($baseDN) {
            $this->baseDN = $baseDN;
        }

        /**
         * @return mixed
         */
        public function getVersion() {
            return $this->version;
        }

        /**
         * @param mixed $version
         */
        public function setVersion($version) {
            $this->version = $version;
        }

        /**
         * @return mixed
         */
        public function getTimeout() {
            return $this->timeout;
        }

        /**
         * @param mixed $timeout
         */
        public function setTimeout($timeout) {
            $this->timeout = $timeout;
        }

        /**
         * @return mixed
         */
        public function getAdminUserName() {
            return $this->adminUserName;
        }

        /**
         * @param mixed $adminUserName
         */
        public function setAdminUserName($adminUserName) {
            $this->adminUserName = $adminUserName;
        }

        /**
         * @return mixed
         */
        public function getAdminPassword() {
            return $this->adminPassword;
        }

        /**
         * @param mixed $adminPassword
         */
        public function setAdminPassword($adminPassword) {
            $this->adminPassword = $adminPassword;
        }

        /**
         * @return mixed
         */
        public function getAdminAccountPrefix() {
            return $this->adminAccountPrefix;
        }

        /**
         * @param mixed $adminAccountPrefix
         */
        public function setAdminAccountPrefix($adminAccountPrefix) {
            $this->adminAccountPrefix = $adminAccountPrefix;
        }

        /**
         * @return mixed
         */
        public function getAdminAccountSuffix() {
            return $this->adminAccountSuffix;
        }

        /**
         * @param mixed $adminAccountSuffix
         */
        public function setAdminAccountSuffix($adminAccountSuffix) {
            $this->adminAccountSuffix = $adminAccountSuffix;
        }

        /**
         * @return mixed
         */
        public function getAccountPrefix() {
            return $this->accountPrefix;
        }

        /**
         * @param mixed $accountPrefix
         */
        public function setAccountPrefix($accountPrefix) {
            $this->accountPrefix = $accountPrefix;
        }

        /**
         * @return mixed
         */
        public function getAccountSuffix() {
            return $this->accountSuffix;
        }

        /**
         * @param mixed $accountSuffix
         */
        public function setAccountSuffix($accountSuffix) {
            $this->accountSuffix = $accountSuffix;
        }

        /**
         * @return boolean
         */
        public function isUseSSL() {
            return $this->useSSL;
        }

        /**
         * @param boolean $useSSL
         */
        public function setUseSSL($useSSL) {
            $this->useSSL = $useSSL;
        }

        /**
         * @return boolean
         */
        public function isUseTLS() {
            return $this->useTLS;
        }

        /**
         * @param boolean $useTSL
         */
        public function setUseTLS($useTSL) {
            $this->useTLS = $useTSL;
        }

        /**
         * @return boolean
         */
        public function isFollowReferrals() {
            return $this->followReferrals;
        }

        /**
         * @param boolean $followReferrals
         */
        public function setFollowReferrals($followReferrals) {
            $this->followReferrals = $followReferrals;
        }
    }