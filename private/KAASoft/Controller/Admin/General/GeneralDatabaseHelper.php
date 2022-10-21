<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\General;

    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\Helper;

    /**
     * Class GeneralDatabaseHelper
     * @package KAASoft\Controller\Admin\General
     */
    class GeneralDatabaseHelper extends DatabaseHelper {
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
    }