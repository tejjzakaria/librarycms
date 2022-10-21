<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Tag;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\Routes\Admin\TagRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;

    /**
     * Class TagsAction
     * @package KAASoft\Controller\Admin\Tag
     */
    class TagsAction extends AdminActionBase {
        /**
         * TagsAction constructor.
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
            $page = isset( $args["page"] ) ? $args["page"] : 1;
            $tagDatabaseHelper = new TagDatabaseHelper($this);

            $perPage = $this->getPerPage(Session::TAG_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::TAGS_PER_PAGE));
            $sortColumn = $this->getSortingColumn(Session::TAG_SORTING_COLUMN);
            $sortOrder = $this->getSortingOrder(Session::TAG_SORTING_ORDER);


            $paginator = new Paginator($page,
                                       $perPage,
                                       $tagDatabaseHelper->getTagsCount());

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(TagRoutes::TAG_LIST_VIEW_ROUTE)));

            $tags = $tagDatabaseHelper->getTags(null,
                                                $paginator->getOffset(),
                                                $perPage,
                                                $sortColumn,
                                                $sortOrder);

            $this->smarty->assign("tags",
                                  $tags);

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('admin/tags/tag-list.tpl');
            }
            else {
                return new DisplaySwitch('admin/tags/tags.tpl');
            }
        }
    }