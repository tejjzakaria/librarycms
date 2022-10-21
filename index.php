<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft;

    use ErrorException;
    use Exception;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Environment\Routes\PublicRoute;
    use KAASoft\Environment\Session;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use Throwable;

    define("SMARTY_SPL_AUTOLOAD",
           1);

    class Index {
        /**
         * Index constructor.
         */
        public function __construct() {
            // register class loader
            spl_autoload_register([ $this,
                                    "loadClass" ]);

            // register error handler
            set_error_handler([ $this,
                                "handleError" ]);

            // register exception handler
            set_exception_handler([ $this,
                                    "handleException" ]);

            // register shutdown handler
            register_shutdown_function([ $this,
                                         "handleShutdown" ]);

            // setup some folders
            $documentRootParentDirectory = realpath(dirname(__FILE__));
            $privateDirectory = $documentRootParentDirectory . DIRECTORY_SEPARATOR . "private";
            $smartyDirectory = $privateDirectory . DIRECTORY_SEPARATOR . "Smarty";
            $log4phpDirectory = $privateDirectory . DIRECTORY_SEPARATOR . "Log4PHP";
            $htmlPurifierDirectory = $privateDirectory . DIRECTORY_SEPARATOR . "HTMLPurifier";

            /** @noinspection PhpIncludeInspection */
            require_once( $smartyDirectory . DIRECTORY_SEPARATOR . "SmartyBC.class.php" );
            /** @noinspection PhpIncludeInspection */
            require_once( $log4phpDirectory . DIRECTORY_SEPARATOR . "Logger.php" );
            /** @noinspection PhpIncludeInspection */
            require_once( $htmlPurifierDirectory . DIRECTORY_SEPARATOR . "HTMLPurifier.auto.php" );
        }

        public function loadClass($className) {
            if ($this->startsWith($className,
                                  "Smarty")
            ) {
                return;
            }
            else {
                $srcRoot = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "private" . DIRECTORY_SEPARATOR;
            }

            $className = str_replace("_",
                                     "\\",
                                     $className);
            $className = ltrim($className,
                               "\\");
            $fileName = "";
            if ($lastNsPos = strripos($className,
                                      "\\")
            ) {
                $namespace = substr($className,
                                    0,
                                    $lastNsPos);
                $className = substr($className,
                                    $lastNsPos + 1);
                $fileName = str_replace("\\",
                                        DIRECTORY_SEPARATOR,
                                        $namespace) . DIRECTORY_SEPARATOR;
            }
            $fileName .= str_replace("_",
                                     DIRECTORY_SEPARATOR,
                                     $className) . ".php";

            if (file_exists($srcRoot . $fileName)) {
                /** @noinspection PhpIncludeInspection */
                require_once( $srcRoot . $fileName );
            }
            else {
                spl_autoload($className,
                             ".php");
            }
        }

        public function handleError($errno, $errstr, $errfile, $errline) {
            if (!( error_reporting() & $errno )) {
                // Error is not specified in the error_reporting setting, so we ignore it.
                return false;
            }

            switch ($errno) {
                case E_ERROR:
                    throw new ErrorException($errstr,
                                             $errno,
                                             0,
                                             $errfile,
                                             $errline);
                    break;
                // todo: process another errors
                case E_WARNING:
                    break;

                case E_NOTICE:
                    break;

                default:
                    break;
            }

            return true;
        }

        /**
         * @param $throwable Throwable
         */
        public function handleException($throwable) {
            Session::addSessionMessage($throwable->getMessage(),
                                       Message::MESSAGE_STATUS_ERROR);
            ControllerBase::getLogger()->error($throwable->getMessage(),
                                               $throwable);
            ControllerBase::getLogger()->error($throwable->getMessage(),
                                               Helper::printExceptionAsString($throwable));
            header("HTTP/1.1 500 Internal Server Error");
            Helper::redirectTo(FileHelper::getSiteRelativeLocation() . "/500");
            die();
        }


        public function handleShutdown() {
            // todo: implement method if it will be required
        }

        public function run() {
            try {
                $installerRoute = new PublicRoute("Installer",
                                                  "[/]??",
                                                  'Installer\\Controller\\SiteInstallAction',
                                                  '/');
                $installerRoute->setName(ControllerBase::INSTALLER_ROUTE_NAME);

                $moduleClassName = 'Installer\\Controller\\SiteInstallAction';
                $moduleClass = new $moduleClassName($installerRoute);

                call_user_func([ $moduleClass,
                                 "processRequest" ]);
            }
            catch (Exception $e) {
                Helper::processFatalException($e,
                                              null);
            }
        }

        /**
         * @param $sourceString
         * @param $searchString
         * @return bool
         */
        private function startsWith($sourceString, $searchString) {
            return strpos($sourceString,
                          $searchString) === 0;
        }
    }

    $index = new Index();
    $index->run();
    ?>