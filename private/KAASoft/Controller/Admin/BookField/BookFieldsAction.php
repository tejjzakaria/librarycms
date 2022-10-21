<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\BookField;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\Routes\Admin\BookFieldRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;

    /**
     * Class BookFieldsAction
     * @package KAASoft\Controller\Admin\BookField
     */
    class BookFieldsAction extends AdminActionBase {
        /**
         * BookFieldsAction constructor.
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
            $page = isset( $args["page"] ) ? $args["page"] : 1;
            $publisherDatabaseHelper = new BookFieldDatabaseHelper($this);

            $perPage = $this->getPerPage(Session::BOOK_FIELD_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::BOOK_FIELDS_PER_PAGE));
            $sortColumn = $this->getSortingColumn(Session::BOOK_FIELD_SORTING_COLUMN);
            $sortOrder = $this->getSortingOrder(Session::BOOK_FIELD_SORTING_ORDER);

            $paginator = new Paginator($page,
                                       $perPage,
                                       $publisherDatabaseHelper->getBookFieldsCount());

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(BookFieldRoutes::BOOK_FIELD_LIST_VIEW_ROUTE)));

            $bookFields = $publisherDatabaseHelper->getBookFields($paginator->getOffset(),
                                                                  $perPage,
                                                                  $sortColumn,
                                                                  $sortOrder);

            $this->smarty->assign("bookFields",
                                  $bookFields);

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('admin/book-fields/book-field-list.tpl');
            }
            else {
                return new DisplaySwitch('admin/book-fields/book-fields.tpl');
            }
        }
    }