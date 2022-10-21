<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub\Publisher;


    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\Admin\Publisher\PublisherDatabaseHelper;
    use KAASoft\Controller\Admin\User\UserDatabaseHelper;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Routes\Pub\BookPublicRoutes;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use KAASoft\Util\Paginator;

    /**
     * Class PublisherBooksPublicAction
     * @package KAASoft\Controller\Pub\Publisher
     */
    class PublisherBooksPublicAction extends PublicActionBase {
        /**
         * PublisherBooksPublicAction constructor.
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
            $publisherId = $args["publisherId"];
            $page = isset( $args["page"] ) ? $args["page"] : 1;
            $bookDatabaseHelper = new BookDatabaseHelper($this);
            $publisherDatabaseHelper = new PublisherDatabaseHelper($this);
            $publisher = $publisherDatabaseHelper->getPublisher($publisherId);

            if ($publisher === null) {
                $this->session->addSessionMessage(sprintf(_("Publisher with id = '%d' is not found."),
                                                          $publisherId),
                                                  Message::MESSAGE_STATUS_ERROR);

                return new DisplaySwitch(null,
                                         $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
            }

            $perPage = $this->getPerPage(Session::BOOK_PER_PAGE_NUMBER_PUBLIC,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::BOOKS_PER_PAGE_PUBLIC));
            $sortColumn = $this->getSortingColumn(Session::BOOK_SORTING_COLUMN_PUBLIC,
                                                  KAASoftDatabase::$BOOKS_TABLE_NAME . ".creationDateTime");
            $sortOrder = $this->getSortingOrder(Session::BOOK_SORTING_ORDER_PUBLIC,
                                                "DESC");

            $totalBooks = $bookDatabaseHelper->getPublisherBooksCount($publisherId);
            $paginator = new Paginator($page,
                                       $perPage,
                                       $totalBooks);

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(BookPublicRoutes::PUBLISHER_BOOKS_PUBLIC_ROUTE,
                                                                                                  [ "publisherId" => $publisherId ])));

            $books = $bookDatabaseHelper->getPublisherBooks($publisherId,
                                                            $paginator->getOffset(),
                                                            $perPage,
                                                            $sortColumn,
                                                            $sortOrder);
            $this->smarty->assign("totalBooks",
                                  $totalBooks);
            $this->smarty->assign("books",
                                  $books);
            $this->smarty->assign("publisher",
                                  $publisher);
            $userHelper = new UserDatabaseHelper($this);
            $this->smarty->assign("users",
                                  $userHelper->getUsers());
            $this->smarty->assign("bindings",
                                  $bookDatabaseHelper->getBindings());

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('books/components/books-by-category-list.tpl');
            }
            else {
                return new DisplaySwitch('publishers/publishers.tpl');
            }
        }
    }