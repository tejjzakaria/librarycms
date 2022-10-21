<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Category;

    use KAASoft\Controller\Admin\Post\PostDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Util\DisplaySwitch;

    /**
     * Class CategoriesAction
     * @package KAASoft\Controller\Admin\Category
     */
    class CategoriesAction extends AdminActionBase {
        /**
         * CategoriesAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $publicHelper = new PostDatabaseHelper($this);
            $this->smarty->assign("categories",
                                  $publicHelper->getCategories());

            return new DisplaySwitch('admin/public/categories/categories.tpl');
        }
    }