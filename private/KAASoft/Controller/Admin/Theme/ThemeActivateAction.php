<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Theme;


    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Environment\ThemeSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class ThemeActivateAction
     * @package KAASoft\Controller\Admin\Theme
     */
    class ThemeActivateAction extends AdminActionBase {
        /**
         * @param $args array
         * @return DisplaySwitch
         */
        protected function action($args) {
            $activeThemeName = ValidationHelper::getString($_POST["activeThemeName"]);
            if (Helper::isAjaxRequest()) {
                if ($activeThemeName !== null) {
                    $themesDir = FileHelper::getThemesDirectory();

                    $activeThemeDir = realpath($themesDir . DIRECTORY_SEPARATOR . $activeThemeName);

                    if (file_exists($activeThemeDir)) {
                        $themeSettings = new ThemeSettings();
                        $themeSettings->setActiveTheme($activeThemeName);
                        if ($themeSettings->saveSettings() === false) {
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't activate theme.") ]);
                        }
                        else {
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => sprintf(_("Theme '%s' is activated."),
                                                                                                           $activeThemeName) ]);
                        }
                    }
                    else {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Specified theme does not exist.") ]);
                    }
                }
                else {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Theme is not specified.") ]);
                }
            }
            else {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
            }

            return new DisplaySwitch();
        }
    }