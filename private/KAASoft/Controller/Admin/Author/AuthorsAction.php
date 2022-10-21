<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Author;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\Routes\Admin\AuthorRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;

    /**
     * Class AuthorsAction
     * @package KAASoft\Controller\Admin\Author
     */
    class AuthorsAction extends AdminActionBase {
        /**
         * AuthorsAction constructor.
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
            $authorDatabaseHelper = new AuthorDatabaseHelper($this);

            $perPage = $this->getPerPage(Session::AUTHOR_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::AUTHORS_PER_PAGE));
            $sortColumn = $this->getSortingColumn(Session::AUTHOR_SORTING_COLUMN);
            $sortOrder = $this->getSortingOrder(Session::AUTHOR_SORTING_ORDER);


            $paginator = new Paginator($page,
                                       $perPage,
                                       $authorDatabaseHelper->getAuthorsCount());

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(AuthorRoutes::AUTHOR_LIST_VIEW_ROUTE)));


            $authors = $authorDatabaseHelper->getAuthors(null,
                                                         $paginator->getOffset(),
                                                         $perPage,
                                                         $sortColumn,
                                                         $sortOrder);

            $this->smarty->assign("authors",
                                  $authors);

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch("admin/authors/authors-list.tpl");
            }
            else {
                return new DisplaySwitch("admin/authors/authors.tpl");
            }
        }
    }