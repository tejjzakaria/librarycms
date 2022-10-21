<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Book;

    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Message;

    class BookViewAction extends AdminActionBase {
        /**
         * BookViewAction constructor.
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

            $book = $bookDatabaseHelper->getBook($bookId);

            if ($book === null) {
                $this->session->addSessionMessage(sprintf(_("Book with id = '%d' is not found."),
                                                          $bookId),
                                                  Message::MESSAGE_STATUS_ERROR);

                return new DisplaySwitch(null,
                                         $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
            }

            $this->smarty->assign("book",
                                  $book);

            return new DisplaySwitch('admin/books/viewBook.tpl');
        }
    }