<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Request;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class RequestDeleteAction
     * @package KAASoft\Controller\Admin\Request
     */
    class RequestDeleteAction extends AdminActionBase {
        /**
         * RequestDeleteAction constructor.
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
            $requestId = $args["requestId"];
            $requestDatabaseHelper = new RequestDatabaseHelper($this);
            try {
                if (Helper::isAjaxRequest()) {
                    if ($this->startDatabaseTransaction()) {
                        $request = $requestDatabaseHelper->isRequestExist($requestId);
                        if ($request !== false) {
                            $result = $requestDatabaseHelper->deleteRequest($requestId);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("Couldn't delete Request '%d' for some reason."),
                                                        $requestId);
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Request is deleted successfully.") ]);
                        }
                        else {
                            $this->kaaSoftDatabase->rollbackTransaction();
                            $errorMessage = sprintf(_("There is no Request with Id '%d' in database table \"%s\"."),
                                                    $requestId,
                                                    KAASoftDatabase::$REQUESTS_TABLE_NAME);
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
                $errorMessage = sprintf(_("Couldn't delete Request '%d'.%s%s"),
                                        $requestId,
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
            }

            return new DisplaySwitch();
        }
    }