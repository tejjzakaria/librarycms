<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\Util;


    use JsonSerializable;
    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class Tag
     * @package KAASoft\Database\Entity\Util
     */
    class Tag extends DatabaseEntity implements JsonSerializable {

        /**
         * @var string
         */
        private $name;
        /**
         * @var string
         */
        private $description;

        /**
         * Tag constructor.
         * @param null $id
         */
        public function __construct($id = null) {
            parent::__construct($id);
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            return array_merge(parent::getDatabaseArray(),
                               [ 'name'        => $this->name,
                                 'description' => $this->description ]);
        }

        /**
         * @param array $databaseArray
         * @return Tag to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $tag = new Tag(ValidationHelper::getNullableInt($databaseArray["id"]));
            $tag->setName(ValidationHelper::getString($databaseArray['name']));
            $tag->setDescription(ValidationHelper::getString($databaseArray['description']));

            return $tag;
        }

        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return array_merge(parent::getDatabaseFieldNames(),
                               [ KAASoftDatabase::$TAGS_TABLE_NAME . ".name",
                                 KAASoftDatabase::$TAGS_TABLE_NAME . ".description" ]);
        }

        /**
         * (PHP 5 &gt;= 5.4.0)<br/>
         * Specify data which should be serialized to JSON
         * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         */
        function jsonSerialize() {
            return $this->getDatabaseArray();
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
        public function getDescription() {
            return $this->description;
        }

        /**
         * @param string $description
         */
        public function setDescription($description) {
            $this->description = $description;
        }
    }