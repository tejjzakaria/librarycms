<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\BookField;

    use Exception;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\DatabaseField;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class BookFieldEditAction
     * @package KAASoft\Controller\Admin\BookField
     */
    class BookFieldEditAction extends AdminActionBase {
        /**
         * BookFieldEditAction constructor.
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
            $bookFieldId = $args["bookFieldId"];
            $bookFieldDatabaseHelper = new BookFieldDatabaseHelper($this);
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {
                            $bookFieldDatabaseHelper = new BookFieldDatabaseHelper($this);
                            $listValues = ValidationHelper::getArray($_POST["listValues"]);
                            //$oldFieldName = ValidationHelper::getString($_POST["oldName"]);

                            $bookField = DatabaseField::getObjectInstance($_POST);
                            $bookField->setId($bookFieldId);
                            $result = $bookFieldDatabaseHelper->saveBookField($bookField);

                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("BookField saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            $result = $bookFieldDatabaseHelper->deleteListValues($bookFieldId);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("BookField list cleanup is failed for some reason.");
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

                            /* $result = $this->kaaSoftDatabase->changeTableColumn(KAASoftDatabase::$BOOKS_TABLE_NAME,
                                                                                 $oldFieldName,
                                                                                 $bookField->getName(),
                                                                                 $bookField->getType());
                             if ($result === false) {
                                 $this->rollbackDatabaseChanges();
                                 $errorMessage = _("Couldn't add column to Books table for some reason.");
                                 $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                 return new DisplaySwitch();
                             }*/

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "bookFieldId" => $bookFieldId ]);
                        }
                    }
                    catch (Exception $e) {
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
                $bookField = null;
                if ($bookFieldId !== null) {
                    $bookField = $bookFieldDatabaseHelper->getBookField($bookFieldId);

                    if ($bookField === null) {
                        $this->session->addSessionMessage(sprintf(_("BookField with id = '%d' is not found."),
                                                                  $bookFieldId),
                                                          Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
                    }
                }
                $this->smarty->assign("action",
                                      "edit");
                $this->smarty->assign("sqlTypes",
                                      DatabaseField::SQL_DATABASE_TYPES);
                $this->smarty->assign("controlTypes",
                                      DatabaseField::CONTROL_TYPES);

                $this->smarty->assign("bookField",
                                      $bookField);

                return new DisplaySwitch('admin/book-fields/book-field.tpl');
            }
        }
    }