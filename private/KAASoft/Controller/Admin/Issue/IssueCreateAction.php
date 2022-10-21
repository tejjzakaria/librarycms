<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Issue;

    use DateInterval;
    use DateTime;
    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\Admin\BookCopy\BookCopyDatabaseHelper;
    use KAASoft\Controller\Admin\User\UserDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Database\Entity\General\BookCopy;
    use KAASoft\Database\Entity\General\Issue;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;
    use PDOException;

    /**
     * Class IssueCreateAction
     * @package KAASoft\Controller\Admin\Issue
     */
    class IssueCreateAction extends AdminActionBase {
        /**
         * IssueCreateAction constructor.
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
            $userId = ValidationHelper::getNullableInt($_POST["userId"]);
            $bookCopyIds = ValidationHelper::getArray($_POST["bookCopyIds"]);
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {
                            $issueDatabaseHelper = new IssueDatabaseHelper($this);
                            $userDatabaseHelper = new UserDatabaseHelper($this);
                            $bookDatabaseHelper = new BookDatabaseHelper($this);
                            $bookCopyDatabaseHelper = new BookCopyDatabaseHelper($this);

                            $user = $userDatabaseHelper->getUser($userId);
                            if ($user !== null) {
                                $userRole = $user->getRole();

                                $userIssuedBookCount = $issueDatabaseHelper->getUserIssuedBookCount($userId);
                                $requiredBooksCount = count($bookCopyIds);

                                $issueIds = [];
                                if ($userIssuedBookCount < $userRole->getIssueBookLimit() and ( $userIssuedBookCount + $requiredBooksCount <= $userRole->getIssueBookLimit() )) {
                                    $dateInterval = new DateInterval("P" . $userRole->getIssueDayLimit() . "D");
                                    $expiryDate = new DateTime();
                                    $expiryDate->add($dateInterval);

                                    $bookCopies = $bookCopyDatabaseHelper->getBookCopiesByIds($bookCopyIds);
                                    $bookIssues = [];
                                    foreach ($bookCopies as $bookCopy) {
                                        if ($bookCopy instanceof BookCopy) {
                                            $bookActualQuantity = $bookDatabaseHelper->getBookActualQuantity($bookCopy->getBookId());

                                            if ($bookActualQuantity <= 0) {
                                                $this->rollbackDatabaseChanges();
                                                $errorMessage = sprintf(_("There is no enough book \"%d\"."),
                                                                        $bookCopy->getBookId());
                                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                                return new DisplaySwitch();
                                            }

                                            $issue = Issue::getObjectInstance($_POST);
                                            $issue->setIssueDate(Helper::getDateString());
                                            $issue->setBookId($bookCopy->getBookId());
                                            $issue->setBookCopyId($bookCopy->getId());
                                            $issue->setExpiryDate($expiryDate->format(Helper::DATABASE_DATE_FORMAT));
                                            $issue->setIsLost(false);

                                            $issueId = $issueDatabaseHelper->saveIssue($issue);
                                            $issueIds[] = $issueId;
                                            if ($issueId === false) {
                                                $this->rollbackDatabaseChanges();
                                                $errorMessage = _("Issue saving is failed for some reason.");
                                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                                return new DisplaySwitch();
                                            }
                                            $issue->setId($issueId);
                                            $bookIssues[$bookCopy->getId()] = $issue;
                                        }
                                    }
                                    $result = $bookDatabaseHelper->setBookCopyIssued($bookCopyIds);
                                    if ($result === false) {
                                        $this->rollbackDatabaseChanges();
                                        $errorMessage = _("Couldn't update Book status for some reason.");
                                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                        return new DisplaySwitch();
                                    }

                                    // log issues
                                    if ($this->siteViewOptions->getOptionValue(SiteViewOptions::ENABLE_BOOK_LOGS)) {
                                        $issueLogs = [];
                                        foreach ($bookCopies as $bookCopy) {
                                            if ($bookCopy instanceof BookCopy) {
                                                $book = $bookCopy->getBook();
                                                $issueLog = $issueDatabaseHelper->getIssueLog();
                                                $issueLog->setBookId($bookCopy->getBookId());
                                                $issueLog->setBookCopyId($bookCopy->getId());
                                                $issueLog->setBookSN($bookCopy->getBookSN());
                                                if($book !== null and $book instanceof Book) {
                                                    $issueLog->setBookTitle($book->getTitle());
                                                    $issueLog->setBookISBN(ValidationHelper::isEmpty($book->getISBN13()) ? $book->getISBN10() : $book->getISBN13());
                                                }
                                                $issueLog->setUserId($userId);
                                                $issueLog->setUserFullName($user->getFirstName() . " " . $user->getMiddleName() . " " . $user->getLastName());
                                                $issueLog->setIsLost(false);

                                                $issue = $bookIssues[$bookCopy->getId()];
                                                $issueLog->setIssueId($issue->getId());
                                                $issueLog->setIssueDate($issue->getIssueDate());
                                                $issueLog->setExpiryDate($issue->getExpiryDate());
                                                $issueLog->setUpdateDateTime(Helper::getDateTimeString());

                                                $issueLogId = $issueDatabaseHelper->saveIssueLog($issueLog);
                                                if ($issueLogId === false) {
                                                    $this->rollbackDatabaseChanges();
                                                    $errorMessage = _("Issue Log saving is failed for some reason.");
                                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                                    return new DisplaySwitch();
                                                }

                                                $issueLogs [] = $issueLog;
                                            }
                                        }
                                    }
                                    $this->commitDatabaseChanges();
                                    $this->putArrayToAjaxResponse([ "issueIds" => $issueIds ]);
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
                                $errorMessage = sprintf(_("User with ID \"%d\" couldn't be found."),
                                                        $userId);
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }
                        }
                    }
                    catch (PDOException $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't save Issue.%s%s%s%s(%d)"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage(),
                                                Helper::HTML_NEW_LINE,
                                                $e->getFile(),
                                                $e->getLine());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }

                return new DisplaySwitch();
            }
            else {
                $this->smarty->assign("action",
                                      "create");

                return new DisplaySwitch('admin/issues/issue.tpl');
            }
        }
    }