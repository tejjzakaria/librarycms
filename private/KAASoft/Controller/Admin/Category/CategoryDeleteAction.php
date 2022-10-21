<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Category;

    use KAASoft\Controller\Admin\Post\PostDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class CategoryDeleteAction
     * @package KAASoft\Controller\Admin\Category
     */
    class CategoryDeleteAction extends AdminActionBase {
        /**
         * CategoryDeleteAction constructor.
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
                    $publicHelper = new PostDatabaseHelper($this);

                    if ($this->startDatabaseTransaction()) {
                        $category = $publicHelper->getCategory($categoryId);
                        if ($category !== null) {
                            $result = $publicHelper->deleteCategory($categoryId);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("Couldn't delete Category '%d' for some reason."),
                                                        $categoryId);
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }
                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Category is deleted successfully.") ]);
                        }
                        else {
                            $this->rollbackDatabaseChanges();
                            $errorMessage = sprintf(_("There is no Category with Id '%d' in database(%s)."),
                                                    $categoryId,
                                                    KAASoftDatabase::$CATEGORIES_TABLE_NAME);
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                            return new DisplaySwitch();
                        }
                    }
                    else {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Database transaction couldn't be created.") ]);
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
                $errorMessage = sprintf(_("Couldn't delete Category '%d'.%s%s"),
                                        $categoryId,
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                return new DisplaySwitch();
            }

            return new DisplaySwitch();
        }
    }