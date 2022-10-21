<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\ElectronicBook;

    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\General\ElectronicBook;
    use KAASoft\Database\KAASoftDatabase;

    /**
     * Class BookDatabaseHelper
     * @package KAASoft\Controller\Admin\Book
     */
    class ElectronicBookDatabaseHelper extends DatabaseHelper {
        /**
         * @param $eBookId
         * @return ElectronicBook|null
         */
        public function getElectronicBook($eBookId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME,
                                                       array_merge(ElectronicBook::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".id" ]),
                                                       [ "id" => $eBookId ]);

            if ($queryResult !== false) {
                return ElectronicBook::getObjectInstance($queryResult);
            }

            return null;
        }

        /**
         * @param $eBook ElectronicBook
         * @return array|bool|int
         */
        public function saveElectronicBook($eBook) {
            $data = $eBook->getDatabaseArray();
            if ($eBook->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME,
                                                      $data);
            }

            else {
                return $this->kaaSoftDatabase->update(KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $eBook->getId() ]);
            }
        }

        public function deleteElectronicBook($electronicBookId) {
            $electronicBook = $this->getElectronicBook($electronicBookId);
            if ($electronicBook !== null) {
                return $this->kaaSoftDatabase->delete(KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME,
                                                      [ "id" => $electronicBookId ]);
            }

            return false;
        }
    }