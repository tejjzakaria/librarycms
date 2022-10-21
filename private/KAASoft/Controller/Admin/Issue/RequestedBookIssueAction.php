<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Issue;


    use DateInterval;
    use DateTime;
    use Exception;
    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\Admin\Request\RequestDatabaseHelper;
    use KAASoft\Controller\Admin\User\UserDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Issue;
    use KAASoft\Database\Entity\General\Request;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;
    use PDOException;

    /**
     * Class RequestedBookIssueAction
     * @package KAASoft\Controller\Admin\Issue
     */
    class RequestedBookIssueAction extends AdminActionBase {
        /**
         * RequestedBookIssueAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         * @throws Exception
         */
        protected function action($args) {
            $requestId = ValidationHelper::getNullableInt($args["requestId"]);
            $bookCopyId = ValidationHelper::getArray($_POST["bookCopyId"]);
            if (Helper::isAjaxRequest() and Helper::isPostRequest()) { // POST request
                try {
                    if ($this->startDatabaseTransaction()) {
                        $issueDatabaseHelper = new IssueDatabaseHelper($this);
                        $requestDatabaseHelper = new RequestDatabaseHelper($this);
                        $userDatabaseHelper = new UserDatabaseHelper($this);
                        $bookDatabaseHelper = new BookDatabaseHelper($this);

                        $request = $requestDatabaseHelper->getRequest($requestId);
                        if ($request !== null) {
                            $userId = $request->getUserId();
                            $bookId = $request->getBookId();
                            $user = $userDatabaseHelper->getUser($userId);
                            if ($user !== null) {
                                $userRole = $user->getRole();

                                $userIssuedBookCount = $issueDatabaseHelper->getUserIssuedBookCount($user->getId());
                                $requiredBooksCount = 1;

                                //$issueIds = [];
                                if ($userIssuedBookCount < $userRole->getIssueBookLimit() and ( $userIssuedBookCount + $requiredBooksCount <= $userRole->getIssueBookLimit() )) {
                                    $dateInterval = new DateInterval("P" . $userRole->getIssueDayLimit() . "D");
                                    $expiryDate = new DateTime();
                                    $expiryDate->add($dateInterval);

                                    $bookActualQuantity = $bookDatabaseHelper->getBookActualQuantity($bookId);

                                    if ($bookActualQuantity <= 0) {
                                        $this->rollbackDatabaseChanges();
                                        $errorMessage = sprintf(_("There is no enough of book with ID = \"%d\"."),
                                                                $bookId);
                                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                        return new DisplaySwitch();
                                    }

                                    $issue = Issue::getObjectInstance($_POST);
                                    $issue->setIssueDate(Helper::getDateString());
                                    $issue->setBookId($bookId);
                                    $issue->setBookCopyId($bookCopyId);
                                    $issue->setUserId($userId);
                                    $issue->setRequestId($requestId);
                                    $issue->setExpiryDate($expiryDate->format(Helper::DATABASE_DATE_FORMAT));
                                    $issue->setIsLost(false);

                                    $issueId = $issueDatabaseHelper->saveIssue($issue);

                                    if ($issueId === false) {
                                        $this->rollbackDatabaseChanges();
                                        $errorMessage = _("Issue saving is failed for some reason.");
                                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                        return new DisplaySwitch();
                                    }

                                    $result = $bookDatabaseHelper->setBookCopyIssued($bookCopyId);
                                    if ($result === false) {
                                        $this->rollbackDatabaseChanges();
                                        $errorMessage = _("Couldn't update Book status for some reason.");
                                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                        return new DisplaySwitch();
                                    }

                                    $result = $requestDatabaseHelper->updateRequestStatus($requestId,
                                                                                          Request::REQUEST_STATUS_ACCEPTED);
                                    if ($result === false) {
                                        $this->rollbackDatabaseChanges();
                                        $errorMessage = _("Request status update is failed for some reason.");
                                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                        return new DisplaySwitch();
                                    }

                                    if ($this->siteViewOptions->getOptionValue(SiteViewOptions::ENABLE_BOOK_LOGS)) {
                                        $issueDatabaseHelper = new IssueDatabaseHelper($this);
                                        $issueLog = $issueDatabaseHelper->getIssueLogByRequestOrIssue($requestId);
                                        if (strcmp($issueLog->getRequestStatus(),
                                                   Request::REQUEST_STATUS_PENDING) == 0
                                        ) {
                                            $issueLog->setRequestStatus(Request::REQUEST_STATUS_ACCEPTED);
                                            $issueLog->setRequestAcceptRejectDateTime(Helper::getDateTimeString());
                                        }
                                        $issueLog->setIssueId($issueId);
                                        $issueLog->setBookCopyId($bookCopyId);
                                        $issueLog->setIssueDate($issue->getIssueDate());
                                        $issueLog->setExpiryDate($issue->getExpiryDate());

                                        $issueLog->setUpdateDateTime(Helper::getDateTimeString());

                                        $issueLogId = $issueDatabaseHelper->saveIssueLog($issueLog);
                                        if ($issueLogId === false) {
                                            $this->rollbackDatabaseChanges();
                                            $errorMessage = _("Request Log saving is failed for some reason.");
                                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                            return new DisplaySwitch();
                                        }
                                    }

                                    $this->commitDatabaseChanges();
                                    $this->putArrayToAjaxResponse([ "issueId" => $issueId ]);
                                }
                                else {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = _("User is already got max number of books(or already issued books + required books more than user's limit).");
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                    return new DisplaySwitch();
                                }
                            }
                            else {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("User with ID = \"%d\" couldn't be found."),
                                                        $userId);
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }
                        }
                        else {
                            $this->rollbackDatabaseChanges();
                            $errorMessage = sprintf(_("Request with ID = \"%d\" couldn't be found."),
                                                    $requestId);
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                            return new DisplaySwitch();
                        }
                    }
                }
                catch (PDOException $e) {
                    $this->rollbackDatabaseChanges();
                    ControllerBase::getLogger()->error($e->getMessage(),
                                                       $e);
                    $errorMessage = sprintf(_("Couldn't save Issue.%s%s%s%s(%d)."),
                                            Helper::HTML_NEW_LINE,
                                            $e->getMessage(),
                                            Helper::HTML_NEW_LINE,
                                            $e->getFile(),
                                            $e->getLine());
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                }
            }
            else {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
            }

            return new DisplaySwitch();
        }
    }
