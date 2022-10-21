<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Request;

    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Database\Entity\General\Issue;
    use KAASoft\Database\Entity\General\Publisher;
    use KAASoft\Database\Entity\General\Request;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Database\Entity\Util\Image;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\Helper;

    /**
     * Class RequestDatabaseHelper
     * @package KAASoft\Controller\Admin\Request
     */
    class RequestDatabaseHelper extends DatabaseHelper {


        /**
         * @param bool $isNeedBookCopies
         * @param null $userId
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getRequests($userId = null, $isNeedBookCopies = false, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = $userId === null ? [] : [ KAASoftDatabase::$USERS_TABLE_NAME . ".id" => $userId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => ( $sortColumn === null ? [ KAASoftDatabase::$REQUESTS_TABLE_NAME . '.creationDate' => 'DESC' ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }


            $requestsQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$REQUESTS_TABLE_NAME,
                                                                  [ "[><]" . KAASoftDatabase::$USERS_TABLE_NAME     => [ "userId" => "id" ],
                                                                    "[><]" . KAASoftDatabase::$BOOKS_TABLE_NAME     => [ "bookId" => "id" ],
                                                                    "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME     => [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".coverId" => "id" ],
                                                                    "[>]" . KAASoftDatabase::$ISSUES_TABLE_NAME     => [ "id" => "requestId" ],
                                                                    "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME => [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".publisherId" => "id" ], ],
                                                                  array_merge(Issue::getDatabaseFieldNames(),
                                                                              User::getDatabaseFieldNames(),
                                                                              Book::getDatabaseFieldNames(),
                                                                              Request::getDatabaseFieldNames(),
                                                                              [ KAASoftDatabase::$REQUESTS_TABLE_NAME . ".id",
                                                                                KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                                KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                                KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime",
                                                                                KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name(publisherName)",
                                                                                KAASoftDatabase::$REQUESTS_TABLE_NAME . ".notes(requestNotes)",
                                                                                KAASoftDatabase::$BOOKS_TABLE_NAME . ".notes(bookNotes)",
                                                                                KAASoftDatabase::$ISSUES_TABLE_NAME . ".id(issueId)" ]),
                                                                  $queryParams);

            if ($requestsQueryResult !== false) {
                $requests = [];

                $bookIds = [];
                $books = [];
                foreach ($requestsQueryResult as $requestRow) {
                    $request = Request::getObjectInstance($requestRow);
                    $request->setNotes($requestRow["requestNotes"]);

                    $request->setCreationDate(Helper::reformatDateString($request->getCreationDate(),
                                                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_FORMAT)));

                    $user = User::getObjectInstance($requestRow);
                    $user->setId($request->getUserId());
                    $request->setUser($user);

                    $book = Book::getObjectInstance($requestRow);
                    $book->setId($request->getBookId());
                    $book->setNotes($requestRow["bookNotes"]);

                    if (isset( $requestRow["issueId"] )) {
                        $issue = Issue::getObjectInstance($requestRow);
                        $issue->setId($requestRow["issueId"]);
                        $request->setIssue($issue);
                    }

                    if (isset( $requestRow["coverId"] )) {
                        $image = Image::getObjectInstance($requestRow);
                        if (file_exists($image->getAbsolutePath())) {
                            $image->setId($requestRow["coverId"]);
                            $image->setTitle($requestRow["imageTitle"]);
                            $book->setCover($image);
                        }
                    }
                    if (isset( $requestRow["publisherId"] )) {
                        $publisher = Publisher::getObjectInstance($requestRow);
                        $publisher->setId($requestRow["publisherId"]);
                        $publisher->setName($requestRow["publisherName"]);
                        $book->setPublisher($publisher);
                    }
                    $request->setBook($book);
                    $bookIds[] = $book->getId();
                    $books [] = $book;

                    $requests[] = $request;
                }
                if ($isNeedBookCopies) {
                    $bookDatabaseHelper = new BookDatabaseHelper($this->action);
                    $bookCopies = $bookDatabaseHelper->getBookCopies($bookIds);

                    if (isset( $bookCopies )) {
                        foreach ($books as $book) {
                            $book->setBookCopies($bookCopies[$book->getId()]);
                        }
                    }
                }

                return $requests;
            }

            return null;
        }

        /**
         * @return bool|int
         */
        public function getRequestsCount() {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$REQUESTS_TABLE_NAME);
        }

        /**
         * @param $userId
         * @return bool|int
         */
        public function getUserRequestsCount($userId) {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$REQUESTS_TABLE_NAME,
                                                 [ "userId" => $userId ]);
        }


        /**
         * @param $requestId
         * @return Request|null
         */
        public function getRequest($requestId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$REQUESTS_TABLE_NAME,
                                                       [ "[><]" . KAASoftDatabase::$USERS_TABLE_NAME => [ "userId" => "id" ],
                                                         "[><]" . KAASoftDatabase::$BOOKS_TABLE_NAME => [ "bookId" => "id" ],
                                                         "[>]" . KAASoftDatabase::$ISSUES_TABLE_NAME => [ "id" => "requestId" ] ],
                                                       array_merge(Issue::getDatabaseFieldNames(),
                                                                   User::getDatabaseFieldNames(),
                                                                   Book::getDatabaseFieldNames(),
                                                                   Request::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$REQUESTS_TABLE_NAME . ".id",
                                                                     KAASoftDatabase::$ISSUES_TABLE_NAME . ".id(issueId)" ]),
                                                       [ KAASoftDatabase::$REQUESTS_TABLE_NAME . ".id" => $requestId ]);
            if ($queryResult !== false) {
                $request = Request::getObjectInstance($queryResult);

                $user = User::getObjectInstance($queryResult);
                $user->setId($request->getUserId());

                $request->setUser($user);


                $book = Book::getObjectInstance($queryResult);
                $book->setId($request->getBookId());

                $request->setBook($book);

                if (isset( $queryResult["issueId"] )) {
                    $issue = Issue::getObjectInstance($queryResult);
                    $issue->setId($queryResult["issueId"]);
                    $request->setIssue($issue);
                }

                return $request;

            }

            return null;
        }

        /**
         * @param Request $request
         * @return bool|int
         */
        public function saveRequest(Request $request) {
            $data = $request->getDatabaseArray();
            if ($request->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$REQUESTS_TABLE_NAME,
                                                      $data);
            }
            else {
                return $this->kaaSoftDatabase->update(KAASoftDatabase::$REQUESTS_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $request->getId() ]);
            }
        }

        /**+
         * @param $requestId
         * @return bool
         */
        public function isRequestExist($requestId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$REQUESTS_TABLE_NAME,
                                               [ "id" => $requestId ]);
        }

        /**
         * @param $requestId
         * @return bool
         */
        public function deleteRequest($requestId) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$REQUESTS_TABLE_NAME,
                                                  [ "id" => $requestId ]);
        }

        /**
         * @param $requestId
         * @param $status
         * @return bool|int
         */
        public function updateRequestStatus($requestId, $status) {
            return $this->kaaSoftDatabase->update(KAASoftDatabase::$REQUESTS_TABLE_NAME,
                                                  [ "status" => $status ],
                                                  [ "id" => $requestId ]);
        }
    }