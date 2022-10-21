<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\General;

    use DateInterval;
    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\Admin\Issue\IssueDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Database\Entity\Util\Role;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class IndexAction
     * @package KAASoft\Controller\Admin\General
     */
    class IndexAction extends AdminActionBase {

        /**
         * IndexAction constructor.
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
            $bookHelper = new BookDatabaseHelper($this);
            $issueHelper = new IssueDatabaseHelper($this);

            if ($this->session->getUser()->getRole()->getId() === Role::BUILTIN_USER_ROLES[Role::ADMIN_ROLE_ID] or $this->session->getUser()->getRole()->getId() === Role::BUILTIN_USER_ROLES[Role::LIBRARIAN_ROLE_ID]) {
                $this->smarty->assign("bookTotal",
                                      $bookHelper->getBooksCount());

                $dateInterval = new DateInterval("P1M");
                $startDateTime = Helper::getDateTimeFromString(Helper::getDateString(),
                                                               date_default_timezone_get());
                $monthAgo = $startDateTime->sub($dateInterval)->format(Helper::DATABASE_DATE_FORMAT);

                $this->smarty->assign("issueCount",
                                      $issueHelper->getIssuesCount($monthAgo));
                $this->smarty->assign("returnCount",
                                      $issueHelper->getReturnBookCount($monthAgo));
                $this->smarty->assign("lostCount",
                                      $issueHelper->getLostBookCount($monthAgo));

                $this->smarty->assign("lastIssuedBooks",
                                      $bookHelper->getUserIssuedBooks(null,
                                                                      0,
                                                                      10));
                $this->smarty->assign("lastRequestedBooks",
                                      $bookHelper->getUserRequestedBooks(null,
                                                                         0,
                                                                         10));

            }
            else {
                $this->smarty->assign("lastIssuedBooks",
                                      $bookHelper->getUserIssuedBooks($this->session->getUser()->getId(),
                                                                      0,
                                                                      10/*$this->siteViewOptions->getOptionValue(SiteViewOptions::BOOKS_PER_PAGE_INDEX)*/));
                $this->smarty->assign("lastRequestedBooks",
                                      $bookHelper->getUserRequestedBooks($this->session->getUser()->getId(),
                                                                         0,
                                                                         10/*$this->siteViewOptions->getOptionValue(SiteViewOptions::BOOKS_PER_PAGE_INDEX)*/));
            }

            return new DisplaySwitch('admin/index.tpl');
        }
    }