<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Review;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Routes\Admin\ReviewRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;

    /**
     * Class ReviewsAction
     * @package KAASoft\Controller\Admin\Review
     */
    class ReviewsAction extends AdminActionBase {
        /**
         * ReviewsAction constructor.
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
            $reviewDatabaseHelper = new ReviewDatabaseHelper($this);

            $perPage = $this->getPerPage(Session::REVIEW_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::REVIEW_PER_PAGE));
            $sortColumn = $this->getSortingColumn(Session::REVIEW_SORTING_COLUMN,
                                                  KAASoftDatabase::$REVIEWS_TABLE_NAME . ".creationDateTime");
            $sortOrder = $this->getSortingOrder(Session::REVIEW_SORTING_ORDER,
                                                "DESC");


            $paginator = new Paginator($page,
                                       $perPage,
                                       $reviewDatabaseHelper->getReviewsCount());

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(ReviewRoutes::REVIEW_LIST_VIEW_ROUTE)));


            $reviews = $reviewDatabaseHelper->getReviews(null,
                                                         $paginator->getOffset(),
                                                         $perPage,
                                                         $sortColumn,
                                                         $sortOrder);

            $this->smarty->assign("reviews",
                                  $reviews);

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('admin/reviews/reviews-list.tpl');
            }
            else {
                return new DisplaySwitch('admin/reviews/reviews.tpl');
            }
        }
    }