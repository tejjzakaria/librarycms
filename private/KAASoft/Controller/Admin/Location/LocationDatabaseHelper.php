<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Location;

    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\General\Location;
    use KAASoft\Database\Entity\General\Store;
    use KAASoft\Database\KAASoftDatabase;

    /**
     * Class LocationDatabaseHelper
     * @package KAASoft\Controller\Admin\Location
     */
    class LocationDatabaseHelper extends DatabaseHelper {
        /**
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getLocations($offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = null;
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }

            $locationsQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$LOCATIONS_TABLE_NAME,
                                                                   [ "[><]" . KAASoftDatabase::$STORES_TABLE_NAME => [ "storeId" => "id" ] ],
                                                                   array_merge(Location::getDatabaseFieldNames(),
                                                                               [ KAASoftDatabase::$LOCATIONS_TABLE_NAME . ".id",
                                                                                 KAASoftDatabase::$STORES_TABLE_NAME . ".name(storeName)" ]),
                                                                   $queryParams);

            if ($locationsQueryResult !== false) {
                $locations = [];

                foreach ($locationsQueryResult as $locationRow) {
                    $location = Location::getObjectInstance($locationRow);

                    $store = new Store();
                    $store->setId($locationRow["storeId"]);
                    $store->setName($locationRow["storeName"]);
                    $location->setStore($store);

                    $locations[] = $location;
                }

                return $locations;
            }

            return null;
        }

        /**
         * @return bool|int
         */
        public function getLocationsCount() {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$LOCATIONS_TABLE_NAME);
        }

        /**
         * @param $locationId
         * @return Location|null
         */
        public function getLocation($locationId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$LOCATIONS_TABLE_NAME,
                                                       [ "[><]" . KAASoftDatabase::$STORES_TABLE_NAME => [ "storeId" => "id" ] ],
                                                       array_merge(Location::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$LOCATIONS_TABLE_NAME . ".id",
                                                                     KAASoftDatabase::$STORES_TABLE_NAME . ".name(storeName)" ]),
                                                       [ KAASoftDatabase::$LOCATIONS_TABLE_NAME . ".id" => $locationId ]);
            if ($queryResult !== false) {
                $location = Location::getObjectInstance($queryResult);

                $store = new Store();
                $store->setId($queryResult["storeId"]);
                $store->setName($queryResult["storeName"]);
                $location->setStore($store);

                return $location;

            }

            return null;
        }

        /**
         * @param array $locations
         * @return bool|int
         */
        public function saveLocations($locations) {
            $data = [];
            foreach ($locations as $location) {
                if ($location instanceof Location) {
                    $data[] = $location->getDatabaseArray();
                }
            }

            return $this->kaaSoftDatabase->insert(KAASoftDatabase::$LOCATIONS_TABLE_NAME,
                                                  $data);
        }

        /**
         * @param $location Location
         * @return array|bool|int
         */
        public function saveLocation($location) {
            $data = $location->getDatabaseArray();
            if ($location->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$LOCATIONS_TABLE_NAME,
                                                      $data);
            }
            else {
                return $this->kaaSoftDatabase->update(KAASoftDatabase::$LOCATIONS_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $location->getId() ]);
            }
        }

        /**
         * @param $locationId
         * @return bool
         */
        public function deleteLocation($locationId) {
            $location = $this->getLocation($locationId);
            if ($location !== null) {
                return $this->kaaSoftDatabase->delete(KAASoftDatabase::$LOCATIONS_TABLE_NAME,
                                                      [ "id" => $locationId ]);
            }

            return false;
        }

        /**+
         * @param $locationId
         * @return bool
         */
        public function isLocationExist($locationId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$LOCATIONS_TABLE_NAME,
                                               [ "id" => $locationId ]);
        }

        /**
         * @param      $searchText
         * @param null $storeIds
         * @param null $limit
         * @return array
         */
        public function searchLocations($searchText, $storeIds = null, $limit = null) {
            $result = [];
            $searchCondition = [];
            $limitCondition = [];
            $storeCondition = [];
            if ($limit !== null) {
                $limitCondition = [ "LIMIT" => $limit ];
            }

            $keywords = preg_split("/[\s,]+/",
                                   $searchText);
            $counter = 0;
            foreach ($keywords as $keyword) {
                $searchCondition = array_merge($searchCondition,
                                               [ "OR " . $counter => [ KAASoftDatabase::$LOCATIONS_TABLE_NAME . ".name[~]" => $keyword ] ]);
                $counter++;
            }

            if ($storeIds !== null and count($storeIds) > 0) {
                $storeCondition = [ KAASoftDatabase::$LOCATIONS_TABLE_NAME . ".storeId" => $storeIds ];
            }

            if (count($storeCondition) > 0) {
                $globalCondition  ["AND"] = array_merge($searchCondition,
                                                        $storeCondition);
            }
            else {
                $globalCondition  ["AND"] = $searchCondition;
            }

            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$LOCATIONS_TABLE_NAME,
                                                          [ "[><]" . KAASoftDatabase::$STORES_TABLE_NAME => [ "storeId" => "id" ] ],
                                                          array_merge(Location::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$LOCATIONS_TABLE_NAME . ".id",
                                                                        KAASoftDatabase::$STORES_TABLE_NAME . ".name(storeName)" ]),
                                                          array_merge($globalCondition,
                                                                      $limitCondition));

            if ($queryResult !== false) {
                foreach ($queryResult as $row) {
                    $location = Location::getObjectInstance($row);

                    $store = new Store();
                    $store->setId($row["storeId"]);
                    $store->setName($row["storeName"]);
                    $location->setStore($store);

                    $result[] = $location;
                }
            }

            return $result;
        }
    }