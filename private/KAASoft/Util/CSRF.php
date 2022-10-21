<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2017-12-13
     */


    namespace KAASoft\Util;


    use KAASoft\Environment\Session;

    /**
     * Class CSRF
     * @package KAASoft\Util
     */
    class CSRF {
        const TOKEN_ID    = "tokenId";
        const TOKEN_VALUE = "tokenValue";

        const FORM_VALUES = "formValues";

        private static $CSRF_INSTANCE = null;

        /**
         * CSRF constructor.
         */
        final protected function __construct() {
        }


        public static function getInstance() {
            if (self::$CSRF_INSTANCE === null) {
                self::$CSRF_INSTANCE = new CSRF();
            }

            return self::$CSRF_INSTANCE;
        }

        public function getTokenId() {

            if (Session::getSessionValue(CSRF::TOKEN_ID) !== null) {
                return Session::getSessionValue(CSRF::TOKEN_ID);
            }
            else {
                $tokenId = $this->random(10);
                Session::addSessionValue(CSRF::TOKEN_ID,
                                         $tokenId);

                return $tokenId;
            }
        }

        public function getToken() {
            if (Session::getSessionValue(CSRF::TOKEN_VALUE) !== null) {
                return Session::getSessionValue(CSRF::TOKEN_VALUE);
            }
            else {
                $token = hash('sha256',
                              $this->random(500));

                Session::addSessionValue(CSRF::TOKEN_VALUE,
                                         $token);

                return $token;
            }

        }

        public function validateToken($method = 'post') {
            if ($method == 'post' || $method == 'get') {
                /** @noinspection PhpUnusedLocalVariableInspection */
                $post = $_POST;
                /** @noinspection PhpUnusedLocalVariableInspection */
                $get = $_GET;
                if (isset( ${$method}[$this->getTokenId()] ) && ( ${$method}[$this->getTokenId()] == $this->getToken() )) {
                    return true;
                }
                else {
                    return false;
                }
            }
            else {
                return false;
            }
        }

        public function encryptFormNames($names, $regenerate = false) {
            if ($regenerate == true) {
                Session::removeSessionValue(CSRF::FORM_VALUES);
            }
            $values = [];
            // get form names from session
            if (Session::getSessionValue(CSRF::FORM_VALUES) === null) {
                Session::addSessionValue(CSRF::FORM_VALUES,
                                         []);
            }
            $sessionArray = Session::getSessionValue(CSRF::FORM_VALUES);

            // encrypt names and save in session
            foreach ($names as $name) {
                $encryptedName = ( $sessionArray[$name] !== null ? $sessionArray[$name] : $this->random(10) );
                $sessionArray[$name] = $encryptedName;
                $values[$name] = $encryptedName;
            }

            Session::addSessionValue(CSRF::FORM_VALUES,
                                     array_merge(Session::getSessionValue(CSRF::FORM_VALUES),
                                                 $sessionArray));

            return $values;
        }


        private function random($len) {
            $result = '';
            if (function_exists('openssl_random_pseudo_bytes')) {
                $byteLen = intval(( $len / 2 ) + 1);
                $result = substr(bin2hex(openssl_random_pseudo_bytes($byteLen)),
                                 0,
                                 $len);
            }
            elseif (@is_readable('/dev/urandom')) {
                $f = fopen('/dev/urandom',
                           'r');
                $urandom = fread($f,
                                 $len);
                fclose($f);
                $result = '';
            }

            if (empty( $result )) {
                for ($i = 0; $i < $len; ++$i) {
                    if (!isset( $urandom )) {
                        if ($i % 2 == 0) {
                            mt_srand(time() % 2147 * 1000000 + (double)microtime() * 1000000);
                        }
                        $rand = 48 + mt_rand() % 64;
                    }
                    else {
                        $rand = 48 + ord($urandom[$i]) % 64;
                    }

                    if ($rand > 57) {
                        $rand += 7;
                    }
                    if ($rand > 90) {
                        $rand += 6;
                    }

                    if ($rand == 123) {
                        $rand = 52;
                    }
                    if ($rand == 124) {
                        $rand = 53;
                    }
                    $result .= chr($rand);
                }
            }

            return $result;
        }
    }