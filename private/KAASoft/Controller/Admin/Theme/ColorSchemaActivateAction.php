<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Theme;


    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Environment\ColorSchema;
    use KAASoft\Environment\Theme;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class ColorSchemaActivateAction
     * @package KAASoft\Controller\Admin\Theme
     */
    class ColorSchemaActivateAction extends AdminActionBase {
        /**
         * @param $args array
         * @return DisplaySwitch
         */
        protected function action($args) {
            $activeColorSchemaName = ValidationHelper::getString($_POST["activeColorSchema"]);
            if (Helper::isAjaxRequest()) {
                if ($activeColorSchemaName !== null) {

                    $themeSettings = ControllerBase::getThemeSettings();
                    $themesDir = FileHelper::getThemesDirectory();

                    $activeTheme = $themeSettings->getActiveTheme();

                    $activeThemeDir = realpath($themesDir . DIRECTORY_SEPARATOR . $activeTheme);

                    if (file_exists($activeThemeDir)) {
                        $themeConfigFile = $themeSettings->getThemeConfigFileName();
                        if (file_exists($themeConfigFile)) {
                            $themeFileContent = file_get_contents($themeConfigFile);
                            $themeArray = json_decode($themeFileContent,
                                                      true);
                            $theme = new Theme();
                            $theme->copySettings($themeArray);

                            $colorSchemas = $theme->getColorSchemas();
                            $isColorSchemaExist = false;

                            foreach ($colorSchemas as $colorSchemaName => $colorSchema) {
                                if ($colorSchema instanceof ColorSchema) {
                                    if (strcmp($colorSchemaName,
                                               $activeColorSchemaName) === 0
                                    ) {
                                        $theme->setActiveColorSchema($activeColorSchemaName);
                                        $isColorSchemaExist = true;

                                        if (file_put_contents($themeConfigFile,
                                                              json_encode($theme)) === false
                                        ) {
                                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => sprintf(_("Couldn't activate color schema (save file)."),
                                                                                                                         $activeColorSchemaName) ]);

                                            return new DisplaySwitch();
                                        }
                                        break;
                                    }
                                }
                            }

                            if (!$isColorSchemaExist) {
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => sprintf(_("There is now color schema '%s' in active theme."),
                                                                                                             $activeColorSchemaName) ]);
                            }
                            else {
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => sprintf(_("Color schema '%s' is activated."),
                                                                                                               $activeColorSchemaName) ]);
                                $this->smarty->assign("theme",
                                                      $theme);
                            }
                        }
                        else {
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Theme config file does not exist.") ]);
                        }
                    }
                    else {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Active theme does not exist.") ]);
                    }
                }
                else {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Color Schema is not specified.") ]);
                }
            }
            else {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
            }

            return new DisplaySwitch();
        }
    }