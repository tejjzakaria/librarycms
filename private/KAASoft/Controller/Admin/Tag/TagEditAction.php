<?php
    /**
 * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
 */

    namespace KAASoft\Controller\Admin\Tag;

    use Exception;
    use KAASoft\Database\Entity\Util\Tag;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;

    /**
     * Class TagEditAction
     * @package KAASoft\Controller\Admin\Tag
     */
    class TagEditAction extends AdminActionBase {
        /**
         * TagEditAction constructor.
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
            $tagId = $args["tagId"];
            $tagDatabaseHelper = new TagDatabaseHelper($this);
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {
                            //$tagId = $_POST["tagId"];
                            $tag = Tag::getObjectInstance($_POST);
                            $tag->setId($tagId);

                            $result = $tagDatabaseHelper->saveTag($tag);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Tag saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "tagId" => $tagId ]);
                        }
                    }
                    catch (Exception $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't save Tag.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }

                return new DisplaySwitch();
            }
            else {
                $tag = null;
                if ($tagId !== null) {
                    $tag = $tagDatabaseHelper->getTag($tagId);

                    if ($tag === null) {
                        $this->session->addSessionMessage(sprintf(_("Tag with id = '%d' is not found."),
                                                                  $tagId),
                                                          Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
                    }
                }
                $this->smarty->assign("action",
                                      "edit");
                $this->smarty->assign("tag",
                                      $tag);

                return new DisplaySwitch('admin/tags/tag.tpl');
            }
        }
    }