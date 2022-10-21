<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-06-04
     */

    namespace KAASoft\Util\Layout;


    use InvalidArgumentException;
    use JsonSerializable;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class LayoutElement
     * @package KAASoft\Util\Layout
     */
    class LayoutElement implements JsonSerializable {
        private $x;
        private $y;
        private $width;
        private $height;
        private $name;
        private $title;

        /**
         * LayoutElement constructor.
         * @param null $x
         * @param null $y
         * @param null $width
         * @param null $height
         * @param null $name
         * @param null $title
         */
        public function __construct($x = null, $y = null, $width = null, $height = null, $name = null, $title = null) {
            $this->x = $x;
            $this->y = $y;
            $this->width = $width;
            $this->height = $height;
            $this->name = $name;
            $this->title = $title;
        }


        /**
         * Specify data which should be serialized to JSON
         * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         * @since 5.4.0
         */
        public function jsonSerialize() {
            return [ "x"      => $this->x,
                     "y"      => $this->y,
                     "width"  => $this->width,
                     "height" => $this->height,
                     "name"   => $this->name,
                     "title"  => $this->title ];
        }

        /**
         * @param $array
         * @return LayoutElement
         */
        public static function getObjectInstance($array) {
            $element = new LayoutElement();
            $element->setX(ValidationHelper::getInt($array["x"]));
            $element->setY(ValidationHelper::getInt($array["y"]));
            $element->setWidth(ValidationHelper::getInt($array["width"]));
            $element->setHeight(ValidationHelper::getInt($array["height"]));
            $element->setName(ValidationHelper::getString($array["name"]));
            $element->setTitle(ValidationHelper::getString($array["title"]));

            return $element;
        }

        /**
         * @param $layoutElement
         * @return bool
         */
        public function isMoreThen($layoutElement) {
            if ($layoutElement instanceof LayoutElement) {
                return $this->y >= $layoutElement->y and $this->x >= $layoutElement->x;

            }
            throw  new InvalidArgumentException("layoutElement should be instance of LayoutElement class");
        }

        /**
         * @param $layoutElement
         * @return bool
         */
        function isLessThen($layoutElement) {
            if ($layoutElement instanceof LayoutElement) {
                return $this->y <= $layoutElement->y and $this->x <= $layoutElement->x;

            }
            throw  new InvalidArgumentException("layoutElement should be instance of LayoutElement class");
        }

        /**
         * @return mixed
         */
        public function getX() {
            return $this->x;
        }

        /**
         * @param mixed $x
         */
        public function setX($x) {
            $this->x = $x;
        }

        /**
         * @return mixed
         */
        public function getY() {
            return $this->y;
        }

        /**
         * @param mixed $y
         */
        public function setY($y) {
            $this->y = $y;
        }

        /**
         * @return mixed
         */
        public function getWidth() {
            return $this->width;
        }

        /**
         * @param mixed $width
         */
        public function setWidth($width) {
            $this->width = $width;
        }

        /**
         * @return mixed
         */
        public function getHeight() {
            return $this->height;
        }

        /**
         * @param mixed $height
         */
        public function setHeight($height) {
            $this->height = $height;
        }

        /**
         * @return mixed
         */
        public function getName() {
            return $this->name;
        }

        /**
         * @param mixed $name
         */
        public function setName($name) {
            $this->name = $name;
        }

        /**
         * @return mixed
         */
        public function getTitle() {
            return $this->title;
        }

        /**
         * @param mixed $title
         */
        public function setTitle($title) {
            $this->title = $title;
        }
    }