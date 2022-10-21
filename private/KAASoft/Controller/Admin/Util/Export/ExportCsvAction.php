<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util\Export;

    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Author;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Database\Entity\General\Genre;
    use KAASoft\Database\Entity\General\Location;
    use KAASoft\Database\Entity\General\Store;
    use KAASoft\Environment\Config;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use KAASoft\Util\Paginator;
    use KAASoft\Util\ValidationHelper;

    class ExportCsvAction extends AdminActionBase {

        const HEARER = [ "Title",
                         "Subtitle",
                         "ISBN 10",
                         "ISBN 13",
                         "Publisher",
                         "Series",
                         "Authors",
                         "Genres",
                         "Edition",
                         "Published Year",
                         "Pages",
                         "Type",
                         "PhysicalForm",
                         "Size",
                         "Binding",
                        // "Quantity",
                         "Price",
                         "Language",
                         "Description",
                         "Notes",
                         "Cover",
                         "Book Serial Number",
                         "Meta Title",
                         "Meta Keywords",
                         "Meta Description",
                         "Stores",
                         "Locations" ];


        private $multipleValuesDelimiter;
        private $columnDelimiter;

        /**
         * ExportCsvAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

        /**
         * @param $args        array
         * @return DisplaySwitch
         */
        protected function action($args) {
            set_time_limit(3 * 60 * 60); // 3 hours
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) {
                    $this->columnDelimiter = ValidationHelper::getChar($_POST["columnDelimiter"],
                                                                       ",");
                    $this->multipleValuesDelimiter = ValidationHelper::getChar($_POST["multipleValuesDelimiter"],
                                                                               "|");


                    $bookHelper = new BookDatabaseHelper($this);
                    $bookCount = $bookHelper->getBooksCount();

                    $paginator = new Paginator(1,
                                               100,
                                               $bookCount);

                    $outputFile = FileHelper::getTempFileName();
                    try {
                        $filePointer = fopen($outputFile,
                                             "w");
                        fputcsv($filePointer,
                                ExportCsvAction::HEARER,
                                $this->columnDelimiter);

                        $this->outputStatus(new Message(_("Export is started...")));
                        $this->outputStatus(new Message(sprintf(_("%d books are found."),
                                                                $bookCount)));
                        $totalBooks = 0;
                        for ($i = 1; $i <= $paginator->getTotalPages(); $i++) {
                            $books = $bookHelper->getFullBooks($paginator->getOffset(),
                                                               $paginator->getPerPage());

                            if ($books !== null) {
                                $this->processBooks($books,
                                                    $filePointer);
                                $totalBooks += count($books);
                            }
                            else {
                                $this->outputStatus(new Message(_("Export is failed for some reason."),
                                                                Message::MESSAGE_STATUS_ERROR));

                            }
                            $paginator->setCurrentPage($i + 1);
                            $this->outputStatus(new Message(sprintf(_("%d books are exported."),
                                                                    $totalBooks)));
                        }
                        $this->outputStatus(new Message(sprintf(_("%d books are exported TOTALLY."),
                                                                $totalBooks)));
                        $this->outputStatus(new Message(_("Export is complete.")));

                        fclose($filePointer);
                        $filePointer = null;

                        header("Content-Type: text/csv");
                        header('Content-Disposition: attachment; filename="' . sprintf("export_%d_books_%s.csv",
                                                                                       $totalBooks,
                                                                                       Helper::getDateString(null,
                                                                                                             "Y.m.d")) . '"');
                        readfile($outputFile);

                    }
                    finally {
                        if ($filePointer !== null) {
                            fclose($filePointer);
                        }
                        //$this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => [ "totalBooks" => $totalBooks ] ]);
                        unlink($outputFile);
                    }

                }
                else {
                    $this->outputStatus(new Message(_("POST method is required only."),
                                                    Message::MESSAGE_STATUS_ERROR));
                }
            }
            else {
                $this->outputStatus(new Message(_("AJAX request is required only."),
                                                Message::MESSAGE_STATUS_ERROR));
            }

            return new DisplaySwitch();
        }

        /**
         * @param $message Message
         */
        private function outputStatus($message) {
            switch ($message->getStatus()) {
                case Message::MESSAGE_STATUS_ERROR:
                    ControllerBase::getLogger()->error($message->getMessage());
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $message->getMessage() ]);
                    break;
                case Message::MESSAGE_STATUS_INFO:
                case Message::MESSAGE_STATUS_SUCCESS:
                    ControllerBase::getLogger()->info($message->getMessage());
                    break;
                case Message::MESSAGE_STATUS_WARNING:
                    ControllerBase::getLogger()->warn($message->getMessage());
                    break;
            }
            //Helper::outputMessage($message);
        }

        private function processBooks($books, $filePointer) {
            $outputArray = [];
            foreach ($books as $book) {
                if ($book instanceof Book) {
                    $outputArray[] = $book->getTitle();
                    $outputArray[] = $book->getSubtitle();
                    $outputArray[] = $book->getISBN10();
                    $outputArray[] = $book->getISBN13();
                    $outputArray[] = $book->getPublisher() !== null ? $book->getPublisher()->getName() : "";
                    $outputArray[] = $book->getSeries() !== null ? $book->getSeries()->getName() : "";
                    // authors
                    $authorsString = "";
                    $authors = $book->getAuthors();
                    if (!ValidationHelper::isArrayEmpty($authors)) {
                        $authorsCount = count($authors);
                        for ($i = 0; $i < $authorsCount; $i++) {
                            $author = $authors[$i];
                            if ($author instanceof Author) {
                                $authorsString .= $author->getLastName() . " " . $author->getFirstName() . ( ( $i == ( $authorsCount - 1 ) ) ? "" : $this->multipleValuesDelimiter );
                            }
                        }
                    }
                    $outputArray[] = $authorsString;
                    // genres
                    $genresString = "";
                    $genres = $book->getGenres();
                    if (!ValidationHelper::isArrayEmpty($genres)) {
                        $genresCount = count($genres);
                        for ($i = 0; $i < $genresCount; $i++) {
                            $genre = $genres[$i];
                            if ($genre instanceof Genre) {
                                $genresString .= $genre->getName() . ( ( $i == ( $genresCount - 1 ) ) ? "" : $this->multipleValuesDelimiter );
                            }
                        }
                    }
                    $outputArray[] = $genresString;
                    $outputArray[] = $book->getEdition();
                    $outputArray[] = $book->getPublishingYear();
                    $outputArray[] = $book->getPages();
                    $outputArray[] = $book->getType();
                    $outputArray[] = $book->getPhysicalForm();
                    $outputArray[] = $book->getSize();
                    $outputArray[] = $book->getBinding();
                   // $outputArray[] = $book->getQuantity();
                    $outputArray[] = $book->getPrice();
                    $outputArray[] = $book->getLanguage();
                    $outputArray[] = Helper::newLineToBr($book->getDescription());
                    $outputArray[] = $book->getNotes();

                    $siteURL = Config::getSiteURL();
                    $outputArray[] = $book->getCover() !== null ? ( $siteURL . $book->getCover()->getWebPath() ) : "";
                    //$outputArray[] = $book->getBookSN();
                    $outputArray[] = $book->getMetaTitle();
                    $outputArray[] = $book->getMetaKeywords();
                    $outputArray[] = $book->getMetaDescription();
                    // stores
                    $storeString = "";
                    $stores = $book->getStores();
                    if (!ValidationHelper::isArrayEmpty($stores)) {
                        $storesCount = count($stores);
                        for ($i = 0; $i < $storesCount; $i++) {
                            $store = $stores[$i];
                            if ($store instanceof Store) {
                                $storeString .= $store->getName() . ( ( $i == ( $storesCount - 1 ) ) ? "" : $this->multipleValuesDelimiter );
                            }
                        }
                    }
                    $outputArray[] = $storeString;
                    // locations
                    $locationString = "";
                    $locations = $book->getLocations();
                    if (!ValidationHelper::isArrayEmpty($locations)) {
                        $locationsCount = count($locations);
                        for ($i = 0; $i < $locationsCount; $i++) {
                            $location = $locations[$i];
                            if ($location instanceof Location) {
                                $locationString .= $location->getName() . ( ( $i == ( $locationsCount - 1 ) ) ? "" : $this->multipleValuesDelimiter );
                            }
                        }
                    }
                    $outputArray[] = $locationString;

                    fputcsv($filePointer,
                            $outputArray,
                            $this->columnDelimiter);

                    unset( $outputArray );
                }
            }
        }
    }