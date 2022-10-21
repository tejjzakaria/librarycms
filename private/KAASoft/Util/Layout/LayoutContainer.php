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

    class LayoutContainer implements JsonSerializable {
        private $name;
        /**
         * @var array LayoutElement
         */
        private $elements;

        /**
         * LayoutContainer constructor.
         * @param null $name
         */
        public function __construct($name = null) {
            $this->name = $name;
        }

        /**
         * @param $array
         * @return LayoutContainer
         */
        public static function getObjectInstance($array) {
            $container = new LayoutContainer();
            $container->setName(ValidationHelper::getString($array["name"]));

            $elements = ValidationHelper::getArray($array["elements"]);
            foreach ($elements as $elementArray) {
                $element = LayoutElement::getObjectInstance($elementArray);
                $container->addElement($element);
            }

            return $container;
        }

        /**
         * Specify data which should be serialized to JSON
         * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         * @since 5.4.0
         */
        public function jsonSerialize() {
            return [ "name"     => $this->name,
                     "elements" => $this->elements ];
        }

        /**
         * @return bool
         */
        public function sortElements() {
            if ($this->elements !== null and count($this->elements) > 1) {
                return uasort($this->elements,
                              [ "self",
                                "compareLayoutElements" ]);
            }

            return true;
        }

        /**
         * @param $a LayoutElement
         * @param $b LayoutElement
         * @return int
         */
        public static function compareLayoutElements($a, $b) {
            if (!( $a instanceof LayoutElement ) or !( $b instanceof LayoutElement )) {
                throw  new InvalidArgumentException("Each parameter should be instance of LayoutElement class");
            }

            if ($a->getY() < $b->getY()) {
                return -1;
            }
            if ($a->getY() > $b->getY()) {
                return 1;
            }

            if ($a->getX() === $b->getX() and $a->getY() === $b->getY()) {
                return 0;
            }

            return ( $a->getX() < $b->getX() ) ? -1 : 1;
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
        public function getElements() {
            return $this->elements;
        }

        /**
         * @param mixed $elements
         */
        public function setElements($elements) {
            $this->elements = $elements;
        }

        /**
         * @param $element LayoutElement
         */
        public function addElement($element) {
            if ($this->elements === null) {
                $this->elements = [];
            }

            $this->elements[] = $element;
        }
    }