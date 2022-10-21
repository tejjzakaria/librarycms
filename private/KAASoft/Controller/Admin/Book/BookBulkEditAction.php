<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-01-05
     */

    namespace KAASoft\Controller\Admin\Book;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;
    use PDOException;

    /**
     * Class BookBulkEditAction
     * @package KAASoft\Controller\Admin\Book
     */
    class BookBulkEditAction extends AdminActionBase {
        /**
         * BookBulkEditAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {

            if (Helper::isPostRequest()) {
                $bookIds = ValidationHelper::getIntArray($_POST["bookIds"]);
                $seriesId = ValidationHelper::getNullableInt($_POST["seriesId"]);
                $publisherId = ValidationHelper::getNullableInt($_POST["publisherId"]);
                $authors = ValidationHelper::getIntArray($_POST["authors"]);
                $isAuthorsReset = (isset( $_POST["authors"] ) and count($_POST["authors"]) === 1 and ValidationHelper::getNullableInt($_POST["authors"][0]) === 0);
                $genres = ValidationHelper::getIntArray($_POST["genres"]);
                $isGenresReset = (isset( $_POST["genres"] ) and count($_POST["genres"]) === 1 and ValidationHelper::getNullableInt($_POST["genres"][0]) === 0);
                $publishingYear = ValidationHelper::getNullableInt($_POST["publishingYear"]);
                $pages = ValidationHelper::getNullableInt($_POST["pages"]);
                $binding = ValidationHelper::getString($_POST["binding"]);
                $physicalForm = ValidationHelper::getString($_POST["physicalForm"]);
                $size = ValidationHelper::getString($_POST["size"]);
                $type = ValidationHelper::getString($_POST["type"]);
                $stores = ValidationHelper::getIntArray($_POST["stores"]);
                $isStoresReset = (isset( $_POST["stores"] ) and count($_POST["stores"]) === 1 and ValidationHelper::getNullableInt($_POST["stores"][0]) === 0);
                $locations = ValidationHelper::getIntArray($_POST["locations"]);
                $isLocationsReset = (isset( $_POST["locations"] ) and count($_POST["locations"]) === 1 and ValidationHelper::getNullableInt($_POST["locations"][0]) === 0);
                $language = ValidationHelper::getString($_POST["language"]);
                $isLanguageReset = (isset($_POST["language"]) and ValidationHelper::getNullableInt($_POST["language"]) === 0);
                $price = ValidationHelper::getFloat($_POST["price"]);
                $isPriceReset = (isset($_POST["price"]) and ValidationHelper::getNullableInt($_POST["price"]) === 0);
                $quantity = ValidationHelper::getNullableInt($_POST["quantity"]);
                $isBookUpdated = ($seriesId !== null or $publisherId !== null or $publishingYear !== null or $pages !== null or $language !== null or $price !==null or $quantity !== null or $isPriceReset or $isLanguageReset);

                $bookDatabaseHelper = new BookDatabaseHelper($this);
                try {
                    if (Helper::isAjaxRequest()) {
                        if ($bookIds !== null) {
                            if ($this->startDatabaseTransaction()) {
                                $books = $bookDatabaseHelper->getBooksByIds($bookIds);

                                foreach ($books as $book) {
                                    if ($book instanceof Book) {
                                        $bookId = $book->getId();
                                        if ($seriesId !== null) {
                                            if ($seriesId === 0) {
                                                $seriesId = null;
                                            }
                                            $book->setSeriesId($seriesId);
                                        }
                                        if ($publisherId !== null) {
                                            if ($publisherId === 0) {
                                                $publisherId = null;
                                            }
                                            $book->setPublisherId($publisherId);
                                        }
                                        if ($authors !== null or $isAuthorsReset) {
                                            $result = $bookDatabaseHelper->deleteBookAuthors($bookId);
                                            if ($result === false) {
                                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't cleanup book authors.") ]);

                                                return new DisplaySwitch(null,
                                                                         null,
                                                                         false);
                                            }
                                        }
                                        if ($authors !== null and !$isAuthorsReset) {
                                            $result = $bookDatabaseHelper->saveBookAuthors($bookId,
                                                                                           $authors);
                                            if ($result === false) {
                                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't update book authors.") ]);

                                                return new DisplaySwitch(null,
                                                                         null,
                                                                         false);
                                            }
                                        }
                                        if ($genres !== null or $isGenresReset) {
                                            $result = $bookDatabaseHelper->deleteBookGenres($bookId);
                                            if ($result === false) {
                                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't cleanup book genres.") ]);

                                                return new DisplaySwitch(null,
                                                                         null,
                                                                         false);
                                            }
                                        }
                                        if ($genres !== null and !$isGenresReset) {
                                            $result = $bookDatabaseHelper->saveBookGenres($bookId,
                                                                                          $genres);
                                            if ($result === false) {
                                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't update book genres.") ]);

                                                return new DisplaySwitch(null,
                                                                         null,
                                                                         false);
                                            }
                                        }
                                        if ($publishingYear !== null) {
                                            $book->setPublishingYear($publishingYear);
                                        }

                                        if ($pages !== null) {
                                            $book->setPages($pages);
                                        }

                                        if ($binding !== null) {
                                            $book->setBinding($binding);
                                        }

                                        if ($physicalForm !== null) {
                                            $book->setPhysicalForm($physicalForm);
                                        }

                                        if ($size !== null) {
                                            $book->setSize($size);
                                        }

                                        if ($type !== null) {
                                            $book->setType($type);
                                        }

                                        if ($stores !== null or $isStoresReset) {
                                            $result = $bookDatabaseHelper->deleteBookStores($bookId);
                                            if ($result === false) {
                                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't cleanup book stores.") ]);

                                                return new DisplaySwitch(null,
                                                                         null,
                                                                         false);
                                            }
                                        }
                                        if ($stores != null and !$isStoresReset) {
                                            $result = $bookDatabaseHelper->saveBookStores($bookId,
                                                                                          $stores);
                                            if ($result === false) {
                                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't update book stores.") ]);

                                                return new DisplaySwitch(null,
                                                                         null,
                                                                         false);
                                            }
                                        }

                                        if ($locations !== null or $isLocationsReset) {
                                            $result = $bookDatabaseHelper->deleteBookLocations($bookId);
                                            if ($result === false) {
                                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't cleanup book locations.") ]);

                                                return new DisplaySwitch(null,
                                                                         null,
                                                                         false);
                                            }
                                        }
                                        if ($locations !== null and !$isLocationsReset) {
                                            $result = $bookDatabaseHelper->saveBookLocations($bookId,
                                                                                             $locations);
                                            if ($result === false) {
                                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't update book locations.") ]);

                                                return new DisplaySwitch(null,
                                                                         null,
                                                                         false);
                                            }
                                        }

                                        if ($language !== null) {
                                            if (empty( $language )) {
                                                $language = null;
                                            }
                                            $book->setLanguage($language);
                                        }

                                        if ($isLanguageReset) {
                                            $book->setLanguage(null);
                                        }

                                        if ($price !== null) {
                                            $newPrice = $book->getPrice() + $price;
                                            if ($newPrice < 0) {
                                                $newPrice = 0;
                                            }
                                            $book->setPrice($newPrice);
                                        }

                                        if ($isPriceReset) {
                                            $book->setPrice(0);
                                        }

                                        /*if ($quantity !== null) {
                                            $issuedQuantity = $book->getQuantity() - $book->getActualQuantity();

                                            $newQuantity = $book->getQuantity() + $quantity;
                                            if ($newQuantity <= 0) {
                                                $newQuantity = $issuedQuantity;
                                            }
                                            $newActualQuantity = $book->getActualQuantity() + $quantity;
                                            if ($newActualQuantity < 0) {
                                                $newActualQuantity = 0;
                                            }

                                            $book->setQuantity($newQuantity);
                                            $book->setActualQuantity($newActualQuantity);
                                        }*/
                                    }
                                }

                                if($isBookUpdated) {
                                    $result = $bookDatabaseHelper->updateBooks($books);
                                    if ($result === false) {
                                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't save books.") ]);

                                        return new DisplaySwitch(null,
                                                                 null,
                                                                 false);
                                    }
                                }
                                $this->commitDatabaseChanges();
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Books are updated successfully.") ]);
                            }
                        }
                        else {
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't find any book id in request.") ]);
                        }
                    }
                    else {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
                    }
                }
                catch (PDOException $e) {
                    $this->rollbackDatabaseChanges();
                    ControllerBase::getLogger()->error($e->getMessage(),
                                                       $e);
                    $errorMessage = sprintf(_("Couldn't update Books.%s%s"),
                                            Helper::HTML_NEW_LINE,
                                            $e->getMessage());
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                }

                return new DisplaySwitch();
            }
            else {

                return new DisplaySwitch("template");
            }
        }
    }