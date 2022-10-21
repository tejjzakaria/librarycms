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

    class CategoryEditAction extends AdminActionBase {
        /**
         * CategoryEditAction constructor.
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
            $categoryId = $args["categoryId"];
            try {
                if (Helper::isAjaxRequest()) {
                    if (Helper::isPostRequest()) { // POST request
                        $publicHelper = new PostDatabaseHelper($this);

                        if ($this->startDatabaseTransaction()) {
                            $category = Category::getObjectInstance($_POST);
                            $result = $publicHelper->saveCategory($category);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Category saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }
                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "categoryId" => $categoryId ]);
                        }
                    }
                }
                else {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
                }

                return new DisplaySwitch();
            }
            catch (PDOException $e) {
                $this->rollbackDatabaseChanges();
                ControllerBase::getLogger()->error($e->getMessage(),
                                                   $e);
                $errorMessage = sprintf(_("Couldn't save Category '%d'.%s%s"),
                                        $categoryId,
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                return new DisplaySwitch();
            }
        }
    }