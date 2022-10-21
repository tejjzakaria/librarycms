<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util\FTP;

    /**
     * Class FTPClient
     * @package KAASoft\Util\FTP
     */
    class FTPClient {
        public static $ASCII_EXTENSIONS = [ 'txt',
                                            'csv' ];

        private $connectionId;
        private $host;
        private $port;
        private $user;
        private $password;

        private $loginOk      = false;
        private $messageArray = [];

        //private $message;

        function __construct($host, $port = 21, $timeout = 90) {

            $this->host = $host;
            $this->port = $port;

            $this->connect($host,
                           $port,
                           $timeout);
        }

        function __destruct() {
            $this->close();
        }

        public function connect($host, $port = 21, $timeout = 90) {
            // *** Set up basic connection
            $this->connectionId = ftp_connect($host,
                                              $port,
                                              $timeout);

            // *** Check connection
            if (( !$this->connectionId )) {
                $this->logMessage(_('FTP connection has failed!'));

                return false;
            }

            return true;
        }

        /**
         * @return resource
         */
        public function getConnectionId() {
            return $this->connectionId;
        }

        public function login($user, $password) {
            // *** Login with username and password
            $this->user = $user;
            $this->password = $password;
            if (isset( $this->connectionId ) && $this->connectionId) {
                $login_result = ftp_login($this->connectionId,
                                          $user,
                                          $password);
                if (!$login_result) {
                    $this->logMessage(sprintf(_("Failed to connect to \"%s:%d\" by user \"%s\"."),
                                              $this->host,
                                              $this->port,
                                              $this->user));

                    return false;
                }
                else {
                    $this->logMessage(sprintf(_("Connection is established to \"%s:%d\" by user \"%s\"."),
                                              $this->host,
                                              $this->port,
                                              $this->user));
                    $this->loginOk = true;

                    return true;
                }

            }
            else {
                $this->logMessage(sprintf(_("Couldn't connect to \"%s:%d\"."),
                                          $this->host,
                                          $this->port));

                return false;
            }
        }

        public function uploadFile($srcFile, $destinationFile) {

            if (isset( $this->connectionId ) && $this->connectionId) {
                // *** Set the transfer mode
                $fileNameParts = explode('.',
                                         $srcFile);
                $extension = end($fileNameParts);
                if (in_array($extension,
                             FTPClient::$ASCII_EXTENSIONS)) {
                    $mode = FTP_ASCII;
                }
                else {
                    $mode = FTP_BINARY;
                }

                // *** Upload the file
                $upload = ftp_put($this->connectionId,
                                  $destinationFile,
                                  $srcFile,
                                  $mode);

                // *** Check upload status
                if (!$upload) {
                    $this->logMessage(_("Couldn't upload file."));

                    return false;
                }
                else {
                    $this->logMessage(sprintf(_("File \"%s\"  is uploaded to FTP \"%s:%d%s/%s\"."),
                                              $srcFile,
                                              $this->host,
                                              $this->port,
                                              $this->getCurrentDirectory(),
                                              $destinationFile));

                    return true;
                }
            }

            return false;
        }

        public function getFileSize($file) {
            $res = ftp_size($this->connectionId,
                            $file);

            if ($res != -1) {
                $this->logMessage(sprintf(_("Size of %s is %d bytes."),
                                          $file,
                                          $res));

                return $res;
            }
            else {
                $this->logMessage(_("Couldn't get the size."));
            }

            return -1;
        }

        public function changeDir($directory) {
            if ($this->connectionId) {
                if (@ftp_chdir($this->connectionId,
                               $directory)
                ) {
                    $this->logMessage(sprintf(_('Current directory is now: "%s".'),
                                              ftp_pwd($this->connectionId)));

                    return true;
                }
                else {
                    $this->logMessage(_('Couldn\'t change directory.'));
                }
            }

            return false;
        }

        public function getDirListing($directory = '.', $parameters = '-la') {
            if ($this->connectionId) {
                // get contents of the current directory
                $contentsArray = ftp_nlist($this->connectionId,
                                           $parameters . '  ' . $directory);

                return $contentsArray;
            }

            return false;
        }

        public function getCurrentDirectory() {
            return ftp_pwd($this->connectionId);
        }

        public function downloadFile($remoteFile, $localFile) {
            if (isset( $this->connectionId ) && $this->connectionId) {
                // *** Set the transfer mode
                $extension = end(explode('.',
                                         $localFile));
                if (in_array($extension,
                             FTPClient::$ASCII_EXTENSIONS)) {
                    $mode = FTP_ASCII;
                }
                else {
                    $mode = FTP_BINARY;
                }

                if (ftp_get($this->connectionId,
                            $localFile,
                            $remoteFile,
                            $mode)) {
                    $this->logMessage(sprintf(_("File \"%s\" is successfully downloaded and saved to \"%s\"."),
                                              $remoteFile,
                                              $localFile));

                    return true;
                }
                else {
                    $this->logMessage(sprintf(_("Couldn't download or save \"%s\"."),
                                              $remoteFile));

                    return false;
                }
            }

            return false;
        }

        public function deleteFile($file) {
            if (isset( $this->connectionId ) && $this->connectionId) {
                // попытка удалить файл
                if (ftp_delete($this->connectionId,
                               $file)) {
                    $this->logMessage(sprintf(_("File \"%s\" is successfully removed."),
                                              $file));

                    return true;
                }
                else {
                    $this->logMessage(sprintf(_("Couldn't delete file \"%s\"."),
                                              $file));

                    return false;
                }
            }

            return false;
        }

        public function setPassiveMode($isPassive) {
            // *** Sets passive mode on/off (default off)
            return ftp_pasv($this->connectionId,
                            $isPassive);
        }

        public function close() {
            if (isset( $this->connectionId ) && $this->connectionId) {
                ftp_close($this->connectionId);
            }
        }

        public function getMessages() {
            return $this->messageArray;
        }

        public function getLastMessage() {
            return end($this->messageArray);
        }

        public function mkDir($ftpPath) {
            $parts = preg_split('/\\\\|\//',
                                $ftpPath); // 2013/06/11/username
            foreach ($parts as $part) {
                if (!empty( $part )) {
                    if (!@ftp_chdir($this->connectionId,
                                    $part)
                    ) {
                        if (ftp_mkdir($this->connectionId,
                                      $part)) {
                            $this->logMessage(sprintf(_('Directory "%s" is created successfully.'),
                                                      $ftpPath));
                            ftp_chdir($this->connectionId,
                                      $part);

                            return true;
                        }
                        else {
                            // *** ...Else, FAIL.
                            $this->logMessage(sprintf(_('Failed creating directory "%s".'),
                                                      $ftpPath));

                            return false;
                        }
                        //ftp_chmod($this->connectionId, 0777, $part);
                    }
                    else {
                        //$this->logMessage('Directory "' . $ftpPath . '" is already exist.');

                        //return true;
                    }
                }
            }

            return false;
        }

        /**
         * @param $message
         */
        private function logMessage($message) {
            $this->messageArray[] = $message;
        }
    }