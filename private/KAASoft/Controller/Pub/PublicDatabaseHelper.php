<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub;

    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\General\Author;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Database\Entity\General\DatabaseField;
    use KAASoft\Database\Entity\General\ElectronicBook;
    use KAASoft\Database\Entity\General\Genre;
    use KAASoft\Database\Entity\General\Publisher;
    use KAASoft\Database\Entity\General\Series;
    use KAASoft\Database\Entity\Post\Page;
    use KAASoft\Database\Entity\Post\Post;
    use KAASoft\Database\Entity\Util\Image;
    use KAASoft\Database\Entity\Util\Tag;
    use KAASoft\Database\Entity\Util\UserMessage;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\BookFilterQuery;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class PublicDatabaseHelper
     * @package KAASoft\Controller\Pub
     */
    class PublicDatabaseHelper extends DatabaseHelper {
        /**
         * @param      $searchText
         * @param null $limit
         * @return array
         */
        public function searchAuthors($searchText, $limit = null) {
            $result = [];
            $searchCondition = [];
            $limitCondition = [];
            if ($limit !== null) {
                $limitCondition = [ "LIMIT" => $limit ];
            }

            $keywords = preg_split("/[\s,]+/",
                                   $searchText);
            $counter = 0;
            foreach ($keywords as $keyword) {
                $searchCondition = array_merge($searchCondition,
                                               [ "OR " . $counter => [ KAASoftDatabase::$AUTHORS_TABLE_NAME . ".firstName[~]"  => $keyword,
                                                                       KAASoftDatabase::$AUTHORS_TABLE_NAME . ".lastName[~]"   => $keyword,
                                                                       KAASoftDatabase::$AUTHORS_TABLE_NAME . ".middleName[~]" => $keyword ] ]);
                $counter++;
            }


            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$AUTHORS_TABLE_NAME,
                                                          array_merge(Author::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$AUTHORS_TABLE_NAME . ".id" ]),
                                                          array_merge([ "AND" => $searchCondition ],
                                                                      $limitCondition));

            if ($queryResult !== false) {

                foreach ($queryResult as $row) {
                    $author = Author::getObjectInstance($row);
                    $result[] = $author;
                }
            }

            return $result;
        }

        /**
         * @param      $searchText
         * @param null $limit
         * @return array
         */
        public function searchPublishers($searchText, $limit = null) {
            $result = [];
            $searchCondition = [];
            $limitCondition = [];
            if ($limit !== null) {
                $limitCondition = [ "LIMIT" => $limit ];
            }

            $keywords = preg_split("/[\s,]+/",
                                   $searchText);
            $counter = 0;
            foreach ($keywords as $keyword) {
                $searchCondition = array_merge($searchCondition,
                                               [ "OR " . $counter => [ KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name[~]" => $keyword ] ]);
                $counter++;
            }


            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$PUBLISHERS_TABLE_NAME,
                                                          array_merge(Publisher::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".id" ]),
                                                          array_merge([ "AND" => $searchCondition ],
                                                                      $limitCondition));

            if ($queryResult !== false) {
                foreach ($queryResult as $row) {
                    $publisher = Publisher::getObjectInstance($row);
                    $result[] = $publisher;
                }

            }

            return $result;
        }

        /**
         * @param      $searchText
         * @param null $limit
         * @return array
         */
        public function searchSeries($searchText, $limit = null) {
            $result = [];
            $searchCondition = [];
            $limitCondition = [];
            if ($limit !== null) {
                $limitCondition = [ "LIMIT" => $limit ];
            }

            $keywords = preg_split("/[\s,]+/",
                                   $searchText);
            $counter = 0;
            foreach ($keywords as $keyword) {
                $searchCondition = array_merge($searchCondition,
                                               [ "OR " . $counter => [ KAASoftDatabase::$SERIES_TABLE_NAME . ".name[~]" => $keyword ] ]);
                $counter++;
            }


            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$SERIES_TABLE_NAME,
                                                          array_merge(Series::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$SERIES_TABLE_NAME . ".id" ]),
                                                          array_merge([ "AND" => $searchCondition ],
                                                                      $limitCondition));

            if ($queryResult !== false) {
                foreach ($queryResult as $row) {
                    $series = Series::getObjectInstance($row);
                    $result[] = $series;
                }
            }

            return $result;
        }

        public function searchGenres($searchText, $limit = null) {
            $result = [];
            $searchCondition = [];
            $limitCondition = [];
            if ($limit !== null) {
                $limitCondition = [ "LIMIT" => $limit ];
            }

            $keywords = preg_split("/[\s,]+/",
                                   $searchText);
            $counter = 0;
            foreach ($keywords as $keyword) {
                $searchCondition = array_merge($searchCondition,
                                               [ "OR " . $counter => [ KAASoftDatabase::$GENRES_TABLE_NAME . ".name[~]" => $keyword ] ]);
                $counter++;
            }


            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$GENRES_TABLE_NAME,
                                                          array_merge(Genre::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$GENRES_TABLE_NAME . ".id" ]),
                                                          array_merge([ "AND" => $searchCondition ],
                                                                      $limitCondition));

            if ($queryResult !== false) {
                foreach ($queryResult as $row) {
                    $genre = Genre::getObjectInstance($row);
                    $result[] = $genre;
                }
            }

            return $result;
        }

        public function searchBooks($searchText, $isNeedBookCopies = false, $offset = null, $perPage = null, $sortBy = "Books.id", $sortOrder = "DESC") {
            $result = [];
            $searchCondition = [];
            $limitCondition = [];

            if ($sortBy != null && $sortOrder != null) {
                $limitCondition = array_merge($limitCondition,
                                              [ "ORDER" => [ $sortBy => $sortOrder ] ]);
            }
            if ($offset !== null && $perPage !== null) {
                $limitCondition = array_merge($limitCondition,
                                              [ "LIMIT" => [ (int)$offset,
                                                             (int)$perPage ] ]);
            }


            $keywords = preg_split("/[\s,]+/",
                                   $searchText);
            $keywords = str_replace("-",
                                    "",
                                    $keywords);
            $counter = 0;
            foreach ($keywords as $keyword) {
                $searchCondition = array_merge($searchCondition,
                                               [ "OR " . $counter => [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".ISBN10[~]"       => $keyword,
                                                                       KAASoftDatabase::$BOOKS_TABLE_NAME . ".ISBN13[~]"       => $keyword,
                                                                       KAASoftDatabase::$BOOKS_TABLE_NAME . ".title[~]"        => $keyword,
                                                                       KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name[~]"    => $keyword,
                                                                       KAASoftDatabase::$AUTHORS_TABLE_NAME . ".firstName[~]"  => $keyword,
                                                                       KAASoftDatabase::$AUTHORS_TABLE_NAME . ".middleName[~]" => $keyword,
                                                                       KAASoftDatabase::$AUTHORS_TABLE_NAME . ".lastName[~]"   => $keyword,
                                                                       KAASoftDatabase::$GENRES_TABLE_NAME . ".name"           => $keyword ] ]);

                $counter++;
            }

            $queryResult = $this->kaaSoftDatabase->distinct()->select(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                                      [ "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME           => [ "coverId" => "id" ],
                                                                        "[>]" . KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME => [ "eBookId" => "id" ],
                                                                        "[>]" . KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME     => [ "id" => "bookId" ],
                                                                        "[>]" . KAASoftDatabase::$AUTHORS_TABLE_NAME          => [ KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME . ".authorId" => "id" ],
                                                                        "[>]" . KAASoftDatabase::$BOOK_GENRES_TABLE_NAME      => [ "id" => "bookId" ],
                                                                        "[>]" . KAASoftDatabase::$GENRES_TABLE_NAME           => [ KAASoftDatabase::$BOOK_GENRES_TABLE_NAME . ".genreId" => "id" ],
                                                                        "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME       => [ "publisherId" => "id" ] ],

                                                                      [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" ],
                                                                      array_merge([ "AND" => $searchCondition ],
                                                                                  $limitCondition));
            if ($queryResult !== false) {
                $bookIds = [];
                foreach ($queryResult as $bookIdRow) {
                    $bookIds [] = $bookIdRow["id"];
                }
                if (count($bookIds) == 0) {
                    return $result;
                }

                $queryResult = $this->kaaSoftDatabase->distinct()->select(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                                          [ "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME           => [ "coverId" => "id" ],
                                                                            "[>]" . KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME => [ "eBookId" => "id" ],
                                                                            "[>]" . KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME     => [ "id" => "bookId" ],
                                                                            "[>]" . KAASoftDatabase::$AUTHORS_TABLE_NAME          => [ KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME . ".authorId" => "id" ],
                                                                            "[>]" . KAASoftDatabase::$BOOK_GENRES_TABLE_NAME      => [ "id" => "bookId" ],
                                                                            "[>]" . KAASoftDatabase::$GENRES_TABLE_NAME           => [ KAASoftDatabase::$BOOK_GENRES_TABLE_NAME . ".genreId" => "id" ],
                                                                            "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME       => [ "publisherId" => "id" ] ],
                                                                          array_merge(Book::getDatabaseFieldNames(),
                                                                                      [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id",
                                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".path(imagePath)",
                                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime(imageUploadingDateTime)",
                                                                                        KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".title(eBookTitle)",
                                                                                        KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".path(eBookPath)",
                                                                                        KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".uploadingDateTime(eBookUploadingDateTime)",
                                                                                        KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name(publisherName)", ]),
                                                                          [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" => $bookIds,
                                                                            "ORDER"                                    => ( $sortBy === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortBy => "ASC" ] : [ $sortBy => $sortOrder ] ) ) ]);

                if ($queryResult !== false) {
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

                        $result[] = $book;
                    }

                    $bookIds = [];
                    foreach ($result as $book) {
                        if ($book instanceof Book) {
                            $bookIds[] = $book->getId();
                        }
                    }
                    if (count($bookIds) > 0) {
                        $bookHelper = new BookDatabaseHelper($this->action);
                        $bookAuthors = $bookHelper->getBookAuthors($bookIds);
                        $bookGenres = $bookHelper->getBookGenres($bookIds);
                        //$bookStores = $bookHelper->getBookStores($bookIds);

                        if ($isNeedBookCopies) {
                            $bookCopies = $bookHelper->getBookCopies($bookIds);
                        }
                        foreach ($result as $book) {
                            if ($book instanceof Book) {
                                $book->setAuthors($bookAuthors[$book->getId()]);
                                $book->setGenres($bookGenres[$book->getId()]);
                                // $book->setStores($bookStores[$book->getId()]);
                                if (isset( $bookCopies )) {
                                    $book->setBookCopies($bookCopies[$book->getId()]);
                                }
                            }
                        }
                    }
                }
            }

            return $result;
        }

        /**
         * @param      $query BookFilterQuery
         * @param bool $countOnly
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|int
         */
        public function filterBooks($query, $countOnly = false, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $result = [];
            $searchCondition = [];
            $limitCondition = [];
            $joinTables = [ "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME     => [ "coverId" => "id" ],
                            "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME => [ "publisherId" => "id" ] ];

            $selectedFields = [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" ];
            $orCounter = 0;

            // set result limit
            if ($offset !== null && $perPage !== null) {
                $limitCondition = [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                    "LIMIT" => [ (int)$offset,
                                                 (int)$perPage ] ];
            }

            // custom book fields
            if (!ValidationHelper::isArrayEmpty($query->getCustomFields())) {
                foreach ($query->getCustomFields() as $fieldName => $searchValue) {
                    if (!ValidationHelper::isEmpty($searchValue)) {

                        $searchWords = preg_split("/[\s,]+/",
                                                  $searchValue);
                        $searchWords = str_replace("-",
                                                   "",
                                                   $searchWords);
                        $searchTextCondition = [];
                        $counter = 0;
                        foreach ($searchWords as $searchWord) {

                            $operation = "";
                            $customField = Book::getCustomField($fieldName);

                            switch ($customField->getType()) {
                                case DatabaseField::SQL_DATABASE_TYPES [DatabaseField::SQL_TYPE_STRING]:
                                case DatabaseField::SQL_DATABASE_TYPES [DatabaseField::SQL_TYPE_LONGTEXT]:
                                case DatabaseField::SQL_DATABASE_TYPES [DatabaseField::SQL_TYPE_INTEGER]:
                                case DatabaseField::SQL_DATABASE_TYPES [DatabaseField::SQL_TYPE_FLOAT]:
                                    $operation = "[~]";
                                    break;

                                case DatabaseField::SQL_DATABASE_TYPES [DatabaseField::SQL_TYPE_BOOL]:
                                    $operation = "";
                                    break;
                            }
                            $searchTextCondition = array_merge($searchTextCondition,
                                                               [ "OR " . $counter => [ KAASoftDatabase::$BOOKS_TABLE_NAME . "." . $fieldName . $operation => $searchWord ] ]);

                            $counter++;
                        }

                        $searchCondition = array_merge($searchCondition,
                                                       [ "OR" . $orCounter => $searchTextCondition ]);

                        $orCounter++;
                    }
                }
            }

            // set authors
            if ($query->getAuthorIds() !== null and count($query->getAuthorIds()) > 0) {
                $counter = 0;
                $authorCondition = [];
                $joinTables = array_merge($joinTables,
                                          [ "[>]" . KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME => [ "id" => "bookId" ] ]);


                foreach ($query->getAuthorIds() as $authorId) {
                    $authorCondition = array_merge($authorCondition,
                                                   [ "OR " . $counter => [ KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME . ".authorId" => $authorId ] ]);
                    $counter++;
                }

                $searchCondition = array_merge($searchCondition,
                                               [ "OR" . $orCounter => $authorCondition ]);
                $orCounter++;
            }

            // set genres
            if ($query->getGenreIds() !== null and count($query->getGenreIds()) > 0) {
                $counter = 0;
                $genreCondition = [];
                $joinTables = array_merge($joinTables,
                                          [ "[>]" . KAASoftDatabase::$BOOK_GENRES_TABLE_NAME => [ "id" => "bookId" ] ]);
                foreach ($query->getGenreIds() as $genreId) {
                    $genreCondition = array_merge($genreCondition,
                                                  [ "OR " . $counter => [ KAASoftDatabase::$BOOK_GENRES_TABLE_NAME . ".genreId" => $genreId ] ]);
                    $counter++;
                }

                $searchCondition = array_merge($searchCondition,
                                               [ "OR" . $orCounter => $genreCondition ]);
                $orCounter++;
            }

            // set bindings
            if ($query->getBindings() !== null and count($query->getBindings()) > 0) {
                $counter = 0;
                $bindingsCondition = [];
                foreach ($query->getBindings() as $binding) {
                    $bindingsCondition = array_merge($bindingsCondition,
                                                     [ "OR " . $counter => [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".binding" => $binding ] ]);
                    $counter++;
                }

                $searchCondition = array_merge($searchCondition,
                                               [ "OR" . $orCounter => $bindingsCondition ]);
                $orCounter++;
            }
            // set owners
            if ($query->getOwnerIds() !== null and count($query->getOwnerIds()) > 0) {
                $counter = 0;
                $ownerCondition = [];
                foreach ($query->getOwnerIds() as $ownerId) {
                    $ownerCondition = array_merge($ownerCondition,
                                                  [ "OR " . $counter => [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".owner" => $ownerId ] ]);
                    $counter++;
                }

                $searchCondition = array_merge($searchCondition,
                                               [ "OR" . $orCounter => $ownerCondition ]);
                $orCounter++;
            }

            // set publishers
            if ($query->getPublisherIds() !== null and count($query->getPublisherIds()) > 0) {
                $counter = 0;
                $publisherCondition = [];
                foreach ($query->getPublisherIds() as $publisherId) {
                    $publisherCondition = array_merge($publisherCondition,
                                                      [ "OR " . $counter => [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".publisherId" => $publisherId ] ]);
                    $counter++;
                }

                $searchCondition = array_merge($searchCondition,
                                               [ "OR" . $orCounter => $publisherCondition ]);
                $orCounter++;
            }

            // set series
            if ($query->getSeriesIds() !== null and count($query->getSeriesIds()) > 0) {
                $counter = 0;
                $seriesCondition = [];
                foreach ($query->getSeriesIds() as $seriesId) {
                    $seriesCondition = array_merge($seriesCondition,
                                                   [ "OR " . $counter => [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".seriesId" => $seriesId ] ]);
                    $counter++;
                }
                $searchCondition = array_merge($searchCondition,
                                               [ "OR" . $orCounter => $seriesCondition ]);
                //$orCounter++;
            }

            // set years
            if ($query->getStartYear() !== null) {
                $searchCondition = array_merge($searchCondition,
                                               [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".publishingYear[>=]" => $query->getStartYear() ]);
            }
            if ($query->getEndYear() !== null) {
                $searchCondition = array_merge($searchCondition,
                                               [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".publishingYear[<=]" => $query->getEndYear() ]);
            }

            $sqlSelectionType = $countOnly ? "count" : "select";
            $queryResult = $this->kaaSoftDatabase->distinct()->$sqlSelectionType(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                                                 $joinTables,
                                                                                 $selectedFields,
                                                                                 array_merge(count($searchCondition) == 0 ? [] : [ "AND" => $searchCondition ],
                                                                                             $limitCondition));
            if ($countOnly) {
                return $queryResult;
            }

            if ($queryResult !== false) {

                $bookIds = [];
                foreach ($queryResult as $bookIdRow) {
                    $bookIds [] = $bookIdRow["id"];
                }
                if (count($bookIds) == 0) {
                    return $result;
                }

                $queryResult = $this->kaaSoftDatabase->distinct()->$sqlSelectionType(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                                                     $joinTables,
                                                                                     array_merge($selectedFields,
                                                                                                 [ KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                                                   KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                                                   KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime",
                                                                                                   KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name(publisherName)",
                                                                                                   "{#}(SELECT COUNT(id) FROM BookRatings where Books.id = BookRatings.bookId) as ratingVotesNumber" ],
                                                                                                 Book::getDatabaseFieldNames()),
                                                                                     [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" => $bookIds,
                                                                                       "ORDER"                                    => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ) ]);

                foreach ($queryResult as $bookRow) {
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

                    $result[] = $book;
                }

                $bookIds = [];
                foreach ($result as $book) {
                    if ($book instanceof Book) {
                        $bookIds[] = $book->getId();
                    }
                }
                if (count($bookIds) > 0) {
                    $bookDatabaseHelper = new BookDatabaseHelper($this->action);
                    $bookAuthors = $bookDatabaseHelper->getBookAuthors($bookIds);

                    foreach ($result as $book) {
                        if ($book instanceof Book) {
                            $book->setAuthors($bookAuthors[$book->getId()]);
                        }
                    }
                }
            }

            return $result;

        }

        /**
         * @param      $searchText
         * @param null $limit
         * @return array|null
         */
        public function searchPosts($searchText, $limit = null) {
            $searchCondition = [];
            $limitCondition = [];
            if ($limit !== null) {
                $limitCondition = [ "LIMIT" => $limit ];
            }

            $keywords = preg_split("/[\s,]+/",
                                   $searchText);
            $counter = 0;
            foreach ($keywords as $keyword) {
                $searchCondition = array_merge($searchCondition,
                                               [ "OR " . $counter => [ KAASoftDatabase::$POSTS_TABLE_NAME . ".title[~]"   => $keyword,
                                                                       KAASoftDatabase::$POSTS_TABLE_NAME . ".content[~]" => $keyword ] ]);
                $counter++;
            }


            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$POSTS_TABLE_NAME,
                                                          [ "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME => [ "imageId" => "id" ] ],
                                                          array_merge([ KAASoftDatabase::$IMAGES_TABLE_NAME . ".id(imageId)",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime",
                                                                        KAASoftDatabase::$POSTS_TABLE_NAME . ".id" ],
                                                                      Post::getDatabaseFieldNames()),

                                                          array_merge([ "AND" => $searchCondition ],
                                                                      $limitCondition));

            if ($queryResult !== false) {
                $result = [];
                foreach ($queryResult as $row) {
                    $post = Post::getObjectInstance($row);

                    if ($row["imageId"] != 0) {
                        $image = Image::getObjectInstance($row);
                        if (file_exists($image->getAbsolutePath())) {
                            $image->setId($row["imageId"]);
                            $image->setTitle($row["imageTitle"]);
                            $post->setImage($image);
                        }
                    }

                    $result[] = $post;
                }

                return $result;
            }

            return null;
        }

        /**
         * @param      $searchText
         * @param null $limit
         * @return array|null
         */
        public function searchPages($searchText, $limit = null) {
            $searchCondition = [];
            $limitCondition = [];
            if ($limit !== null) {
                $limitCondition = [ "LIMIT" => $limit ];
            }

            $keywords = preg_split("/[\s,]+/",
                                   $searchText);
            $counter = 0;
            foreach ($keywords as $keyword) {
                $searchCondition = array_merge($searchCondition,
                                               [ "OR " . $counter => [ KAASoftDatabase::$PAGES_TABLE_NAME . ".title[~]"   => $keyword,
                                                                       KAASoftDatabase::$PAGES_TABLE_NAME . ".content[~]" => $keyword ] ]);
                $counter++;
            }


            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$PAGES_TABLE_NAME,
                                                          [ "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME => [ "imageId" => "id" ] ],
                                                          array_merge([ KAASoftDatabase::$PAGES_TABLE_NAME . ".id",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".id(imageId)",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime" ],
                                                                      Page::getDatabaseFieldNames()),
                                                          array_merge([ "AND" => $searchCondition ],
                                                                      $limitCondition));

            if ($queryResult !== false) {
                $result = [];
                foreach ($queryResult as $row) {
                    $page = Page::getObjectInstance($row);

                    if ($row["imageId"] != 0) {
                        $image = Image::getObjectInstance($row);
                        if (file_exists($image->getAbsolutePath())) {
                            $image->setId($row["imageId"]);
                            $image->setTitle($row["imageTitle"]);
                            $page->setImage($image);
                        }
                    }

                    $result[] = $page;
                }

                return $result;
            }

            return null;
        }

        /**
         * @param      $searchText
         * @param null $limit
         * @return array|null
         */
        public function searchPagesByTitle($searchText, $limit = null) {
            $searchCondition = [];
            $limitCondition = [];
            if ($limit !== null) {
                $limitCondition = [ "LIMIT" => $limit ];
            }

            $keywords = preg_split("/[\s,]+/",
                                   $searchText);
            $counter = 0;
            foreach ($keywords as $keyword) {
                $searchCondition = array_merge($searchCondition,
                                               [ "OR " . $counter => [ KAASoftDatabase::$PAGES_TABLE_NAME . ".title[~]" => $keyword ] ]);
                $counter++;
            }


            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$PAGES_TABLE_NAME,
                                                          [ KAASoftDatabase::$PAGES_TABLE_NAME . ".id",
                                                            KAASoftDatabase::$PAGES_TABLE_NAME . ".title" ],
                                                          array_merge([ "AND" => $searchCondition ],
                                                                      $limitCondition,
                                                                      [ "ORDER" => [ KAASoftDatabase::$PAGES_TABLE_NAME . ".title" => "ASC" ] ]));

            if ($queryResult !== false) {

                return $queryResult;
            }

            return null;
        }

        /**
         * @param      $searchText
         * @param null $limit
         * @return array|bool|null
         */
        public function searchPostsByTitle($searchText, $limit = null) {
            $searchCondition = [];
            $limitCondition = [];
            if ($limit !== null) {
                $limitCondition = [ "LIMIT" => $limit ];
            }

            $keywords = preg_split("/[\s,]+/",
                                   $searchText);
            $counter = 0;
            foreach ($keywords as $keyword) {
                $searchCondition = array_merge($searchCondition,
                                               [ "OR " . $counter => [ KAASoftDatabase::$POSTS_TABLE_NAME . ".title[~]" => $keyword ] ]);
                $counter++;
            }


            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$POSTS_TABLE_NAME,
                                                          [ KAASoftDatabase::$POSTS_TABLE_NAME . ".id",
                                                            KAASoftDatabase::$POSTS_TABLE_NAME . ".title" ],
                                                          array_merge([ "AND" => $searchCondition ],
                                                                      $limitCondition));

            if ($queryResult !== false) {
                return $queryResult;
            }

            return null;
        }

        /**
         * @param UserMessage $userMessage
         * @return array|bool|int
         */
        public function saveUserMessage($userMessage) {
            $data = $userMessage->getDatabaseArray();
            if ($userMessage->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$USER_MESSAGES_TABLE_NAME,
                                                      $data);
            }
            else {
                unset( $data["creationDate"] );

                return $this->kaaSoftDatabase->update(KAASoftDatabase::$USER_MESSAGES_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $userMessage->getId() ]);
            }
        }

        /**
         * @param $searchText
         * @return bool|int
         */
        public function getBookSearchCount($searchText) {
            $searchCondition = [];

            $keywords = preg_split("/[\s,]+/",
                                   $searchText);
            $keywords = str_replace("-",
                                    "",
                                    $keywords);
            $counter = 0;
            foreach ($keywords as $keyword) {
                $searchCondition = array_merge($searchCondition,
                                               [ "OR " . $counter => [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".ISBN10[~]"       => $keyword,
                                                                       KAASoftDatabase::$BOOKS_TABLE_NAME . ".ISBN13[~]"       => $keyword,
                                                                       KAASoftDatabase::$BOOKS_TABLE_NAME . ".title[~]"        => $keyword,
                                                                       KAASoftDatabase::$PUBLISHERS_TABLE_NAME . ".name[~]"    => $keyword,
                                                                       KAASoftDatabase::$AUTHORS_TABLE_NAME . ".firstName[~]"  => $keyword,
                                                                       KAASoftDatabase::$AUTHORS_TABLE_NAME . ".middleName[~]" => $keyword,
                                                                       KAASoftDatabase::$AUTHORS_TABLE_NAME . ".lastName[~]"   => $keyword ] ]);
                $counter++;
            }

            return $this->kaaSoftDatabase->distinct()->count(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                             [ "[>]" . KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME => [ "id" => "bookId" ],
                                                               "[>]" . KAASoftDatabase::$AUTHORS_TABLE_NAME      => [ KAASoftDatabase::$BOOK_AUTHORS_TABLE_NAME . ".authorId" => "id" ],
                                                               "[>]" . KAASoftDatabase::$PUBLISHERS_TABLE_NAME   => [ "publisherId" => "id" ] ],
                                                             [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id" ],
                                                             [ "AND" => $searchCondition ]);
        }

        /**
         * @param      $searchText
         * @param null $limit
         * @return array
         */
        public function searchTags($searchText, $limit = null) {
            $result = [];
            $searchCondition = [];
            $limitCondition = [];
            if ($limit !== null) {
                $limitCondition = [ "LIMIT" => $limit ];
            }

            $keywords = preg_split("/[\s,]+/",
                                   $searchText);
            $counter = 0;
            foreach ($keywords as $keyword) {
                $searchCondition = array_merge($searchCondition,
                                               [ "OR " . $counter => [ KAASoftDatabase::$TAGS_TABLE_NAME . ".name[~]" => $keyword ] ]);
                $counter++;
            }


            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$TAGS_TABLE_NAME,
                                                          array_merge(Tag::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$TAGS_TABLE_NAME . ".id" ]),
                                                          array_merge([ "AND" => $searchCondition ],
                                                                      $limitCondition));

            if ($queryResult !== false) {
                foreach ($queryResult as $row) {
                    $tag = Tag::getObjectInstance($row);
                    $result[] = $tag;
                }
            }

            return $result;
        }
    }