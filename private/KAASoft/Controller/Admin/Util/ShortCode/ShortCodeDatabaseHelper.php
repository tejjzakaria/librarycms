<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util\ShortCode;

    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\Util\DynamicShortCode;
    use KAASoft\Database\Entity\Util\StaticShortCode;
    use KAASoft\Database\KAASoftDatabase;

    /**
     * Class ShortCodeDatabaseHelper
     * @package KAASoft\Controller\Admin\Util\ShortCode
     */
    class ShortCodeDatabaseHelper extends DatabaseHelper {
        /**
         * @param int    $offset
         * @param int    $perPage
         * @param string $sortBy
         * @param string $sortOrder
         * @return array|null
         */
        public function getStaticShortCodes($offset = null, $perPage = null, $sortBy = "StaticShortCodes.code", $sortOrder = "ASC") {
            $queryParams = [];

            if ($sortBy != null && $sortOrder != null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => [ $sortBy => $sortOrder ] ]);
            }
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }
            $result = $this->kaaSoftDatabase->select(KAASoftDatabase::$STATIC_SHORT_CODES_TABLE_NAME,
                                                     array_merge(StaticShortCode::getDatabaseFieldNames(),
                                                                 [ KAASoftDatabase::$STATIC_SHORT_CODES_TABLE_NAME . ".id" ]),
                                                     $queryParams);

            if ($result !== false) {
                $staticShortCodes = [];
                foreach ($result as $staticShortCodeRow) {
                    $staticShortCode = StaticShortCode::getObjectInstance($staticShortCodeRow);

                    $staticShortCodes[] = $staticShortCode;
                }

                return $staticShortCodes;
            }

            return null;
        }

        /**
         * @param int    $offset
         * @param int    $perPage
         * @param string $sortBy
         * @param string $sortOrder
         * @return array|null
         */
        public function getDynamicShortCodes($offset = null, $perPage = null, $sortBy = "DynamicShortCodes.code", $sortOrder = "ASC") {
            $queryParams = [];

            if ($sortBy != null && $sortOrder != null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => [ $sortBy => $sortOrder ] ]);
            }
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }
            $result = $this->kaaSoftDatabase->select(KAASoftDatabase::$DYNAMIC_SHORT_CODES_TABLE_NAME,
                                                     array_merge(DynamicShortCode::getDatabaseFieldNames(),
                                                                 [ KAASoftDatabase::$DYNAMIC_SHORT_CODES_TABLE_NAME . ".id" ]),
                                                     $queryParams);

            if ($result !== false) {
                $staticShortCodes = [];
                foreach ($result as $dynamicShortCodeRow) {
                    $staticShortCode = DynamicShortCode::getObjectInstance($dynamicShortCodeRow);

                    $staticShortCodes[] = $staticShortCode;
                }

                return $staticShortCodes;
            }

            return null;
        }

        /**
         * @param array $staticShortCodes
         * @return array|bool|int
         */
        public function saveStaticShortCodes(array $staticShortCodes) {

            $data = [];
            foreach ($staticShortCodes as $staticShortCode) {
                if ($staticShortCode instanceof StaticShortCode) {
                    $data[] = $staticShortCode->getDatabaseArray();
                }
            }

            return $this->kaaSoftDatabase->insert(KAASoftDatabase::$STATIC_SHORT_CODES_TABLE_NAME,
                                                  $data);
        }

        public function saveDynamicShortCodes(array $dynamicShortCodes) {

            $data = [];
            foreach ($dynamicShortCodes as $dynamicShortCode) {
                if ($dynamicShortCode instanceof DynamicShortCode) {
                    $data[] = $dynamicShortCode->getDatabaseArray();
                }
            }

            return $this->kaaSoftDatabase->insert(KAASoftDatabase::$DYNAMIC_SHORT_CODES_TABLE_NAME,
                                                  $data);
        }

        /**
         * @return bool|int
         */
        public function getStaticShortCodesCount() {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$STATIC_SHORT_CODES_TABLE_NAME);
        }

        /**
         * @return bool|int
         */
        public function deleteAllStaticCodes() {
            return $this->kaaSoftDatabase->deleteAllTableContent(KAASoftDatabase::$STATIC_SHORT_CODES_TABLE_NAME);
        }

        public function deleteAllDynamicCodes() {
            return $this->kaaSoftDatabase->deleteAllTableContent(KAASoftDatabase::$DYNAMIC_SHORT_CODES_TABLE_NAME);
        }

    }