<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Image;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Environment\Routes\Admin\ImageRoutes;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class ImagesViewAction
     * @package KAASoft\Controller\Admin\Image
     */
    class ImagesViewAction extends AdminActionBase {
        /**
         * ImagesViewAction constructor.
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
            if (Helper::isAjaxRequest()) {
                $viewType = ValidationHelper::getString($_POST["viewType"]);
                $page = isset( $args["page"] ) ? $args["page"] : 1;
                $imageHelper = new ImageDatabaseHelper($this);
                $paginator = new Paginator($page,
                                           $this->siteViewOptions->getOptionValue(SiteViewOptions::IMAGES_PER_PAGE),
                                           $imageHelper->getImageCount(true));
                $this->smarty->assign("pages",
                                      $paginator->preparePages($page,
                                                               $this->routeController->getRouteString(ImageRoutes::IMAGE_LIST_VIEW_ROUTE)));

                $this->smarty->assign("images",
                                      $imageHelper->getImages($paginator->getOffset(),
                                                              $this->siteViewOptions->getOptionValue(SiteViewOptions::IMAGES_PER_PAGE),
                                                              true));

                $this->smarty->assign("viewType",
                                      $viewType);

                return new DisplaySwitch('admin/public/images/imageList.tpl');
            }
            else {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
            }

            return new DisplaySwitch();
        }
    }