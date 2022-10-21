<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Book;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Util\BarCodePrintSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class BookBulkBarCodeGenerateAction
     * @package KAASoft\Controller\Admin\Book
     */
    class BookBulkBarCodeGenerateAction extends AdminActionBase {
        /**
         * BookBulkBarCodeGenerateAction constructor.
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
                $bookDatabaseHelper = new BookDatabaseHelper($this);
                $bookIds = ValidationHelper::getIntArray($_POST["bookIds"]);
                $barCodeQuantities = ValidationHelper::getIntArray($_POST["barCodeQuantity"]);


                if ($bookIds !== null) {
                    $books = $bookDatabaseHelper->getBooksByIds($bookIds);
                    $this->smarty->assign("books",
                                          $books);
                }

                if (Helper::isAjaxRequest()) {
                    $this->setJsonContentType();
                    $barCodePrintSettings = BarCodePrintSettings::getObjectInstance($_POST);
                    $this->smarty->assign("barCodePrintSettings",
                                          $barCodePrintSettings);

                    $bookCount = count($bookIds);
                    $barCodeCount = count($barCodeQuantities);

                    if ($bookCount != $barCodeCount) {
                        $this->putArrayToAjaxResponse([ ControllerBase::AJAX_PARAM_NAME_ERROR => _("Books couldn't be mapped to bar code quantity.") ]);

                        return new DisplaySwitch();
                    }

                    $mapping = [];
                    for ($i = 0; $i < count($bookIds); $i++) {
                        $mapping[$bookIds[$i]] = $barCodeQuantities[$i];
                    }
                    if (isset( $books )) {
                        foreach ($books as $book) {
                            if ($book instanceof Book) {
                                $book->setQuantity($mapping[$book->getId()]);
                            }
                        }
                    }

                    return new DisplaySwitch("admin/books/booksBarCode.tpl");
                }
            }

            return new DisplaySwitch("admin/books/booksBarCodeGenerate.tpl");
        }
    }