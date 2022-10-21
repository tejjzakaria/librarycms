<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub\Post;

    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\Routes\Pub\PostAndPageRoutes;
    use KAASoft\Controller\Admin\Post\PostDatabaseHelper;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Message;
    use KAASoft\Util\Paginator;

    /**
     * Class PostCategoryViewAction
     * @package KAASoft\Controller\Pub\Post
     */
    class PostCategoryViewAction extends PublicActionBase {

        /**
         * PostCategoryViewAction constructor.
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

            $categoryUrl = $args["categoryUrl"];
            $page = isset( $args["page"] ) ? $args["page"] : 1;

            $postHelper = new PostDatabaseHelper($this);

            $category = $postHelper->getCategoryByUrl($categoryUrl);
            if ($category === null) {
                $errorMessage = sprintf(_("Category with name '%s' is not exist."),
                                        $categoryUrl);
                $this->session->addSessionMessage($errorMessage,
                                                  Message::MESSAGE_STATUS_ERROR);

                return new DisplaySwitch(null,
                                         $this->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
            }


            $paginator = new Paginator($page,
                                       $this->siteViewOptions->getOptionValue(SiteViewOptions::POSTS_PER_PAGE_PUBLIC),
                                       $postHelper->countCategoryPosts($category));
            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->getRouteString(PostAndPageRoutes::POST_LIST_BY_CATEGORY_VIEW_ROUTE,
                                                                                 [ "categoryUrl" => $category->getUrl() ])));

            $this->smarty->assign("category",
                                  $category);
            $this->smarty->assign("blog",
                                  $postHelper->getPost(0));
            $this->smarty->assign("posts",
                                  $postHelper->getPublicCategoryPosts($paginator->getOffset(),
                                                                      $this->siteViewOptions->getOptionValue(SiteViewOptions::POSTS_PER_PAGE_PUBLIC),
                                                                      $category));

            return new DisplaySwitch('blog/posts.tpl');
        }
    }