<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Page;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\Post\Page;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\CSV\CustomTemplateReader;
    use KAASoft\Util\CustomTemplate;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Enum\PostStatus;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class PageCreateAction
     * @package KAASoft\Controller\Admin\Page
     */
    class PageCreateAction extends AdminActionBase {

        /**
         * PageCreateAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

      /*  protected function sanitizeUserInput($excludeKeys = []) {
            parent::sanitizeUserInput(array_merge($excludeKeys,
                                                  [ "content" ]));
        }*/

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $pageHelper = new PageDatabaseHelper($this);

            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {

                            $page = Page::getObjectInstance($_POST);
                            if ($pageHelper->hasPageUrl($page->getUrl())) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("Page with URL '<b>%s</b>' is already exist.%sPlease use another URL."),
                                                        $page->getUrl(),
                                                        Helper::HTML_NEW_LINE);
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            $page->setUrl($pageHelper->buildFullUrl($page));
                            $page->setCreationDateTime(Helper::getDateTimeString());
                            $page->setUpdateDateTime($page->getCreationDateTime());
                            $page->setUserId($this->session->getUser()->getId());
                            $page->setPublishDateTime(Helper::reformatDateTimeString(strcmp($page->getStatus(),
                                                                                            PostStatus::PUBLISH) !== 0 ? $page->getPublishDateTime() : $page->getCreationDateTime(),
                                                                                     Helper::DATABASE_DATE_TIME_FORMAT,
                                                                                     $this->siteViewOptions->getOptionValue(SiteViewOptions::INPUT_DATE_TIME_FORMAT)));

                            $pageId = $pageHelper->savePage($page);
                            if ($pageId === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Page saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "pageId"       => $pageId,
                                                            "creationDate" => Helper::reformatDateTimeString($page->getPublishDateTime(),
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
                $customTemplateReader = new CustomTemplateReader(ControllerBase::getThemeSettings()->getActiveTheme());

                $this->smarty->assign("customTemplates",
                                      $customTemplateReader->readConfigFile(CustomTemplate::PAGE_GROUP));
                $this->smarty->assign("action",
                                      "create");
                $this->smarty->assign("postStatuses",
                                      PostStatus::getConstants(PostStatus::class));
                $this->smarty->assign("pageTree",
                                      $pageHelper->getPageTree());
                $this->smarty->assign("post",
                                      null);

                return new DisplaySwitch('admin/public/pages/page.tpl');
            }
        }


    }