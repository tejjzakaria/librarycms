<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-06-10
     */


    namespace KAASoft\Util\SMS;


    use JsonSerializable;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class SmsProvider
     * @package KAASoft\Util
     */
    abstract class SmsProviderConfiguration implements JsonSerializable {
        protected $name;
        protected $title;

        /**
         * SmsProvider constructor.
         */
        public function __construct() {

        }

        /**
         * @param array $databaseArray
         */
        public function populateInstance(array $databaseArray) {
            $this->setName(ValidationHelper::getString($databaseArray["name"]));
            $this->setTitle(ValidationHelper::getString($databaseArray["title"]));
        }

        /**
         * Specify data which should be serialized to JSON
         * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         * @since 5.4.0
         */
        function jsonSerialize() {
            return [ "name"  => $this->name,
                     "title" => $this->title ];
        }

        /**
         * @return array
         */
        abstract public function getConfig();

        /**
         * @return array
         */
        abstract public function getTitledConfig();

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