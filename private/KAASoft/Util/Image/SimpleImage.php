<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util\Image;
    /**
     * Class SimpleImage
     * @package KAASoft\Util\Image
     */
    class SimpleImage {

        private $image;
        private $imageType;

        /**
         * @param $fileName
         */
        function load($fileName) {
            $imageInfo = getimagesize($fileName);
            $this->imageType = $imageInfo[2];
            if ($this->imageType == IMAGETYPE_JPEG) {
                $this->image = imagecreatefromjpeg($fileName);
            }
            elseif ($this->imageType == IMAGETYPE_GIF) {
                $this->image = imagecreatefromgif($fileName);
            }
            elseif ($this->imageType == IMAGETYPE_PNG) {
                $this->image = imagecreatefrompng($fileName);
            }
        }

        /**
         * @param     $filename
         * @param int $imageType
         * @param int $compression
         * @param int $permissions
         */
        function save($filename, $imageType = IMAGETYPE_JPEG, $compression = 75, $permissions = 0777) {
            if ($imageType == IMAGETYPE_JPEG) {
                imagejpeg($this->image,
                          $filename,
                          $compression);
            }
            elseif ($imageType == IMAGETYPE_GIF) {
                imagegif($this->image,
                         $filename);
            }
            elseif ($imageType == IMAGETYPE_PNG) {
                imagepng($this->image,
                         $filename);
            }
            if ($permissions != null) {
                chmod($filename,
                      $permissions);
            }
        }

        /**
         * @param int $imageType
         */
        function output($imageType = IMAGETYPE_JPEG) {
            if ($imageType == IMAGETYPE_JPEG) {
                imagejpeg($this->image);
            }
            elseif ($imageType == IMAGETYPE_GIF) {
                imagegif($this->image);
            }
            elseif ($imageType == IMAGETYPE_PNG) {
                imagepng($this->image);
            }
        }

        /**
         * @return int
         */
        function getWidth() {
            return imagesx($this->image);
        }

        /**
         * @return int
         */
        function getHeight() {
            return imagesy($this->image);
        }

        /**
         * @param $height
         */
        function resizeToHeight($height) {
            $ratio = $height / $this->getHeight();
            $width = $this->getWidth() * $ratio;
            $this->resize($width,
                          $height);
        }

        /**
         * @param $width
         */
        function resizeToWidth($width) {
            $ratio = $width / $this->getWidth();
            $height = $this->getHeight() * $ratio;
            $this->resize($width,
                          $height);
        }

        /**
         * @param $scale
         */
        function scale($scale) {
            $width = $this->getWidth() * $scale / 100;
            $height = $this->getHeight() * $scale / 100;
            $this->resize($width,
                          $height);
        }

        /**
         * @param $width
         * @param $height
         */
        function resize($width, $height) {
            $new_image = imagecreatetruecolor($width,
                                              $height);
            imagecopyresampled($new_image,
                               $this->image,
                               0,
                               0,
                               0,
                               0,
                               $width,
                               $height,
                               $this->getWidth(),
                               $this->getHeight());
            $this->image = $new_image;
        }
    }

    ?>