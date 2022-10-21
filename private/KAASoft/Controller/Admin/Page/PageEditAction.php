<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Page;


    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\Post\Page;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\CSV\CustomTemplateReader;
    use KAASoft\Util\CustomTemplate;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Enum\PostStatus;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use PDOException;

    /**
     * Class PageEditAction
     * @package KAASoft\Controller\Admin\Page
     */
    class PageEditAction extends AdminActionBase {

        /**
         * PageEditAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

   /*     protected function sanitizeUserInput($excludeKeys = []) {
            parent::sanitizeUserInput(array_merge($excludeKeys,
                                                  [ "content" ]));
        }*/

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $pageId = $args["pageId"];
            $pageHelper = new PageDatabaseHelper($this);
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {

                            $page = Page::getObjectInstance($_POST);
                            if ($pageHelper->hasPageUrl($page->getUrl(),
                                                        $pageId)
                            ) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("Page with URL '<b>%s</b>' is already exist.%sPlease use another URL."),
                                                        $page->getUrl(),
                                                        Helper::HTML_NEW_LINE);
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }
                            $page->setId($pageId);
                            $page->setUrl($pageHelper->buildFullUrl($page));
                            $page->setUpdateDateTime(Helper::getDateTimeString());
                            $page->setUserId($this->session->getUser()->getId());
                            $page->setPublishDateTime(Helper::reformatDateTimeString($page->getPublishDateTime(),
                                                                                     Helper::DATABASE_DATE_TIME_FORMAT,
                                                                                     $this->siteViewOptions->getOptionValue(SiteViewOptions::INPUT_DATE_TIME_FORMAT)));

                            $queryResult = $pageHelper->savePage($page);
                            if ($queryResult === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Page saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "pageId"         => $pageId,
                                                            "updateDateTime" => Helper::reformatDateTimeString($page->getUpdateDateTime(),
                                                                                                               $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_TIME_FORMAT)) ]);
                        }
                    }
                    catch (PDOException $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't save Page.%s%s"),
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
                $page = $pageHelper->getPage($pageId);

                if ($page === null) {
                    $errorMessage = sprintf(_("Page with ID '%d' is not exist."),
                                            $pageId);
                    $this->session->addSessionMessage($errorMessage,
                                                      Message::MESSAGE_STATUS_ERROR);

                    return new DisplaySwitch(null,
                                             $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
                }

                $page->setPublishDateTime(Helper::reformatDateTimeString($page->getPublishDateTime(),
                                                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_TIME_FORMAT)));
                $page->setUpdateDateTime(Helper::reformatDateTimeString($page->getUpdateDateTime(),
                                                                        $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_TIME_FORMAT)));
                $page->setCreationDateTime(Helper::reformatDateTimeString($page->getCreationDateTime(),
                                                                          $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_TIME_FORMAT)));

                $customTemplateReader = new CustomTemplateReader(ControllerBase::getThemeSettings()->getActiveTheme());

                $this->smarty->assign("customTemplates",
                                      $customTemplateReader->readConfigFile(CustomTemplate::PAGE_GROUP));
                $this->smarty->assign("page",
                                      $page);
                $this->smarty->assign("action",
                                      "edit");
                $this->smarty->assign("postStatuses",
                                      PostStatus::getConstants(PostStatus::class));

                $this->smarty->assign("pageTree",
                                      $pageHelper->getPageTree());

                return new DisplaySwitch('admin/public/pages/page.tpl');
            }
        }
    }