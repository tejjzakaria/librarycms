<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util\Import;

    use Exception;
    use KAASoft\Controller\Admin\Author\AuthorDatabaseHelper;
    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\Admin\Genre\GenreDatabaseHelper;
    use KAASoft\Controller\Admin\Image\ImageDatabaseHelper;
    use KAASoft\Controller\Admin\Image\ImageUploadBaseAction;
    use KAASoft\Controller\Admin\Publisher\PublisherDatabaseHelper;
    use KAASoft\Controller\Admin\Series\SeriesDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Author;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Database\Entity\General\Genre;
    use KAASoft\Database\Entity\General\Location;
    use KAASoft\Database\Entity\General\Publisher;
    use KAASoft\Database\Entity\General\Series;
    use KAASoft\Database\Entity\General\Store;
    use KAASoft\Database\Entity\Util\Image;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\CSV\CSVReader;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\HTTP\HttpClient;
    use KAASoft\Util\Message;
    use KAASoft\Util\ValidationHelper;

    class ImportCsvAction extends AdminActionBase {
        private $multipleValuesDelimiter;

        private $books;
        private $authors;
        private $genres;
        private $series;
        private $covers;
        private $publishers;
        private $stores;
        private $locations;

        private $httpClient = null;


        /**
         * ImportCsvAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
            $this->books = [];
            $this->authors = [];
            $this->genres = [];
            $this->series = [];
            $this->covers = [];
        }

        /**
         * @return HttpClient
         */
        public function getHttpClient() {
            if ($this->httpClient === null) {
                $this->httpClient = new HttpClient();
            }

            return $this->httpClient;
        }

        /**
         * @param $args        array
         * @return DisplaySwitch
         */
        protected function action($args) {
            set_time_limit(3 * 60 * 60); // 3 hours

            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) {
                    if (FileHelper::hasUploadedFile()) {
                        try {
                            $csvFileName = FileHelper::getUploadedFileName();
                            $columnDelimiter = ValidationHelper::getChar($_POST["columnDelimiter"],
                                                                         ",");
                            $this->multipleValuesDelimiter = ValidationHelper::getChar($_POST["multipleValuesDelimiter"],
                                                                                       "|");
                            $keys = $_POST["keys"];
                            $values = $_POST["values"];

                            $mapping = [];
                            for ($i = 0; $i < count($keys); $i++) {
                                if (!ValidationHelper::isEmpty($values[$i])) {
                                    $mapping[$keys[$i]] = $values[$i];
                                }
                            }
                            $this->outputStatus(new Message(_("Start reading file...")));
                            $csvFile = new CSVReader($csvFileName,
                                                     true,
                                                     $columnDelimiter);

                            $totalLines = 0;
                            while (1) { // demo:    while ($totalLines < 50) {
                                try {
                                    $line = $csvFile->readLine();
                                    if ($line === false) {
                                        break; // EOF
                                    }
                                    if ($line === null) {
                                        continue; // skip empty line
                                    }
                                    $book = $this->processLine($line,
                                                               $mapping);

                                    if ($book === false) {
                                        return new DisplaySwitch();
                                    }
                                    $this->books [] = $book;
                                    $totalLines++;

                                    if ($totalLines % 100 === 0) {
                                        $this->outputStatus(new Message(sprintf(_("Reading progress: %d lines."),
                                                                                $totalLines)));
                                    }
                                }
                                catch (Exception $e) {
                                    $this->outputStatus(new Message(sprintf(_("Import failed on line %d. Details: %s"),
                                                                            $csvFile->getLineNumber(),
                                                                            $e->getMessage()),
                                                                    Message::MESSAGE_STATUS_ERROR));

                                    return new DisplaySwitch();
                                }
                            }
                            $this->outputStatus(new Message(sprintf(_("Reading is finished. %d lines was processed TOTALLY."),
                                                                    $totalLines),
                                                            Message::MESSAGE_STATUS_SUCCESS));
                            if (!$this->saveAll()) {
                                return new DisplaySwitch();
                            }
                        }
                        catch (Exception $e) {
                            $this->outputStatus(new Message(sprintf(_("Import failed for some reason. Details: %s"),
                                                                    $e->getMessage()),
                                                            Message::MESSAGE_STATUS_ERROR));

                            return new DisplaySwitch();
                        }
                    }
                    else {
                        $this->outputStatus(new Message(_("There is no file in request."),
                                                        Message::MESSAGE_STATUS_ERROR));
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
         * @param $csvLine array
         * @param $columnMapping
         * @return Book|false
         */
        private function processLine($csvLine, $columnMapping) {
            $bookArray = [];
            $authorArray = [];
            $genreArray = [];
            $publisherArray = [];
            $seriesArray = [];
            $coverArray = [];
            $storeArray = [];
            $locationArray = [];

            // build object's arrays
            foreach ($columnMapping as $key => $value) {
                $tableColumnArray = explode(".",
                                            $value);

                $table = $tableColumnArray[0];
                $column = $tableColumnArray[1];

                switch (strtoupper($table)) {
                    case strtoupper(KAASoftDatabase::$BOOKS_TABLE_NAME):
                        $bookArray[$column] = $csvLine[$key];
                        break;
                    case strtoupper(KAASoftDatabase::$PUBLISHERS_TABLE_NAME):
                        $publisherArray[$column] = $csvLine[$key];
                        break;
                    case strtoupper(KAASoftDatabase::$SERIES_TABLE_NAME):
                        $seriesArray[$column] = $csvLine[$key];
                        break;
                    case strtoupper(KAASoftDatabase::$STORES_TABLE_NAME):
                        $storeArray[$column] = $csvLine[$key];
                        break;
                    case strtoupper(KAASoftDatabase::$LOCATIONS_TABLE_NAME):
                        $locationArray[$column] = $csvLine[$key];
                        break;
                    case strtoupper(KAASoftDatabase::$AUTHORS_TABLE_NAME):
                        $authorArray[$column] = $csvLine[$key];
                        break;
                    case strtoupper(KAASoftDatabase::$GENRES_TABLE_NAME):
                        $genreArray[$column] = $csvLine[$key];
                        break;
                    case strtoupper(KAASoftDatabase::$IMAGES_TABLE_NAME):
                        $coverArray[$column] = $csvLine[$key];
                        break;
                }
            }

            // process arrays
            $book = Book::getObjectInstance($bookArray);

            $result = false;
            if (!ValidationHelper::isArrayEmpty($authorArray)) {
                $result = !$this->processBookAuthors($book,
                                                     $authorArray);
            }
            if (!ValidationHelper::isArrayEmpty($genreArray)) {
                $result = ( $result or !$this->processBookGenres($book,
                                                                 $genreArray) );
            }
            if (!ValidationHelper::isArrayEmpty($publisherArray)) {
                $result = ( $result or !$this->processBookPublisher($book,
                                                                    $publisherArray) );
            }
            if (!ValidationHelper::isArrayEmpty($seriesArray)) {
                $result = ( $result or !$this->processBookSeries($book,
                                                                 $seriesArray) );
            }
            if (!ValidationHelper::isArrayEmpty($storeArray)) {
                $result = ( $result or !$this->processBookStores($book,
                                                                 $storeArray) );
            }
            if (!ValidationHelper::isArrayEmpty($locationArray)) {
                $result = ( $result or !$this->processBookLocations($book,
                                                                    $locationArray) );
            }
            if (!ValidationHelper::isArrayEmpty($coverArray)) {
                $result = ( $result or !$this->processBookCovers($book,
                                                                 $coverArray) );
            }

            if ($result) {
                return false;
            }

            return $book;
        }

        /**
         * @return bool
         */
        private function saveAll() {
            $totalAuthors = 0;
            $totalGenres = 0;
            $totalBooks = 0;
            $totalPublishers = 0;
            $totalSeries = 0;
            $totalCovers = 0;
            if ($this->startDatabaseTransaction()) {
                // save authors
                if (!ValidationHelper::isArrayEmpty($this->authors)) {
                    // !!!! below important line!!!!!
                    $this->authors = array_values($this->authors);

                    $totalAuthors = count($this->authors);
                    $this->outputStatus(new Message(sprintf(_("%d authors are saving..."),
                                                            $totalAuthors)));

                    $authorHelper = new AuthorDatabaseHelper($this);
                    $authorIds = $authorHelper->saveAuthors($this->authors);
                    if ($authorIds === false or ( is_array($authorIds) and count($this->authors) != count($authorIds) )) {
                        $this->rollbackDatabaseChanges();
                        $this->outputStatus(new Message(_("Authors saving is failed for some reason."),
                                                        Message::MESSAGE_STATUS_ERROR));

                        return false; // error
                    }
                    for ($i = 0; $i < count($this->authors); $i++) {
                        $this->authors[$i]->setId($authorIds[$i]);
                    }
                    $this->outputStatus(new Message(sprintf(_("%d authors are saved."),
                                                            $totalAuthors)));
                }
                // save genres
                if (!ValidationHelper::isArrayEmpty($this->genres)) {
                    $this->genres = array_values($this->genres);
                    $totalGenres = count($this->genres);
                    $this->outputStatus(new Message(sprintf(_("%d genres are saving..."),
                                                            $totalGenres)));

                    $genreHelper = new GenreDatabaseHelper($this);
                    $genreIds = $genreHelper->saveGenres($this->genres);

                    if ($genreIds === false or ( is_array($genreIds) and count($this->genres) != count($genreIds) )) {
                        $this->rollbackDatabaseChanges();
                        $this->outputStatus(new Message(_("Genres saving is failed for some reason."),
                                                        Message::MESSAGE_STATUS_ERROR));

                        return false; // error
                    }
                    for ($i = 0; $i < count($this->genres); $i++) {
                        $this->genres[$i]->setId($genreIds[$i]);
                    }
                    $totalGenres = count($this->genres);
                    $this->outputStatus(new Message(sprintf(_("%d genres are saved."),
                                                            $totalGenres)));
                }
                // save publishers
                if (!ValidationHelper::isArrayEmpty($this->publishers)) {
                    $this->publishers = array_values($this->publishers);
                    $totalPublishers = count($this->publishers);
                    $this->outputStatus(new Message(sprintf(_("%d publishers are saving..."),
                                                            $totalPublishers)));
                    $publisherHelper = new PublisherDatabaseHelper($this);
                    $publisherIds = $publisherHelper->savePublishers($this->publishers);

                    if ($publisherIds === false or ( is_array($publisherIds) and count($this->publishers) != count($publisherIds) )) {
                        $this->rollbackDatabaseChanges();
                        $this->outputStatus(new Message(_("Publishers saving is failed for some reason."),
                                                        Message::MESSAGE_STATUS_ERROR));

                        return false; // error
                    }
                    for ($i = 0; $i < count($this->publishers); $i++) {
                        $publisher = $this->publishers[$i];
                        if ($publisher instanceof Publisher) {
                            $publisher->setId($publisherIds[$i]);
                        }
                    }
                    $this->outputStatus(new Message(sprintf(_("%d publishers are saved."),
                                                            $totalPublishers)));
                }
                // save series
                if (!ValidationHelper::isArrayEmpty($this->series)) {
                    $this->series = array_values($this->series);
                    $totalSeries = count($this->series);
                    $this->outputStatus(new Message(sprintf(_("%d series are saving..."),
                                                            $totalSeries)));
                    $seriesHelper = new SeriesDatabaseHelper($this);
                    $seriesIds = $seriesHelper->saveSeriesS($this->series);

                    if ($seriesIds === false or ( is_array($seriesIds) and count($this->series) != count($seriesIds) )) {
                        $this->rollbackDatabaseChanges();
                        $this->outputStatus(new Message(_("Series saving is failed for some reason."),
                                                        Message::MESSAGE_STATUS_ERROR));

                        return false; // error
                    }
                    for ($i = 0; $i < count($this->series); $i++) {
                        $this->series[$i]->setId($seriesIds[$i]);
                    }
                    $this->outputStatus(new Message(sprintf(_("%d series are saved."),
                                                            $totalSeries)));
                }
                // save covers
                if (!ValidationHelper::isArrayEmpty($this->covers)) {
                    $totalCovers = count($this->covers);
                    $this->outputStatus(new Message(sprintf(_("%d covers are saving..."),
                                                            $totalCovers)));
                    $imageHelper = new ImageDatabaseHelper($this);
                    $imageIds = $imageHelper->saveImages($this->covers);

                    if ($imageIds === false or ( is_array($imageIds) and count($this->covers) != count($imageIds) )) {
                        $this->rollbackDatabaseChanges();
                        $this->outputStatus(new Message(_("Covers saving is failed for some reason."),
                                                        Message::MESSAGE_STATUS_ERROR));

                        return false; // error
                    }
                    for ($i = 0; $i < count($this->covers); $i++) {
                        $this->covers[$i]->setId($imageIds[$i]);
                    }
                    $totalCovers = count($this->covers);
                    $this->outputStatus(new Message(sprintf(_("%d covers are saved."),
                                                            $totalCovers)));
                }
                // save books
                if (!ValidationHelper::isArrayEmpty($this->books)) {
                    $totalBooks = count($this->books);
                    $this->outputStatus(new Message(sprintf(_("%d books are processing..."),
                                                            $totalBooks)));

                    foreach ($this->books as $book) {
                        if ($book instanceof Book) {
                            if ($book->getSeries() != null) {
                                $book->setSeriesId($book->getSeries()->getId());
                            }
                            if ($book->getPublisher() != null) {
                                $book->setPublisherId($book->getPublisher()->getId());
                            }
                            if ($book->getCover()) {
                                $book->setCoverId($book->getCover()->getId());
                            }
                            /* if ($book->getQuantity() == null) {
                                 $book->setQuantity(0);
                             }*/

                            if ($book->getBinding() === null) {
                                $book->setBinding("Hardcover");
                            }
                            if ($book->getPhysicalForm() === null) {
                                $book->setPhysicalForm("Book");
                            }
                            if ($book->getSize() === null) {
                                $book->setSize("Medium");
                            }
                            if ($book->getType() === null) {
                                $book->setType("Standard");
                            }

                            //$book->setActualQuantity($book->getQuantity());
                            $book->setISBN13(str_replace("-",
                                                         "",
                                                         $book->getISBN13()));
                            $book->setISBN10(str_replace("-",
                                                         "",
                                                         $book->getISBN10()));
                            $book->setCreationDateTime(Helper::getDateTimeString());
                            $book->setUpdateDateTime($book->getCreationDateTime());

                        }
                    }
                    unset( $this->publishers );
                    unset( $this->series );
                    unset( $this->covers );
                    $this->outputStatus(new Message(sprintf(_("%d books are processed."),
                                                            $totalBooks)));

                    // save books
                    $bookHelper = new BookDatabaseHelper($this);
                    if (!ValidationHelper::isArrayEmpty($this->books)) {
                        $this->outputStatus(new Message(sprintf("%d books are saving...",
                                                                $totalBooks)));
                        $bookIds = $bookHelper->saveBooks($this->books);

                        if ($bookIds === false or ( is_array($bookIds) and count($this->books) != count($bookIds) )) {
                            $this->rollbackDatabaseChanges();
                            $this->outputStatus(new Message(_("Books saving is failed for some reason."),
                                                            Message::MESSAGE_STATUS_ERROR));

                            return false; // error
                        }
                        for ($i = 0; $i < count($this->books); $i++) {
                            $this->books[$i]->setId($bookIds[$i]);
                        }
                        $this->outputStatus(new Message(sprintf(_("%d books are saved."),
                                                                $totalBooks)));
                    }

                    // save book authors
                    $bookAuthorIds = [];
                    $bookGenreIds = [];
                    foreach ($this->books as $book) {
                        foreach ($book->getAuthors() as $author) {
                            if ($author instanceof Author) {
                                $bookAuthorIds[] = [ "bookId"   => $book->getId(),
                                                     "authorId" => $author->getId() ];
                            }
                        }
                        foreach ($book->getGenres() as $genre) {
                            if ($genre instanceof Genre) {
                                $bookGenreIds[] = [ "bookId"  => $book->getId(),
                                                    "genreId" => $genre->getId() ];
                            }
                        }
                    }
                    if (!ValidationHelper::isArrayEmpty($bookAuthorIds)) {
                        $this->outputStatus(new Message(sprintf(_("%d book authors are saving..."),
                                                                count($bookAuthorIds))));
                        $result = $bookHelper->saveBookAuthorsArray($bookAuthorIds);
                        if ($result === false) {
                            $this->rollbackDatabaseChanges();
                            $this->outputStatus(new Message(_("Book authors saving is failed for some reason."),
                                                            Message::MESSAGE_STATUS_ERROR));

                            return false; // error
                        }
                        $this->outputStatus(new Message(sprintf(_("%d book authors are saved."),
                                                                count($bookAuthorIds))));
                    }
                    // save book genres
                    if (!ValidationHelper::isArrayEmpty($bookGenreIds)) {
                        $this->outputStatus(new Message(sprintf(_("%d book genres are saving..."),
                                                                count($bookGenreIds))));
                        $result = $bookHelper->saveBookGenresArray($bookGenreIds);
                        if ($result === false) {
                            $this->rollbackDatabaseChanges();
                            $this->outputStatus(new Message(_("Book genres saving is failed for some reason.")));

                            return false; // error
                        }
                        $this->outputStatus(new Message(sprintf(_("%d book genres are saved."),
                                                                count($bookGenreIds)),
                                                        Message::MESSAGE_STATUS_SUCCESS));
                    }
                }
            }

            $this->outputStatus(new Message(sprintf(_("Total Books: %d."),
                                                    $totalBooks),
                                            Message::MESSAGE_STATUS_SUCCESS));
            $this->outputStatus(new Message(sprintf(_("Total Authors: %d."),
                                                    $totalAuthors),
                                            Message::MESSAGE_STATUS_SUCCESS));
            $this->outputStatus(new Message(sprintf(_("Total Genres: %d."),
                                                    $totalGenres),
                                            Message::MESSAGE_STATUS_SUCCESS));
            $this->outputStatus(new Message(sprintf(_("Total Publishers: %d."),
                                                    $totalPublishers),
                                            Message::MESSAGE_STATUS_SUCCESS));
            $this->outputStatus(new Message(sprintf(_("Total Series: %d."),
                                                    $totalSeries),
                                            Message::MESSAGE_STATUS_SUCCESS));
            $this->outputStatus(new Message(sprintf(_("Total Covers: %d."),
                                                    $totalCovers),
                                            Message::MESSAGE_STATUS_SUCCESS));

            $this->commitDatabaseChanges();

            return true;
        }

        /**
         * @param $message Message
         */
        private function outputStatus($message) {
            switch ($message->getStatus()) {
                case Message::MESSAGE_STATUS_ERROR:
                    ControllerBase::getLogger()->error($message->getMessage());
                    break;
                case Message::MESSAGE_STATUS_INFO:
                case Message::MESSAGE_STATUS_SUCCESS:
                    ControllerBase::getLogger()->info($message->getMessage());
                    break;
                case Message::MESSAGE_STATUS_WARNING:
                    ControllerBase::getLogger()->warn($message->getMessage());
                    break;
            }
            Helper::outputMessage($message);
        }

        /**
         * @param $book        Book
         * @param $authorArray array
         * @return bool
         */
        private function processBookAuthors($book, $authorArray) {
            //ControllerBase::getLogger()->info("Start.");
            $bookAuthors = [];
            foreach (explode($this->multipleValuesDelimiter,
                             $authorArray["lastName"]) as $authorName) {
                if (!ValidationHelper::isEmpty($authorName)) {
                    $author = new Author();
                    $author->setLastName($authorName);

                    if (!isset( $this->authors[$authorName] )) {
                        $this->authors[$authorName] = $author;
                    }

                    $bookAuthors[] = $this->authors[$authorName];
                }
            }
            $book->setAuthors($bookAuthors);

            //ControllerBase::getLogger()->info("End.");
            return true;
        }

        /**
         * @param $book        Book
         * @param $genreArray  array
         * @return bool
         */
        private function processBookGenres($book, $genreArray) {
            //ControllerBase::getLogger()->info("Start.");
            $bookGenres = [];
            foreach (explode($this->multipleValuesDelimiter,
                             $genreArray["name"]) as $genreName) {
                if (!ValidationHelper::isEmpty($genreName)) {
                    $genre = new Genre();
                    $genre->setName($genreName);

                    if (!isset( $this->genres[$genreName] )) {
                        $this->genres[$genreName] = $genre;
                    }

                    $bookGenres[] = $this->genres[$genreName];
                }
            }
            $book->setGenres($bookGenres);

            //ControllerBase::getLogger()->info("End.");
            return true;
        }

        /**
         * @param $book        Book
         * @param $storeArray  array
         * @return bool
         */
        private function processBookStores($book, $storeArray) {
            //ControllerBase::getLogger()->info("Start.");
            $bookStores = [];
            foreach (explode($this->multipleValuesDelimiter,
                             $storeArray["name"]) as $storeName) {
                if (!ValidationHelper::isEmpty($storeName)) {
                    $store = new Store();
                    $store->setName($storeName);

                    if (!isset( $this->stores[$storeName] )) {
                        $this->stores[$storeName] = $store;
                    }

                    $bookStores[] = $this->stores[$storeName];
                }
            }
            $book->setStores($bookStores);

            //ControllerBase::getLogger()->info("End.");
            return true;
        }

        /**
         * @param $book           Book
         * @param $locationArray  array
         * @return bool
         */
        private function processBookLocations($book, $locationArray) {
            //ControllerBase::getLogger()->info("Start.");
            $bookLocations = [];
            foreach (explode($this->multipleValuesDelimiter,
                             $locationArray["name"]) as $locationName) {
                if (!ValidationHelper::isEmpty($locationName)) {
                    $location = new Location();
                    $location->setName($locationName);

                    if (!isset( $this->locations[$locationName] )) {
                        $this->locations[$locationName] = $location;
                    }

                    $bookLocations[] = $this->stores[$locationName];
                }
            }
            $book->setLocations($bookLocations);

            //ControllerBase::getLogger()->info("End.");
            return true;
        }


        /**
         * @param $book           Book
         * @param $publisherArray array
         * @return bool
         */
        private function processBookPublisher($book, $publisherArray) {
            //ControllerBase::getLogger()->info("Start.");
            $publisherName = $publisherArray["name"];

            if (!ValidationHelper::isEmpty($publisherName)) {
                $publisher = new Publisher();
                $publisher->setName($publisherName);

                if (!isset( $this->publishers[$publisherName] )) {
                    $this->publishers[$publisherName] = $publisher;
                }

                $book->setPublisher($this->publishers[$publisherName]);
            }

            // ControllerBase::getLogger()->info("End.");
            return true;
        }

        /**
         * @param $book           Book
         * @param $seriesArray    array
         * @return bool
         */
        private function processBookSeries($book, $seriesArray) {
            //ControllerBase::getLogger()->info("Start.");
            $seriesName = $seriesArray["name"];
            if (!ValidationHelper::isEmpty($seriesName)) {
                $series = new Series();
                $series->setName($seriesName);
                $series->setIsComplete(false);

                if (!isset( $this->series[$seriesName] )) {
                    $this->series[$seriesName] = $series;
                }
                $book->setSeries($this->series[$seriesName]);
            }

            //  ControllerBase::getLogger()->info("End.");
            return true;
        }

        /**
         * @param $book           Book
         * @param $coverArray     array
         * @return bool
         */
        private function processBookCovers($book, $coverArray) {
            //ControllerBase::getLogger()->info("Start.");
            $image = Image::getObjectInstance($coverArray);
            if (!ValidationHelper::isEmpty($image->getPath())) {
                $this->covers[] = $image;

                $destFolder = FileHelper::getUniqueFolderName(FileHelper::getImageCurrentMonthLocation(FileHelper::getCoverLocation()));

                FileHelper::createDirectory($destFolder);
                $localFileName = FileHelper::getUniqueFileName($destFolder,
                                                               basename($image->getPath()));


                if (file_exists($image->getPath())) {
                    // if file is local - get it
                    $fileContent = file_get_contents($image->getPath());
                }
                else {
                    // if file is remote - download it
                    $htpClient = $this->getHttpClient();
                    if ($htpClient->fetch($image->getPath()) === true) {
                        $fileContent = $htpClient->getResults();
                    }
                    else {
                        $this->outputStatus(new Message(sprintf(_("Couldn't download cover file: %s"),
                                                                $image->getPath()),
                                                        Message::MESSAGE_STATUS_ERROR));

                        return false;
                    }
                }
                ControllerBase::getLogger()->debug(sprintf("Cover '%s' is downloaded. Size: %d",
                                                           $image->getPath(),
                                                           strlen($fileContent)));

                $result = FileHelper::createFile($localFileName);
                if ($result === false) {
                    $this->outputStatus(new Message(_("Couldn't create cover file. Please check permissions on the server."),
                                                    Message::MESSAGE_STATUS_ERROR));

                    return false;
                }

                $result = file_put_contents($localFileName,
                                            $fileContent);

                if ($result === false) {
                    $this->outputStatus(new Message(_("Couldn't copy cover to the database."),
                                                    Message::MESSAGE_STATUS_ERROR));

                    return false;
                }

                $image->setPath(FileHelper::getRelativePath(FileHelper::getImageRootLocation(),
                                                            $localFileName));
                $image->setUploadingDateTime(Helper::getDateTimeString());

                ImageUploadBaseAction::resizeImages($localFileName,
                                                    ImageUploadBaseAction::getCoverResolutions());


                $book->setCover($image);
            }

            //ControllerBase::getLogger()->info("End.");
            return true;
        }
    }

