<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util\SiteViewOptions;


    use Exception;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class SiteViewOptionDeleteAction
     * @package KAASoft\Controller\Admin\Util\SiteViewOptions
     */
    class SiteViewOptionDeleteAction extends AdminActionBase {
        /**
         * SiteViewOptionDeleteAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            try {
                if (Helper::isAjaxRequest()) {
                    $filePath = ValidationHelper::getString($_POST["path"]);

                    $fullPath = realpath(FileHelper::getSiteRoot() . DIRECTORY_SEPARATOR . $filePath);
                    $svsDir = realpath(FileHelper::getSiteViewOptionFilesLocation());
                    if (Helper::startsWith($fullPath,
                                           $svsDir)
                    ) {
                        $result = FileHelper::deleteFile($fullPath);
                        if ($result === false) {
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't delete file for some reason.") ]);
                        }
                        else {
                            $this->putArrayToAjaxResponse([ "path" => $filePath ]);
                        }
                    }
                    else {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't delete file. File is not in Site View Options.") ]);
                    }

                    return new DisplaySwitch();
                }
                else {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
                }
            }
            catch (Exception $e) {
                ControllerBase::getLogger()->error($e->getMessage(),
                                                   $e);
                $errorMessage = sprintf(_("Couldn't delete file.%s%s"),
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
            }

            return new DisplaySwitch();
        }
    }