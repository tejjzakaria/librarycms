<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Image;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\Util\Image;
    use KAASoft\Database\Entity\Util\ImageResolution;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Image\SimpleImage;
    use KAASoft\Util\ValidationHelper;
    use PDOException;

    /**
     * Class ImageUploadBaseAction
     * @package KAASoft\Controller\Admin\Image
     */
    abstract class ImageUploadBaseAction extends AdminActionBase {
        const VALID_FILE_EXTENSIONS = [ ".jpg",
                                        ".jpeg",
                                        ".gif",
                                        ".png" ];

        private static $postImageResolutions      = [];
        private static $pageImageResolutions      = [];
        private static $generalImageResolutions   = [];
        private static $userPhotoResolutions      = [];
        private static $coverResolutions          = [];
        private static $authorPhotoResolutions    = [];
        private static $portfolioImageResolutions = [];
        /**
         * @var string Base image folder
         */
        private $imagesLocation;
        /**
         * @var array
         */
        private $resolutions = [];
        /**
         * @var Image
         */
        private $imageToDelete;
        /**
         * @var bool
         */
        private $isGalleryImage = true;

        private $uploadedImage = null;

        /**
         * ImageUploadAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
            $this->imagesLocation = FileHelper::getImageRootLocation();
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            try {
                if (Helper::isAjaxRequest()) {
                    if ($this->startDatabaseTransaction()) {
                        $imageHelper = new ImageDatabaseHelper($this);

                        if ($this->getImageToDelete() !== null) {
                            $image = $this->getImageToDelete();

                            FileHelper::deleteFolder(dirname($image->getAbsolutePath()));
                        }

                        $uploadResult = FileHelper::saveUploadedFile(FileHelper::getUniqueFolderName(FileHelper::getImageCurrentMonthLocation($this->imagesLocation)));

                        if ($uploadResult !== null and $uploadResult !== false and is_array($uploadResult) and $uploadResult["error"] === null) {
                            $image = new Image();
                            $image->setPath(FileHelper::getRelativePath(FileHelper::getImageRootLocation(),
                                                                        $uploadResult["uploadedFile"]));
                            $image->setUploadingDateTime(Helper::getDateTimeString());
                            $image->setIsGallery($this->isGalleryImage());
                            $image->setSize($_FILES["file"]["size"]);
                            $image->setTitle(FileHelper::getFileName($uploadResult["uploadedFile"]));
                            $dimensions = getimagesize($uploadResult["uploadedFile"]);
                            if ($dimensions !== false and count($dimensions) >= 2) {
                                $image->setWidth(ValidationHelper::getNullableInt($dimensions[0]));
                                $image->setHeight(ValidationHelper::getNullableInt($dimensions[1]));
                            }

                            $this->resizeImages($uploadResult["uploadedFile"],
                                                $this->resolutions);

                            $imageId = $imageHelper->saveImage($image);
                            if ($imageId === false) {
                                $this->rollbackDatabaseChanges();
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Image path couldn't be saved in database.") ]);

                                return new DisplaySwitch();
                            }

                            $image->setId($imageId);
                            $this->uploadedImage = $image;

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "imageId" => $imageId,
                                                            "url"     => $image->getWebPath() ]);
                        }
                        else {
                            $this->rollbackDatabaseChanges();
                            $this->putArrayToAjaxResponse($uploadResult);

                            return new DisplaySwitch();
                        }
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
                $errorMessage = sprintf(_("Couldn't upload or save image.%s%s"),
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
            }

            return new DisplaySwitch();
        }

        /**
         * @return array of ImageResolution
         */
        public static function getUserPhotoResolutions() {
            if (!isset( ImageUploadBaseAction::$userPhotoResolutions ) or count(ImageUploadBaseAction::$userPhotoResolutions) == 0) {
                $small = new ImageResolution();

                $small->setName("small");
                $small->setWidth(100);
                $small->setHeight(100);

                ImageUploadBaseAction::$userPhotoResolutions [] = $small;
            }

            return ImageUploadBaseAction::$userPhotoResolutions;
        }

        /**
         * @return array of ImageResolution
         */
        public static function getCoverResolutions() {
            if (!isset( ImageUploadBaseAction::$coverResolutions ) or count(ImageUploadBaseAction::$coverResolutions) == 0) {
                $small = new ImageResolution();
                $small->setName("small");
                $small->setWidth(300);
                $small->setHeight(0);

                ImageUploadBaseAction::$coverResolutions[] = $small;
            }

            return ImageUploadBaseAction::$coverResolutions;
        }

        /**
         * @return array of ImageResolution
         */
        public static function getBookImageResolutions() {
            if (!isset( ImageUploadBaseAction::$coverResolutions ) or count(ImageUploadBaseAction::$coverResolutions) == 0) {
                $small = new ImageResolution();
                $small->setName("small");
                $small->setWidth(300);
                $small->setHeight(0);

                ImageUploadBaseAction::$coverResolutions[] = $small;
            }

            return ImageUploadBaseAction::$coverResolutions;
        }

        /**
         * @return array of ImageResolution
         */
        public static function getAuthorPhotoResolutions() {
            if (!isset( ImageUploadBaseAction::$authorPhotoResolutions ) or count(ImageUploadBaseAction::$authorPhotoResolutions) == 0) {
                $small = new ImageResolution();
                $small->setName("small");
                $small->setWidth(200);
                $small->setHeight(300);

                ImageUploadBaseAction::$authorPhotoResolutions[] = $small;
            }

            return ImageUploadBaseAction::$authorPhotoResolutions;
        }

        public static function getPostImageResolutions() {
            if (!isset( ImageUploadBaseAction::$postImageResolutions ) or count(ImageUploadBaseAction::$postImageResolutions) == 0) {
                $small = new ImageResolution();

                $small->setName("small");
                $small->setWidth(420);
                $small->setHeight(0);

                ImageUploadBaseAction::$postImageResolutions [] = $small;
            }

            return ImageUploadBaseAction::$postImageResolutions;
        }

        public static function getPageImageResolutions() {
            if (!isset( ImageUploadBaseAction::$pageImageResolutions ) or count(ImageUploadBaseAction::$pageImageResolutions) == 0) {
                $small = new ImageResolution();

                $small->setName("small");
                $small->setWidth(420);
                $small->setHeight(0);

                ImageUploadBaseAction::$pageImageResolutions [] = $small;
            }

            return ImageUploadBaseAction::$pageImageResolutions;
        }

        public static function getPortfolioImageResolutions() {
            if (!isset( ImageUploadBaseAction::$portfolioImageResolutions ) or count(ImageUploadBaseAction::$portfolioImageResolutions) == 0) {
                $small = new ImageResolution();

                $small->setName("small");
                $small->setWidth(300);
                $small->setHeight(150);

                $medium = new ImageResolution();

                $medium->setName("medium");
                $medium->setWidth(300);
                $medium->setHeight(300);

                $workSmall = new ImageResolution();

                $workSmall->setName("work-small");
                $workSmall->setWidth(320);
                $workSmall->setHeight(230);

                $workBig = new ImageResolution();

                $workBig->setName("work-big");
                $workBig->setWidth(1020);
                $workBig->setHeight(500);

                ImageUploadBaseAction::$portfolioImageResolutions [] = $small;
                ImageUploadBaseAction::$portfolioImageResolutions [] = $medium;
                ImageUploadBaseAction::$portfolioImageResolutions [] = $workSmall;
                ImageUploadBaseAction::$portfolioImageResolutions [] = $workBig;
            }

            return ImageUploadBaseAction::$portfolioImageResolutions;
        }

        public static function getGeneralImageResolutions() {
            if (!isset( ImageUploadBaseAction::$generalImageResolutions ) or count(ImageUploadBaseAction::$generalImageResolutions) == 0) {
                $small = new ImageResolution();

                $small->setName("small");
                $small->setWidth(200);
                $small->setHeight(300);

                $medium = new ImageResolution();

                $medium->setName("medium");
                $medium->setWidth(200);
                $medium->setHeight(300);

                $big = new ImageResolution();

                $big->setName("big");
                $big->setWidth(600);
                $big->setHeight(600);

                $blogLarge = new ImageResolution();

                $blogLarge->setName("blog-large");
                $blogLarge->setWidth(800);
                $blogLarge->setHeight(430);

                $workSmall = new ImageResolution();

                $workSmall->setName("work-small");
                $workSmall->setWidth(320);
                $workSmall->setHeight(230);

                $workBig = new ImageResolution();

                $workBig->setName("work-big");
                $workBig->setWidth(1020);
                $workBig->setHeight(500);

                ImageUploadBaseAction::$generalImageResolutions [] = $small;
                ImageUploadBaseAction::$generalImageResolutions [] = $medium;
                ImageUploadBaseAction::$generalImageResolutions [] = $big;
                ImageUploadBaseAction::$generalImageResolutions [] = $blogLarge;
                ImageUploadBaseAction::$generalImageResolutions [] = $workSmall;
                ImageUploadBaseAction::$generalImageResolutions [] = $workBig;
            }

            return ImageUploadBaseAction::$generalImageResolutions;
        }

        public static function resizeImages($imagePath, $resolutions) {
            if ($imagePath !== null and file_exists($imagePath)) {
                $imageName = basename($imagePath);
                $imageParentFolder = dirname($imagePath);
                foreach ($resolutions as $resolution) {
                    if ($resolution instanceof ImageResolution) {
                        $subFolderName = $imageParentFolder . DIRECTORY_SEPARATOR . $resolution->getName();
                        if (false !== FileHelper::createDirectory($subFolderName)) {
                            $simpleImage = new SimpleImage();
                            $simpleImage->load($imagePath);

                            if (ValidationHelper::isEmpty($resolution->getWidth())) {
                                $simpleImage->resizeToHeight($resolution->getHeight());
                            }
                            elseif (ValidationHelper::isEmpty($resolution->getHeight())) {
                                $simpleImage->resizeToWidth($resolution->getWidth());
                            }
                            else {
                                $simpleImage->resize($resolution->getWidth(),
                                                     $resolution->getHeight());
                            }
                            $simpleImage->save($subFolderName . DIRECTORY_SEPARATOR . $imageName);
                        }
                    }
                }
            }
        }

        /**
         * @return string
         */
        public function getImagesLocation() {
            return $this->imagesLocation;
        }

        /**
         * @param string $imagesLocation
         */
        public function setImagesLocation($imagesLocation) {
            $this->imagesLocation = $imagesLocation;
        }

        /**
         * @return mixed
         */
        public function getResolutions() {
            return $this->resolutions;
        }

        /**
         * @param mixed $resolutions
         */
        public function setResolutions($resolutions) {
            $this->resolutions = $resolutions;
        }

        /**
         * @return Image
         */
        public function getImageToDelete() {
            return $this->imageToDelete;
        }

        /**
         * @param Image $imageToDelete
         */
        public function setImageToDelete($imageToDelete) {
            $this->imageToDelete = $imageToDelete;
        }

        /**
         * @return boolean
         */
        public function isGalleryImage() {
            return $this->isGalleryImage;
        }

        /**
         * @param boolean $isGalleryImage
         */
        public function setIsGalleryImage($isGalleryImage) {
            $this->isGalleryImage = $isGalleryImage;
        }

        /**
         * @return null|Image
         */
        public function getUploadedImage() {
            return $this->uploadedImage;
        }

        /**
         * @param null|Image $uploadedImage
         */
        public function setUploadedImage($uploadedImage) {
            $this->uploadedImage = $uploadedImage;
        }

    }