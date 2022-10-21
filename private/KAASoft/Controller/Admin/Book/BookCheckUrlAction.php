<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Book;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class BookCheckUrlAction
     * @package KAASoft\Controller\Admin\User
     */
    class BookCheckUrlAction extends AdminActionBase {
        /**
         * BookCheckUrlAction constructor.
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
            $url = isset( $_POST["url"] ) ? $_POST["url"] : "";
            $bookDatabaseHelper = new BookDatabaseHelper($this);
            if (Helper::isAjaxRequest()) {
                Helper::printAsJSON(!$bookDatabaseHelper->isBookUrlExist($url));
            }
            else {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
            }

            return new DisplaySwitch();
        }
    }