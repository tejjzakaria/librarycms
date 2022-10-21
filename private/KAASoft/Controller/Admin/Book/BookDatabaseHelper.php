<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Book;

    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\General\Author;
    use KAASoft\Database\Entity\General\Binding;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Database\Entity\General\BookCopy;
    use KAASoft\Database\Entity\General\BookRating;
    use KAASoft\Database\Entity\General\ElectronicBook;
    use KAASoft\Database\Entity\General\Genre;
    use KAASoft\Database\Entity\General\Issue;
    use KAASoft\Database\Entity\General\IssueLog;
    use KAASoft\Database\Entity\General\Location;
    use KAASoft\Database\Entity\General\Publisher;
    use KAASoft\Database\Entity\General\Series;
    use KAASoft\Database\Entity\General\Store;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Database\Entity\Util\Image;
    use KAASoft\Database\Entity\Util\Role;
    use KAASoft\Database\Entity\Util\Tag;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;
    use PDO;

    /**
     * Class BookDatabaseHelper
     * @package KAASoft\Controller\Admin\Book
     */
    class BookDatabaseHelper extends DatabaseHelper {
        /**
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getBooks($offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = null;
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }

            $booksQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                               [ "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME           => [ "coverId" => "id" ],
                                                                 "[>]" . KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME => [ "eBookId" => "id" ],
                                                                 "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME       => [ "publisherId" => "id" ],
                                                                 "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME       => [ "publisherId" => "id" ] ],
                                                               array_merge([ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" ]),
                                                               $queryParams);

            if ($booksQueryResult !== false) {
                $bookIds = [];
                foreach ($booksQueryResult as $bookIdRow) {
                    $bookIds [] = $bookIdRow["id"];
                }

                if (count($bookIds) == 0) {
                    return null;
                }


                $booksQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                                   [ "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME           => [ "coverId" => "id" ],
                                                                     "[>]" . KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME => [ "eBookId" => "id" ],
                                                                     "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME       => [ "publisherId" => "id" ],
                                                                     "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME       => [ "publisherId" => "id" ] ],
                                                                   array_merge(Book::getDatabaseFieldNames(),
                                                                               [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id",
                                                                                 KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                                 KAASoftDatabase::$IMAGES_TABLE_NAME . ".path(imagePath)",
                                                                                 KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime(imageUploadingDateTime)",
                                                                                 KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".title(eBookTitle)",
                                                                                 KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".path(eBookPath)",
                                                                                 KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".uploadingDateTime(eBookUploadingDateTime)",
                                                                                 KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name(publisherName)",
                                                                                 "{#}(SELECT COUNT(id) FROM BookRatings WHERE Books.id = BookRatings.bookId) as ratingVotesNumber",
                                                                                 "{#}(SELECT COUNT(id) FROM BookCopies WHERE BookCopies.bookId = Books.id and BookCopies.issueStatus != 'LOST') as bookQuantity",
                                                                                 "{#}(SELECT COUNT(id) FROM BookCopies WHERE BookCopies.bookId = Books.id and BookCopies.issueStatus = 'ISSUED') as bookIssuedQuantity" ]),
                                                                   [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" => $bookIds,
                                                                     "ORDER"                                    => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ) ]);

                if ($booksQueryResult !== false) {
                    $books = [];

                    foreach ($booksQueryResult as $bookRow) {
                        $book = Book::getObjectInstance($bookRow);
                        $book->setBookRatingVotesNumber($bookRow["ratingVotesNumber"]);

                        if (isset( $bookRow["coverId"] )) {
                            $image = Image::getObjectInstance($bookRow);
                            if (file_exists($image->getAbsolutePath())) {
                                $image->setId($bookRow["coverId"]);
                                $image->setTitle($bookRow["imageTitle"]);
                                $image->setPath($bookRow["imagePath"]);
                                $image->setUploadingDateTime($bookRow["imageUploadingDateTime"]);
                                $book->setCover($image);
                            }
                        }

                        if (isset( $bookRow["eBookId"] )) {
                            $eBook = ElectronicBook::getObjectInstance($bookRow);
                            if (file_exists($eBook->getAbsolutePath())) {
                                $eBook->setId($bookRow["eBookId"]);
                                $eBook->setTitle($bookRow["eBookTitle"]);
                                $eBook->setPath($bookRow["eBookPath"]);
                                $eBook->setUploadingDateTime($bookRow["eBookUploadingDateTime"]);
                                $book->setEBook($eBook);
                            }
                        }

                        if (isset( $bookRow["publisherId"] )) {
                            $publisher = Publisher::getObjectInstance($bookRow);
                            $publisher->setId($bookRow["publisherId"]);
                            $publisher->setName($bookRow["publisherName"]);
                            $book->setPublisher($publisher);
                        }

                        $book->setQuantity($bookRow["bookQuantity"]);
                        $book->setIssuedQuantity($bookRow["bookIssuedQuantity"]);

                        $books[] = $book;
                    }

                    $bookIds = [];
                    foreach ($books as $book) {
                        if ($book instanceof Book) {
                            $bookIds[] = $book->getId();
                        }
                    }

                    if (count($bookIds) > 0) {
                        $bookAuthors = $this->getBookAuthors($bookIds);
                        $bookGenres = $this->getBookGenres($bookIds);
                        $bookStores = $this->getBookStores($bookIds);

                        foreach ($books as $book) {
                            if ($book instanceof Book) {
                                $book->setAuthors($bookAuthors[$book->getId()]);
                                $book->setGenres($bookGenres[$book->getId()]);
                                $book->setStores($bookStores[$book->getId()]);
                            }
                        }
                    }

                    return $books;
                }
            }

            return null;
        }

        /**
         * @param $bookId
         * @return Book|null
         */
        public function getSimpleBook($bookId) {
            $booksQueryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                            [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id",
                                                              KAASoftDatabase::$BOOKS_TABLE_NAME . ".title",
                                                              KAASoftDatabase::$BOOKS_TABLE_NAME . ".ISBN10",
                                                              KAASoftDatabase::$BOOKS_TABLE_NAME . ".ISBN13",
                                                              KAASoftDatabase::$BOOKS_TABLE_NAME . ".bookSN" ],
                                                            [ "id" => $bookId ]);

            if ($booksQueryResult !== false) {
                return Book::getObjectInstance($booksQueryResult);
            }

            return null;
        }

        public function getSimpleBooks($offset = null, $perPage = null) {
            $queryParams = null;
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }
            $booksQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                               [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id",
                                                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".updateDateTime" ],
                                                               $queryParams);

            if ($booksQueryResult !== false) {
                return $booksQueryResult;
            }

            return null;
        }

        /**
         * @return bool|int
         */
        public function getBooksCount() {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$BOOKS_TABLE_NAME);
        }

        /**
         * @param $bookId
         * @return Book|null
         */
        public function getBook($bookId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                       [ "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME           => [ "coverId" => "id" ],
                                                         "[>]" . KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME => [ "eBookId" => "id" ],
                                                         "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME       => [ "publisherId" => "id" ],
                                                         "[>]" . KAASoftDatabase::$SERIES_TABLE_NAME           => [ "seriesId" => "id" ] ],
                                                       array_merge(Book::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".path(imagePath)",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime(imageUploadingDateTime)",
                                                                     KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".title(eBookTitle)",
                                                                     KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".path(eBookPath)",
                                                                     KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".uploadingDateTime(eBookUploadingDateTime)",
                                                                     KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name(publisherName)",
                                                                     KAASoftDatabase::$SERIES_TABLE_NAME . ".name(seriesName)",
                                                                     "{#}(SELECT COUNT(id) FROM BookRatings where Books.id = BookRatings.bookId) as ratingVotesNumber" ]),
                                                       [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" => $bookId ]);
            if ($queryResult !== false) {
                $book = Book::getObjectInstance($queryResult);
                $book->setBookRatingVotesNumber($queryResult["ratingVotesNumber"]);

                if (isset( $queryResult["coverId"] )) {
                    $image = Image::getObjectInstance($queryResult);
                    if (file_exists($image->getAbsolutePath())) {
                        $image->setId($queryResult["coverId"]);
                        $image->setTitle($queryResult["imageTitle"]);
                        $image->setPath($queryResult["imagePath"]);
                        $image->setUploadingDateTime($queryResult["imageUploadingDateTime"]);
                        $book->setCover($image);
                    }
                }
                if (isset( $queryResult["eBookId"] )) {
                    $eBook = ElectronicBook::getObjectInstance($queryResult);
                    if (file_exists($eBook->getAbsolutePath())) {
                        $eBook->setId($queryResult["eBookId"]);
                        $eBook->setTitle($queryResult["eBookTitle"]);
                        $eBook->setPath($queryResult["eBookPath"]);
                        $eBook->setUploadingDateTime($queryResult["eBookUploadingDateTime"]);
                        $book->setEBook($eBook);
                    }
                }
                if (isset( $queryResult["seriesId"] )) {
                    $series = Series::getObjectInstance($queryResult);
                    $series->setId($queryResult["seriesId"]);
                    $series->setName($queryResult["seriesName"]);
                    $book->setSeries($series);
                }
                if (isset( $queryResult["publisherId"] )) {
                    $publisher = Publisher::getObjectInstance($queryResult);
                    $publisher->setId($queryResult["publisherId"]);
                    $publisher->setName($queryResult["publisherName"]);
                    $book->setPublisher($publisher);
                }

                $bookAuthors = $this->getBookAuthors($bookId);
                $book->setAuthors($bookAuthors[$book->getId()]);

                $bookGenres = $this->getBookGenres($bookId);
                $book->setGenres($bookGenres[$book->getId()]);

                $bookTags = $this->getBookTags($bookId);
                $book->setTags($bookTags[$book->getId()]);

                $bookStores = $this->getBookStores($bookId);
                $book->setStores($bookStores[$book->getId()]);

                $bookLocations = $this->getBookLocations($bookId);
                $book->setLocations($bookLocations[$book->getId()]);

                $bookImages = $this->getBookImages($bookId);
                $book->setImages($bookImages);

                return $book;
            }

            return null;
        }

        /**
         * @param      $bookId
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getBookLogs($bookId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [ KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".bookId" => $bookId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => ( $sortColumn === null ? [ KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . '.updateDateTime' => 'DESC' ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
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

        public function getUserLogs($userId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [ KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".userId" => $userId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => ( $sortColumn === null ? [ KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . '.updateDateTime' => 'DESC' ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
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

        public function getBookGenresIds($bookId) {
            $queryParams = [ KAASoftDatabase::$BOOK_GENRES_TABLE_NAME . ".bookId" => $bookId ];

            $genresQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOK_GENRES_TABLE_NAME,
                                                                [ "[><]" . KAASoftDatabase::$GENRES_TABLE_NAME => [ "genreId" => "id" ] ],
                                                                [ KAASoftDatabase::$GENRES_TABLE_NAME . ".id" ],
                                                                $queryParams);

            if ($genresQueryResult !== false) {
                $genreIds = [];

                foreach ($genresQueryResult as $genreRow) {

                    $genreIds[] = $genreRow["id"];
                }

                return $genreIds;
            }

            return null;
        }

        /**
         * @param $bookId
         * @return array|null
         */
        public function getBookTagsIds($bookId) {
            $queryParams = [ KAASoftDatabase::$BOOK_TAGS_TABLE_NAME . ".bookId" => $bookId ];

            $tagsQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOK_TAGS_TABLE_NAME,
                                                              [ "[><]" . KAASoftDatabase::$TAGS_TABLE_NAME => [ "tagId" => "id" ] ],
                                                              [ KAASoftDatabase::$TAGS_TABLE_NAME . ".id" ],
                                                              $queryParams);

            if ($tagsQueryResult !== false) {
                $tagIds = [];

                foreach ($tagsQueryResult as $tagRow) {

                    $tagIds[] = $tagRow["id"];
                }

                return $tagIds;
            }

            return null;
        }

        /**
         * @param $bookId
         * @return array|null
         */
        public function getBookStoreIds($bookId) {
            $queryParams = [ KAASoftDatabase::$BOOK_STORES_TABLE_NAME . ".bookId" => $bookId ];

            $storesQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOK_STORES_TABLE_NAME,
                                                                [ "[><]" . KAASoftDatabase::$STORES_TABLE_NAME => [ "storeId" => "id" ] ],
                                                                [ KAASoftDatabase::$STORES_TABLE_NAME . ".id" ],
                                                                $queryParams);

            if ($storesQueryResult !== false) {
                $storeIds = [];

                foreach ($storesQueryResult as $storeRow) {

                    $storeIds[] = $storeRow["id"];
                }

                return $storeIds;
            }

            return null;
        }

        public function getBookGenres($bookId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [ KAASoftDatabase::$BOOK_GENRES_TABLE_NAME . ".bookId" => $bookId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }

            $genreQueryResult = $this->kaaSoftDatabase->distinct()->select(KAASoftDatabase::$BOOK_GENRES_TABLE_NAME,
                                                                           [ "[><]" . KAASoftDatabase::$GENRES_TABLE_NAME => [ "genreId" => "id" ] ],
                                                                           array_merge(Genre::getDatabaseFieldNames(),
                                                                                       [ KAASoftDatabase::$GENRES_TABLE_NAME . ".id",
                                                                                         KAASoftDatabase::$BOOK_GENRES_TABLE_NAME . ".bookId" ]),
                                                                           $queryParams);

            if ($genreQueryResult !== false) {
                $genres = [];

                foreach ($genreQueryResult as $genreRow) {
                    $genre = Genre::getObjectInstance($genreRow);

                    $genres[$genreRow["bookId"]][] = $genre;
                }

                return $genres;
            }

            return null;
        }

        public function getBookTags($bookId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [ KAASoftDatabase::$BOOK_TAGS_TABLE_NAME . ".bookId" => $bookId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }

            $tagQueryResult = $this->kaaSoftDatabase->distinct()->select(KAASoftDatabase::$BOOK_TAGS_TABLE_NAME,
                                                                         [ "[><]" . KAASoftDatabase::$TAGS_TABLE_NAME => [ "tagId" => "id" ] ],
                                                                         array_merge(Tag::getDatabaseFieldNames(),
                                                                                     [ KAASoftDatabase::$TAGS_TABLE_NAME . ".id",
                                                                                       KAASoftDatabase::$BOOK_TAGS_TABLE_NAME . ".bookId" ]),
                                                                         $queryParams);

            if ($tagQueryResult !== false) {
                $tags = [];

                foreach ($tagQueryResult as $tagRow) {
                    $tag = Tag::getObjectInstance($tagRow);

                    $tags[$tagRow["bookId"]][] = $tag;
                }

                return $tags;
            }

            return null;
        }

        public function getBindings($offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = null;
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }

            $usersQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BINDINGS_TABLE_NAME,
                                                               array_merge(Binding::getDatabaseFieldNames(),
                                                                           [ KAASoftDatabase::$BINDINGS_TABLE_NAME . ".id" ]),
                                                               $queryParams);

            if ($usersQueryResult !== false) {
                $bindings = [];

                foreach ($usersQueryResult as $userRow) {
                    $binding = Binding::getObjectInstance($userRow);
                    $bindings[] = $binding;
                }

                return $bindings;
            }

            return null;
        }

        public function getGenres($offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = null;
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }

            $usersQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$GENRES_TABLE_NAME,
                                                               array_merge(Genre::getDatabaseFieldNames(),
                                                                           [ KAASoftDatabase::$GENRES_TABLE_NAME . ".id" ]),
                                                               $queryParams);

            if ($usersQueryResult !== false) {
                $genres = [];

                foreach ($usersQueryResult as $genreRow) {
                    $genre = Genre::getObjectInstance($genreRow);
                    $genres[] = $genre;
                }

                return $genres;
            }

            return null;
        }

        public function getBookAuthorsIds($bookId) {
            $queryParams = [ KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME . ".bookId" => $bookId ];

            $booksQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME,
                                                               [ "[><]" . KAASoftDatabase::$AUTHORS_TABLE_NAME => [ "authorId" => "id" ] ],
                                                               [ KAASoftDatabase::$AUTHORS_TABLE_NAME . ".id" ],
                                                               $queryParams);

            if ($booksQueryResult !== false) {
                $authorIds = [];

                foreach ($booksQueryResult as $bookRow) {
                    $authorIds[] = $bookRow["id"];
                }

                return $authorIds;
            }

            return null;
        }

        public function getBookAuthors($bookId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [ KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME . ".bookId" => $bookId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }

            $authorsQueryResult = $this->kaaSoftDatabase->distinct()->select(KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME,
                                                                             [ "[><]" . KAASoftDatabase::$AUTHORS_TABLE_NAME => [ "authorId" => "id" ] ],
                                                                             array_merge(Author::getDatabaseFieldNames(),
                                                                                         [ KAASoftDatabase::$AUTHORS_TABLE_NAME . ".id",
                                                                                           KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME . ".bookId" ]),
                                                                             $queryParams);

            if ($authorsQueryResult !== false) {
                $authors = [];

                foreach ($authorsQueryResult as $authorRow) {
                    $author = Author::getObjectInstance($authorRow);

                    $authors[$authorRow["bookId"]][] = $author;
                }

                return $authors;
            }

            return null;
        }

        /**
         * @param Book $book
         * @return bool|int
         */
        public function saveBook(Book $book) {
            $data = $book->getDatabaseArray();
            if ($book->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                      $data);
            }
            else {
                unset( $data["creationDateTime"] );

                //unset( $data["actualQuantity"] );//it should be updated by another method

                return $this->kaaSoftDatabase->update(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $book->getId() ]);
            }
        }

        /**
         * @param $books
         * @return array|bool
         */
        public function saveBooks($books) {
            $data = [];
            foreach ($books as $book) {
                if ($book instanceof Book) {
                    $data[] = $book->getDatabaseArray();
                }
            }

            $insertCount = KAASoftDatabase::INSERT_PACKET_SIZE;
            $dataCount = count($data);
            if ($dataCount > 0) {
                $iterations = ceil($dataCount / $insertCount);
                $result = [];
                for ($i = 0; $i < $iterations; $i++) {
                    $slice = array_slice($data,
                                         $insertCount * $i,
                                         $insertCount);

                    if (count($slice) > 0) {
                        $tempResult = $this->kaaSoftDatabase->insert(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                                     $slice);
                        if ($tempResult === false) {
                            return false;
                        }
                        $result = array_merge($result,
                                              $tempResult);
                    }
                }

                return $result;

            }
            else {
                return true;
            }
        }

        /**
         * @param $books
         * @return bool
         */
        public function updateBooks($books) {
            $result = true;
            foreach ($books as $book) {
                if ($book instanceof Book) {
                    $data = $book->getDatabaseArray();
                    $updateResult = $this->kaaSoftDatabase->update(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                                   $data,
                                                                   [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" => $book->getId() ]) !== false;
                    $result = ( $result and $updateResult );
                    if ($result === false) {
                        return false;
                    }
                }
            }

            return true;
        }

        /**
         * @param $bookId
         * @param $authorIds
         * @return bool
         */
        public function saveBookAuthors($bookId, $authorIds) {
            $result = true;
            foreach ($authorIds as $authorId) {
                $insertResult = $this->kaaSoftDatabase->insert(KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME,
                                                               [ "bookId"   => $bookId,
                                                                 "authorId" => $authorId ]) !== false;
                $result = ( $result and $insertResult );
            }

            return $result;
        }

        public function saveBookAuthorsArray($bookAuthorIds) {
            return $this->kaaSoftDatabase->insert(KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME,
                                                  $bookAuthorIds) !== false;
        }

        public function saveBookGenresArray($bookGenreIds) {
            return $this->kaaSoftDatabase->insert(KAASoftDatabase::$BOOK_GENRES_TABLE_NAME,
                                                  $bookGenreIds) !== false;
        }

        /**
         * @param $bookId
         * @return bool
         */
        public function deleteBookAuthors($bookId) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME,
                                                  [ "bookId" => $bookId ]);
        }

        /**
         * @param $bookId
         * @param $genreIds
         * @return bool
         */
        public function saveBookGenres($bookId, $genreIds) {
            $result = true;
            foreach ($genreIds as $genreId) {
                $insertResult = $this->kaaSoftDatabase->insert(KAASoftDatabase::$BOOK_GENRES_TABLE_NAME,
                                                               [ "bookId"  => $bookId,
                                                                 "genreId" => $genreId ]) !== false;
                $result = ( $result and $insertResult );
            }

            return $result;
        }

        /**
         * @param $bookId
         * @param $tagIds
         * @return bool
         */
        public function saveBookTags($bookId, $tagIds) {
            $result = true;
            foreach ($tagIds as $tagId) {
                $insertResult = $this->kaaSoftDatabase->insert(KAASoftDatabase::$BOOK_TAGS_TABLE_NAME,
                                                               [ "bookId" => $bookId,
                                                                 "tagId"  => $tagId ]) !== false;
                $result = ( $result and $insertResult );
            }

            return $result;
        }

        /**
         * @param $bookId
         * @return bool
         */
        public function deleteBookGenres($bookId) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$BOOK_GENRES_TABLE_NAME,
                                                  [ "bookId" => $bookId ]);
        }

        /**
         * @param $bookId
         * @return bool
         */
        public function deleteBookTags($bookId) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$BOOK_TAGS_TABLE_NAME,
                                                  [ "bookId" => $bookId ]);
        }

        /**
         * @param $bookId
         * @return bool
         */
        public function deleteBook($bookId) {
            $book = $this->getBook($bookId);
            if ($book !== null) {
                return $this->kaaSoftDatabase->delete(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                      [ "id" => $bookId ]);
            }

            return false;
        }

        /**+
         * @param $bookId
         * @return bool
         */
        public function isBookExist($bookId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                               [ "id" => $bookId ]);
        }

        /**
         * @param $bookSN
         * @return bool
         */
        public function isBookSNExist($bookSN) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                               [ "bookSN" => $bookSN ]);
        }

        /**
         * @param $bookId
         * @param $authorId
         * @return bool
         */
        public function isBookAuthorExist($bookId, $authorId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME,
                                               [ "AND" => [ "bookId"   => $bookId,
                                                            "authorId" => $authorId ] ]);
        }

        /**
         * @param $bookId
         * @param $genreId
         * @return bool
         */
        public function isBookGenreExist($bookId, $genreId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$BOOK_GENRES_TABLE_NAME,
                                               [ "AND" => [ "bookId"  => $bookId,
                                                            "genreId" => $genreId ] ]);
        }

        public function getAuthorBooks($authorId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [ KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME . ".authorId" => $authorId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }

            $booksQueryResult = $this->kaaSoftDatabase->distinct()->select(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                                           [ "[><]" . KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME => [ "id" => "bookId" ],
                                                                             "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME        => [ "coverId" => "id" ],
                                                                             "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME    => [ "publisherId" => "id" ] ],
                                                                           array_merge(Book::getDatabaseFieldNames(),
                                                                                       [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id",
                                                                                         KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                                         KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                                         KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime",
                                                                                         KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name(publisherName)",
                                                                                         "{#}(SELECT COUNT(id) FROM BookRatings where Books.id = BookRatings.bookId) as ratingVotesNumber" ]),
                                                                           $queryParams);

            if ($booksQueryResult !== false) {
                $books = [];

                foreach ($booksQueryResult as $bookRow) {
                    $book = Book::getObjectInstance($bookRow);
                    $book->setBookRatingVotesNumber($bookRow["ratingVotesNumber"]);

                    if (isset( $bookRow["coverId"] )) {
                        $image = Image::getObjectInstance($bookRow);
                        if (file_exists($image->getAbsolutePath())) {
                            $image->setId($bookRow["coverId"]);
                            $image->setTitle($bookRow["imageTitle"]);
                            $book->setCover($image);
                        }
                    }

                    if (isset( $bookRow["publisherId"] )) {
                        $publisher = Publisher::getObjectInstance($bookRow);
                        $publisher->setId($bookRow["publisherId"]);
                        $publisher->setName($bookRow["publisherName"]);
                        $book->setPublisher($publisher);
                    }

                    $books[] = $book;
                }

                return $books;
            }

            return null;
        }

        public function getAuthorBooksCount($authorId) {
            $queryParams = [ KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME . ".authorId" => $authorId ];

            return $this->kaaSoftDatabase->distinct()->count(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                             [ "[><]" . KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME => [ "id" => "bookId" ] ],
                                                             [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" ],
                                                             $queryParams);
        }

        public function getPublisherBooksCount($publisherId) {
            $queryParams = [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".publisherId" => $publisherId ];

            return $this->kaaSoftDatabase->count(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                 [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" ],
                                                 $queryParams);
        }

        public function getPublisherBooks($publisherId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".publisherId" => $publisherId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }

            $booksQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                               [ "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME => [ "coverId" => "id" ], ],
                                                               array_merge(Book::getDatabaseFieldNames(),
                                                                           [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime",
                                                                             "{#}(SELECT COUNT(id) FROM BookRatings where Books.id = BookRatings.bookId) as ratingVotesNumber" ]),
                                                               $queryParams);
            if ($booksQueryResult !== false) {
                $books = [];

                foreach ($booksQueryResult as $bookRow) {
                    $book = Book::getObjectInstance($bookRow);
                    $book->setBookRatingVotesNumber($bookRow["ratingVotesNumber"]);

                    if (isset( $bookRow["coverId"] )) {
                        $image = Image::getObjectInstance($bookRow);
                        if (file_exists($image->getAbsolutePath())) {
                            $image->setId($bookRow["coverId"]);
                            $image->setTitle($bookRow["imageTitle"]);
                            $book->setCover($image);
                        }
                    }
                    //$book->setAuthors($this->getBookAuthors($book->getId()));

                    $books[] = $book;
                }

                $bookIds = [];
                foreach ($books as $book) {
                    if ($book instanceof Book) {
                        $bookIds[] = $book->getId();
                    }
                }
                if (count($bookIds) > 0) {
                    $bookAuthors = $this->getBookAuthors($bookIds);

                    foreach ($books as $book) {
                        if ($book instanceof Book) {
                            $book->setAuthors($bookAuthors[$book->getId()]);
                        }
                    }
                }

                return $books;
            }

            return null;
        }

        public function getSeriesBooksCount($seriesId) {
            $queryParams = [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".seriesId" => $seriesId ];

            return $this->kaaSoftDatabase->count(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                 [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" ],
                                                 $queryParams);
        }

        public function getSeriesBooks($seriesId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".seriesId" => $seriesId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }

            $booksQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                               [ "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME     => [ "coverId" => "id" ],
                                                                 "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME => [ "publisherId" => "id" ] ],
                                                               array_merge(Book::getDatabaseFieldNames(),
                                                                           [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime",
                                                                             KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name(publisherName)",
                                                                             "{#}(SELECT COUNT(id) FROM BookRatings where Books.id = BookRatings.bookId) as ratingVotesNumber" ]),
                                                               $queryParams);

            if ($booksQueryResult !== false) {
                $books = [];

                foreach ($booksQueryResult as $bookRow) {
                    $book = Book::getObjectInstance($bookRow);
                    $book->setBookRatingVotesNumber($bookRow["ratingVotesNumber"]);

                    if (isset( $bookRow["coverId"] )) {
                        $image = Image::getObjectInstance($bookRow);
                        if (file_exists($image->getAbsolutePath())) {
                            $image->setId($bookRow["coverId"]);
                            $image->setTitle($bookRow["imageTitle"]);
                            $book->setCover($image);
                        }
                    }

                    if (isset( $bookRow["publisherId"] )) {
                        $publisher = Publisher::getObjectInstance($bookRow);
                        $publisher->setId($bookRow["publisherId"]);
                        $publisher->setName($bookRow["publisherName"]);
                        $book->setPublisher($publisher);
                    }

                    $books[] = $book;
                }

                return $books;
            }

            return null;
        }

        public function getGenreBooks($genreId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [ KAASoftDatabase::$BOOK_GENRES_TABLE_NAME . ".genreId" => $genreId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }

            $booksQueryResult = $this->kaaSoftDatabase->distinct()->select(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                                           [ "[><]" . KAASoftDatabase::$BOOK_GENRES_TABLE_NAME => [ "id" => "bookId" ],
                                                                             "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME       => [ "coverId" => "id" ],
                                                                             "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME   => [ "publisherId" => "id" ] ],
                                                                           array_merge(Book::getDatabaseFieldNames(),
                                                                                       [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id",
                                                                                         KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                                         KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                                         KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime",
                                                                                         KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name(publisherName)",
                                                                                         "{#}(SELECT COUNT(id) FROM BookRatings where Books.id = BookRatings.bookId) as ratingVotesNumber" ]),
                                                                           $queryParams);

            if ($booksQueryResult !== false) {
                $books = [];

                foreach ($booksQueryResult as $bookRow) {
                    $book = Book::getObjectInstance($bookRow);
                    $book->setBookRatingVotesNumber($bookRow["ratingVotesNumber"]);

                    if (isset( $bookRow["coverId"] )) {
                        $image = Image::getObjectInstance($bookRow);
                        if (file_exists($image->getAbsolutePath())) {
                            $image->setId($bookRow["coverId"]);
                            $image->setTitle($bookRow["imageTitle"]);
                            $book->setCover($image);
                        }
                    }

                    if (isset( $bookRow["publisherId"] )) {
                        $publisher = Publisher::getObjectInstance($bookRow);
                        $publisher->setId($bookRow["publisherId"]);
                        $publisher->setName($bookRow["publisherName"]);
                        $book->setPublisher($publisher);
                    }

                    //$book->setAuthors($this->getBookAuthors($book->getId()));

                    $books[] = $book;
                }

                $bookIds = [];
                foreach ($books as $book) {
                    if ($book instanceof Book) {
                        $bookIds[] = $book->getId();
                    }
                }
                if (count($bookIds) > 0) {
                    $bookAuthors = $this->getBookAuthors($bookIds);

                    foreach ($books as $book) {
                        if ($book instanceof Book) {
                            $book->setAuthors($bookAuthors[$book->getId()]);
                        }
                    }
                }

                return $books;
            }

            return null;
        }

        public function getGenreBooksCount($genreId) {
            $queryParams = [ KAASoftDatabase::$BOOK_GENRES_TABLE_NAME . ".genreId" => $genreId ];

            return $this->kaaSoftDatabase->distinct()->count(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                             [ "[><]" . KAASoftDatabase::$BOOK_GENRES_TABLE_NAME => [ "id" => "bookId" ] ],
                                                             [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" ],
                                                             $queryParams);
        }

        /**
         * @param $bookId
         * @return int
         */
        public function getBookQuantity($bookId) {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$BOOK_COPIES_TABLE_NAME,
                                                 "id",
                                                 [ "AND" => [ "bookId"         => $bookId,
                                                              "issueStatus[!]" => Issue::ISSUE_STATUS_LOST ] ]);
        }

        /**
         * @param $bookId
         * @return bool|int
         */
        public function getBookActualQuantity($bookId) {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$BOOK_COPIES_TABLE_NAME,
                                                 "id",
                                                 [ "AND" => [ "bookId" => $bookId,
                                                              "OR"     => [ "OR1" => [ "issueStatus" => null ],
                                                                            "OR2" => [ "issueStatus" => Issue::ISSUE_STATUS_AVAILABLE ] ] ] ]);
        }

        /**
         * @param $bookCopyId
         * @return bool|int
         */
        public function setBookCopyIssued($bookCopyId) {
            return $this->setBookCopyIssueStatus($bookCopyId,
                                                 Issue::ISSUE_STATUS_ISSUED);
        }

        /**
         * @param $bookCopyId
         * @return bool|int
         */
        public function setBookCopyReturned($bookCopyId) {
            return $this->setBookCopyIssueStatus($bookCopyId,
                                                 Issue::ISSUE_STATUS_AVAILABLE);
        }

        /**
         * @param $bookCopyId
         * @return bool|int
         */
        public function setBookCopyLost($bookCopyId) {
            return $this->setBookCopyIssueStatus($bookCopyId,
                                                 Issue::ISSUE_STATUS_LOST);
        }

        /**
         * @param $bookCopyId
         * @param $issueStatus
         * @return bool|int
         */
        public function setBookCopyIssueStatus($bookCopyId, $issueStatus) {
            return $this->kaaSoftDatabase->update(KAASoftDatabase::$BOOK_COPIES_TABLE_NAME,
                                                  [ "issueStatus" => $issueStatus ],
                                                  [ "id" => $bookCopyId ]);
        }

        /**
         * @param $bookCopyId
         * @return int|null
         */
        public function getBookIdByCopy($bookCopyId) {
            $result = $this->kaaSoftDatabase->get(KAASoftDatabase::$BOOK_COPIES_TABLE_NAME,
                                                  [ "bookId" ],
                                                  [ "id" => $bookCopyId ]);

            if ($result !== null) {
                return (int)$result["bookId"];
            }

            return null;
        }

        /**
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getFullBooks($offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = null;
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }
            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                          [ "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME     => [ "coverId" => "id" ],
                                                            "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME => [ "publisherId" => "id" ],
                                                            "[>]" . KAASoftDatabase::$SERIES_TABLE_NAME     => [ "seriesId" => "id" ] ],
                                                          array_merge(Book::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                        KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name(publisherName)",
                                                                        KAASoftDatabase::$SERIES_TABLE_NAME . ".name(seriesName)", ]),
                                                          $queryParams);

            if ($queryResult !== false) {
                $books = [];

                foreach ($queryResult as $bookRow) {

                    $book = Book::getObjectInstance($bookRow);

                    if (isset( $bookRow["coverId"] )) {
                        $image = Image::getObjectInstance($bookRow);
                        if (file_exists($image->getAbsolutePath())) {
                            $image->setId($bookRow["coverId"]);
                            $image->setTitle($bookRow["imageTitle"]);
                            $book->setCover($image);
                        }
                    }
                    if (isset( $bookRow["seriesId"] )) {
                        $series = Series::getObjectInstance($bookRow);
                        $series->setId($bookRow["seriesId"]);
                        $series->setName($bookRow["seriesName"]);
                        $book->setSeries($series);
                    }
                    if (isset( $bookRow["publisherId"] )) {
                        $publisher = Publisher::getObjectInstance($bookRow);
                        $publisher->setId($bookRow["publisherId"]);
                        $publisher->setName($bookRow["publisherName"]);
                        $book->setPublisher($publisher);
                    }

                    $books[] = $book;
                }

                $bookIds = [];
                foreach ($books as $book) {
                    if ($book instanceof Book) {
                        $bookIds[] = $book->getId();
                    }
                }
                if (count($bookIds) > 0) {
                    $bookAuthors = $this->getBookAuthors($bookIds);
                    $bookGenres = $this->getBookGenres($bookIds);
                    $bookStores = $this->getBookStores($bookIds);
                    $bookLocations = $this->getBookLocations($bookIds);

                    foreach ($books as $book) {
                        if ($book instanceof Book) {
                            $book->setAuthors($bookAuthors[$book->getId()]);
                            $book->setGenres($bookGenres[$book->getId()]);
                            $book->setStores($bookStores[$book->getId()]);
                            $book->setLocations($bookLocations[$book->getId()]);
                        }
                    }
                }

                return $books;

            }

            return null;
        }

        /**
         * @param      $storeId
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getStoreBooks($storeId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [ KAASoftDatabase::$BOOK_STORES_TABLE_NAME . ".storeId" => $storeId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }

            $booksQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                               [ "[><]" . KAASoftDatabase::$BOOK_STORES_TABLE_NAME     => [ "id" => "bookId" ],
                                                                 "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME           => [ "coverId" => "id" ],
                                                                 "[>]" . KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME => [ "eBookId" => "id" ],
                                                                 "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME       => [ "publisherId" => "id" ] ],
                                                               array_merge(Book::getDatabaseFieldNames(),
                                                                           [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".path(imagePath)",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime(imageUploadingDateTime)",
                                                                             KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".title(eBookTitle)",
                                                                             KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".path(eBookPath)",
                                                                             KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".uploadingDateTime(eBookUploadingDateTime)",
                                                                             KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name(publisherName)",
                                                                             "{#}(SELECT COUNT(id) FROM BookCopies WHERE BookCopies.bookId = Books.id and BookCopies.issueStatus != 'LOST') as bookQuantity",
                                                                             "{#}(SELECT COUNT(id) FROM BookCopies WHERE BookCopies.bookId = Books.id and BookCopies.issueStatus = 'ISSUED') as bookIssuedQuantity" ]),
                                                               $queryParams);

            if ($booksQueryResult !== false) {
                $books = [];

                foreach ($booksQueryResult as $bookRow) {
                    $book = Book::getObjectInstance($bookRow);

                    if (isset( $bookRow["coverId"] )) {
                        $image = Image::getObjectInstance($bookRow);
                        if (file_exists($image->getAbsolutePath())) {
                            $image->setId($bookRow["coverId"]);
                            $image->setTitle($bookRow["imageTitle"]);
                            $image->setPath($bookRow["imagePath"]);
                            $image->setUploadingDateTime($bookRow["imageUploadingDateTime"]);
                            $book->setCover($image);
                        }
                    }

                    if (isset( $bookRow["eBookId"] )) {
                        $eBook = ElectronicBook::getObjectInstance($bookRow);
                        if (file_exists($eBook->getAbsolutePath())) {
                            $eBook->setId($bookRow["eBookId"]);
                            $eBook->setTitle($bookRow["eBookTitle"]);
                            $eBook->setPath($bookRow["eBookPath"]);
                            $eBook->setUploadingDateTime($bookRow["eBookUploadingDateTime"]);
                            $book->setEBook($eBook);
                        }
                    }

                    if (isset( $bookRow["publisherId"] )) {
                        $publisher = Publisher::getObjectInstance($bookRow);
                        $publisher->setId($bookRow["publisherId"]);
                        $publisher->setName($bookRow["publisherName"]);
                        $book->setPublisher($publisher);
                    }

                    $bookLocations = $this->getBookLocations($book->getId());
                    $book->setLocations($bookLocations[$book->getId()]);

                    $book->setQuantity($bookRow["bookQuantity"]);
                    $book->setIssuedQuantity($bookRow["bookIssuedQuantity"]);

                    $books[] = $book;
                }

                $bookIds = [];
                foreach ($books as $book) {
                    if ($book instanceof Book) {
                        $bookIds[] = $book->getId();
                    }
                }
                if (count($bookIds) > 0) {
                    $bookAuthors = $this->getBookAuthors($bookIds);
                    $bookGenres = $this->getBookGenres($bookIds);

                    foreach ($books as $book) {
                        if ($book instanceof Book) {
                            $book->setAuthors($bookAuthors[$book->getId()]);
                            $book->setGenres($bookGenres[$book->getId()]);
                        }
                    }
                }

                return $books;
            }

            return null;
        }

        /**
         * @param      $locationId
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getLocationBooks($locationId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [ KAASoftDatabase::$BOOK_LOCATIONS_TABLE_NAME . ".locationId" => $locationId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }

            $booksQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                               [ "[><]" . KAASoftDatabase::$BOOK_LOCATIONS_TABLE_NAME  => [ "id" => "bookId" ],
                                                                 "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME           => [ "coverId" => "id" ],
                                                                 "[>]" . KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME => [ "eBookId" => "id" ],
                                                                 "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME       => [ "publisherId" => "id" ] ],
                                                               array_merge(Book::getDatabaseFieldNames(),
                                                                           [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".path(imagePath)",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime(imageUploadingDateTime)",
                                                                             KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".title(eBookTitle)",
                                                                             KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".path(eBookPath)",
                                                                             KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".uploadingDateTime(eBookUploadingDateTime)",
                                                                             KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name(publisherName)",
                                                                             "{#}(SELECT COUNT(id) FROM BookCopies WHERE BookCopies.bookId = Books.id and BookCopies.issueStatus != 'LOST') as bookQuantity",
                                                                             "{#}(SELECT COUNT(id) FROM BookCopies WHERE BookCopies.bookId = Books.id and BookCopies.issueStatus = 'ISSUED') as bookIssuedQuantity" ]),
                                                               $queryParams);

            if ($booksQueryResult !== false) {
                $books = [];

                foreach ($booksQueryResult as $bookRow) {
                    $book = Book::getObjectInstance($bookRow);

                    if (isset( $bookRow["coverId"] )) {
                        $image = Image::getObjectInstance($bookRow);
                        if (file_exists($image->getAbsolutePath())) {
                            $image->setId($bookRow["coverId"]);
                            $image->setTitle($bookRow["imageTitle"]);
                            $image->setPath($bookRow["imagePath"]);
                            $image->setUploadingDateTime($bookRow["imageUploadingDateTime"]);
                            $book->setCover($image);
                        }
                    }

                    if (isset( $bookRow["eBookId"] )) {
                        $eBook = ElectronicBook::getObjectInstance($bookRow);
                        if (file_exists($eBook->getAbsolutePath())) {
                            $eBook->setId($bookRow["eBookId"]);
                            $eBook->setTitle($bookRow["eBookTitle"]);
                            $eBook->setPath($bookRow["eBookPath"]);
                            $eBook->setUploadingDateTime($bookRow["eBookUploadingDateTime"]);
                            $book->setEBook($eBook);
                        }
                    }

                    if (isset( $bookRow["publisherId"] )) {
                        $publisher = Publisher::getObjectInstance($bookRow);
                        $publisher->setId($bookRow["publisherId"]);
                        $publisher->setName($bookRow["publisherName"]);
                        $book->setPublisher($publisher);
                    }
                    $book->setQuantity($bookRow["bookQuantity"]);
                    $book->setIssuedQuantity($bookRow["bookIssuedQuantity"]);

                    $books[] = $book;
                }

                $bookIds = [];
                foreach ($books as $book) {
                    if ($book instanceof Book) {
                        $bookIds[] = $book->getId();
                    }
                }
                if (count($bookIds) > 0) {
                    $bookAuthors = $this->getBookAuthors($bookIds);
                    $bookGenres = $this->getBookGenres($bookIds);
                    $bookStores = $this->getBookStores($bookIds);

                    foreach ($books as $book) {
                        if ($book instanceof Book) {
                            $book->setAuthors($bookAuthors[$book->getId()]);
                            $book->setGenres($bookGenres[$book->getId()]);
                            $book->setStores($bookStores[$book->getId()]);
                        }
                    }
                }

                return $books;
            }

            return null;
        }

        public function getUserBooks($userId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [ "AND" => [ KAASoftDatabase::$ISSUES_TABLE_NAME . ".userId"     => $userId,
                                        KAASoftDatabase::$ISSUES_TABLE_NAME . ".returnDate" => null,
                                        KAASoftDatabase::$ISSUES_TABLE_NAME . ".isLost"     => false ], ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }

            $booksQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                               [ "[><]" . KAASoftDatabase::$ISSUES_TABLE_NAME          => [ "id" => "bookId" ],
                                                                 "[><]" . KAASoftDatabase::$USERS_TABLE_NAME           => [ KAASoftDatabase::$ISSUES_TABLE_NAME . ".userId" => "id" ],
                                                                 "[><]" . KAASoftDatabase::$ROLES_TABLE_NAME           => [ KAASoftDatabase::$USERS_TABLE_NAME . ".roleId" => "id" ],
                                                                 "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME           => [ "coverId" => "id" ],
                                                                 "[>]" . KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME => [ "eBookId" => "id" ],
                                                                 "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME       => [ "publisherId" => "id" ] ],
                                                               array_merge(Book::getDatabaseFieldNames(),
                                                                           Issue::getDatabaseFieldNames(),
                                                                           Role::getDatabaseFieldNames(),
                                                                           User::getDatabaseFieldNames(),
                                                                           [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id",
                                                                             KAASoftDatabase::$ISSUES_TABLE_NAME . ".id(issueId)",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".path(imagePath)",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime(imageUploadingDateTime)",
                                                                             KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".title(eBookTitle)",
                                                                             KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".path(eBookPath)",
                                                                             KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".uploadingDateTime(eBookUploadingDateTime)",
                                                                             KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name(publisherName)", ]),
                                                               $queryParams);

            if ($booksQueryResult !== false) {
                $books = [];

                foreach ($booksQueryResult as $bookRow) {
                    $book = Book::getObjectInstance($bookRow);

                    if (isset( $bookRow["coverId"] )) {
                        $image = Image::getObjectInstance($bookRow);
                        if (file_exists($image->getAbsolutePath())) {
                            $image->setId($bookRow["coverId"]);
                            $image->setTitle($bookRow["imageTitle"]);
                            $image->setPath($bookRow["imagePath"]);
                            $image->setUploadingDateTime($bookRow["imageUploadingDateTime"]);
                            $book->setCover($image);
                        }
                    }

                    if (isset( $bookRow["eBookId"] )) {
                        $eBook = ElectronicBook::getObjectInstance($bookRow);
                        if (file_exists($eBook->getAbsolutePath())) {
                            $eBook->setId($bookRow["eBookId"]);
                            $eBook->setTitle($bookRow["eBookTitle"]);
                            $eBook->setPath($bookRow["eBookPath"]);
                            $eBook->setUploadingDateTime($bookRow["eBookUploadingDateTime"]);
                            $book->setEBook($eBook);
                        }
                    }

                    if (isset( $bookRow["publisherId"] )) {
                        $publisher = Publisher::getObjectInstance($bookRow);
                        $publisher->setId($bookRow["publisherId"]);
                        $publisher->setName($bookRow["publisherName"]);
                        $book->setPublisher($publisher);
                    }
                    if (isset( $bookRow["issueId"] )) {
                        $issue = Issue::getObjectInstance($bookRow);
                        $issue->setId($bookRow["issueId"]);

                        $user = User::getObjectInstance($bookRow);
                        $user->setId($issue->getUserId());

                        $role = Role::getObjectInstance($bookRow);
                        $role->setId($user->getRoleId());
                        $user->setRole($role);

                        $issue->setUser($user);

                        $isExpired = Issue::isIssueExpired($issue);
                        if ($isExpired) {
                            // if penalty not paid calculate it
                            $issue->setPenalty(Issue::calculatePenalty($user,
                                                                       $issue));
                            $issue->setIsExpired($isExpired);
                        }
                        $book->setIssue($issue);
                    }

                    $books[] = $book;
                }

                $bookIds = [];
                foreach ($books as $book) {
                    if ($book instanceof Book) {
                        $bookIds[] = $book->getId();
                    }
                }
                if (count($bookIds) > 0) {
                    $bookAuthors = $this->getBookAuthors($bookIds);
                    $bookGenres = $this->getBookGenres($bookIds);

                    foreach ($books as $book) {
                        if ($book instanceof Book) {
                            $book->setAuthors($bookAuthors[$book->getId()]);
                            $book->setGenres($bookGenres[$book->getId()]);
                        }
                    }
                }

                return $books;
            }

            return null;
        }

        public function getUserBooksCount($userId) {
            $queryParams = [ "AND" => [ KAASoftDatabase::$ISSUES_TABLE_NAME . ".userId"     => $userId,
                                        KAASoftDatabase::$ISSUES_TABLE_NAME . ".returnDate" => null ] ];

            return $this->kaaSoftDatabase->count(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                 [ "[><]" . KAASoftDatabase::$ISSUES_TABLE_NAME => [ "id" => "bookId" ] ],
                                                 [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" ],
                                                 $queryParams);

        }

        public function getUserRequestedBooksCount($userId) {
            $queryParams = [ KAASoftDatabase::$REQUESTS_TABLE_NAME . ".userId" => $userId ];

            return $this->kaaSoftDatabase->count(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                 [ "[><]" . KAASoftDatabase::$REQUESTS_TABLE_NAME => [ "id" => "bookId" ] ],
                                                 [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" ],
                                                 $queryParams);
        }

        public function getUserRequestedBooks($userId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = $userId === null ? [] : [ KAASoftDatabase::$REQUESTS_TABLE_NAME . ".userId" => $userId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }

            $booksQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                               [ "[><]" . KAASoftDatabase::$REQUESTS_TABLE_NAME  => [ "id" => "bookId" ],
                                                                 "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME     => [ "coverId" => "id" ],
                                                                 "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME => [ "publisherId" => "id" ] ],
                                                               array_merge(Book::getDatabaseFieldNames(),
                                                                           [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime",
                                                                             KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name(publisherName)", ]),
                                                               $queryParams);

            if ($booksQueryResult !== false) {
                $books = [];

                foreach ($booksQueryResult as $bookRow) {
                    $book = Book::getObjectInstance($bookRow);
                    if (isset( $bookRow["coverId"] )) {
                        $image = Image::getObjectInstance($bookRow);
                        if (file_exists($image->getAbsolutePath())) {
                            $image->setId($bookRow["coverId"]);
                            $image->setTitle($bookRow["imageTitle"]);
                            $book->setCover($image);
                        }
                    }
                    if (isset( $bookRow["publisherId"] )) {
                        $publisher = Publisher::getObjectInstance($bookRow);
                        $publisher->setId($bookRow["publisherId"]);
                        $publisher->setName($bookRow["publisherName"]);
                        $book->setPublisher($publisher);
                    }

                    $books[] = $book;
                }

                $bookIds = [];
                foreach ($books as $book) {
                    if ($book instanceof Book) {
                        $bookIds[] = $book->getId();
                    }
                }
                if (count($bookIds) > 0) {
                    $bookAuthors = $this->getBookAuthors($bookIds);
                    $bookGenres = $this->getBookGenres($bookIds);

                    foreach ($books as $book) {
                        if ($book instanceof Book) {
                            $book->setAuthors($bookAuthors[$book->getId()]);
                            $book->setGenres($bookGenres[$book->getId()]);
                        }
                    }
                }

                return $books;
            }

            return null;
        }

        public function getUserIssuedBooksCount($userId) {
            $queryParams = [ KAASoftDatabase::$ISSUES_TABLE_NAME . ".userId" => $userId ];

            return $this->kaaSoftDatabase->count(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                 [ "[><]" . KAASoftDatabase::$ISSUES_TABLE_NAME => [ "id" => "bookId" ] ],
                                                 [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" ],
                                                 $queryParams);

        }

        public function getUserIssuedBooks($userId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = $userId == null ? [] : [ KAASoftDatabase::$ISSUES_TABLE_NAME . ".userId" => $userId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }

            $booksQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                               [ "[><]" . KAASoftDatabase::$ISSUES_TABLE_NAME          => [ "id" => "bookId" ],
                                                                 "[><]" . KAASoftDatabase::$USERS_TABLE_NAME           => [ KAASoftDatabase::$ISSUES_TABLE_NAME . ".userId" => "id" ],
                                                                 "[><]" . KAASoftDatabase::$ROLES_TABLE_NAME           => [ KAASoftDatabase::$USERS_TABLE_NAME . ".roleId" => "id" ],
                                                                 "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME           => [ "coverId" => "id" ],
                                                                 "[>]" . KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME => [ "eBookId" => "id" ],
                                                                 "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME       => [ "publisherId" => "id" ] ],
                                                               array_merge(Book::getDatabaseFieldNames(),
                                                                           Role::getDatabaseFieldNames(),
                                                                           Issue::getDatabaseFieldNames(),
                                                                           User::getDatabaseFieldNames(),
                                                                           [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id",
                                                                             KAASoftDatabase::$ISSUES_TABLE_NAME . ".id(issueId)",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".path(imagePath)",
                                                                             KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime(imageUploadingDateTime)",
                                                                             KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".title(eBookTitle)",
                                                                             KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".path(eBookPath)",
                                                                             KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".uploadingDateTime(eBookUploadingDateTime)",
                                                                             KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name(publisherName)", ]),
                                                               $queryParams);

            if ($booksQueryResult !== false) {
                $books = [];

                foreach ($booksQueryResult as $bookRow) {
                    $book = Book::getObjectInstance($bookRow);
                    if (isset( $bookRow["coverId"] )) {
                        $image = Image::getObjectInstance($bookRow);
                        if (file_exists($image->getAbsolutePath())) {
                            $image->setId($bookRow["coverId"]);
                            $image->setTitle($bookRow["imageTitle"]);
                            $image->setPath($bookRow["imagePath"]);
                            $image->setUploadingDateTime($bookRow["imageUploadingDateTime"]);
                            $book->setCover($image);
                        }
                    }

                    if (isset( $bookRow["eBookId"] )) {
                        $eBook = ElectronicBook::getObjectInstance($bookRow);
                        if (file_exists($eBook->getAbsolutePath())) {
                            $eBook->setId($bookRow["eBookId"]);
                            $eBook->setTitle($bookRow["eBookTitle"]);
                            $eBook->setPath($bookRow["eBookPath"]);
                            $eBook->setUploadingDateTime($bookRow["eBookUploadingDateTime"]);
                            $book->setEBook($eBook);
                        }
                    }

                    if (isset( $bookRow["publisherId"] )) {
                        $publisher = Publisher::getObjectInstance($bookRow);
                        $publisher->setId($bookRow["publisherId"]);
                        $publisher->setName($bookRow["publisherName"]);
                        $book->setPublisher($publisher);
                    }

                    if (isset( $bookRow["issueId"] )) {
                        $issue = Issue::getObjectInstance($bookRow);
                        $issue->setId($bookRow["issueId"]);

                        $user = User::getObjectInstance($bookRow);
                        $user->setId($issue->getUserId());

                        $role = Role::getObjectInstance($bookRow);
                        $role->setId($user->getRoleId());
                        $user->setRole($role);

                        $issue->setUser($user);

                        $isExpired = Issue::isIssueExpired($issue);
                        if ($isExpired) {
                            // if penalty not paid calculate it
                            $issue->setPenalty(Issue::calculatePenalty($user,
                                                                       $issue));
                            $issue->setIsExpired($isExpired);
                        }
                        $book->setIssue($issue);
                    }

                    $books[] = $book;
                }

                $bookIds = [];
                foreach ($books as $book) {
                    if ($book instanceof Book) {
                        $bookIds[] = $book->getId();
                    }
                }

                if (count($bookIds) > 0) {
                    $bookAuthors = $this->getBookAuthors($bookIds);
                    $bookGenres = $this->getBookGenres($bookIds);

                    foreach ($books as $book) {
                        if ($book instanceof Book) {
                            $book->setAuthors($bookAuthors[$book->getId()]);
                            $book->setGenres($bookGenres[$book->getId()]);
                        }
                    }
                }

                return $books;
            }

            return null;
        }

        /**
         * @param $bookIds
         * @return array|null
         */
        public function getBooksByIds($bookIds) {
            $condition = [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" => $bookIds ];
            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                          [ "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME           => [ "coverId" => "id" ],
                                                            "[>]" . KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME => [ "eBookId" => "id" ],
                                                            "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME       => [ "publisherId" => "id" ],
                                                            "[>]" . KAASoftDatabase::$SERIES_TABLE_NAME           => [ "seriesId" => "id" ] ],
                                                          array_merge(Book::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".path(imagePath)",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime(imageUploadingDateTime)",
                                                                        KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".title(eBookTitle)",
                                                                        KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".path(eBookPath)",
                                                                        KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".uploadingDateTime(eBookUploadingDateTime)",
                                                                        KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name(publisherName)",
                                                                        KAASoftDatabase::$SERIES_TABLE_NAME . ".name(seriesName)", ]),
                                                          $condition);

            if ($queryResult !== false) {
                $books = [];

                foreach ($queryResult as $bookRow) {
                    $book = Book::getObjectInstance($bookRow);
                    if (isset( $bookRow["coverId"] )) {
                        $image = Image::getObjectInstance($bookRow);
                        if (file_exists($image->getAbsolutePath())) {
                            $image->setId($bookRow["coverId"]);
                            $image->setTitle($bookRow["imageTitle"]);
                            $image->setPath($bookRow["imagePath"]);
                            $image->setUploadingDateTime($bookRow["imageUploadingDateTime"]);
                            $book->setCover($image);
                        }
                    }
                    if (isset( $bookRow["eBookId"] )) {
                        $eBook = ElectronicBook::getObjectInstance($bookRow);
                        if (file_exists($eBook->getAbsolutePath())) {
                            $eBook->setId($bookRow["eBookId"]);
                            $eBook->setTitle($bookRow["eBookTitle"]);
                            $eBook->setPath($bookRow["eBookPath"]);
                            $eBook->setUploadingDateTime($bookRow["eBookUploadingDateTime"]);
                            $book->setEBook($eBook);
                        }
                    }

                    if (isset( $bookRow["publisherId"] )) {
                        $publisher = Publisher::getObjectInstance($bookRow);
                        $publisher->setId($bookRow["publisherId"]);
                        $publisher->setName($bookRow["publisherName"]);
                        $book->setPublisher($publisher);
                    }

                    $books[] = $book;
                }

                $bookIds = [];
                foreach ($books as $book) {
                    if ($book instanceof Book) {
                        $bookIds[] = $book->getId();
                    }
                }
                if (count($bookIds) > 0) {
                    $bookAuthors = $this->getBookAuthors($bookIds);
                    $bookGenres = $this->getBookGenres($bookIds);

                    foreach ($books as $book) {
                        if ($book instanceof Book) {
                            $book->setAuthors($bookAuthors[$book->getId()]);
                            $book->setGenres($bookGenres[$book->getId()]);
                        }
                    }
                }

                return $books;
            }

            return null;
        }

        /**
         * @param $bookId
         * @param $storeIds
         * @return bool
         */
        public function saveBookStores($bookId, $storeIds) {
            $result = true;
            foreach ($storeIds as $storeId) {
                $insertResult = $this->kaaSoftDatabase->insert(KAASoftDatabase::$BOOK_STORES_TABLE_NAME,
                                                               [ "bookId"  => $bookId,
                                                                 "storeId" => $storeId ]) !== false;
                $result = ( $result and $insertResult );
            }

            return $result;
        }

        /**
         * @param $bookId
         * @param $locationIds
         * @return bool
         */
        public function saveBookLocations($bookId, $locationIds) {
            $result = true;
            foreach ($locationIds as $locationId) {
                $insertResult = $this->kaaSoftDatabase->insert(KAASoftDatabase::$BOOK_LOCATIONS_TABLE_NAME,
                                                               [ "bookId"     => $bookId,
                                                                 "locationId" => $locationId ]) !== false;
                $result = ( $result and $insertResult );
            }

            return $result;
        }

        /**
         * @param $bookId
         * @param $imageIds
         * @return bool
         */
        public function saveBookImages($bookId, $imageIds) {
            $result = true;
            foreach ($imageIds as $imageId) {
                $insertResult = $this->kaaSoftDatabase->insert(KAASoftDatabase::$BOOK_IMAGES_TABLE_NAME,
                                                               [ "bookId"  => $bookId,
                                                                 "imageId" => $imageId ]) !== false;
                $result = ( $result and $insertResult );
            }

            return $result;
        }

        /**
         * @param $bookId
         * @return bool|int
         */
        public function deleteBookImages($bookId) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$BOOK_IMAGES_TABLE_NAME,
                                                  [ "bookId" => $bookId ]);
        }

        /**
         * @param $bookId
         * @return bool|int
         */
        public function deleteBookStores($bookId) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$BOOK_STORES_TABLE_NAME,
                                                  [ "bookId" => $bookId ]);
        }

        /**
         * @param $bookId
         * @return bool|int
         */
        public function deleteBookLocations($bookId) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$BOOK_LOCATIONS_TABLE_NAME,
                                                  [ "bookId" => $bookId ]);
        }

        /**
         * @param      $bookId
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getBookStores($bookId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [ KAASoftDatabase::$BOOK_STORES_TABLE_NAME . ".bookId" => $bookId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }

            $storesQueryResult = $this->kaaSoftDatabase->distinct()->select(KAASoftDatabase::$BOOK_STORES_TABLE_NAME,
                                                                            [ "[><]" . KAASoftDatabase::$STORES_TABLE_NAME => [ "storeId" => "id" ] ],
                                                                            array_merge(Store::getDatabaseFieldNames(),
                                                                                        [ KAASoftDatabase::$STORES_TABLE_NAME . ".id",
                                                                                          KAASoftDatabase::$BOOK_STORES_TABLE_NAME . ".bookId" ]),
                                                                            $queryParams);

            if ($storesQueryResult !== false) {
                $stores = [];

                foreach ($storesQueryResult as $storeRow) {
                    $store = Store::getObjectInstance($storeRow);

                    $stores[$storeRow["bookId"]][] = $store;
                }

                return $stores;
            }

            return null;
        }

        public function getBookCopies($bookId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = ( ( ValidationHelper::isEmpty($bookId) || ValidationHelper::isArrayEmpty($bookId) ) ? [] : [ KAASoftDatabase::$BOOK_COPIES_TABLE_NAME . ".bookId" => $bookId ] );
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }

            $storesQueryResult = $this->kaaSoftDatabase->distinct()->select(KAASoftDatabase::$BOOK_COPIES_TABLE_NAME,
                                                                            array_merge(BookCopy::getDatabaseFieldNames(),
                                                                                        [ KAASoftDatabase::$BOOK_COPIES_TABLE_NAME . ".id" ]),
                                                                            $queryParams);

            if ($storesQueryResult !== false) {
                $bookCopies = [];

                foreach ($storesQueryResult as $bookCopyRow) {
                    $bookCopy = BookCopy::getObjectInstance($bookCopyRow);

                    $bookCopies[$bookCopyRow["bookId"]][] = $bookCopy;
                }

                return $bookCopies;
            }

            return null;
        }

        /**
         * @param $bookIds
         * @return bool|int
         */
        public function deleteBooks($bookIds) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                  [ "id" => $bookIds ]);

        }

        /**
         * @param $storeId
         * @return bool|int
         */
        public function getStoreBooksCount($storeId) {
            $queryParams = [ KAASoftDatabase::$BOOK_STORES_TABLE_NAME . ".storeId" => $storeId ];

            return $this->kaaSoftDatabase->count(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                 [ "[><]" . KAASoftDatabase::$BOOK_STORES_TABLE_NAME => [ "id" => "bookId" ] ],
                                                 [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" ],
                                                 $queryParams);
        }


        /**
         * @param $locationId
         * @return bool|int
         */
        public function getLocationBooksCount($locationId) {
            $queryParams = [ KAASoftDatabase::$BOOK_LOCATIONS_TABLE_NAME . ".locationId" => $locationId ];

            return $this->kaaSoftDatabase->count(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                 [ "[><]" . KAASoftDatabase::$BOOK_LOCATIONS_TABLE_NAME => [ "id" => "bookId" ] ],
                                                 [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" ],
                                                 $queryParams);
        }

        /**
         * @param $bookId
         * @param $userId
         * @return bool
         */
        public function isBookRatingExists($bookId, $userId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$BOOK_RATINGS_TABLE_NAME,
                                               [ "AND" => [ "bookId" => $bookId,
                                                            "userId" => $userId ] ]);
        }

        /**
         * @param $bookRating BookRating
         * @return array|bool|int
         */
        public function saveBookRating($bookRating) {
            $data = $bookRating->getDatabaseArray();
            if ($bookRating->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$BOOK_RATINGS_TABLE_NAME,
                                                      $data);
            }
            else {
                unset( $data["creationDateTime"] );

                return $this->kaaSoftDatabase->update(KAASoftDatabase::$BOOK_RATINGS_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $bookRating->getId() ]);
            }
        }

        /**
         * @param $bookId int
         * @param $rating float
         * @return bool|int
         */
        public function saveRating($bookId, $rating) {
            return $this->kaaSoftDatabase->update(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                  [ "rating" => $rating ],
                                                  [ "id" => $bookId ]);
        }

        /**
         * @param $bookId
         * @return float|null
         */
        public function getCalculatedBookRating($bookId) {

            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$BOOK_RATINGS_TABLE_NAME,
                                                       [ "{#}SUM(rating) as ratingSum",
                                                         "{#}COUNT(id) as voteCount" ],
                                                       [ "bookId" => $bookId ]);
            if ($queryResult !== false) {
                if ($queryResult["voteCount"] == 0) {
                    return null; //there is no rating at all
                }
                $rating = $queryResult["ratingSum"] / $queryResult["voteCount"];

                return $rating;
            }

            return null;
        }

        /**
         * @param $bookId
         * @return bool|int
         */
        public function getBookRatingVotesNumber($bookId) {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$BOOK_RATINGS_TABLE_NAME,
                                                 [ "bookId" => $bookId ]);
        }

        /**
         * @param $bookId
         * @param $userId
         * @return int|null
         */
        public function getUserBookRating($bookId, $userId) {

            $rating = $this->kaaSoftDatabase->get(KAASoftDatabase::$BOOK_RATINGS_TABLE_NAME,
                                                  "rating",
                                                  [ "AND" => [ "bookId" => $bookId,
                                                               "userId" => $userId ] ]);
            if ($rating !== false) {

                return $rating;
            }

            return null;
        }

        /**
         * @param $bookId
         * @param $userId
         * @return BookRating|null
         */
        public function getBookRating($bookId, $userId) {
            $databaseArray = $this->kaaSoftDatabase->get(KAASoftDatabase::$BOOK_RATINGS_TABLE_NAME,
                                                         array_merge(BookRating::getDatabaseFieldNames(),
                                                                     [ KAASoftDatabase::$BOOK_RATINGS_TABLE_NAME . ".id" ]),
                                                         [ "AND" => [ "bookId" => $bookId,
                                                                      "userId" => $userId ] ]);
            if ($databaseArray !== false) {

                return BookRating::getObjectInstance($databaseArray);
            }

            return null;
        }

        /**
         * @return array|null
         */
        public function getBookUrls() {
            $booksQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                               array_merge([ KAASoftDatabase::$BOOKS_TABLE_NAME . ".url" ]));

            if ($booksQueryResult !== false) {
                $bookUrls = [];

                foreach ($booksQueryResult as $bookRow) {
                    $url = $bookRow["url"];
                    if (!in_array($url,
                                  $bookUrls)
                    ) {
                        $bookUrls[] = $url;
                    }
                }

                return $bookUrls;
            }

            return null;
        }

        /**
         * @param $bookId
         * @param $url
         * @return bool|int
         */
        public function updateBookUrl($bookId, $url) {
            return $this->kaaSoftDatabase->update(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                  [ "url" => $url ],
                                                  [ "id" => $bookId ]);
        }

        /**
         * @param $bookUrl
         * @return Book|null
         */
        public function getBookViaUrl($bookUrl) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                       [ "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME           => [ "coverId" => "id" ],
                                                         "[>]" . KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME => [ "eBookId" => "id" ],
                                                         "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME       => [ "publisherId" => "id" ],
                                                         "[>]" . KAASoftDatabase::$SERIES_TABLE_NAME           => [ "seriesId" => "id" ] ],
                                                       array_merge(Book::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".path(imagePath)",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime(imageUploadingDateTime)",
                                                                     KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".title(eBookTitle)",
                                                                     KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".path(eBookPath)",
                                                                     KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".uploadingDateTime(eBookUploadingDateTime)",
                                                                     KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name(publisherName)",
                                                                     KAASoftDatabase::$SERIES_TABLE_NAME . ".name(seriesName)",
                                                                     "{#}(SELECT COUNT(id) FROM BookRatings where Books.id = BookRatings.bookId) as ratingVotesNumber" ]),
                                                       [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".url" => $bookUrl ]);
            if ($queryResult !== false) {
                $book = Book::getObjectInstance($queryResult);
                $book->setBookRatingVotesNumber($queryResult["ratingVotesNumber"]);

                if (isset( $queryResult["coverId"] )) {
                    $image = Image::getObjectInstance($queryResult);
                    if (file_exists($image->getAbsolutePath())) {
                        $image->setId($queryResult["coverId"]);
                        $image->setTitle($queryResult["imageTitle"]);
                        $image->setPath($queryResult["imagePath"]);
                        $image->setUploadingDateTime($queryResult["imageUploadingDateTime"]);
                        $book->setCover($image);
                    }
                }
                if (isset( $queryResult["eBookId"] )) {
                    $eBook = ElectronicBook::getObjectInstance($queryResult);
                    if (file_exists($eBook->getAbsolutePath())) {
                        $eBook->setId($queryResult["eBookId"]);
                        $eBook->setTitle($queryResult["eBookTitle"]);
                        $eBook->setPath($queryResult["eBookPath"]);
                        $eBook->setUploadingDateTime($queryResult["eBookUploadingDateTime"]);
                        $book->setEBook($eBook);
                    }
                }
                if (isset( $queryResult["seriesId"] )) {
                    $series = Series::getObjectInstance($queryResult);
                    $series->setId($queryResult["seriesId"]);
                    $series->setName($queryResult["seriesName"]);
                    $book->setSeries($series);
                }
                if (isset( $queryResult["publisherId"] )) {
                    $publisher = Publisher::getObjectInstance($queryResult);
                    $publisher->setId($queryResult["publisherId"]);
                    $publisher->setName($queryResult["publisherName"]);
                    $book->setPublisher($publisher);
                }

                $bookAuthors = $this->getBookAuthors($book->getId());
                $book->setAuthors($bookAuthors[$book->getId()]);

                $bookGenres = $this->getBookGenres($book->getId());
                $book->setGenres($bookGenres[$book->getId()]);

                $bookTags = $this->getBookTags($book->getId());
                $book->setTags($bookTags[$book->getId()]);

                $bookStores = $this->getBookStores($book->getId());
                $book->setStores($bookStores[$book->getId()]);

                $bookLocations = $this->getBookLocations($book->getId());
                $book->setLocations($bookLocations[$book->getId()]);

                $bookImages = $this->getBookImages($book->getId());
                $book->setImages($bookImages);

                return $book;

            }

            return null;
        }

        /**
         * @return bool|int
         */
        public function cleanBookUrls() {
            return $this->kaaSoftDatabase->update(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                  [ "url" => null ]);
        }

        /**
         * @param $tagId
         * @return bool|int
         */
        public function getTagBooksCount($tagId) {
            $queryParams = [ KAASoftDatabase::$BOOK_TAGS_TABLE_NAME . ".tagId" => $tagId ];

            return $this->kaaSoftDatabase->distinct()->count(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                             [ "[><]" . KAASoftDatabase::$BOOK_TAGS_TABLE_NAME => [ "id" => "bookId" ] ],
                                                             [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" ],
                                                             $queryParams);
        }

        /**
         * @param      $tagId
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getTagBooks($tagId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [ KAASoftDatabase::$BOOK_TAGS_TABLE_NAME . ".tagId" => $tagId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }

            $booksQueryResult = $this->kaaSoftDatabase->distinct()->select(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                                           [ "[><]" . KAASoftDatabase::$BOOK_TAGS_TABLE_NAME => [ "id" => "bookId" ],
                                                                             "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME     => [ "coverId" => "id" ],
                                                                             "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME => [ "publisherId" => "id" ] ],
                                                                           array_merge(Book::getDatabaseFieldNames(),
                                                                                       [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id",
                                                                                         KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                                         KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                                         KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime",
                                                                                         KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name(publisherName)",
                                                                                         "{#}(SELECT COUNT(id) FROM BookRatings where Books.id = BookRatings.bookId) as ratingVotesNumber" ]),
                                                                           $queryParams);

            if ($booksQueryResult !== false) {
                $books = [];

                foreach ($booksQueryResult as $bookRow) {
                    $book = Book::getObjectInstance($bookRow);
                    $book->setBookRatingVotesNumber($bookRow["ratingVotesNumber"]);

                    if (isset( $bookRow["coverId"] )) {
                        $image = Image::getObjectInstance($bookRow);
                        if (file_exists($image->getAbsolutePath())) {
                            $image->setId($bookRow["coverId"]);
                            $image->setTitle($bookRow["imageTitle"]);
                            $book->setCover($image);
                        }
                    }

                    if (isset( $bookRow["publisherId"] )) {
                        $publisher = Publisher::getObjectInstance($bookRow);
                        $publisher->setId($bookRow["publisherId"]);
                        $publisher->setName($bookRow["publisherName"]);
                        $book->setPublisher($publisher);
                    }

                    //$book->setAuthors($this->getBookAuthors($book->getId()));

                    $books[] = $book;
                }

                $bookIds = [];
                foreach ($books as $book) {
                    if ($book instanceof Book) {
                        $bookIds[] = $book->getId();
                    }
                }
                if (count($bookIds) > 0) {
                    $bookAuthors = $this->getBookAuthors($bookIds);

                    foreach ($books as $book) {
                        if ($book instanceof Book) {
                            $book->setAuthors($bookAuthors[$book->getId()]);
                        }
                    }
                }

                return $books;
            }

            return null;
        }

        /**
         * @param $bookId
         * @return bool|int
         */
        public function getBookIssueLogsCount($bookId) {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME,
                                                 [ "bookId" => $bookId ]);
        }

        /**
         * @param $userId
         * @return bool|int
         */
        public function getUserIssueLogsCount($userId) {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME,
                                                 [ "userId" => $userId ]);
        }

        /**
         * @param $url
         * @return bool
         */
        public function isBookUrlExist($url) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                               [ "url" => $url ]);
        }

        /**
         * @param      $bookId
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        private function getBookLocations($bookId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [ KAASoftDatabase::$BOOK_LOCATIONS_TABLE_NAME . ".bookId" => $bookId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }

            $locationsQueryResult = $this->kaaSoftDatabase->distinct()->select(KAASoftDatabase::$BOOK_LOCATIONS_TABLE_NAME,
                                                                               [ "[><]" . KAASoftDatabase::$LOCATIONS_TABLE_NAME => [ "locationId" => "id" ],
                                                                                 "[><]" . KAASoftDatabase::$STORES_TABLE_NAME    => [ KAASoftDatabase::$LOCATIONS_TABLE_NAME . ".storeId" => "id" ] ],
                                                                               array_merge(Location::getDatabaseFieldNames(),
                                                                                           [ KAASoftDatabase::$LOCATIONS_TABLE_NAME . ".id",
                                                                                             KAASoftDatabase::$BOOK_LOCATIONS_TABLE_NAME . ".bookId",
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

                    $locations[$locationRow["bookId"]][] = $location;
                }

                return $locations;
            }

            return null;
        }

        /**
         * @param      $bookId
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        private function getBookImages($bookId, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [ KAASoftDatabase::$BOOK_IMAGES_TABLE_NAME . ".bookId" => $bookId ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                 "LIMIT" => [ (int)$offset,
                                              (int)$perPage ] ];
            }

            $imagesQueryResult = $this->kaaSoftDatabase->distinct()->select(KAASoftDatabase::$BOOK_IMAGES_TABLE_NAME,
                                                                            [ "[><]" . KAASoftDatabase::$IMAGES_TABLE_NAME => [ "imageId" => "id" ] ],
                                                                            array_merge(Image::getDatabaseFieldNames(),
                                                                                        [ KAASoftDatabase::$IMAGES_TABLE_NAME . ".id" ]),
                                                                            $queryParams);

            if ($imagesQueryResult !== false) {
                $images = [];

                foreach ($imagesQueryResult as $imageRow) {
                    $image = Image::getObjectInstance($imageRow);

                    $images[] = $image;
                }

                return $images;
            }

            return null;
        }

        /**
         * @param int $bookLimit
         * @return Book[]
         */
        public function getRandomBooks($bookLimit = 4) {
            $books = [];
            if ($bookLimit !== null) {
                $query = sprintf("CALL getRandomBooks( %d)",
                                 $bookLimit);
                $pdo = $this->kaaSoftDatabase->getPDO();

                $stmt = $pdo->query($query);
                if ($stmt) {
                    $dataBaseRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($dataBaseRows !== false) {
                        foreach ($dataBaseRows as $authorRow) {
                            $book = Book::getObjectInstance($authorRow);

                            if (isset( $authorRow["coverId"] )) {
                                $image = Image::getObjectInstance($authorRow);
                                if (file_exists($image->getAbsolutePath())) {
                                    $image->setId($authorRow["coverId"]);
                                    $image->setTitle($authorRow["imageTitle"]);
                                    $book->setCover($image);
                                }
                            }

                            $books [] = $book;
                        }

                        $stmt = null;
                    }
                }
            }

            return $books;
        }
    }