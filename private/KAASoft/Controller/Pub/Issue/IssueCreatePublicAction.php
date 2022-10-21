<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub\Issue;

    use DateInterval;
    use DateTime;
    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\Admin\BookCopy\BookCopyDatabaseHelper;
    use KAASoft\Controller\Admin\Issue\IssueDatabaseHelper;
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
     * Class IssueCreatePublicAction
     * @package KAASoft\Controller\Pub\Issue
     */
    class IssueCreatePublicAction extends AdminActionBase {
        /**
         * IssueCreatePublicAction constructor.
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
            $bookId = ValidationHelper::getArray($_POST["bookId"]);
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {
                            $issueDatabaseHelper = new IssueDatabaseHelper($this);
                            $bookDatabaseHelper = new BookDatabaseHelper($this);
                            $bookCopyDatabaseHelper = new BookCopyDatabaseHelper($this);

                            $user = $this->session->getUser();
                            if ($user !== null) {
                                $userRole = $user->getRole();

                                $userIssuedBookCount = $issueDatabaseHelper->getUserIssuedBookCount($user->getId());

                                if ($userIssuedBookCount < $userRole->getIssueBookLimit() and ( $userIssuedBookCount + 1 <= $userRole->getIssueBookLimit() )) {
                                    $dateInterval = new DateInterval("P" . $userRole->getIssueDayLimit() . "D");
                                    $expiryDate = new DateTime();
                                    $expiryDate->add($dateInterval);

                                    $bookCopy = $bookCopyDatabaseHelper->getAvailableBookCopy($bookId);

                                    if ($bookCopy !== null and $bookCopy instanceof BookCopy) {
                                        $issue = Issue::getObjectInstance($_POST);
                                        $issue->setUserId($user->getId());
                                        $issue->setIssueDate(Helper::getDateString());
                                        $issue->setBookId($bookCopy->getBookId());
                                        $issue->setBookCopyId($bookCopy->getId());
                                        $issue->setExpiryDate($expiryDate->format(Helper::DATABASE_DATE_FORMAT));
                                        $issue->setIsLost(false);

                                        $issueId = $issueDatabaseHelper->saveIssue($issue);
                                        if ($issueId === false) {
                                            $this->rollbackDatabaseChanges();
                                            $errorMessage = _("Couldn't issue book for some reason.");
                                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                            return new DisplaySwitch();
                                        }
                                        $issue->setId($issueId);
                                    }
                                    else {
                                        $this->rollbackDatabaseChanges();
                                        $errorMessage = _("There is no available book copies.");
                                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                        return new DisplaySwitch();
                                    }

                                    $result = $bookDatabaseHelper->setBookCopyIssued($bookCopy->getId());
                                    if ($result === false) {
                                        $this->rollbackDatabaseChanges();
                                        $errorMessage = _("Couldn't update book status for some reason.");
                                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                        return new DisplaySwitch();
                                    }

                                    // log issues
                                    if ($this->siteViewOptions->getOptionValue(SiteViewOptions::ENABLE_BOOK_LOGS)) {
                                        $book = $bookCopy->getBook();
                                        $issueLog = $issueDatabaseHelper->getIssueLog();
                                        $issueLog->setBookId($bookCopy->getBookId());
                                        $issueLog->setBookCopyId($bookCopy->getId());
                                        $issueLog->setBookSN($bookCopy->getBookSN());
                                        if ($book !== null and $book instanceof Book) {
                                            $issueLog->setBookTitle($book->getTitle());
                                            $issueLog->setBookISBN(ValidationHelper::isEmpty($book->getISBN13()) ? $book->getISBN10() : $book->getISBN13());
                                        }
                                        $issueLog->setUserId($user->getId());
                                        $issueLog->setUserFullName($user->getFirstName() . " " . $user->getMiddleName() . " " . $user->getLastName());
                                        $issueLog->setIsLost(false);

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
                                    }
                                    $this->commitDatabaseChanges();
                                    $this->putArrayToAjaxResponse([ "issueId" => $issueId,
                                        Controller::AJAX_PARAM_NAME_SUCCESS => _("Book is successfully issued.")]);
                                }
                                else {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = _("You already got max number of books(or already issued books + required books more than your user's limit).");
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                    return new DisplaySwitch();
                                }
                            }
                            else {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Please log in to get book.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }
                        }
                    }
                    catch (PDOException $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't issue book for some reason.%s%s%s%s(%d)"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage(),
                                                Helper::HTML_NEW_LINE,
                                                $e->getFile(),
                                                $e->getLine());
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