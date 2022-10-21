<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\BookCopy;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\BookCopy;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use PDOException;

    class BookCopyEditAction extends AdminActionBase {
        /**
         * BookCopyEditAction constructor.
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
            $bookCopyId = $args["bookCopyId"];
            try {
                if (Helper::isAjaxRequest()) {
                    if (Helper::isPostRequest()) { // POST request
                        $bookCopyDatabaseHelper = new BookCopyDatabaseHelper($this);

                        if ($this->startDatabaseTransaction()) {
                            $bookCopy = BookCopy::getObjectInstance($_POST);
                            $result = $bookCopyDatabaseHelper->saveBookCopy($bookCopy);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Book Copy saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }
                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "bookCopyId" => $bookCopyId ]);
                        }
                    }
                }
                else {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
                }

                return new DisplaySwitch();
            }
            catch (PDOException $e) {
                $this->rollbackDatabaseChanges();
                ControllerBase::getLogger()->error($e->getMessage(),
                                                   $e);
                $errorMessage = sprintf(_("Couldn't save Book Copy '%d'.%s%s"),
                                        $bookCopyId,
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                return new DisplaySwitch();
            }
        }
    }