<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub\Issue;

    use Exception;
    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\Admin\Issue\IssueDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class BookLostSetPublicAction
     * @package KAASoft\Controller\Pub\Issue
     */
    class BookLostSetPublicAction extends AdminActionBase {
        /**
         * BookLostSetPublicAction constructor.
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
            $issueId = $args["issueId"];
            $isLost = strcmp($args["isLost"],
                             "true") === 0;
            $issueDatabaseHelper = new IssueDatabaseHelper($this);
            $bookDatabaseHelper = new BookDatabaseHelper($this);
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {
                            $user = $this->session->getUser();
                            if ($user !== null) {
                                $issue = $issueDatabaseHelper->getIssue($issueId);
                                if ($issue === null) {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = sprintf(_("Couldn't get issue by id = \"%d\"."),
                                                            $issueId);
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                                }

                                $result = $issueDatabaseHelper->updateIssueLostStatus($issueId,
                                                                                      $isLost);
                                if ($result === false) {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = _("Issue lost status updating is failed for some reason.");
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                                }

                                $result = $bookDatabaseHelper->setBookCopyLost($issue->getBookCopyId());
                                if ($result === false) {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = _("Couldn't update Book status for some reason.");
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                    return new DisplaySwitch();
                                }

                                if ($this->siteViewOptions->getOptionValue(SiteViewOptions::ENABLE_BOOK_LOGS)) {
                                    $issueDatabaseHelper = new IssueDatabaseHelper($this);
                                    $issueLog = $issueDatabaseHelper->getIssueLogByRequestOrIssue(null,
                                                                                                  $issueId);
                                    $issueLog->setIsLost($isLost);
                                    $issueLog->setIssueId($issueId);
                                    $issueLog->setPenalty($issue->getPenalty());
                                    $issueLog->setUpdateDateTime(Helper::getDateTimeString());

                                    $issueLogId = $issueDatabaseHelper->saveIssueLog($issueLog);
                                    if ($issueLogId === false) {
                                        $this->rollbackDatabaseChanges();
                                        $errorMessage = _("Issue Log saving is failed for some reason.");
                                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                        return new DisplaySwitch();
                                    }
                                }

                                $this->commitDatabaseChanges();
                                $this->putArrayToAjaxResponse([ "issueId" => $issueId ]);
                            }
                            else {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Please log in to update book.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }
                        }
                    }
                    catch (Exception $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't update lost status of Issue.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }
            }
            else {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
            }

            return new DisplaySwitch();
        }
    }