<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util;


    use Exception;
    use KAASoft\Controller\Admin\BookField\BookFieldDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Environment\Routes\Admin\UtilRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;

    /**
     * Class FilterSettingsEditAction
     * @package KAASoft\Controller\Admin\Util
     */
    class FilterSettingsEditAction extends AdminActionBase {
        /**
         * FilterSettingsEditAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         * @throws Exception
         */
        protected function action($args) {
            $customFields = Book::getCustomFields();

            $this->smarty->assign("customFields",
                                  $customFields);

            if (Helper::isPostRequest()) {
                $filterableFields = $_POST["filterableFields"];
                $bookFieldDatabaseHelper = new BookFieldDatabaseHelper($this);

                if ($this->startDatabaseTransaction()) {
                    $result = $bookFieldDatabaseHelper->setBookFieldFilterable($filterableFields);
                    if ($result === false) {
                        $this->rollbackDatabaseChanges();
                        Session::addSessionMessage(_("Couldn't save filterable book fields."),
                                                   Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 $this->getRouteString(UtilRoutes::FILTER_SETTINGS_ROUTE));
                    }

                    $nonFilterableFields = [];
                    foreach ($customFields as $customField) {
                        $customField->setIsFilterable(false);
                        foreach ($filterableFields as $id) {
                            if ($customField->getId() == $id) {
                                $customField->setIsFilterable(true);
                                break;
                            }
                        }

                        if (!$customField->isFilterable()) {
                            $nonFilterableFields[] = $customField->getId();
                        }
                    }
                    $result = $bookFieldDatabaseHelper->setBookFieldNonFilterable($nonFilterableFields);
                    if ($result === false) {
                        $this->rollbackDatabaseChanges();
                        Session::addSessionMessage(_("Couldn't save non filterable book fields."),
                                                   Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 $this->getRouteString(UtilRoutes::FILTER_SETTINGS_ROUTE));
                    }

                    $this->commitDatabaseChanges();
                    Session::addSessionMessage(_("Book fields are successfully updated."),
                                               Message::MESSAGE_STATUS_INFO);

                    return new DisplaySwitch(null,
                                             $this->getRouteString(UtilRoutes::FILTER_SETTINGS_ROUTE));
                }
            }

            return new DisplaySwitch('admin/filterSettings.tpl');
        }
    }