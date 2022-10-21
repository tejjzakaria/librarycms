<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Post;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\Post\Post;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Enum\PostStatus;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use PDOException;

    /**
     * Class PostEditAction
     * @package KAASoft\Controller\Admin\Post
     */
    class PostEditAction extends AdminActionBase {

        /**
         * PostEditAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

/*        protected function sanitizeUserInput($excludeKeys = []) {
            parent::sanitizeUserInput(array_merge($excludeKeys,
                                                  [ "content" ]));
        }*/

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $postId = $args["postId"];
            $postHelper = new PostDatabaseHelper($this);
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {


                            $post = Post::getObjectInstance($_POST);
                            if ($postHelper->hasPostUrl($post->getUrl(),
                                                        $postId)
                            ) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("Post with URL '<b>%s</b>' is already exist.%sPlease use another URL."),
                                                        $post->getUrl(),
                                                        Helper::HTML_NEW_LINE);
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }
                            $post->setContent(trim($post->getContent()));
                            //$post->setPublishDateTime(Helper::getDateTimeString());
                            $post->setUserId($this->session->getUser()->getId());
                            $post->setUpdateDateTime(Helper::getDateTimeString());
                            $post->setPublishDateTime(Helper::reformatDateTimeString($post->getPublishDateTime(),
                                                                                     Helper::DATABASE_DATE_TIME_FORMAT,
                                                                                     $this->siteViewOptions->getOptionValue(SiteViewOptions::INPUT_DATE_TIME_FORMAT)));

                            $queryResult = $postHelper->savePost($post);
                            if ($queryResult === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Post saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            if ($postHelper->deletePostCategories($postId) !== false) {

                                $categories = $_POST["categories"];
                                if (is_array($categories)) {
                                    foreach ($categories as $categoryId) {
                                        if (false === $postHelper->savePostCategory($postId,
                                                                                    $categoryId)
                                        ) {
                                            $this->rollbackDatabaseChanges();
                                            $errorMessage = _("Post saving is failed for some reason (during categories saving).");
                                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                            return new DisplaySwitch();
                                        }
                                    }
                                }
                            }
                            else {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Post saving is failed for some reason (during categories saving).");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "postId"         => $postId,
                                                            "updateDateTime" => Helper::reformatDateTimeString($post->getUpdateDateTime(),
                                                                                                               $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_TIME_FORMAT)) ]);
                        }
                    }
                    catch (PDOException $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't save Post.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }

                //$utilHelper = new UtilDatabaseHelper($this);
                //$utilHelper->generateSitemap($this->routes);

                return new DisplaySwitch();
            }
            else {
                $post = $postHelper->getPost($postId);
                if ($post === null) {
                    $errorMessage = sprintf(_("Post with ID '%d' is not exist."),
                                            $postId);
                    $this->session->addSessionMessage($errorMessage,
                                                      Message::MESSAGE_STATUS_ERROR);

                    return new DisplaySwitch(null,
                                             $this->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
                }
                $post->setPublishDateTime(Helper::reformatDateTimeString($post->getPublishDateTime(),
                                                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::INPUT_DATE_TIME_FORMAT)));
                $post->setUpdateDateTime(Helper::reformatDateTimeString($post->getUpdateDateTime(),
                                                                        $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_TIME_FORMAT)));
                $post->setCreationDateTime(Helper::reformatDateTimeString($post->getCreationDateTime(),
                                                                          $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_TIME_FORMAT)));
                $this->smarty->assign("post",
                                      $post);
                $this->smarty->assign("action",
                                      "edit");
                $this->smarty->assign("categories",
                                      $postHelper->getCategories());
                $this->smarty->assign("postStatuses",
                                      PostStatus::getConstants(PostStatus::class));


                return new DisplaySwitch('admin/public/posts/post.tpl');
            }
        }
    }