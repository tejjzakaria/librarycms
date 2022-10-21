<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Genre;

    use KAASoft\Environment\Routes\Admin\GenreRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;

    /**
     * Class GenresAction
     * @package KAASoft\Controller\Admin\Genre
     */
    class GenresAction extends AdminActionBase {
        /**
         * GenresAction constructor.
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
            $genreDatabaseHelper = new GenreDatabaseHelper($this);

            $perPage = $this->getPerPage(Session::GENRE_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::GENRES_PER_PAGE));
            $sortColumn = $this->getSortingColumn(Session::GENRE_SORTING_COLUMN);
            $sortOrder = $this->getSortingOrder(Session::GENRE_SORTING_ORDER);


            $paginator = new Paginator($page,
                                       $perPage,
                                       $genreDatabaseHelper->getGenresCount());

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(GenreRoutes::GENRE_LIST_VIEW_ROUTE)));

            $genres = $genreDatabaseHelper->getGenres($paginator->getOffset(),
                                                      $perPage,
                                                      $sortColumn,
                                                      $sortOrder);

            $this->smarty->assign("genres",
                                  $genres);

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('admin/genres/genre-list.tpl');
            }
            else {
                return new DisplaySwitch('admin/genres/genres.tpl');
            }
        }
    }