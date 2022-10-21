<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller;

    use KAASoft\Environment\Routes\PublicRoute;
    use KAASoft\Environment\Routes\Route;
    use KAASoft\Environment\ThemeSettings;
    use KAASoft\Util\FileHelper;
    use SmartyBC;

    class ConfiguredSmarty extends SmartyBC {
        protected static $INSTANCE = null;
        /**
         * @var Route
         */
        protected $activeRoute;

        /**
         * @param Route $activeRoute
         * @return ConfiguredSmarty
         */
        public static function getInstance(Route $activeRoute) {
            if (ConfiguredSmarty::$INSTANCE === null) {
                ConfiguredSmarty::$INSTANCE = new ConfiguredSmarty($activeRoute);
            }

            return ConfiguredSmarty::$INSTANCE;
        }

        final function __clone() {
        }

        final function __construct(Route $activeRoute) {
            parent::__construct();
            $this->activeRoute = $activeRoute;
            $isPublicRoute = $this->activeRoute instanceof PublicRoute;

            $this->setErrorReporting(error_reporting());

            if ($isPublicRoute) {
                $themeSettings = new ThemeSettings();
                $themeSettings->loadSettings();
                $templateDir = FileHelper::getThemesDirectory() . DIRECTORY_SEPARATOR . $themeSettings->getActiveTheme();

            }
            else {
                $templateDir = FileHelper::getDefaultTemplateDirectory();
            }
            $this->setTemplateDir($templateDir);
            $this->setCompileDir(FileHelper::getSmartyWorkingLocation() . DIRECTORY_SEPARATOR . 'templates_c');
            $this->setCacheDir(FileHelper::getSmartyWorkingLocation() . DIRECTORY_SEPARATOR . 'cache');
            $this->setConfigDir(FileHelper::getSmartyWorkingLocation() . DIRECTORY_SEPARATOR . 'configs');
            $this->addTrustedDirectory(FileHelper::getPrivateFolderLocation());
        }

        /**
         * @param $directory
         */
        public function addTrustedDirectory($directory) {
            $this->trusted_dir[] = $directory;
        }
    }

    ?>