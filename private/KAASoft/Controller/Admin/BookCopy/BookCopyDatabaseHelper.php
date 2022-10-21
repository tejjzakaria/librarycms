<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\BookCopy;

    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Database\Entity\General\BookCopy;
    use KAASoft\Database\Entity\General\Issue;
    use KAASoft\Database\KAASoftDatabase;

    /**
     * Class BookCopyDatabaseHelper
     * @package KAASoft\Controller\Admin\Post
     */
    class BookCopyDatabaseHelper extends DatabaseHelper {
        /**
         * @param $bookSN
         * @return bool
         */
        public function isBookCopyExists($bookSN) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$BOOK_COPIES_TABLE_NAME,
                                               [ "bookSN" => $bookSN ]);
        }

        /**
         * @param BookCopy $bookCopy
         * @param bool     $isUpdateIssueStatus
         * @return bool $userId
         */
        public function saveBookCopy(BookCopy $bookCopy, $isUpdateIssueStatus = true) {
            $data = $bookCopy->getDatabaseArray();
            if ($bookCopy->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$BOOK_COPIES_TABLE_NAME,
                                                      $data);
            }

            else {
                if (!$isUpdateIssueStatus) {
                    unset( $data["issueStatus"] );
                }

                return $this->kaaSoftDatabase->update(KAASoftDatabase::$BOOK_COPIES_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $bookCopy->getId() ]);
            }
        }

        /**
         * @param $bookCopyId
         * @return bool|int
         */
        public function deleteBookCopy($bookCopyId) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$BOOK_COPIES_TABLE_NAME,
                                                  [ "id" => $bookCopyId ]);

        }

        /**
         * @param $bookCopyId
         * @return BookCopy|null
         */
        public function getBookCopy($bookCopyId) {

            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$BOOK_COPIES_TABLE_NAME,
                                                       array_merge(BookCopy::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$BOOK_COPIES_TABLE_NAME . ".id" ]),
                                                       [ "id" => $bookCopyId ]);
            if ($queryResult !== false) {
                $bookCopy = BookCopy::getObjectInstance($queryResult);

                return $bookCopy;
            }

            return null;
        }

        /**
         * @param $bookId
         * @return array|null
         */
        public function getBookCopies($bookId) {
            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOK_COPIES_TABLE_NAME,
                                                          array_merge(BookCopy::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$BOOK_COPIES_TABLE_NAME . ".id" ]),
                                                          [ KAASoftDatabase::$BOOK_COPIES_TABLE_NAME . ".bookId" => $bookId,
                                                            "ORDER"                                              => [ KAASoftDatabase::$BOOK_COPIES_TABLE_NAME . ".bookId" => "ASC",
                                                                                                                      KAASoftDatabase::$BOOK_COPIES_TABLE_NAME . ".id"     => "ASC" ] ]);
            if ($queryResult !== false) {
                $bookCopies = [];
                foreach ($queryResult as $row) {
                    $bookCopy = BookCopy::getObjectInstance($row);
                    $bookCopies[] = $bookCopy;
                }

                return $bookCopies;
            }

            return null;
        }

        /**
         * @param $bookCopyIds
         * @return array|null
         */
        public function getBookCopiesByIds($bookCopyIds) {
            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOK_COPIES_TABLE_NAME,
                                                          [ "[><]" . KAASoftDatabase::$BOOKS_TABLE_NAME => [ "bookId" => "id" ] ],
                                                          array_merge(BookCopy::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$BOOK_COPIES_TABLE_NAME . ".id",
                                                                        KAASoftDatabase::$BOOKS_TABLE_NAME . ".ISBN13",
                                                                        KAASoftDatabase::$BOOKS_TABLE_NAME . ".ISBN10",
                                                                        KAASoftDatabase::$BOOKS_TABLE_NAME . ".title" ]),
                                                          [ KAASoftDatabase::$BOOK_COPIES_TABLE_NAME . ".id" => $bookCopyIds ]);
            if ($queryResult !== false) {
                $bookCopies = [];
                foreach ($queryResult as $row) {
                    $bookCopy = BookCopy::getObjectInstance($row);

                    $book = Book::getObjectInstance($row);
                    $book->setId($bookCopy->getBookId());
                    $bookCopy->setBook($book);

                    $bookCopies[] = $bookCopy;
                }

                return $bookCopies;
            }

            return null;
        }

        /**
         * @param $bookId
         * @return BookCopy|null
         */
        public function getAvailableBookCopy($bookId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$BOOK_COPIES_TABLE_NAME,
                                                       array_merge(BookCopy::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$BOOK_COPIES_TABLE_NAME . ".id" ]),
                                                       [ "AND"   => [ KAASoftDatabase::$BOOK_COPIES_TABLE_NAME . ".bookId"      => $bookId,
                                                                      KAASoftDatabase::$BOOK_COPIES_TABLE_NAME . ".issueStatus" => Issue::ISSUE_STATUS_AVAILABLE ],
                                                         "ORDER" => [ KAASoftDatabase::$BOOK_COPIES_TABLE_NAME . ".bookId" => "ASC",
                                                                      KAASoftDatabase::$BOOK_COPIES_TABLE_NAME . ".id"     => "ASC" ] ]);
            if ($queryResult !== false) {
                $bookCopy = BookCopy::getObjectInstance($queryResult);

                return $bookCopy;
            }

            return null;
        }

        /**
         * @param $bookCopyId
         * @param $issueStatus
         * @return bool|int
         */
        public function setBookCopyIssueStatus($bookCopyId, $issueStatus) {
            $data = [ "issueStatus" => $issueStatus ];

            return $this->kaaSoftDatabase->update(KAASoftDatabase::$BOOK_COPIES_TABLE_NAME,
                                                  $data,
                                                  [ "id" => $bookCopyId ]);
        }
    }