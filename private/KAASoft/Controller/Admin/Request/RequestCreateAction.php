<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Request;

    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\Admin\Issue\IssueDatabaseHelper;
    use KAASoft\Controller\Admin\User\UserDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Database\Entity\General\Request;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use KAASoft\Util\ValidationHelper;
    use PDOException;

    /**
     * Class RequestCreateAction
     * @package KAASoft\Controller\Admin\Request
     */
    class RequestCreateAction extends AdminActionBase {
        /**
         * RequestCreateAction constructor.
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
            $bookIds = ValidationHelper::getIntArray($_POST["bookIds"]);
            $user = $this->session->getUser();
            $errorMessage = _("You are not logged in. Please log in to create request.");
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    if ($user !== null) {
                        $userId = $user->getId();
                        try {
                            if ($this->startDatabaseTransaction()) {
                                $requestDatabaseHelper = new RequestDatabaseHelper($this);
                                $issueDatabaseHelper = new IssueDatabaseHelper($this);
                                $userDatabaseHelper = new UserDatabaseHelper($this);

                                $user = $userDatabaseHelper->getUser($userId);
                                if ($user !== null) {
                                    $requestIds = [];

                                    $requests = [];

                                    $bookRequests = [];
                                    foreach ($bookIds as $bookId) {
                                        $request = Request::getObjectInstance($_POST);
                                        $request->setCreationDate(Helper::getDateString());
                                        $request->setStatus("Pending");
                                        $request->setBookId($bookId);
                                        $request->setUserId($userId);

                                        $requestId = $requestDatabaseHelper->saveRequest($request);
                                        if ($requestId === false) {
                                            $this->rollbackDatabaseChanges();
                                            $errorMessage = _("Request saving is failed for some reason.");
                                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                            return new DisplaySwitch();
                                        }
                                        $requestIds[] = $requestId;
                                        $request->setId($requestId);
                                        $requests [] = $request;
                                        $bookRequests[$bookId] = $request;
                                    }


                                    // email setup
                                    if (count($requests) > 0) {
                                        $bookHelper = new BookDatabaseHelper($this);
                                        $user = $this->session->getUser();
                                        $this->addShortCode("FIRST_NAME",
                                                            $user->getFirstName());
                                        $this->addShortCode("LAST_NAME",
                                                            $user->getLastName());
                                        $this->addShortCode("REQUEST_DATE",
                                                            Helper::reformatDateString($requests[0]->getCreationDate(),
                                                                                       $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_FORMAT)));


                                        $books = $bookHelper->getBooksByIds($bookIds);
                                        $this->smarty->assign("books",
                                                              $books);
                                        $activeTheme = ControllerBase::getThemeSettings()->getActiveTheme();
                                        $booksTemplate = $this->smarty->fetch(FileHelper::getEmailNotificationTemplateDirectory($activeTheme) . DIRECTORY_SEPARATOR . "books.tpl");
                                        $this->addShortCode("BOOKS",
                                                            $booksTemplate);

                                        // log requests
                                        if ($this->siteViewOptions->getOptionValue(SiteViewOptions::ENABLE_BOOK_LOGS)) {
                                            $issueLogs = [];
                                            foreach ($books as $book) {
                                                if ($book instanceof Book) {
                                                    $issueLog = $issueDatabaseHelper->getIssueLog();
                                                    $issueLog->setBookId($book->getId());
                                                    //$issueLog->setBookSN($book->getBookSN());
                                                    $issueLog->setBookTitle($book->getTitle());
                                                    $issueLog->setBookISBN(ValidationHelper::isEmpty($book->getISBN13()) ? $book->getISBN10() : $book->getISBN13());
                                                    $request = $bookRequests[$book->getId()];
                                                    $issueLog->setRequestId($request->getId());
                                                    $issueLog->setRequestDateTime(Helper::getDateTimeString());
                                                    $issueLog->setRequestStatus($request->getStatus());
                                                    $issueLog->setRequestNotes($request->getNotes());
                                                    $issueLog->setUserId($userId);
                                                    $issueLog->setUserFullName($user->getFirstName() . " " . $user->getMiddleName() . " " . $user->getLastName());
                                                    $issueLog->setIsLost(false);
                                                    $issueLog->setUpdateDateTime($issueLog->getRequestDateTime());

                                                    $issueLogId = $issueDatabaseHelper->saveIssueLog($issueLog);
                                                    if ($issueLogId === false) {
                                                        $this->rollbackDatabaseChanges();
                                                        $errorMessage = _("Request Log saving is failed for some reason.");
                                                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                                        return new DisplaySwitch();
                                                    }

                                                    $issueLogs [] = $issueLog;
                                                }
                                            }
                                        }

                                    }
                                    // email setup end

                                    $this->commitDatabaseChanges();
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Book is successfully requested."),
                                        "requestIds" => $requestIds ]);
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
                            $errorMessage = sprintf(_("Couldn't save Request.%s%s%s%s(%d)."),
                                                    Helper::HTML_NEW_LINE,
                                                    $e->getMessage(),
                                                    Helper::HTML_NEW_LINE,
                                                    $e->getFile(),
                                                    $e->getLine());
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                        }
                    }
                    else {
                        Helper::printAsJSON([ Controller::AJAX_PARAM_NAME_WARNING => $errorMessage ]);
                        exit( 0 );
                    }
                }

                return new DisplaySwitch();
            }
            else {

                if ($user === null) {
                    Session::addSessionMessage($errorMessage,
                                               Message::MESSAGE_STATUS_WARNING);
                }

                $this->smarty->assign("action",
                                      "create");

                return new DisplaySwitch('admin/requests/request.tpl');
            }
        }
    }