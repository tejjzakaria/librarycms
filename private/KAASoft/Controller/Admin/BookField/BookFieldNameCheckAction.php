<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\BookField;

    use KAASoft\Controller\Controller;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class BookFieldNameCheckAction
     * @package KAASoft\Controller\Admin\Book
     */
    class BookFieldNameCheckAction extends PublicActionBase {

        /**
         * BookFieldNameCheckAction constructor.
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
            $this->setJsonContentType();
            $newBookFieldName = ValidationHelper::getString($_POST["bookFieldName"]);

            if (ValidationHelper::isEmpty($newBookFieldName)) {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Book field shouldn't be empty.") ]);

                return new DisplaySwitch();
            }

            $bookFieldDatabaseHelper = new BookFieldDatabaseHelper($this);
            $isColumnExists = $bookFieldDatabaseHelper->isBookColumnExist($newBookFieldName);

            if (Helper::isAjaxRequest()) {
                Helper::printAsJSON(!$isColumnExists);
            }
            else {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
            }

            return new DisplaySwitch();
        }
    }