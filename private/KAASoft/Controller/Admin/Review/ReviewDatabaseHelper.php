<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Review;

    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Database\Entity\General\Review;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Database\KAASoftDatabase;

    /**
     * Class ReviewDatabaseHelper
     * @package KAASoft\Controller\Admin\Review
     */
    class ReviewDatabaseHelper extends DatabaseHelper {

        /**
         * @param null $bookId
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getReviews($bookId = null, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = ( $bookId === null ? [] : [ "AND" => [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".id"          => $bookId,
                                                                  KAASoftDatabase::$REVIEWS_TABLE_NAME . ".isPublish" => true ] ] );
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => ( $sortColumn === null ? [ KAASoftDatabase::$REVIEWS_TABLE_NAME . ".creationDateTime" => "DESC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }

            $reviewsQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$REVIEWS_TABLE_NAME,
                                                                 [ "[><]" . KAASoftDatabase::$BOOKS_TABLE_NAME       => [ "bookId" => "id" ],
                                                                   "[>]" . KAASoftDatabase::$USERS_TABLE_NAME        => [ "userId" => "id" ],
                                                                   "[>]" . KAASoftDatabase::$BOOK_RATINGS_TABLE_NAME => [ "userId" => "userId",
                                                                                                                          "bookId" => "bookId" ], ],
                                                                 array_merge(Review::getDatabaseFieldNames(),
                                                                             User::getDatabaseFieldNames(),
                                                                             Book::getDatabaseFieldNames(),
                                                                             [ KAASoftDatabase::$REVIEWS_TABLE_NAME . ".id",
                                                                               KAASoftDatabase::$BOOK_RATINGS_TABLE_NAME . ".rating(bookRating)",
                                                                               KAASoftDatabase::$REVIEWS_TABLE_NAME . ".email(reviewEmail)",
                                                                               KAASoftDatabase::$REVIEWS_TABLE_NAME . ".creationDateTime(reviewCreationDateTime)" ]),
                                                                 $queryParams);

            if ($reviewsQueryResult !== false) {
                $reviews = [];

                foreach ($reviewsQueryResult as $reviewRow) {
                    $review = Review::getObjectInstance($reviewRow);
                    $review->setEmail($reviewRow["reviewEmail"]);
                    $review->setCreationDateTime($reviewRow["reviewCreationDateTime"]);

                    if ($review->getUserId() !== null) {
                        $user = User::getObjectInstance($reviewRow);
                        $user->setId($review->getUserId());
                        $review->setUser($user);
                    }

                    if (isset( $reviewRow["bookRating"] )) {
                        $review->setBookRating($reviewRow["bookRating"]);
                    }

                    $book = Book::getObjectInstance($reviewRow);
                    $book->setId($review->getBookId());
                    $review->setBook($book);

                    $reviews[] = $review;
                }

                return $reviews;
            }

            return null;
        }

        /**
         * @return bool|int
         */
        public function getReviewsCount() {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$REVIEWS_TABLE_NAME);
        }

        /**
         * @param $reviewId
         * @return Review|null
         */
        public function getReview($reviewId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$REVIEWS_TABLE_NAME,
                                                       [ "[>]" . KAASoftDatabase::$USERS_TABLE_NAME  => [ "userId" => "id" ],
                                                         "[><]" . KAASoftDatabase::$BOOKS_TABLE_NAME => [ "bookId" => "id" ] ],
                                                       array_merge(Review::getDatabaseFieldNames(),
                                                                   User::getDatabaseFieldNames(),
                                                                   Book::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$REVIEWS_TABLE_NAME . ".id",
                                                                     KAASoftDatabase::$REVIEWS_TABLE_NAME . ".email(reviewEmail)" ]),
                                                       [ KAASoftDatabase::$REVIEWS_TABLE_NAME . ".id" => $reviewId ]);
            if ($queryResult !== false) {
                $review = Review::getObjectInstance($queryResult);
                $review->setEmail($queryResult["reviewEmail"]);

                if ($review->getUserId() !== null) {
                    $user = User::getObjectInstance($queryResult);
                    $user->setId($review->getUserId());
                    $review->setUser($user);
                }

                $book = Book::getObjectInstance($queryResult);
                $book->setId($review->getBookId());
                $review->setBook($book);

                return $review;

            }

            return null;
        }

        /**
         * @param Review $review
         * @return bool|int
         */
        public function saveReview(Review $review) {
            $data = $review->getDatabaseArray();
            if ($review->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$REVIEWS_TABLE_NAME,
                                                      $data);
            }
            else {
                unset( $data["creationDateTime"] );

                return $this->kaaSoftDatabase->update(KAASoftDatabase::$REVIEWS_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $review->getId() ]);
            }
        }

        /**
         * @param $reviewId
         * @return bool
         */
        public function deleteReview($reviewId) {
            $review = $this->getReview($reviewId);
            if ($review !== null) {
                return $this->kaaSoftDatabase->delete(KAASoftDatabase::$REVIEWS_TABLE_NAME,
                                                      [ "id" => $reviewId ]);
            }

            return false;
        }

        /**+
         * @param $reviewId
         * @return bool
         */
        public function isReviewExist($reviewId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$REVIEWS_TABLE_NAME,
                                               [ "id" => $reviewId ]);
        }
    }