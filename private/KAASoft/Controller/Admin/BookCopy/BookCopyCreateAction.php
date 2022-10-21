<?php
    /**
 * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
 */

    namespace KAASoft\Controller\Admin\BookCopy;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\BookCopy;
    use KAASoft\Database\Entity\General\Issue;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class BookCopyCreateAction
     * @package KAASoft\Controller\Admin\BookCopy
     */
    class BookCopyCreateAction extends AdminActionBase {
        /**
         * BookCopyCreateAction constructor.
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
            try {
                if (Helper::isAjaxRequest() && Helper::isPostRequest()) {
                    $bookCopyDatabaseHelper = new BookCopyDatabaseHelper($this);
                    if ($this->startDatabaseTransaction()) {
                        $bookCopy = BookCopy::getObjectInstance($_POST);
                        $bookCopy->setIssueStatus(Issue::ISSUE_STATUS_AVAILABLE);

                        $bookCopyId = $bookCopyDatabaseHelper->saveBookCopy($bookCopy);
                        if ($bookCopyId === false) {
                            $this->rollbackDatabaseChanges();
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Book Copy saving is failed for some reason.") ]);

                            return new DisplaySwitch();
                        }

                        // if all is ok
                        $this->commitDatabaseChanges();
                        $this->putArrayToAjaxResponse([ "bookCopyId" => $bookCopyId ]);
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
                $errorMessage = sprintf(_("Couldn't create Book Copy.%s%s"),
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                return new DisplaySwitch();
            }

            return new DisplaySwitch();
        }
    }