<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Category;

    use KAASoft\Controller\Admin\Post\PostDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\Post\Category;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class CategoryCreateAction
     * @package KAASoft\Controller\Admin\Category
     */
    class CategoryCreateAction extends AdminActionBase {
        /**
         * CategoryCreateAction constructor.
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
            try {
                if (Helper::isAjaxRequest() && Helper::isPostRequest()) {
                    $publicHelper = new PostDatabaseHelper($this);
                    if ($this->startDatabaseTransaction()) {
                        $category = Category::getObjectInstance($_POST);

                        $categoryId = $publicHelper->saveCategory($category);
                        if ($categoryId === false) {
                            $this->rollbackDatabaseChanges();
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Category saving is failed for some reason.") ]);

                            return new DisplaySwitch();
                        }

                        // if all is ok
                        $this->commitDatabaseChanges();
                        $this->putArrayToAjaxResponse([ "categoryId" => $categoryId ]);
                    }
                }
                else {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
                }
            }
            catch (PDOException $e) {
                $this->rollbackDatabaseChanges();
                ControllerBase::getLogger()->error($e->getMessage(),
                                                   $e);
                $errorMessage = sprintf(_("Couldn't create Category.%s%s"),
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                return new DisplaySwitch();
            }

            return new DisplaySwitch();
        }
    }