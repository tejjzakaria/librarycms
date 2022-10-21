<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\BookField;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\DatabaseField;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;
    use PDOException;

    /**
     * Class BookFieldCreateAction
     * @package KAASoft\Controller\Admin\BookField
     */
    class BookFieldCreateAction extends AdminActionBase {
        /**
         * BookFieldCreateAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         * @throws \Exception
         */
        protected function action($args) {
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {
                            $bookFieldDatabaseHelper = new BookFieldDatabaseHelper($this);
                            $listValues = ValidationHelper::getArray($_POST["listValues"]);

                            $bookField = DatabaseField::getObjectInstance($_POST);
                            $bookFieldId = $bookFieldDatabaseHelper->saveBookField($bookField);

                            if ($bookFieldId === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("BookField saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            if ($listValues !== null and strcmp($bookField->getControl(),
                                                                DatabaseField::CONTROL_TYPE_SELECT) === 0
                            ) {
                                $result = $bookFieldDatabaseHelper->saveListValues($bookFieldId,
                                                                                   $listValues);
                                if ($result === false) {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = _("BookField list values saving is failed for some reason.");
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                    return new DisplaySwitch();
                                }
                            }

                            $result = $this->kaaSoftDatabase->addTableColumn(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                                             $bookField->getName(),
                                                                             $bookField->getType());
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Couldn't add column to Books table for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "bookFieldId" => $bookFieldId ]);
                        }
                    }
                    catch (PDOException $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't save BookField.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }

                return new DisplaySwitch();
            }
            else {
                $this->smarty->assign("action",
                                      "create");
                $this->smarty->assign("sqlTypes",
                                      DatabaseField::SQL_DATABASE_TYPES);
                $this->smarty->assign("controlTypes",
                                      DatabaseField::CONTROL_TYPES);

                return new DisplaySwitch('admin/book-fields/book-field.tpl');
            }
        }
    }