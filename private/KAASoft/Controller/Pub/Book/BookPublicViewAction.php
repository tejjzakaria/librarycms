<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub\Book;

    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\Admin\Review\ReviewDatabaseHelper;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Environment\BookFieldSettings;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Message;

    /**
     * Class BookPublicViewAction
     * @package KAASoft\Controller\Pub\Book
     */
    class BookPublicViewAction extends PublicActionBase {
        /**
         * BookPublicViewAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute,
                                true);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $bookId = $args["bookId"];
            $bookDatabaseHelper = new BookDatabaseHelper($this);
            $reviewDatabaseHelper = new ReviewDatabaseHelper($this);

            $book = $bookDatabaseHelper->getBook($bookId);

            if ($book === null) {
                $this->session->addSessionMessage(sprintf(_("Book with id = '%d' is not found."),
                                                          $bookId),
                                                  Message::MESSAGE_STATUS_ERROR);

                return new DisplaySwitch(null,
                                         $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
            }
            $perPage = $this->getPerPage(Session::REVIEW_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::REVIEW_PER_PAGE));
            $book->setReviews($reviewDatabaseHelper->getReviews($bookId,
                                                                0,
                                                                $perPage));

            $this->smarty->assign("book",
                                  $book);

            $user = $this->session->getUser();

            if ($user !== null and $user instanceof User) {
                $this->smarty->assign("userBookRating",
                                      $bookDatabaseHelper->getUserBookRating($bookId,
                                                                             $user->getId()));
            }

            $bookFieldSettings = new BookFieldSettings();
            $bookFieldSettings->loadSettings();
            $this->smarty->assign("bookFieldSettings",
                                  $bookFieldSettings);

            return new DisplaySwitch('books/book.tpl');
        }
    }