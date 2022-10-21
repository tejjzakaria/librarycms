<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-01-07
     */


    namespace KAASoft\Controller\Admin\Book;

    use KAASoft\Controller\Admin\Store\StoreDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Routes\Admin\BookRoutes;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use KAASoft\Util\Paginator;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class StoreBooksViewAction
     * @package KAASoft\Controller\Admin\Book
     */
    class StoreBooksViewAction extends AdminActionBase {
        /**
         * StoreBooksViewAction constructor.
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
            $storeId = ValidationHelper::getInt($args["storeId"]);
            $page = isset( $args["page"] ) ? ValidationHelper::getInt($args["page"]) : 1;
            $bookDatabaseHelper = new BookDatabaseHelper($this);
            $storeDatabaseHelper = new StoreDatabaseHelper($this);

            $store = $storeDatabaseHelper->getStore($storeId);
            if ($store === null) {
                Session::addSessionMessage(sprintf(_("Store with id = '%d' is not found."),
                                                   $storeId),
                                           Message::MESSAGE_STATUS_ERROR);

                return new DisplaySwitch(null,
                                         $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
            }

            $perPage = $this->getPerPage(Session::BOOK_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::BOOKS_PER_PAGE_ADMIN));
            $sortColumn = $this->getSortingColumn(Session::BOOK_SORTING_COLUMN,
                                                  KAASoftDatabase::$BOOKS_TABLE_NAME . ".creationDateTime");
            $sortOrder = $this->getSortingOrder(Session::BOOK_SORTING_ORDER,
                                                "DESC");

            $paginator = new Paginator($page,
                                       $perPage,
                                       $bookDatabaseHelper->getStoreBooksCount($storeId));

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(BookRoutes::STORE_BOOKS_VIEW_ROUTE,
                                                                                                  [ "storeId" => $storeId ])));

            $books = $bookDatabaseHelper->getStoreBooks($storeId,
                                                        $paginator->getOffset(),
                                                        $perPage,
                                                        $sortColumn,
                                                        $sortOrder);

            $this->smarty->assign("books",
                                  $books);

            $this->smarty->assign("store",
                                  $store);

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('admin/books/storeBooks-list.tpl');
            }
            else {
                return new DisplaySwitch('admin/books/storeBooks.tpl');
            }
        }
    }