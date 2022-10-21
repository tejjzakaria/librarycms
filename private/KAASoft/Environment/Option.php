<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;


    use JsonSerializable;
    use KAASoft\Util\Enum\Control;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;

    /**
     * Class Option
     * @package KAASoft\Environment
     */
    class Option implements JsonSerializable {
        private $group;
        private $name;
        private $description;
        private $title;
        private $control;
        private $listValues;
        private $value;
        private $defaultValue;

        /**
         * @param string        $name
         * @param string        $title
         * @param string        $control
         * @param null          $description
         * @param string|object $value
         * @param null          $defaultValue
         * @param array         $listValues
         * @param string        $group
         */
        function __construct($name, $title, $control, $description = null, $value = null, $defaultValue = null, $listValues = null, $group = OptionGroup::OPTION_GROUP_GENERAL) {
            $this->name = $name;
            $this->control = $control;
            $this->value = $value;
            $this->defaultValue = $defaultValue;
            $this->group = $group;
            $this->description = $description;

            if ($title == null) {
                $title = $name;
            }
            $this->title = $title;

            if (is_array($listValues)) {
                foreach ($listValues as $key => $value) {
                    if (!empty( $value )) {
                        $this->listValues[$key] = trim($value);
                    }
                }
            }
        }

        /**
         * @return bool
         */
        public function isListValue() {
            return isset( $this->listValues ) && is_array($this->listValues) && count($this->listValues) > 0;
        }

        /**
         * @return string
         */
        public function getName() {
            return $this->name;
        }

        /**
         * @param string $name
         */
        public function setName($name) {
            $this->name = $name;
        }

        /**
         * @return string
         */
        public function getControl() {
            return $this->control;
        }

        /**
         * @param string $control
         */
        public function setControl($control) {
            $this->control = $control;
        }

        /**
         * @return array
         */
        public function getListValues() {
            return $this->listValues;
        }

        /**
         * @param array $listValues
         */
        public function setListValues(array $listValues) {
            if (isset( $listValues )) {
                $this->listValues = $listValues;
            }
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

        /**
         * @return null
         */
        public function getValue() {
            $value = $this->value;
            // if control is FILE prepend it by relative site location
            if (strcmp($this->getControl(),
                       Control::FILE) === 0
            ) {
                $value = FileHelper::getSiteRelativeLocation() . $value;
            }

            return $value;
        }

        /**
         * @param null $value
         */
        public function setValue($value) {
            // if control is FILE remove relative site location
            if (strcmp($this->getControl(),
                       Control::FILE) === 0
            ) {
                if (Helper::startsWith($value,
                                       FileHelper::getSiteRelativeLocation())
                ) {
                    $value = substr($value,
                                    strlen(FileHelper::getSiteRelativeLocation()));

                }
            }

            $this->value = $value;
        }

        /**
         * @return array
         */
        function jsonSerialize() {
            return [ "group"        => $this->group,
                     "name"         => $this->name,
                     "description"  => $this->description,
                     "title"        => $this->title,
                     "control"      => $this->control,
                     "listValues"   => $this->listValues,
                     "value"        => $this->value,
                     "defaultValue" => $this->defaultValue ];
        }

        /**
         * @return mixed
         */
        public function getGroup() {
            return $this->group;
        }

        /**
         * @param mixed $group
         */
        public function setGroup($group) {
            $this->group = $group;
        }

        /**
         * @return mixed
         */
        public function getDescription() {
            return $this->description;
        }

        /**
         * @param mixed $description
         */
        public function setDescription($description) {
            $this->description = $description;
        }

        /**
         * @return null
         */
        public function getDefaultValue() {
            return $this->defaultValue;
        }

        /**
         * @param null $defaultValue
         */
        public function setDefaultValue($defaultValue) {
            $this->defaultValue = $defaultValue;
        }
    }