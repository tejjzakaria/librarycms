<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Author;

    use Exception;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Author;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;

    /**
     * Class AuthorEditAction
     * @package KAASoft\Controller\Admin\Author
     */
    class AuthorEditAction extends AdminActionBase {
        /**
         * AuthorEditAction constructor.
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
            $authorId = $args["authorId"];
            $authorDatabaseHelper = new AuthorDatabaseHelper($this);
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {
                            //$authorId = $_POST["authorId"];
                            $author = Author::getObjectInstance($_POST);
                            $author->setId($authorId);

                            $result = $authorDatabaseHelper->saveAuthor($author);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Author saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "authorId" => $authorId ]);
                        }
                    }
                    catch (Exception $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't save Author.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }

                return new DisplaySwitch();
            }
            else {
                $author = null;
                if ($authorId !== null) {
                    $author = $authorDatabaseHelper->getAuthor($authorId);

                    if ($author === null) {
                        $this->session->addSessionMessage(sprintf(_("Author with id = '%d' is not found."),
                                                                  $authorId),
                                                          Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
                    }
                }
                $this->smarty->assign("action",
                                      "edit");
                $this->smarty->assign("author",
                                      $author);

                return new DisplaySwitch("admin/authors/author.tpl");
            }
        }
    }