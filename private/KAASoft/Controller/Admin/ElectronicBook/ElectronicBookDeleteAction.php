<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\ElectronicBook;


    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class ElectronicBookDeleteAction
     * @package KAASoft\Controller\Admin\Util\ElectronicBooks
     */
    class ElectronicBookDeleteAction extends AdminActionBase {
        /**
         * ElectronicBookDeleteAction constructor.
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
            $electronicBookId = $args["electronicBookId"];

            try {
                if (Helper::isAjaxRequest()) {
                    $electronicBookDatabaseHelper = new ElectronicBookDatabaseHelper($this);

                    if ($this->startDatabaseTransaction()) {
                        $electronicBook = $electronicBookDatabaseHelper->getElectronicBook($electronicBookId);
                        if ($electronicBook !== null) {
                            $filePath = FileHelper::getElectronicBookRootLocation() . $electronicBook->getPath();
                            if (FileHelper::deleteFile($filePath) === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("Couldn't delete eBook '%d' from hard drive for some reason."),
                                                        $electronicBookId);
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch(null,
                                                         null,
                                                         true);
                            }

                            $result = $electronicBookDatabaseHelper->deleteElectronicBook($electronicBookId);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("Couldn't delete eBook '%d' from database for some reason."),
                                                        $electronicBookId);
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch(null,
                                                         null,
                                                         true);
                            }
                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("eBook is deleted successfully.") ]);

                        }
                        else {
                            $this->rollbackDatabaseChanges();
                            $errorMessage = sprintf(_("There is no eBook with Id '%d' in database(%s)."),
                                                    $electronicBookId,
                                                    KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME);
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                            return new DisplaySwitch(null,
                                                     null,
                                                     true);
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
                $errorMessage = sprintf(_("Couldn't delete eBook '%d'.%s%s"),
                                        $electronicBookId,
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                return new DisplaySwitch(null,
                                         null,
                                         true);
            }

            return new DisplaySwitch();
        }
    }