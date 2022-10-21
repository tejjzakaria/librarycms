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
     * Class SiteViewOptionUploadAction
     * @package KAASoft\Controller\Admin\Image
     */
    class SiteViewOptionUploadAction extends AdminActionBase {

        /**
         * SiteViewOptionUploadAction constructor.
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
                    $uploadResult = FileHelper::saveUploadedFile(FileHelper::getSiteViewOptionFilesLocation());

                    if ($uploadResult !== null and $uploadResult !== false and is_array($uploadResult) and $uploadResult["error"] === null) {
                        $oldPath = ValidationHelper::getString($_POST["path"]);
                        $optionName = ValidationHelper::getString($_POST["optionName"]);

                        $option = $this->siteViewOptions->getOption($optionName);

                        if ($option !== null) {
                            if(!ValidationHelper::isEmpty($oldPath)) {
                                if (strcmp($option->getDefaultValue(),
                                           $oldPath) !== 0
                                ) {
                                    $result = FileHelper::deleteFile(FileHelper::getSiteRoot() . $oldPath);
                                    if ($result === false) {
                                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_WARNING => _("Couldn't delete old file but new file is successfully uploaded.") ]);
                                    }
                                }
                            }

                            $path = FileHelper::getRelativePath(FileHelper::getSiteRoot(),
                                                                $uploadResult["uploadedFile"]);

                            $this->putArrayToAjaxResponse([ "path" => $path ]);
                        }
                        else {
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_WARNING => _("There is no OptionName in POST parameters.") ]);
                        }
                    }
                    else {
                        $this->putArrayToAjaxResponse($uploadResult);

                        return new DisplaySwitch();
                    }
                }
                else {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
                }
            }
            catch (Exception $e) {
                ControllerBase::getLogger()->error($e->getMessage(),
                                                   $e);
                $errorMessage = sprintf(_("Couldn't upload or save file.%s%s"),
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
            }

            return new DisplaySwitch();
        }
    }