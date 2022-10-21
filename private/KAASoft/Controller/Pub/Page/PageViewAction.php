<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub\Page;


    use KAASoft\Controller\Admin\Page\PageDatabaseHelper;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Message;

    /**
     * Class PageViewAction
     * @package KAASoft\Controller\Pub\Page
     */
    class PageViewAction extends PublicActionBase {

        /**
         * PageViewAction constructor.
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

            $pageUrl = $args["pageUrl"];

            $pageHelper = new PageDatabaseHelper($this);
            $page = $pageHelper->getPageByURL($pageUrl);
            if ($page === null) {
                $errorMessage = sprintf(_("Page with url '%s' is not exist."),
                                        $pageUrl);
                $this->session->addSessionMessage($errorMessage,
                                                  Message::MESSAGE_STATUS_ERROR);

                return new DisplaySwitch(null,
                                         $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
            }

            $this->smarty->assign("page",
                                  $page);

            $this->smarty->assign("breadcrumbs",
                                  $pageHelper->getParentPages($page));

            $customTemplate = $page->getCustomTemplate();
            if ($customTemplate !== null) {
                $fullCustomTemplatePath = FileHelper::getActiveThemeDirectory(ControllerBase::getThemeSettings()->getActiveTheme()) . DIRECTORY_SEPARATOR . $customTemplate;
                // if custom template file is not exist - ignore it
                if (!file_exists($fullCustomTemplatePath)) {
                    $customTemplate = null;
                }
            }

            return new DisplaySwitch($customTemplate == null ? 'page/page.tpl' : $customTemplate);
        }
    }