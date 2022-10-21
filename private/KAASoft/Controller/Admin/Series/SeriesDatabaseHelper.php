<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Series;

    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\General\Series;
    use KAASoft\Database\KAASoftDatabase;

    /**
     * Class SeriesDatabaseHelper
     * @package KAASoft\Controller\Admin\Series
     */
    class SeriesDatabaseHelper extends DatabaseHelper {
        /**
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getSeriesList($offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = null;
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }

            $seriesQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$SERIES_TABLE_NAME,
                                                                array_merge(Series::getDatabaseFieldNames(),
                                                                            [ KAASoftDatabase::$SERIES_TABLE_NAME . ".id" ]),
                                                                $queryParams);

            if ($seriesQueryResult !== false) {
                $seriesList = [];

                foreach ($seriesQueryResult as $seriesRow) {
                    $series = Series::getObjectInstance($seriesRow);
                    $seriesList[] = $series;
                }

                return $seriesList;
            }

            return null;
        }

        /**
         * @return bool|int
         */
        public function getSeriesCount() {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$SERIES_TABLE_NAME);
        }

        /**
         * @param $seriesId
         * @return Series|null
         */
        public function getSeries($seriesId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$SERIES_TABLE_NAME,
                                                       array_merge(Series::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$SERIES_TABLE_NAME . ".id" ]),
                                                       [ "id" => $seriesId ]);
            if ($queryResult !== false) {
                $series = Series::getObjectInstance($queryResult);

                return $series;

            }

            return null;
        }

        /**
         * @param Series $series
         * @return bool|int
         */
        public function saveSeries(Series $series) {
            $data = $series->getDatabaseArray();
            if ($series->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$SERIES_TABLE_NAME,
                                                      $data);
            }
            else {
                return $this->kaaSoftDatabase->update(KAASoftDatabase::$SERIES_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $series->getId() ]);
            }
        }

        public function saveSeriesS($series) {
            $data = [];
            foreach ($series as $serie) {
                if ($serie instanceof Series) {
                    $data[] = $serie->getDatabaseArray();
                }
            }

            return $this->kaaSoftDatabase->insert(KAASoftDatabase::$SERIES_TABLE_NAME,
                                                  $data);
        }

        /**
         * @param $seriesId
         * @return bool
         */
        public function deleteSeries($seriesId) {
            $series = $this->getSeries($seriesId);
            if ($series !== null) {
                return $this->kaaSoftDatabase->delete(KAASoftDatabase::$SERIES_TABLE_NAME,
                                                      [ "id" => $seriesId ]);
            }

            return false;
        }

        /**+
         * @param $seriesId
         * @return bool
         */
        public function isSeriesExist($seriesId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$SERIES_TABLE_NAME,
                                               [ "id" => $seriesId ]);
        }

    }