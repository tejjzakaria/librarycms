<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Post;

    use KAASoft\Environment\Routes\Admin\PostRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;


    /**
     * Class PostsAction
     * @package KAASoft\Controller\Admin\Post
     */
    class PostsAction extends AdminActionBase {

        /**
         * PostsAction constructor.
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

            $perPage = $this->getPerPage(Session::POST_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::POSTS_PER_PAGE));
            $sortColumn = $this->getSortingColumn(Session::POST_SORTING_COLUMN,
                                                  KAASoftDatabase::$POSTS_TABLE_NAME . ".creationDateTime");
            $sortOrder = $this->getSortingOrder(Session::POST_SORTING_ORDER,
                                                "DESC");

            $postHelper = new PostDatabaseHelper($this);
            $paginator = new Paginator($page,
                                       $perPage,
                                       $postHelper->getPostsCount());
            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->getRouteString(PostRoutes::POST_LIST_VIEW_ROUTE)));


            $this->smarty->assign("posts",
                                  $postHelper->getPosts($paginator->getOffset(),
                                                        $perPage,
                                                        $sortColumn,
                                                        $sortOrder));

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch("admin/public/posts/post-list.tpl");
            }
            else {
                return new DisplaySwitch("admin/public/posts/posts.tpl");
            }
        }
    }