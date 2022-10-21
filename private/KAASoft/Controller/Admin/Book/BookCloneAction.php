<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Book;

    use Exception;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Environment\Routes\Admin\BookRoutes;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;

    /**
     * Class BookCloneAction
     * @package KAASoft\Controller\Admin\Book
     */
    class BookCloneAction extends AdminActionBase {
        /**
         * BookCloneAction constructor.
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
            $bookId = $args["bookId"];
            $bookDatabaseHelper = new BookDatabaseHelper($this);
            try {
                if ($this->startDatabaseTransaction()) {
                    $book = $bookDatabaseHelper->getBook($bookId);
                    if ($book === null) {
                        Session::addSessionMessage(sprintf("There is no requested book(%d) in database.",
                                                           $bookId));

                        return new DisplaySwitch(null,
                                                 $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
                    }
                    $book->setId(null);
                    $book->setCoverId(null);
                    $book->setEBookId(null);
                    $book->setUrl(null);
                    $book->setRating(null);
                    $book->setCreationDateTime(Helper::getDateTimeString());

                    $newBookId = $bookDatabaseHelper->saveBook($book);
                    if ($newBookId === false) {
                        $this->rollbackDatabaseChanges();
                        $errorMessage = _("Book cloning is failed for some reason.");
                        Session::addSessionMessage($errorMessage,
                                                   Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 $this->getRouteString(BookRoutes::BOOK_EDIT_ROUTE,
                                                                       [ "bookId" => $bookId ]));
                    }
                    if ($bookDatabaseHelper->saveBookAuthors($newBookId,
                                                             $bookDatabaseHelper->getBookAuthorsIds($bookId)) === false
                    ) {
                        $this->rollbackDatabaseChanges();
                        $errorMessage = _("Book author(s) saving is failed for some reason.");
                        Session::addSessionMessage($errorMessage,
                                                   Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 $this->getRouteString(BookRoutes::BOOK_EDIT_ROUTE,
                                                                       [ "bookId" => $bookId ]));
                    }

                    if ($bookDatabaseHelper->saveBookGenres($newBookId,
                                                            $bookDatabaseHelper->getBookGenresIds($bookId)) === false
                    ) {
                        $this->rollbackDatabaseChanges();
                        $errorMessage = _("Book genre(s) saving is failed for some reason.");
                        Session::addSessionMessage($errorMessage,
                                                   Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 $this->getRouteString(BookRoutes::BOOK_EDIT_ROUTE,
                                                                       [ "bookId" => $bookId ]));
                    }

                    if ($bookDatabaseHelper->saveBookTags($newBookId,
                                                          $bookDatabaseHelper->getBookTagsIds($bookId)) === false
                    ) {
                        $this->rollbackDatabaseChanges();
                        $errorMessage = _("Book tag(s) saving is failed for some reason.");
                        Session::addSessionMessage($errorMessage,
                                                   Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 $this->getRouteString(BookRoutes::BOOK_EDIT_ROUTE,
                                                                       [ "bookId" => $bookId ]));
                    }

                    if ($bookDatabaseHelper->saveBookStores($newBookId,
                                                            $bookDatabaseHelper->getBookStoreIds($bookId)) === false
                    ) {
                        $this->rollbackDatabaseChanges();
                        $errorMessage = _("Book store(s) saving is failed for some reason.");
                        Session::addSessionMessage($errorMessage,
                                                   Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 $this->getRouteString(BookRoutes::BOOK_EDIT_ROUTE,
                                                                       [ "bookId" => $bookId ]));
                    }

                    $this->commitDatabaseChanges();
                    $message = _("Book is successfully cloned.");
                    Session::addSessionMessage($message,
                                               Message::MESSAGE_STATUS_INFO);

                    return new DisplaySwitch(null,
                                             $this->getRouteString(BookRoutes::BOOK_EDIT_ROUTE,
                                                                   [ "bookId" => $newBookId ]));
                }
            }
            catch (Exception $e) {
                $this->rollbackDatabaseChanges();
                ControllerBase::getLogger()->error($e->getMessage(),
                                                   $e);
                $errorMessage = sprintf(_("Couldn't save Book.%s%s"),
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                Session::addSessionMessage($errorMessage,
                                           Message::MESSAGE_STATUS_ERROR);

                return new DisplaySwitch(null,
                                         $this->getRouteString(BookRoutes::BOOK_EDIT_ROUTE,
                                                               [ "bookId" => $bookId ]));
            }

            return new DisplaySwitch(null,
                                     $this->getRouteString(BookRoutes::BOOK_EDIT_ROUTE,
                                                           [ "bookId" => $bookId ]));
        }
    }