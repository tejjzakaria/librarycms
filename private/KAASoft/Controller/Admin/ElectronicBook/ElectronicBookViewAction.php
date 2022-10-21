<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-01-07
     */

    namespace KAASoft\Controller\Admin\ElectronicBook;

    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Message;

    class ElectronicBookViewAction extends PublicActionBase {
        /**
         * ElectronicBookViewAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute,
                                true);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $bookId = $args["bookId"];
            $bookDatabaseHelper = new BookDatabaseHelper($this);
            $book = $bookDatabaseHelper->getBook($bookId);
            if ($book !== null) {
                $eBook = $book->getEBook();
                if ($eBook !== null) {
                    $path = $eBook->getPath();
                    $fileName = FileHelper::getElectronicBookRootLocation() . $path;

                    if (file_exists($fileName) /*or $book->getExternalPreview() !== null*/) {
                        $this->smarty->assign("book",
                                              $book);

                        return new DisplaySwitch('books/readBook.tpl');
                    }
                }
                /*elseif ($book->getExternalPreview() !== null) {
                    $this->smarty->assign("book",
                                          $book);

                    return new DisplaySwitch('books/readBook.tpl');
                }*/
            }
            Session::addSessionMessage(sprintf(_("Couldn't find requested book(%d)."),
                                               $bookId),
                                       Message::MESSAGE_STATUS_ERROR);

            return new DisplaySwitch(null,
                                     $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));

        }
    }