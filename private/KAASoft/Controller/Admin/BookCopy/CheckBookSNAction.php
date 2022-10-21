<?php
    /**
 * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
 */

    namespace KAASoft\Controller\Admin\BookCopy;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class CheckBookSNAction
     * @package KAASoft\Controller\Admin\User
     */
    class CheckBookSNAction extends AdminActionBase {
        /**
         * CheckBookSNAction constructor.
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
            $bookSN = isset( $_POST["bookSN"] ) ? $_POST["bookSN"] : "";
            $bookCopyDatabaseHelper = new BookCopyDatabaseHelper($this);
            if (Helper::isAjaxRequest()) {
                Helper::printAsJSON(!$bookCopyDatabaseHelper->isBookCopyExists($bookSN));
            }
            else {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
            }

            return new DisplaySwitch();
        }
    }