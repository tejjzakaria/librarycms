<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Theme;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\Theme;
    use KAASoft\Environment\ThemeSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;

    /**
     * Class ThemesAction
     * @package KAASoft\Controller\Admin\Theme
     */
    class ThemesAction extends AdminActionBase {
        /**
         * ThemesAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute,
                                true);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $themesDir = FileHelper::getThemesDirectory();

            $subDirs = glob($themesDir . '/*',
                            GLOB_ONLYDIR);

            $themeSettings = new ThemeSettings();
            $themeSettings->loadSettings();

            $themes = [];
            foreach ($subDirs as $subDir) {
                $themeName = basename($subDir);
                $themeConfigFileName = realpath($subDir . DIRECTORY_SEPARATOR . Theme::THEME_CONFIG_FILE_NAME);

                if (file_exists($themeConfigFileName)) {
                    $configContent = file_get_contents($themeConfigFileName);
                    if ($configContent !== false) {

                        $jsonContent = json_decode($configContent,
                                                   true);

                        $theme = new Theme();
                        $theme->copySettings($jsonContent);
                        $theme->setLocation(substr(FileHelper::getRelativePath(FileHelper::getSiteRoot(),
                                                                               $subDir),
                                                   1));

                        $themes [$themeName] = $theme;
                    }
                }
            }

            $this->smarty->assign("themes",
                                  $themes);
            $this->smarty->assign("activeTheme",
                                  $themeSettings->getActiveTheme());

            return new DisplaySwitch('admin/themes.tpl');
        }
    }