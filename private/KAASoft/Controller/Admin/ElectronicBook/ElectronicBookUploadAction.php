<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\ElectronicBook;

    use Exception;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\ElectronicBook;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class ElectronicBookUploadAction
     * @package KAASoft\Controller\Admin\Image
     */
    class ElectronicBookUploadAction extends AdminActionBase {
        const VALID_FILE_EXTENSIONS = [ ".fb2",
                                        ".epub",
                                        ".mobi",
                                        ".djvu",
                                        ".pdf",
                                        ".azw",
                                        ".azw3",
                                        ".html",
                                        ".doc",
                                        ".docx",
                                        ".rtf",
                                        ".txt",
                                        ".cbr",
                                        ".cbz",
                                        ".CHM",
                                        ".rar",
                                        ".zip" ];

        /**
         * ElectronicBookUploadAction constructor.
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
                    $eBookHelper = new ElectronicBookDatabaseHelper($this);
                    $uploadResult = FileHelper::saveUploadedFile(FileHelper::getUniqueFolderName(FileHelper::getImageCurrentMonthLocation(FileHelper::getElectronicBookRootLocation())),
                                                                 "file",
                                                                 ElectronicBookUploadAction::VALID_FILE_EXTENSIONS);

                    if ($uploadResult !== null and $uploadResult !== false and is_array($uploadResult) and $uploadResult["error"] === null) {
                        $oldBookId = ValidationHelper::getNullableInt($_POST["oldBookId"]);
                        if ($oldBookId !== null) {
                            $oldElectronicBook = $eBookHelper->getElectronicBook($oldBookId);

                            $result = FileHelper::deleteFile(FileHelper::getElectronicBookRootLocation() . $oldElectronicBook->getPath());
                            if ($result === false) {
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_WARNING => _("Couldn't delete old file but new file is successfully uploaded.") ]);
                            }
                        }

                        $eBook = new ElectronicBook();
                        $eBook->setPath(FileHelper::getRelativePath(FileHelper::getElectronicBookRootLocation(),
                                                                    $uploadResult["uploadedFile"]));
                        $eBook->setUploadingDateTime(Helper::getDateTimeString());


                        $eBookId = $eBookHelper->saveElectronicBook($eBook);
                        if ($eBookId === false) {
                            $this->rollbackDatabaseChanges();
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("eBook path couldn't be saved in database.") ]);

                            return new DisplaySwitch();
                        }

                        if ($eBook->getWebPath() !== null) {
                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "eBookId" => $eBookId,
                                                            "path"    => $eBook->getWebPath() ]);
                        }
                        else {
                            $this->rollbackDatabaseChanges();
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("eBook file couldn't be save on drive.") ]);

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