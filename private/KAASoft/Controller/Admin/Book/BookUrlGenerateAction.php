<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Book;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Environment\Routes\Admin\BookRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class BookUrlGenerateAction
     * @package KAASoft\Controller\Admin\Book
     */
    class BookUrlGenerateAction extends AdminActionBase {
        /**
         * BookUrlGenerateAction constructor.
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
            if (Helper::isPostRequest()) {
                $isOverride = ValidationHelper::getBool($_POST["override"]);
                $urlFormat = ValidationHelper::getInt($_POST["urlFormat"],
                                                      1);

                $bookDatabaseHelper = new BookDatabaseHelper($this);

                $bookCount = $bookDatabaseHelper->getBooksCount();
                $pageSize = 50;
                $pageNumber = ceil($bookCount / $pageSize);

                $existingUrls = [];
                if (!$isOverride) {
                    $existingUrls = $bookDatabaseHelper->getBookUrls();
                }
                else {
                    $result = $bookDatabaseHelper->cleanBookUrls();
                    if ($result === false) {
                        $this->rollbackDatabaseChanges();
                        Session::addSessionMessage(_("Couldn't clean book URLs. Please try again."),
                                                   Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 BookRoutes::BOOK_URL_GENERATE_ROUTE);
                    }
                }

                if ($this->startDatabaseTransaction()) {
                    for ($i = 0; $i < $pageNumber; $i++) {
                        $books = $bookDatabaseHelper->getBooks($i * $pageSize,
                                                               $pageSize);

                        foreach ($books as $book) {
                            if ($book instanceof Book) {
                                $url = $book->generateUrl($urlFormat);


                                if (!$isOverride and !ValidationHelper::isEmpty($book->getUrl())) {
                                    // skip non null url
                                    continue;
                                }

                                $index = 1;
                                $tempUrl = $url;
                                while (in_array($tempUrl,
                                                $existingUrls)) {
                                    $tempUrl = $url . $index;
                                    $index++;
                                }
                                $url = $tempUrl;

                                $existingUrls[] = $url;

                                $result = $bookDatabaseHelper->updateBookUrl($book->getId(),
                                                                             $url);
                                if ($result === false) {
                                    $this->rollbackDatabaseChanges();
                                    Session::addSessionMessage(_("Book URLs couldn't be updated for some reason. Please try again."),
                                                               Message::MESSAGE_STATUS_ERROR);

                                    return new DisplaySwitch(null,
                                                             BookRoutes::BOOK_URL_GENERATE_ROUTE);
                                }
                            }
                        }
                    }
                    Session::addSessionMessage(_("Book URLs are successfully updated."));
                    $this->commitDatabaseChanges();
                }
            }

            return new DisplaySwitch("admin/generateURL.tpl");
        }
    }