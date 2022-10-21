<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-06-01
     */


    namespace KAASoft\Controller\Admin\BookField;


    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\General\DatabaseField;
    use KAASoft\Database\Entity\General\ListValue;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class BookFieldDatabaseHelper
     * @package KAASoft\Controller\Admin\BookField
     */
    class BookFieldDatabaseHelper extends DatabaseHelper {
        /**
         * @param $columnName
         * @return bool
         */
        public function isBookColumnExist($columnName) {
            $bookFields = $this->kaaSoftDatabase->getTableColumns(KAASoftDatabase::$BOOKS_TABLE_NAME);

            return in_array($columnName,
                            $bookFields);
        }

        /**
         * @param DatabaseField $databaseField
         * @return array|bool|int
         */
        public function saveBookField(DatabaseField $databaseField) {
            $data = $databaseField->getDatabaseArray();
            if ($databaseField->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$BOOK_FIELDS_TABLE_NAME,
                                                      $data);
            }
            else {
                return $this->kaaSoftDatabase->update(KAASoftDatabase::$BOOK_FIELDS_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $databaseField->getId() ]);
            }
        }

        /**
         * @param $ids
         * @return bool|int
         */
        public function setBookFieldFilterable($ids) {
            return $this->kaaSoftDatabase->update(KAASoftDatabase::$BOOK_FIELDS_TABLE_NAME,
                                                  [ "isFilterable" => true ],
                                                  [ "id" => $ids ]);
        }

        /**
         * @param $ids
         * @return bool|int
         */
        public function setBookFieldNonFilterable($ids) {
            if (!ValidationHelper::isArrayEmpty($ids)) {
                return $this->kaaSoftDatabase->update(KAASoftDatabase::$BOOK_FIELDS_TABLE_NAME,
                                                      [ "isFilterable" => false ],
                                                      [ "id" => $ids ]);
            }

            return true;
        }

        /**
         * @param $fieldId
         * @param $listValues
         * @return array|bool
         */
        public function saveListValues($fieldId, $listValues) {
            $data = [];

            foreach ($listValues as $listValue) {
                $data[] = [ "fieldId" => $fieldId,
                            "value"   => $listValue ];
            }

            return $this->kaaSoftDatabase->insert(KAASoftDatabase::$LIST_VALUES_TABLE_NAME,
                                                  $data);
        }

        /**
         * @param $fieldId
         * @return bool|int
         */
        public function deleteListValues($fieldId) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$LIST_VALUES_TABLE_NAME,
                                                  [ "fieldId" => $fieldId ]);
        }

        /**
         * @param $bookFieldId
         * @return DatabaseField|null
         */
        public function getBookField($bookFieldId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$BOOK_FIELDS_TABLE_NAME,
                                                       array_merge(DatabaseField::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$BOOK_FIELDS_TABLE_NAME . ".id" ]),
                                                       [ "id" => $bookFieldId ]);
            if ($queryResult !== false) {
                $databaseField = DatabaseField::getObjectInstance($queryResult);

                $databaseField->setListValues($this->getListValues($bookFieldId));

                return $databaseField;

            }

            return null;
        }

        /**
         * @param $fieldId
         * @return array|null
         */
        public function getListValues($fieldId) {
            $queryParams = [ "fieldId" => $fieldId ];

            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$LIST_VALUES_TABLE_NAME,
                                                          array_merge(ListValue::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$LIST_VALUES_TABLE_NAME . ".id" ]),
                                                          $queryParams);

            if ($queryResult !== false) {
                $listValues = [];

                foreach ($queryResult as $valueRow) {
                    $listValue = ListValue::getObjectInstance($valueRow);
                    $listValues[] = $listValue;
                }

                return $listValues;
            }

            return null;
        }

        /**
         * @param $bookFieldId
         * @return bool
         */
        public function isBookFieldExist($bookFieldId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$BOOK_FIELDS_TABLE_NAME,
                                               [ "id" => $bookFieldId ]);
        }

        /**
         * @param $bookFieldId
         * @return bool|int
         */
        public function deleteBookField($bookFieldId) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$BOOK_FIELDS_TABLE_NAME,
                                                  [ "id" => $bookFieldId ]);
        }

        /**
         * @return bool|int
         */
        public function getBookFieldsCount() {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$BOOK_FIELDS_TABLE_NAME);
        }

        /**
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getBookFields($offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = null;
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }

            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOK_FIELDS_TABLE_NAME,
                                                          array_merge(DatabaseField::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$BOOK_FIELDS_TABLE_NAME . ".id" ]),
                                                          $queryParams);

            if ($queryResult !== false) {
                $bookFields = [];

                foreach ($queryResult as $bookFieldRow) {
                    $databaseField = DatabaseField::getObjectInstance($bookFieldRow);
                    $bookFields[] = $databaseField;
                }

                return $bookFields;
            }

            return null;
        }
    }