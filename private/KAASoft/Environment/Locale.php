<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;


    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\Util\Language;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\Message;

    /**
     * Class Locale
     * @package KAASoft\Environment
     */
    class Locale {
        const ACTIVE_LANGUAGE  = "activeLanguage";
        const DEFAULT_LANGUAGE = [ "code"      => "en_US",
                                   "isActive"  => true,
                                   "name"      => "English",
                                   "shortCode" => "en" ];

        private static $DEFAULT_LANGUAGE;

        private $language;
        private $domain;
        private $localeDirectory;

        function __construct($localeDirectory, $domain = "messages") {
            $this->localeDirectory = $localeDirectory;
            $this->domain = $domain;
        }

        /**
         * @return Language|null
         */
        public static function getDefaultLanguage() {
            if (!isset( self::$DEFAULT_LANGUAGE )) {
                $language = null;
                if (Config::getDatabaseConnection() !== null) {
                    $kaasoftDatabase = KAASoftDatabase::getInstance(ControllerBase::getLogger());
                    $langCode = SiteViewOptions::getInstance()->getOptionValue(SiteViewOptions::DEFAULT_LANGUAGE);
                    $language = $kaasoftDatabase->getLanguage($langCode);
                }
                if ($language === null) {
                    $language = Language::getObjectInstance(Locale::DEFAULT_LANGUAGE);
                }
                self::$DEFAULT_LANGUAGE = $language;
            }

            return self::$DEFAULT_LANGUAGE;
        }

        /**
         * @param string $domain
         */
        public function setDomain($domain) {
            $this->domain = $domain;
        }

        /**
         * @param mixed $localeDirectory
         */
        public function setLocaleDirectory($localeDirectory) {
            $this->localeDirectory = $localeDirectory;
        }

        public function setLanguage($languageObject) { // Language object
            if ($languageObject instanceof Language) {
                $this->language = $languageObject;
                $language = $languageObject->getCode();
                $shortCode = $languageObject->getShortCode();

                putenv('LC_ALL=' . $language);
                if (false == putenv("LANGUAGE=" . $language)) {
                    Session::addSessionMessage(sprintf(_("Could not set the ENV variable LANGUAGE = %s"),
                                                       $language),
                                               Message::MESSAGE_STATUS_ERROR);
                }
                if (false == putenv("LANG=" . $language)) {
                    Session::addSessionMessage(sprintf(_("Could not set the ENV variable LANG = %s"),
                                                       $language),
                                               Message::MESSAGE_STATUS_ERROR);
                }
                if (!defined('LC_MESSAGES')) {
                    define('LC_MESSAGES',
                           5);
                } // в Windows эта константа может быть не определена

                ////////////////////////////////////////////////////
                setcookie(Locale::ACTIVE_LANGUAGE,
                          $language);
                ////////////////////////////////////////////////////

                $localeSet = setlocale(LC_MESSAGES,
                                       [ $language,
                                         $language . ".utf8",
                                         $language . ".UTF8",
                                         $language . ".utf-8",
                                         $language . ".UTF-8",
                                         str_replace("_",
                                                     "-",
                                                     $language),
                                         str_replace("_",
                                                     "-",
                                                     $language) . ".utf8",
                                         str_replace("_",
                                                     "-",
                                                     $language) . ".UTF8",
                                         str_replace("_",
                                                     "-",
                                                     $language) . ".utf-8",
                                         str_replace("_",
                                                     "-",
                                                     $language) . ".UTF-8",
                                         $shortCode ]);

                // if we don't get the setting we want, make sure to complain!
                $isLocaleNotFound =  empty( $localeSet );/*( or strcmp($localeSet,
                                             $language) !== 0 and strcmp(Locale::getDefaultLanguage()->getCode(),
                                                                        $localeSet) !== 0 )*/
                if ($isLocaleNotFound) {
                    Session::addSessionMessage(sprintf(_("Tried: setlocale to '%s', but could only set to '%s'."),
                                                       $language,
                                                       $localeSet),
                                               Message::MESSAGE_STATUS_WARNING);
                }

                //if ($language != Locale::getDefaultLanguage()->getCode()) {
                // comment line below in prod
                /*bindtextdomain($this->domain,
                               $this->localeDirectory . '/nocache');*/
                $bindtextdomainSet = bindtextdomain($this->domain,
                                                    $this->localeDirectory);
                if (empty( $bindtextdomainSet )) {
                    Session::addSessionMessage(sprintf(_("Tried: bindtextdomain '%s', to directory '%s', but received '%s'"),
                                                       $this->domain,
                                                       $this->localeDirectory,
                                                       $bindtextdomainSet),
                                               Message::MESSAGE_STATUS_ERROR);
                }
                if ($isLocaleNotFound) {
                    bindtextdomain($this->domain,
                                   "");
                }

                bind_textdomain_codeset($this->domain,
                                        "UTF-8");
                $textdomainSet = textdomain($this->domain);
                if (empty( $textdomainSet )) {
                    Session::addSessionMessage(sprintf(_("Tried: set textdomain to '%s', but got '%s'"),
                                                       $this->domain,
                                                       $textdomainSet),
                                               Message::MESSAGE_STATUS_ERROR);
                }
            }
        }
    }