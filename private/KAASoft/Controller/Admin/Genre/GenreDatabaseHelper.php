<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Genre;

    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\General\Genre;
    use KAASoft\Database\KAASoftDatabase;

    /**
     * Class GenreDatabaseHelper
     * @package KAASoft\Controller\Admin\Genre
     */
    class GenreDatabaseHelper extends DatabaseHelper {
        /**
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getGenres($offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = null;
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }

            $genresQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$GENRES_TABLE_NAME,
                                                                array_merge(Genre::getDatabaseFieldNames(),
                                                                            [ KAASoftDatabase::$GENRES_TABLE_NAME . ".id" ]),
                                                                $queryParams);

            if ($genresQueryResult !== false) {
                $genres = [];

                foreach ($genresQueryResult as $genreRow) {
                    $genre = Genre::getObjectInstance($genreRow);
                    $genres[] = $genre;
                }

                return $genres;
            }

            return null;
        }

        /**
         * @return bool|int
         */
        public function getGenresCount() {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$GENRES_TABLE_NAME);
        }

        /**
         * @param $genreId
         * @return Genre|null
         */
        public function getGenre($genreId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$GENRES_TABLE_NAME,
                                                       array_merge(Genre::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$GENRES_TABLE_NAME . ".id" ]),
                                                       [ "id" => $genreId ]);
            if ($queryResult !== false) {
                $genre = Genre::getObjectInstance($queryResult);

                return $genre;

            }

            return null;
        }

        /**
         * @param array $genres
         * @return bool|int
         */
        public function saveGenres($genres) {
            $data = [];
            foreach ($genres as $genre) {
                if ($genre instanceof Genre) {
                    $data[] = $genre->getDatabaseArray();
                }
            }

            return $this->kaaSoftDatabase->insert(KAASoftDatabase::$GENRES_TABLE_NAME,
                                                  $data);
        }

        /**
         * @param $genre Genre
         * @return array|bool|int
         */
        public function saveGenre($genre) {
            $data = $genre->getDatabaseArray();
            if ($genre->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$GENRES_TABLE_NAME,
                                                      $data);
            }
            else {
                return $this->kaaSoftDatabase->update(KAASoftDatabase::$GENRES_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $genre->getId() ]);
            }
        }

        /**
         * @param $genreId
         * @return bool
         */
        public function deleteGenre($genreId) {
            $genre = $this->getGenre($genreId);
            if ($genre !== null) {
                return $this->kaaSoftDatabase->delete(KAASoftDatabase::$GENRES_TABLE_NAME,
                                                      [ "id" => $genreId ]);
            }

            return false;
        }

        /**+
         * @param $genreId
         * @return bool
         */
        public function isGenreExist($genreId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$GENRES_TABLE_NAME,
                                               [ "id" => $genreId ]);
        }

    }