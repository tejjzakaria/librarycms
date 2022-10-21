<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Image;


    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\Util\ImageResolution;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class ImageDeleteBaseAction
     * @package KAASoft\Controller\Admin\Image
     */
    abstract class ImageDeleteBaseAction extends AdminActionBase {
        /**
         * @var array
         */
        private $resolutions;

        /**
         * ImageDeleteBaseAction constructor.
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
            $imageId = $args["imageId"];

            try {
                if (Helper::isAjaxRequest()) {
                    $imageHelper = new ImageDatabaseHelper($this);

                    if ($this->startDatabaseTransaction()) {
                        $image = $imageHelper->getImage($imageId);
                        if ($image !== null) {
                            $filePath = FileHelper::getImageRootLocation() . $image->getPath();
                            if ($this->deleteResizedImages($filePath) and FileHelper::deleteFile($filePath) === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("Couldn't delete Image '%d' from hard drive for some reason."),
                                                        $imageId);
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch(null,
                                                         null,
                                                         true);
                            }
                            $result = $imageHelper->deleteImage($imageId);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("Couldn't delete Image '%d' from database for some reason."),
                                                        $imageId);
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch(null,
                                                         null,
                                                         true);
                            }
                            $this->commitDatabaseChanges();
                            // !!!!!!!!!!!!!!
                            // ALL is OK. Results will be published by child classes. ????
                            // !!!!!!!!!!!!!!
                            //$this->putArrayToAjaxResponse([ "removedLines" => $result ]);
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Image is deleted successfully.") ]);

                        }
                        else {
                            $this->rollbackDatabaseChanges();
                            $errorMessage = sprintf(_("There is no Image with Id '%d' in database(%s)."),
                                                    $imageId,
                                                    KAASoftDatabase::$IMAGES_TABLE_NAME);
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                            return new DisplaySwitch(null,
                                                     null,
                                                     true);
                        }
                    }
                    else {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Database transaction couldn't be created.") ]);
                    }
                }
                else {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
                }
            }
            catch (PDOException $e) {
                $this->rollbackDatabaseChanges();
                ControllerBase::getLogger()->error($e->getMessage(),
                                                   $e);
                $errorMessage = sprintf(_("Couldn't delete Image '%d'.%s%s"),
                                        $imageId,
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                return new DisplaySwitch(null,
                                         null,
                                         true);
            }

            return new DisplaySwitch();
        }

        private function deleteResizedImages($imagePath) {
            if ($imagePath !== null and file_exists($imagePath)) {
                $imageResolutions = $this->getResolutions();
                $imageName = basename($imagePath);
                $imageParentFolder = dirname($imagePath);
                foreach ($imageResolutions as $resolution) {
                    if ($resolution instanceof ImageResolution) {
                        $subFolderName = $imageParentFolder . DIRECTORY_SEPARATOR . $resolution->getName();
                        if (false === FileHelper::deleteFile($subFolderName . DIRECTORY_SEPARATOR . $imageName)) {
                            return false;
                        }
                    }
                }
            }

            return true;
        }

        /**
         * @return array
         */
        public function getResolutions() {
            return $this->resolutions;
        }

        /**
         * @param array $resolutions
         */
        public function setResolutions($resolutions) {
            $this->resolutions = $resolutions;
        }
    }