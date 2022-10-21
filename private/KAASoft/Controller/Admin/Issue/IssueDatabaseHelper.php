<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Issue;

    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Database\Entity\General\BookCopy;
    use KAASoft\Database\Entity\General\Issue;
    use KAASoft\Database\Entity\General\IssueLog;
    use KAASoft\Database\Entity\General\Publisher;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Database\Entity\Util\Image;
    use KAASoft\Database\Entity\Util\Role;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\Helper;

    /**
     * Class IssueDatabaseHelper
     * @package KAASoft\Controller\Admin\Issue
     */
    class IssueDatabaseHelper extends DatabaseHelper {
        /**
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getIssues($offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = null;
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ KAASoftDatabase::$ISSUES_TABLE_NAME . '.issueDate' => 'DESC' ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }


            $issuesQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$ISSUES_TABLE_NAME,
                                                                [ "[><]" . KAASoftDatabase::$USERS_TABLE_NAME       => [ "userId" => "id" ],
                                                                  "[><]" . KAASoftDatabase::$BOOKS_TABLE_NAME       => [ "bookId" => "id" ],
                                                                  "[><]" . KAASoftDatabase::$BOOK_COPIES_TABLE_NAME => [ "bookCopyId" => "id" ],
                                                                  "[><]" . KAASoftDatabase::$ROLES_TABLE_NAME       => [ KAASoftDatabase::$USERS_TABLE_NAME . ".roleId" => "id" ],
                                                                  "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME       => [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".coverId" => "id" ],
                                                                  "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME   => [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".publisherId" => "id" ], ],
                                                                array_merge(Issue::getDatabaseFieldNames(),
                                                                            User::getDatabaseFieldNames(),
                                                                            Book::getDatabaseFieldNames(),
                                                                            Role::getDatabaseFieldNames(),
                                                                            BookCopy::getDatabaseFieldNames(),
                                                                            [ KAASoftDatabase::$ISSUES_TABLE_NAME . ".id",
                                                                              KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                              KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                              KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime",
                                                                              KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name(publisherName)" ]),
                                                                $queryParams);

            if ($issuesQueryResult !== false) {
                $issues = [];

                foreach ($issuesQueryResult as $issueRow) {
                    $issue = Issue::getObjectInstance($issueRow);

                    $user = User::getObjectInstance($issueRow);
                    $user->setId($issue->getUserId());

                    $role = Role::getObjectInstance($issueRow);
                    $role->setId($user->getRoleId());
                    $user->setRole($role);

                    $issue->setUser($user);

                    $book = Book::getObjectInstance($issueRow);
                    $book->setId($issue->getBookId());

                    if (isset( $issueRow["coverId"] )) {
                        $image = Image::getObjectInstance($issueRow);
                        if (file_exists($image->getAbsolutePath())) {
                            $image->setId($issueRow["coverId"]);
                            $image->setTitle($issueRow["imageTitle"]);
                            $book->setCover($image);
                        }
                    }
                    if (isset( $issueRow["publisherId"] )) {
                        $publisher = Publisher::getObjectInstance($issueRow);
                        $publisher->setId($issueRow["publisherId"]);
                        $publisher->setName($issueRow["publisherName"]);
                        $book->setPublisher($publisher);
                    }
                    $issue->setBook($book);

                    $bookCopy = BookCopy::getObjectInstance($issueRow);
                    $bookCopy->setId($issue->getBookCopyId());
                    $issue->setBookCopy($bookCopy);

                    $isExpired = Issue::isIssueExpired($issue);
                    if ($isExpired) {
                        $issue->setPenalty(Issue::calculatePenalty($issue->getUser(),
                                                                   $issue));
                        $issue->setIsExpired($isExpired);
                    }

                    $issue->setIssueDate(Helper::reformatDateString($issue->getIssueDate(),
                                                                    $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_FORMAT)));
                    $issue->setExpiryDate(Helper::reformatDateString($issue->getExpiryDate(),
                                                                     $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_FORMAT)));
                    $issue->setReturnDate(Helper::reformatDateString($issue->getReturnDate(),
                                                                     $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_FORMAT)));

                    $issues[] = $issue;
                }

                return $issues;
            }

            return null;
        }


        /**
         * @param $issueId
         * @return Issue|null
         */
        public function getIssue($issueId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$ISSUES_TABLE_NAME,
                                                       [ "[><]" . KAASoftDatabase::$USERS_TABLE_NAME       => [ "userId" => "id" ],
                                                         "[><]" . KAASoftDatabase::$ROLES_TABLE_NAME       => [ KAASoftDatabase::$USERS_TABLE_NAME . ".roleId" => "id" ],
                                                         "[><]" . KAASoftDatabase::$BOOKS_TABLE_NAME       => [ "bookId" => "id" ],
                                                         "[><]" . KAASoftDatabase::$BOOK_COPIES_TABLE_NAME => [ "bookCopyId" => "id" ], ],
                                                       array_merge(Book::getDatabaseFieldNames(),
                                                                   BookCopy::getDatabaseFieldNames(),
                                                                   Role::getDatabaseFieldNames(),
                                                                   User::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$ISSUES_TABLE_NAME . ".id" ],
                                                                   Issue::getDatabaseFieldNames()),
                                                       [ KAASoftDatabase::$ISSUES_TABLE_NAME . ".id" => $issueId ]);
            if ($queryResult !== false) {
                $issue = Issue::getObjectInstance($queryResult);

                $user = User::getObjectInstance($queryResult);
                $user->setId($issue->getUserId());

                $role = Role::getObjectInstance($queryResult);
                $role->setId($user->getRoleId());
                $user->setRole($role);
                $issue->setUser($user);

                $isExpired = Issue::isIssueExpired($issue);
                if ($isExpired) {
                    $issue->setPenalty(Issue::calculatePenalty($issue->getUser(),
                                                               $issue));
                    $issue->setIsExpired($isExpired);
                }

                $book = Book::getObjectInstance($queryResult);
                $book->setId($issue->getBookId());
                $issue->setBook($book);

                $bookCopy = BookCopy::getObjectInstance($queryResult);
                $bookCopy->setId($issue->getBookCopyId());
                $issue->setBookCopy($bookCopy);

                return $issue;

            }

            return null;
        }

        /**
         * @param Issue $issue
         * @return bool|int
         */
        public function saveIssue(Issue $issue) {
            $data = $issue->getDatabaseArray();
            if ($issue->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$ISSUES_TABLE_NAME,
                                                      $data);
            }
            else {
                return $this->kaaSoftDatabase->update(KAASoftDatabase::$ISSUES_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $issue->getId() ]);
            }
        }

        /**
         * @param $userId
         * @return bool|int
         */
        public function getUserIssuedBookCount($userId) {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$ISSUES_TABLE_NAME,
                                                 KAASoftDatabase::$ISSUES_TABLE_NAME . ".id",
                                                 [ "AND" => [ KAASoftDatabase::$ISSUES_TABLE_NAME . ".userId"     => $userId,
                                                              KAASoftDatabase::$ISSUES_TABLE_NAME . ".returnDate" => null,
                                                              KAASoftDatabase::$ISSUES_TABLE_NAME . ".isLost"     => false ] ]);
        }

        /**
         * @param $issueId
         * @return bool
         */
        public function deleteIssue($issueId) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$ISSUES_TABLE_NAME,
                                                  [ "id" => $issueId ]);
        }

        /**+
         * @param $issueId
         * @return bool
         */
        public function isIssueExist($issueId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$ISSUES_TABLE_NAME,
                                               [ "id" => $issueId ]);
        }

        /**
         * @param $issueId
         * @param $isLost
         * @return bool|int
         */
        public function updateIssueLostStatus($issueId, $isLost) {
            return $this->kaaSoftDatabase->update(KAASoftDatabase::$ISSUES_TABLE_NAME,
                                                  [ "isLost" => $isLost ],
                                                  [ "id" => $issueId ]);
        }

        /**
         * @param $issueId
         * @return bool|int
         */
        public function setIssueLogDeleted($issueId) {
            return $this->kaaSoftDatabase->update(KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME,
                                                  [ "isIssueDeleted" => true,
                                                    "updateDateTime" => Helper::getDateTimeString() ],
                                                  [ "issueId" => $issueId ]);
        }

        /**
         * @param $issueId
         * @param $returnDate
         * @param $penalty
         * @return bool|int
         */
        public function updateIssueReturnDate($issueId, $returnDate, $penalty) {
            return $this->kaaSoftDatabase->update(KAASoftDatabase::$ISSUES_TABLE_NAME,
                                                  [ "returnDate" => $returnDate,
                                                    "penalty"    => $penalty ],
                                                  [ "id" => $issueId ]);
        }

        /**
         * @param null|string $startingDate in Database DATE format
         * @return bool|int
         */
        public function getReturnBookCount($startingDate = null) {
            $condition = [ "returnDate[!]" => null ];
            if ($startingDate !== null) {
                $condition = [ "AND" => array_merge($condition,
                                                    [ "issueDate[>]" => $startingDate ]) ];
            }

            return $this->kaaSoftDatabase->count(KAASoftDatabase::$ISSUES_TABLE_NAME,
                                                 "id",
                                                 $condition);
        }

        /**
         * @param null|string $startingDate in Database DATE format
         * @return bool|int
         */
        public function getLostBookCount($startingDate = null) {
            $condition = [ "isLost" => true ];
            if ($startingDate !== null) {
                $condition = [ "AND" => array_merge($condition,
                                                    [ "issueDate[>]" => $startingDate ]) ];
            }

            return $this->kaaSoftDatabase->count(KAASoftDatabase::$ISSUES_TABLE_NAME,
                                                 "id",
                                                 $condition);
        }

        /**
         * @param null|string $startingDate in Database DATE format
         * @return bool|int
         */
        public function getIssuesCount($startingDate = null) {
            $condition = null;
            if ($startingDate !== null) {
                $condition = array_merge($condition,
                                         [ "issueDate[>]" => $startingDate ]);
            }

            return $this->kaaSoftDatabase->count(KAASoftDatabase::$ISSUES_TABLE_NAME,
                                                 "id",
                                                 $condition);
        }

        /**
         * @param $issueLogId
         * @return IssueLog
         */
        public function getIssueLog($issueLogId = null) {
            if ($issueLogId !== null) {
                $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME,
                                                           array_merge(IssueLog::getDatabaseFieldNames(),
                                                                       [ KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".id" ]),
                                                           [ KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".id" => $issueLogId ]);
                if ($queryResult !== false) {
                    $issueLog = IssueLog::getObjectInstance($queryResult);


                    return $issueLog;

                }
            }

            return new IssueLog();
        }

        /**
         * @param $requestId
         * @param $issueId
         * @return IssueLog
         */
        public function getIssueLogByRequestOrIssue($requestId, $issueId = null) {
            //$condition = [];
            if ($requestId === null and $issueId !== null) {
                $condition = [ KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".issueId" => $issueId ];
            }
            elseif ($requestId !== null and $issueId === null) {
                $condition = [ KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".requestId" => $requestId ];
            }
            elseif ($requestId !== null and $issueId !== null) {
                $condition = [ "AND" => [ KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".requestId" => $requestId,
                                          KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".issueId"   => $issueId ] ];
            }
            else {
                return new IssueLog();
            }

            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME,
                                                       array_merge(IssueLog::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".id" ]),
                                                       $condition);
            if ($queryResult !== false) {
                $issueLog = IssueLog::getObjectInstance($queryResult);


                return $issueLog;

            }

            return new IssueLog();
        }


        /**
         * @param IssueLog $issueLog
         * @return array|bool|int
         */
        public function saveIssueLog(IssueLog $issueLog) {
            $data = $issueLog->getDatabaseArray();
            if ($issueLog->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME,
                                                      $data);
            }
            else {
                return $this->kaaSoftDatabase->update(KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $issueLog->getId() ]);
            }
        }

        /**
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getIssueLogs($offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = null;
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . '.updateDateTime' => 'DESC' ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }


            $issueLogsQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME,
                                                                   array_merge(IssueLog::getDatabaseFieldNames(),
                                                                               [ KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".id" ]),
                                                                   $queryParams);

            if ($issueLogsQueryResult !== false) {
                $issueLogs = [];

                foreach ($issueLogsQueryResult as $issueRow) {
                    $issueLog = IssueLog::getObjectInstance($issueRow);
                    $issueLog->setUpdateDateTime(Helper::reformatDateTimeString($issueLog->getUpdateDateTime(),
                                                                                $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_TIME_FORMAT)));
                    $issueLog->setRequestDateTime(Helper::reformatDateTimeString($issueLog->getRequestDateTime(),
                                                                                 $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_FORMAT)));
                    $issueLog->setIssueDate(Helper::reformatDateString($issueLog->getIssueDate(),
                                                                       $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_FORMAT)));
                    $issueLog->setExpiryDate(Helper::reformatDateString($issueLog->getExpiryDate(),
                                                                        $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_FORMAT)));
                    $issueLog->setReturnDate(Helper::reformatDateString($issueLog->getReturnDate(),
                                                                        $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_FORMAT)));

                    $issueLogs[] = $issueLog;
                }

                return $issueLogs;
            }

            return null;
        }

        /**
         * @return bool|int
         */
        public function getIssueLogsCount() {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME,
                                                 "id");
        }
    }