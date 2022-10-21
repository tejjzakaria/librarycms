<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */


    namespace KAASoft\Controller\Pub\Post;

    use KAASoft\Environment\Routes\Pub\PostAndPageRoutes;
    use KAASoft\Controller\Admin\Post\PostDatabaseHelper;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Paginator;

    class PostsViewAction extends PublicActionBase {

        /**
         * PostsViewAction constructor.
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

            $postHelper = new PostDatabaseHelper($this);


            $paginator = new Paginator($page,
                                       $this->siteViewOptions->getOptionValue(SiteViewOptions::POSTS_PER_PAGE_PUBLIC),
                                       $postHelper->getPublicPostsCount());
            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->getRouteString(PostAndPageRoutes::POST_LIST_VIEW_PUBLIC_ROUTE)));

            $this->smarty->assign("blog",
                                  $postHelper->getPost(0));
            $this->smarty->assign("posts",
                                  $postHelper->getPublicPosts($paginator->getOffset(),
                                                              $this->siteViewOptions->getOptionValue(SiteViewOptions::POSTS_PER_PAGE_PUBLIC)));

            return new DisplaySwitch('blog/posts.tpl');
        }
    }