<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub\Post;


    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Controller\Admin\Post\PostDatabaseHelper;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Message;

    class PostViewAction extends PublicActionBase {

        /**
         * PostViewAction constructor.
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

            $postUrl = $args["postUrl"];

            $postHelper = new PostDatabaseHelper($this);
            $post = $postHelper->getPublicPostByURL($postUrl);
            if ($post === null) {
                $errorMessage = sprintf(_("Post with url '%s' is not exist."),
                                        $postUrl);
                $this->session->addSessionMessage($errorMessage,
                                                  Message::MESSAGE_STATUS_ERROR);

                return new DisplaySwitch(null,
                                         $this->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
            }

            $this->smarty->assign("post",
                                  $post);

            return new DisplaySwitch('blog/post.tpl');
        }
    }