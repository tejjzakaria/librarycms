<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Book;

    use KAASoft\Controller\Admin\BookCopy\BookCopyDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Database\Entity\General\BookCopy;
    use KAASoft\Database\Entity\General\Issue;
    use KAASoft\Environment\BookLayoutSettings;
    use KAASoft\Environment\GoogleSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;
    use PDOException;

    /**
     * Class BookCreateAction
     * @package KAASoft\Controller\Admin\Book
     */
    class BookCreateAction extends AdminActionBase {
        /**
         * BookCreateAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         * @throws \Exception
         */
        protected function action($args) {
            $bookDatabaseHelper = new BookDatabaseHelper($this);
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {
                            $book = Book::getObjectInstance($_POST);

                            $book->setCreationDateTime(Helper::getDateTimeString());
                            $book->setUpdateDateTime($book->getCreationDateTime());
                            $bookId = $bookDatabaseHelper->saveBook($book);

                            if ($bookId === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Book saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            if (isset( $_POST["authors"] ) and is_array($_POST["authors"])) {
                                if ($bookDatabaseHelper->saveBookAuthors($bookId,
                                                                         $_POST["authors"]) === false
                                ) {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = _("Book author(s) saving is failed for some reason.");
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                    return new DisplaySwitch();
                                }
                            }

                            if (isset( $_POST["genres"] ) and is_array($_POST["genres"])) {
                                if ($bookDatabaseHelper->saveBookGenres($bookId,
                                                                        $_POST["genres"]) === false
                                ) {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = _("Book genre(s) saving is failed for some reason.");
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                    return new DisplaySwitch();
                                }
                            }

                            if (isset( $_POST["tags"] ) and is_array($_POST["tags"])) {
                                if ($bookDatabaseHelper->saveBookTags($bookId,
                                                                      $_POST["tags"]) === false
                                ) {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = _("Book tag(s) saving is failed for some reason.");
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                    return new DisplaySwitch();
                                }
                            }

                            if (isset( $_POST["stores"] ) and is_array($_POST["stores"])) {
                                if ($bookDatabaseHelper->saveBookStores($bookId,
                                                                        $_POST["stores"]) === false
                                ) {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = _("Book store(s) saving is failed for some reason.");
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                    return new DisplaySwitch();
                                }
                            }

                            if (isset( $_POST["locations"] ) and is_array($_POST["locations"])) {
                                if ($bookDatabaseHelper->saveBookLocations($bookId,
                                                                           $_POST["locations"]) === false
                                ) {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = _("Book locations(s) saving is failed for some reason.");
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                    return new DisplaySwitch();
                                }
                            }

                            if (isset( $_POST["imageIds"] ) and is_array($_POST["imageIds"])) {
                                $imageIds = ValidationHelper::getIntArray($_POST["imageIds"]);
                                if ($bookDatabaseHelper->saveBookImages($bookId,
                                                                        $imageIds) === false
                                ) {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = _("Book image(s) saving is failed for some reason.");
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                    return new DisplaySwitch();
                                }
                            }

                            $bookCopyIds = [];
                            if (!ValidationHelper::isArrayEmpty($_POST["bookCopies"])) {
                                $bookCopiesArray = ValidationHelper::getArray($_POST["bookCopies"]);

                                $bookCopyDatabaseHelper = new BookCopyDatabaseHelper($this);

                                foreach ($bookCopiesArray as $tempId => $bookCopyArray) {
                                    $bookCopy = BookCopy::getObjectInstance($bookCopyArray);
                                    $isNewBookCopy = $bookCopy->getId() === null;
                                    $bookCopy->setBookId($bookId);
                                    if ($isNewBookCopy) {
                                        $bookCopy->setIssueStatus(Issue::ISSUE_STATUS_AVAILABLE);
                                    }
                                    $result = $bookCopyDatabaseHelper->saveBookCopy($bookCopy,
                                                                                    $isNewBookCopy);
                                    if ($result === false) {
                                        $this->rollbackDatabaseChanges();
                                        $errorMessage = _("Book copy(s) saving is failed for some reason.");
                                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                        return new DisplaySwitch();
                                    }

                                    $bookCopyIds[$tempId] = $bookCopy->getId() !== null ? $bookCopy->getId() : $result;
                                }
                            }

                            $response = [ "bookId" => $bookId ];
                            if (count($bookCopyIds) > 0) {
                                $response = array_merge($response,
                                                        [ "bookCopyIds" => $bookCopyIds ]);
                            }
                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse($response);
                        }
                    }
                    catch (PDOException $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't save Book.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }

                return new DisplaySwitch();
            }
            else {
                $this->smarty->assign("action",
                                      "create");

                $this->smarty->assign("bindings",
                                      $this->kaaSoftDatabase->getBindings());
                $this->smarty->assign("bookTypes",
                                      $this->kaaSoftDatabase->getBookTypes());
                $this->smarty->assign("bookSizes",
                                      $this->kaaSoftDatabase->getBookSizes());
                $this->smarty->assign("physicalForms",
                                      $this->kaaSoftDatabase->getPhysicalForms());

                $googleSettings = new GoogleSettings();
                $googleSettings->loadSettings();
                $this->smarty->assign("hasGoogleAPI",
                                      !ValidationHelper::isEmpty($googleSettings->getApiKey()));


                $bookLayoutSettings = new BookLayoutSettings();
                $bookLayoutSettings->loadSettings();
                $this->smarty->assign("bookVisibleFieldList",
                                      Book::getFieldListPrivate());
                $this->smarty->assign("bookLayoutSettings",
                                      $bookLayoutSettings);
                $this->smarty->assign("bookStatuses",
                                      BookCopy::getBookStatuses());
                $this->smarty->assign("issueStatuses",
                                      Issue::getIssueStatuses());

                return new DisplaySwitch('admin/books/book.tpl');
            }
        }
    }