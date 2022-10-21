<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub\Author;

    use KAASoft\Controller\Admin\Author\AuthorDatabaseHelper;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Environment\Routes\Pub\BookPublicRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;

    /**
     * Class AuthorsPublicAction
     * @package KAASoft\Controller\Pub\Author
     */
    class AuthorsPublicAction extends PublicActionBase {
        /**
         * AuthorsPublicAction constructor.
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
            $page = isset( $args["page"] ) ? $args["page"] : 1;
            $authorDatabaseHelper = new AuthorDatabaseHelper($this);
            $totalAuthors = $authorDatabaseHelper->getAuthorsCount();

            $perPage = $this->getPerPage(Session::AUTHOR_PER_PAGE_NUMBER_PUBLIC,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::AUTHORS_PER_PAGE_PUBLIC));
            $sortColumn = $this->getSortingColumn(Session::AUTHOR_SORTING_COLUMN_PUBLIC);
            $sortOrder = $this->getSortingOrder(Session::AUTHOR_SORTING_ORDER_PUBLIC);

            $paginator = new Paginator($page,
                                       $perPage,
                                       $totalAuthors);

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(BookPublicRoutes::AUTHOR_LIST_VIEW_PUBLIC_ROUTE)));

            $authors = $authorDatabaseHelper->getAuthors(null,
                                                         $paginator->getOffset(),
                                                         $perPage,
                                                         $sortColumn,
                                                         $sortOrder);

            $this->smarty->assign("authors",
                                  $authors);
            $this->smarty->assign("totalAuthors",
                                  $totalAuthors);

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('authors/authors-list.tpl');
            }
            else {
                return new DisplaySwitch('authors/authors.tpl');
            }
        }
    }