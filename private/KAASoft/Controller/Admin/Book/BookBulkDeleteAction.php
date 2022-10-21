<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-01-05
     */

    namespace KAASoft\Controller\Admin\Book;

    use KAASoft\Controller\Admin\ElectronicBook\ElectronicBookDatabaseHelper;
    use KAASoft\Controller\Admin\Image\ImageDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;
    use PDOException;

    /**
     * Class BookBulkDeleteAction
     * @package KAASoft\Controller\Admin\Book
     */
    class BookBulkDeleteAction extends AdminActionBase {
        /**
         * BookDeleteAction constructor.
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
            $bookIds = ValidationHelper::getIntArray($_POST["bookIds"]);
            $bookDatabaseHelper = new BookDatabaseHelper($this);
            try {
                if (Helper::isAjaxRequest()) {
                    if ($bookIds !== null) {
                        if ($this->startDatabaseTransaction()) {
                            $books = $bookDatabaseHelper->getBooksByIds($bookIds);

                            $result = $bookDatabaseHelper->deleteBooks($bookIds);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("Couldn't delete Books for some reason."));
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            foreach ($books as $book) {
                                if ($book instanceof Book) {
                                    if ($book->getCover() !== null) {
                                        $imageHelper = new ImageDatabaseHelper($this);
                                        if ($imageHelper->deleteImage($book->getCover()->getId()) === false) {
                                            $this->rollbackDatabaseChanges();
                                            $errorMessage = _("Couldn't delete Book Cover.");
                                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                                        }

                                        $coverPath = $book->getCover()->getAbsolutePath();
                                        FileHelper::deleteFolder(dirname($coverPath));
                                    }

                                    if ($book->getEBook() !== null) {
                                        $eBookHelper = new ElectronicBookDatabaseHelper($this);
                                        if ($eBookHelper->deleteElectronicBook($book->getEBook()->getId()) === false) {
                                            $this->rollbackDatabaseChanges();
                                            $errorMessage = _("Couldn't delete Book Cover.");
                                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                                        }
                                        $eBookPath = $book->getEBook()->getAbsolutePath();
                                        FileHelper::deleteFolder(dirname($eBookPath));
                                    }
                                }
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Books are deleted successfully.") ]);
                        }
                        else {
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Database transaction couldn't be created.") ]);
                        }
                    }
                    else {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't find any book id in request.") ]);
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
                $errorMessage = sprintf(_("Couldn't delete Books.%s%s"),
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
            }

            return new DisplaySwitch();
        }
    }