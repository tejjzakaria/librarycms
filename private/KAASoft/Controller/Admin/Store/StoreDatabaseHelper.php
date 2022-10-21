<?php
    /**
 * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
 */

    namespace KAASoft\Controller\Admin\Store;

    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\General\Store;
    use KAASoft\Database\KAASoftDatabase;

    /**
     * Class StoreDatabaseHelper
     * @package KAASoft\Controller\Admin\Store
     */
    class StoreDatabaseHelper extends DatabaseHelper {
        /**
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getStores($offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = null;
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }

            $storesQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$STORES_TABLE_NAME,
                                                                array_merge(Store::getDatabaseFieldNames(),
                                                                            [ KAASoftDatabase::$STORES_TABLE_NAME . ".id" ]),
                                                                $queryParams);

            if ($storesQueryResult !== false) {
                $stores = [];

                foreach ($storesQueryResult as $storeRow) {
                    $store = Store::getObjectInstance($storeRow);
                    $stores[] = $store;
                }

                return $stores;
            }

            return null;
        }

        /**
         * @return bool|int
         */
        public function getStoresCount() {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$STORES_TABLE_NAME);
        }

        /**
         * @param $storeId
         * @return Store|null
         */
        public function getStore($storeId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$STORES_TABLE_NAME,
                                                       array_merge(Store::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$STORES_TABLE_NAME . ".id" ]),
                                                       [ "id" => $storeId ]);
            if ($queryResult !== false) {
                $store = Store::getObjectInstance($queryResult);

                return $store;

            }

            return null;
        }

        /**
         * @param array $stores
         * @return bool|int
         */
        public function saveStores($stores) {
            $data = [];
            foreach ($stores as $store) {
                if ($store instanceof Store) {
                    $data[] = $store->getDatabaseArray();
                }
            }

            return $this->kaaSoftDatabase->insert(KAASoftDatabase::$STORES_TABLE_NAME,
                                                  $data);
        }

        /**
         * @param $store Store
         * @return array|bool|int
         */
        public function saveStore($store) {
            $data = $store->getDatabaseArray();
            if ($store->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$STORES_TABLE_NAME,
                                                      $data);
            }
            else {
                return $this->kaaSoftDatabase->update(KAASoftDatabase::$STORES_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $store->getId() ]);
            }
        }

        /**
         * @param $storeId
         * @return bool
         */
        public function deleteStore($storeId) {
            $store = $this->getStore($storeId);
            if ($store !== null) {
                return $this->kaaSoftDatabase->delete(KAASoftDatabase::$STORES_TABLE_NAME,
                                                      [ "id" => $storeId ]);
            }

            return false;
        }

        /**+
         * @param $storeId
         * @return bool
         */
        public function isStoreExist($storeId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$STORES_TABLE_NAME,
                                               [ "id" => $storeId ]);
        }

        /**
         * @param      $searchText
         * @param null $limit
         * @return array
         */
        public function searchStores($searchText, $limit = null) {
            $result = [];
            $searchCondition = [];
            $limitCondition = [];
            if ($limit !== null) {
                $limitCondition = [ "LIMIT" => $limit ];
            }

            $keywords = preg_split("/[\s,]+/",
                                   $searchText);
            $counter = 0;
            foreach ($keywords as $keyword) {
                $searchCondition = array_merge($searchCondition,
                                               [ "OR " . $counter => [ KAASoftDatabase::$STORES_TABLE_NAME . ".name[~]" => $keyword ] ]);
                $counter++;
            }


            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$STORES_TABLE_NAME,
                                                          array_merge(Store::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$STORES_TABLE_NAME . ".id" ]),
                                                          array_merge([ "AND" => $searchCondition ],
                                                                      $limitCondition));

            if ($queryResult !== false) {
                foreach ($queryResult as $row) {
                    $store = Store::getObjectInstance($row);
                    $result[] = $store;
                }
            }

            return $result;
        }
    }