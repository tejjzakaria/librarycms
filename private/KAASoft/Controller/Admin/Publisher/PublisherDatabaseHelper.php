<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Publisher;

    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\General\Publisher;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class PublisherDatabaseHelper
     * @package KAASoft\Controller\Admin\Publisher
     */
    class PublisherDatabaseHelper extends DatabaseHelper {

        /**
         * @param null $publisherIds
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getPublishers($publisherIds = null, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [];
            if (!ValidationHelper::isArrayEmpty($publisherIds)) {
                $queryParams = array_merge($queryParams,
                                           [ KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".id" => $publisherIds ]);
            }
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }

            $publishersQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$PUBLISHERS_TABLE_NAME,
                                                                    array_merge(Publisher::getDatabaseFieldNames(),
                                                                                [ KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".id" ]),
                                                                    $queryParams);

            if ($publishersQueryResult !== false) {
                $publishers = [];

                foreach ($publishersQueryResult as $publisherRow) {
                    $publisher = Publisher::getObjectInstance($publisherRow);
                    $publishers[] = $publisher;
                }

                return $publishers;
            }

            return null;
        }

        /**
         * @return bool|int
         */
        public function getPublishersCount() {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$PUBLISHERS_TABLE_NAME);
        }

        /**
         * @param $publisherId
         * @return Publisher|null
         */
        public function getPublisher($publisherId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$PUBLISHERS_TABLE_NAME,
                                                       array_merge(Publisher::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".id" ]),
                                                       [ "id" => $publisherId ]);
            if ($queryResult !== false) {
                $publisher = Publisher::getObjectInstance($queryResult);

                return $publisher;

            }

            return null;
        }

        /**
         * @param $publisherName
         * @return Publisher|null
         */
        public function getPublisherByName($publisherName) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$PUBLISHERS_TABLE_NAME,
                                                       array_merge(Publisher::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".id" ]),
                                                       [ "name" => $publisherName ]);
            if ($queryResult !== false) {
                $publisher = Publisher::getObjectInstance($queryResult);

                return $publisher;

            }

            return null;
        }

        /**
         * @param Publisher $publisher
         * @return bool|int
         */
        public function savePublisher(Publisher $publisher) {
            $data = $publisher->getDatabaseArray();
            if ($publisher->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$PUBLISHERS_TABLE_NAME,
                                                      $data);
            }
            else {
                return $this->kaaSoftDatabase->update(KAASoftDatabase::$PUBLISHERS_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $publisher->getId() ]);
            }
        }

        /**
         * @param $publishers
         * @return array|bool
         */
        public function savePublishers($publishers) {
            $data = [];
            foreach ($publishers as $publisher) {
                if ($publisher instanceof Publisher) {
                    $data[] = $publisher->getDatabaseArray();
                }
            }

            return $this->kaaSoftDatabase->insert(KAASoftDatabase::$PUBLISHERS_TABLE_NAME,
                                                  $data);
        }

        /**
         * @param $publisherId
         * @return bool
         */
        public function deletePublisher($publisherId) {
            $publisher = $this->getPublisher($publisherId);
            if ($publisher !== null) {
                return $this->kaaSoftDatabase->delete(KAASoftDatabase::$PUBLISHERS_TABLE_NAME,
                                                      [ "id" => $publisherId ]);
            }

            return false;
        }

        /**+
         * @param $publisherId
         * @return bool
         */
        public function isPublisherExist($publisherId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$PUBLISHERS_TABLE_NAME,
                                               [ "id" => $publisherId ]);
        }


    }