<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Image;

    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\Util\Image;
    use KAASoft\Database\Entity\Util\ImageResolution;
    use KAASoft\Database\KAASoftDatabase;

    /**
     * Class ImageDatabaseHelper
     * @package KAASoft\Controller\Admin\Image
     */
    class ImageDatabaseHelper extends DatabaseHelper {

        /**
         * @param bool $isGalleryOnly
         * @return int
         */
        public function getImageCount($isGalleryOnly = false) {
            $queryParams = $isGalleryOnly ? [ "isGallery" => true ] : [];

            return $this->kaaSoftDatabase->count(KAASoftDatabase::$IMAGES_TABLE_NAME,
                                                 $queryParams);
        }

        /**
         * @param      $offset
         * @param      $perPage
         * @param bool $isGalleryOnly
         * @return array
         */
        public function getImages($offset = null, $perPage = null, $isGalleryOnly = false) {

            $queryParams = $isGalleryOnly ? [ "isGallery" => true ] : [];
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => [ 'uploadingDateTime' => 'DESC' ],
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }

            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$IMAGES_TABLE_NAME,
                                                          array_merge(Image::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$IMAGES_TABLE_NAME . ".id" ]),
                                                          $queryParams);

            if ($queryResult !== false) {
                $images = [];

                foreach ($queryResult as $row) {
                    $image = Image::getObjectInstance($row);

                    $images[] = $image;
                }

                return $images;
            }

            return null;
        }

        /**
         * @param $imageId
         * @return Image|null
         */
        public function getImage($imageId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$IMAGES_TABLE_NAME,
                                                       array_merge(Image::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$IMAGES_TABLE_NAME . ".id" ]),
                                                       [ "id" => $imageId ]);

            if ($queryResult !== false) {
                return Image::getObjectInstance($queryResult);
            }

            return null;
        }

        /**
         * @param Image $image
         * @return bool|$imageId
         */
        public function saveImage(Image $image) {
            $data = $image->getDatabaseArray();
            if ($image->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$IMAGES_TABLE_NAME,
                                                      $data);
            }

            else {
                return $this->kaaSoftDatabase->update(KAASoftDatabase::$IMAGES_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $image->getId() ]);
            }
        }

        /**
         * @param $covers
         * @return array|bool+
         */
        public function saveImages($covers) {
            $data = [];
            foreach ($covers as $cover) {
                if ($cover instanceof Image) {
                    $data[] = $cover->getDatabaseArray();
                }
            }

            return $this->kaaSoftDatabase->insert(KAASoftDatabase::$IMAGES_TABLE_NAME,
                                                  $data);
        }

        /**
         * @param $imageId
         * @return bool
         */
        public function deleteImage($imageId) {
            $image = $this->getImage($imageId);
            if ($image !== null) {
                return $this->kaaSoftDatabase->delete(KAASoftDatabase::$IMAGES_TABLE_NAME,
                                                      [ "id" => $imageId ]);
            }

            return false;
        }


        /**
         * @return array|null
         */
        public function getImageResolutions() {
            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$IMAGE_RESOLUTIONS_TABLE_NAME,
                                                          array_merge([ KAASoftDatabase::$IMAGES_TABLE_NAME . ".id" ],
                                                                      ImageResolution::getDatabaseFieldNames()));

            if ($queryResult !== false) {
                $resolutions = [];

                foreach ($queryResult as $resolutionRow) {
                    $resolution = ImageResolution::getObjectInstance($resolutionRow);
                    $resolutions[] = $resolution;
                }

                return $resolutions;
            }

            return null;
        }

        /**
         * @param $resolutionId
         * @return ImageResolution|null
         */
        public function getImageResolution($resolutionId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$IMAGE_RESOLUTIONS_TABLE_NAME,
                                                       array_merge(Image::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$IMAGES_TABLE_NAME . ".id" ]),
                                                       [ "id" => $resolutionId ]);
            if ($queryResult !== false) {
                $resolution = ImageResolution::getObjectInstance($queryResult);

                return $resolution;

            }

            return null;
        }

        /**
         * @param ImageResolution $resolution
         * @return array|bool|int
         */
        public function saveImageResolution(ImageResolution $resolution) {
            $data = $resolution->getDatabaseArray();
            if ($resolution->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$IMAGE_RESOLUTIONS_TABLE_NAME,
                                                      $data);
            }

            else {
                return $this->kaaSoftDatabase->update(KAASoftDatabase::$IMAGE_RESOLUTIONS_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $resolution->getId() ]);
            }
        }

        /**
         * @param $resolutionId
         * @return bool
         */
        public function deleteImageResolution($resolutionId) {
            $resolution = $this->getImageResolution($resolutionId);
            if ($resolution !== null) {
                return $this->kaaSoftDatabase->delete(KAASoftDatabase::$IMAGE_RESOLUTIONS_TABLE_NAME,
                                                      [ "id" => $resolutionId ]);
            }

            return false;
        }


        public function isImageResolutionExist($resolutionId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$IMAGE_RESOLUTIONS_TABLE_NAME,
                                               [ "id" => $resolutionId ]);
        }

    }