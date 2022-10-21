<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Book;

    use KAASoft\Controller\Admin\ElectronicBook\ElectronicBookDatabaseHelper;
    use KAASoft\Controller\Admin\Image\ImageDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class BookDeleteAction
     * @package KAASoft\Controller\Admin\Book
     */
    class BookDeleteAction extends AdminActionBase {
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
            $bookId = $args["bookId"];
            $bookDatabaseHelper = new BookDatabaseHelper($this);
            try {
                if (Helper::isAjaxRequest()) {
                    if ($this->startDatabaseTransaction()) {
                        $book = $bookDatabaseHelper->getBook($bookId);
                        if ($book !== null) {
                            $result = $bookDatabaseHelper->deleteBook($bookId);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("Couldn't delete Book '%d' for some reason."),
                                                        $bookId);
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }


                            if ($book->getCover() !== null) {
                                $imageHelper = new ImageDatabaseHelper($this);
                                if ($imageHelper->deleteImage($book->getCover()->getId()) === false) {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = _("Couldn't delete Book Cover.");
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                    return new DisplaySwitch();
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
                                FileHelper::deleteFolder($eBookPath);
                            }

                        }
                        else {
                            $this->kaaSoftDatabase->rollbackTransaction();
                            $errorMessage = sprintf(_("There is no Book with Id '%d' in database table \"%s\"."),
                                                    $bookId,
                                                    KAASoftDatabase::$BOOKS_TABLE_NAME);
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                            return new DisplaySwitch();
                        }

                        $this->commitDatabaseChanges();
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Book is deleted successfully.") ]);
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
                $errorMessage = sprintf(_("Couldn't delete Book '%d'.%s%s"),
                                        $bookId,
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
            }

            return new DisplaySwitch();

        }
    }